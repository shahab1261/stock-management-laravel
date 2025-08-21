<?php

namespace App\Http\Controllers;

use App\Models\Dip;
use App\Models\Sales;
use App\Models\Purchase;
use App\Models\Management\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WetStockController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $tankId = $request->get('tank_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get all dippable tanks for dropdown
        $tanks = Tank::where('is_dippable', 1)
            ->with('product')
            ->orderBy('tank_name')
            ->get();

        // Get all dips based on filters
        $dipsQuery = Dip::with(['tank', 'product']);

        if ($tankId) {
            $dipsQuery->where('tankId', $tankId);
        }

        $dipsQuery->whereBetween('dip_date', [$startDate, $endDate])
                  ->orderBy('dip_date', 'asc');

        $allDips = $dipsQuery->get();

        // Process wet stock data
        $wetStockData = $this->processWetStockData($allDips);

        // Calculate summary stats
        $stats = $this->calculateStats($wetStockData);

        return view('admin.pages.wet-stock.index', compact(
            'tanks',
            'allDips',
            'wetStockData',
            'stats',
            'tankId',
            'startDate',
            'endDate'
        ));
    }

    private function processWetStockData($dips)
    {
        $wetStockData = [];
        $cumulativeSale = 0;
        $totalGainLoss = 0;
        $totalPurchase = 0;
        $totalSale = 0;

        foreach ($dips as $dip) {
            $dipDate = $dip->dip_date->format('Y-m-d');
            $tankId = $dip->tankId;

            // Get purchase for this tank and date
            $purchaseStock = $this->getPurchaseByTankDate($tankId, $dipDate);

            // Get sales for this tank and date
            $salesStock = $this->getSalesByTankDate($tankId, $dipDate);

            // Calculate values
            $openingStock = $dip->previous_stock;
            $bookStock = $openingStock + $purchaseStock - $salesStock;
            $dipStock = $dip->liters;
            $gainLoss = $dipStock - $bookStock;
            $totalGainLoss += $gainLoss;
            $cumulativeSale += $salesStock;
            $totalPurchase += $purchaseStock;
            $totalSale += $salesStock;

            // Calculate variance
            $variance = $salesStock > 0 ? ($gainLoss / $salesStock) * 100 : null;
            $cumulativeVariance = $cumulativeSale > 0 ? ($totalGainLoss / $cumulativeSale) * 100 : null;

            $wetStockData[] = [
                'date' => $dip->dip_date,
                'tank_name' => $dip->tank->tank_name ?? 'N/A',
                'product_name' => $dip->product->name ?? 'N/A',
                'opening_stock' => $openingStock,
                'purchase_stock' => $purchaseStock,
                'sales_stock' => $salesStock,
                'book_stock' => $bookStock,
                'dip_value' => $dip->dip_value,
                'dip_stock' => $dipStock,
                'gain_loss' => $gainLoss,
                'total_gain_loss' => $totalGainLoss,
                'variance' => $variance,
                'cumulative_sale' => $cumulativeSale,
                'cumulative_variance' => $cumulativeVariance,
            ];
        }

        // Add totals row
        if (!empty($wetStockData)) {
            $wetStockData[] = [
                'date' => null,
                'tank_name' => 'TOTAL',
                'product_name' => '',
                'opening_stock' => null,
                'purchase_stock' => $totalPurchase,
                'sales_stock' => $totalSale,
                'book_stock' => null,
                'dip_value' => null,
                'dip_stock' => null,
                'gain_loss' => null,
                'total_gain_loss' => null,
                'variance' => null,
                'cumulative_sale' => null,
                'cumulative_variance' => null,
            ];
        }

        return $wetStockData;
    }

    private function getPurchaseByTankDate($tankId, $date)
    {
        $purchase = Purchase::where('tank_id', $tankId)
            ->whereDate('purchase_date', $date)
            ->sum('stock');

        return $purchase ?: 0;
    }

    private function getSalesByTankDate($tankId, $date)
    {
        $sales = Sales::where('tank_id', $tankId)
            ->whereDate('create_date', $date)
            ->sum('quantity');

        return $sales ?: 0;
    }

    private function calculateStats($wetStockData)
    {
        if (empty($wetStockData)) {
            return [
                'total_records' => 0,
                'total_purchase' => 0,
                'total_sales' => 0,
                'total_gain_loss' => 0,
                'avg_variance' => 0,
            ];
        }

        // Remove totals row for calculations
        $dataRows = array_slice($wetStockData, 0, -1);

        $totalPurchase = array_sum(array_column($dataRows, 'purchase_stock'));
        $totalSales = array_sum(array_column($dataRows, 'sales_stock'));
        $totalGainLoss = end($dataRows)['total_gain_loss'] ?? 0;

        // Calculate average variance (excluding null values)
        $variances = array_filter(array_column($dataRows, 'variance'), function($v) {
            return $v !== null;
        });
        $avgVariance = !empty($variances) ? array_sum($variances) / count($variances) : 0;

        return [
            'total_records' => count($dataRows),
            'total_purchase' => $totalPurchase,
            'total_sales' => $totalSales,
            'total_gain_loss' => $totalGainLoss,
            'avg_variance' => $avgVariance,
        ];
    }

    public function export(Request $request)
    {
        // Implementation for export functionality
        $tankId = $request->get('tank_id', '');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get data
        $dipsQuery = Dip::with(['tank', 'product']);

        if ($tankId) {
            $dipsQuery->where('tankId', $tankId);
        }

        $dipsQuery->whereBetween('dip_date', [$startDate, $endDate])
                  ->orderBy('dip_date', 'asc');

        $allDips = $dipsQuery->get();
        $wetStockData = $this->processWetStockData($allDips);

        // Return JSON for AJAX export
        return response()->json([
            'success' => true,
            'data' => $wetStockData,
            'filename' => 'wet_stock_' . $startDate . '_to_' . $endDate . '.csv'
        ]);
    }
}
