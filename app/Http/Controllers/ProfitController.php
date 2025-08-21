<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sales;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Management\Product;
use App\Models\Management\Tank;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $productId = $request->get('product_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $enableSettlement = filter_var($request->get('enable_settlement', false), FILTER_VALIDATE_BOOLEAN);

        // Sheets (per product profit)
        $sheets = $this->getProfitSheetInfo($startDate, $endDate, $productId);

        // Income and Expenses (grouped)
        $incomeTransactions = $this->getIncomeTransactionsForProfitSheet($startDate, $endDate);
        $expenseTransactions = $this->getExpenseTransactionsForProfitSheet($startDate, $endDate);

        // Gain/Loss from purchase/sale
        $gainPurchase = $this->getGainFromPurchase($startDate, $endDate);
        $lossSale = $this->getLossFromSale($startDate, $endDate);

        // Compute totals
        $grossProfit = (float) ($sheets->sum('profit'));
        $incomeTotal = (float) ($incomeTransactions->sum('amount'));
        $expenseTotal = (float) ($expenseTransactions->sum('amount'));
        $gainLossTotal = (float) ($gainPurchase->sum('amount') - $lossSale->sum('amount'));

        $netProfit = $grossProfit + $incomeTotal - $expenseTotal + $gainLossTotal;

        $mpProfit = 0;
        $settlementRows = collect();
        if ($enableSettlement) {
            $settlement = $this->calculateRateSettlementProfit();
            $mpProfit = $settlement['total'];
            $settlementRows = $settlement['rows'];
        }

        return view('admin.pages.profit.index', [
            'sheets' => $sheets,
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'gainPurchase' => $gainPurchase,
            'lossSale' => $lossSale,
            'grossProfit' => $grossProfit,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'gainLossTotal' => $gainLossTotal,
            'netProfit' => $netProfit,
            'mpProfit' => $mpProfit,
            'settlementRows' => $settlementRows,
            'enableSettlement' => $enableSettlement,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'productId' => $productId,
        ]);
    }

    private function getProfitSheetInfo(string $startDate, string $endDate, $productId)
    {
        $query = Sales::select('product_id',
                DB::raw('SUM(profit) as profit'),
                DB::raw('SUM(quantity) as soldstock'))
            ->whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate])
            ->groupBy('product_id')
            ->orderBy('product_id');

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        return $query->get()->map(function($row){
            $product = Product::find($row->product_id);
            $row->product_name = $product->name ?? 'deleted item';
            return $row;
        });
    }

    private function getIncomeTransactionsForProfitSheet(string $startDate, string $endDate)
    {
        // vendor_type 5 = income
        return Transaction::select('vendor_id', 'vendor_name',
                DB::raw("SUM(CASE WHEN transaction_type = 2 THEN -amount ELSE amount END) as amount"))
            ->where('vendor_type', 5)
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->groupBy('vendor_id', 'vendor_name')
            ->orderBy('vendor_name')
            ->get();
    }

    private function getExpenseTransactionsForProfitSheet(string $startDate, string $endDate)
    {
        // vendor_type 4 = expense
        return Transaction::select('vendor_id', 'vendor_name',
                DB::raw("SUM(CASE WHEN transaction_type = 2 THEN amount ELSE -amount END) as amount"))
            ->where('vendor_type', 4)
            ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
            ->groupBy('vendor_id', 'vendor_name')
            ->orderBy('vendor_name')
            ->get();
    }

    private function getGainFromPurchase(string $startDate, string $endDate)
    {
        // vendor_type in (4,5) for purchases per old code
        return Purchase::select('product_id', DB::raw('SUM(total_amount) as amount'), DB::raw('SUM(stock) as quantity'))
            ->whereIn('vendor_type', [4, 5])
            ->whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate])
            ->groupBy('product_id')
            ->get()
            ->map(function ($row) {
                $product = Product::find($row->product_id);
                $row->product_name = $product->name ?? 'Unknown Product';
                return $row;
            });
    }

    private function getLossFromSale(string $startDate, string $endDate)
    {
        // vendor_type in (4,5) for sales per old code
        return Sales::select('product_id', DB::raw('SUM(amount) as amount'), DB::raw('SUM(quantity) as quantity'))
            ->whereIn('vendor_type', [4, 5])
            ->whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate])
            ->groupBy('product_id')
            ->get()
            ->map(function ($row) {
                $product = Product::find($row->product_id);
                $row->product_name = $product->name ?? 'Unknown Product';
                return $row;
            });
    }

    private function calculateRateSettlementProfit(): array
    {
        $rows = collect();
        $total = 0;

        $products = Product::orderBy('name')->get(['id', 'name', 'current_sale', 'current_purchase']);

        foreach ($products as $product) {
            $stockInTank = (float) Tank::where('product_id', $product->id)->sum('opening_stock');
            $profit = $this->calculateChangeRateProfit($product->id, $stockInTank, (float) $product->current_sale, (float) $product->current_purchase);
            $profit = (float) ($profit ?? 0);
            $total += $profit;
            $rows->push((object) [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'profit' => $profit,
            ]);
        }

        return ['total' => $total, 'rows' => $rows];
    }

    // Port of old calculateChangeRateProfit
    private function calculateChangeRateProfit(int $productId, float $openingStock, float $currentSaleValue, float $currentPurchase): float
    {
        $saleQuantity = $openingStock;
        $purchaseRows = Purchase::where('product_id', $productId)
            ->whereRaw('CAST(sold_quantity AS UNSIGNED) < CAST(stock AS UNSIGNED)')
            ->orderBy('purchase_date', 'asc')
            ->get(['id', 'rate', 'sold_quantity', 'stock']);

        $totalProfit = 0.0;

        foreach ($purchaseRows as $purchase) {
            $availableStock = (float) $purchase->stock - (float) $purchase->sold_quantity;
            if ($availableStock <= 0) {
                continue;
            }

            if ($saleQuantity <= $availableStock) {
                $purchasePriceDifference = $currentPurchase - (float) $purchase->rate;
                $profitPrice = $saleQuantity * $purchasePriceDifference;
                $totalProfit += $profitPrice;
                return $totalProfit;
            } else {
                $purchasePriceDifference = $currentPurchase - (float) $purchase->rate;
                $profitPrice = $availableStock * $purchasePriceDifference;
                $totalProfit += $profitPrice;
                $saleQuantity -= $availableStock;
            }
        }

        return $totalProfit;
    }
}


