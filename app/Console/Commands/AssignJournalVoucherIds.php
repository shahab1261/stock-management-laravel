<?php

namespace App\Console\Commands;

use App\Models\JournalEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignJournalVoucherIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journal:assign-voucher-ids {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign voucher IDs to existing journal entries based on their creation time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $this->info('📋 Starting voucher ID assignment for journal entries...');
        $this->newLine();

        // Get all journal entries without voucher_id, ordered by created_at
        $entries = JournalEntry::whereNull('voucher_id')
            ->orderBy('created_at')
            ->get();

        if ($entries->isEmpty()) {
            $this->info('✅ No journal entries found without voucher IDs.');
            return;
        }

        $this->info("📊 Found {$entries->count()} journal entries without voucher IDs");
        $this->newLine();

        // Group entries by exact created_at timestamp
        $groupedEntries = $entries->groupBy(function ($entry) {
            return $entry->created_at->format('Y-m-d H:i:s');
        });

        $this->info("📦 Grouped into {$groupedEntries->count()} voucher groups");
        $this->newLine();

        $voucherCounter = 1;
        $totalProcessed = 0;

        foreach ($groupedEntries as $timestamp => $group) {
            $voucherId = 'J' . str_pad($voucherCounter, 6, '0', STR_PAD_LEFT);

            $this->info("🏷️  Assigning Voucher ID: {$voucherId}");
            $this->info("   📅 Created at: {$timestamp}");
            $this->info("   📝 Entries count: {$group->count()}");

            // Show entry details
            foreach ($group as $entry) {
                $debitCredit = $entry->debit_credit == 2 ? 'Debit' : 'Credit';
                $this->line("      • {$entry->vendor_name} - Rs {$entry->amount} ({$debitCredit})");
            }

            if (!$isDryRun) {
                // Update all entries in this group with the same voucher_id
                DB::transaction(function () use ($group, $voucherId) {
                    $group->each(function ($entry) use ($voucherId) {
                        $entry->update(['voucher_id' => $voucherId]);
                    });
                });

                $this->info("   ✅ Updated {$group->count()} entries with voucher ID: {$voucherId}");
            } else {
                $this->info("   🔍 Would update {$group->count()} entries with voucher ID: {$voucherId}");
            }

            $totalProcessed += $group->count();
            $voucherCounter++;
            $this->newLine();
        }

        if ($isDryRun) {
            $this->info("🔍 DRY RUN COMPLETE");
            $this->info("📊 Summary:");
            $this->info("   • Total entries to process: {$totalProcessed}");
            $this->info("   • Total voucher groups: {$groupedEntries->count()}");
            $this->info("   • Voucher ID range: J000001 to J" . str_pad($groupedEntries->count(), 6, '0', STR_PAD_LEFT));
            $this->newLine();
            $this->info("💡 Run without --dry-run to apply these changes");
        } else {
            $this->info("✅ VOUCHER ID ASSIGNMENT COMPLETE");
            $this->info("📊 Summary:");
            $this->info("   • Total entries processed: {$totalProcessed}");
            $this->info("   • Total voucher groups created: {$groupedEntries->count()}");
            $this->info("   • Voucher ID range: J000001 to J" . str_pad($groupedEntries->count(), 6, '0', STR_PAD_LEFT));
        }
    }
}
