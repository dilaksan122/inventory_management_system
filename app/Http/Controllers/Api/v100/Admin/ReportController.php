<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Services\Expenses\ExpensesService;
use App\Services\Invoice\InvoiceService;
use App\Services\Product\ProductService;
use App\Services\Purchase\PurchaseServices;
use App\Services\Report\ReportServices;
use App\Traits\ApiReturnFormatTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    use ApiReturnFormatTrait;

    protected $expensesService;
    protected $invoiceService;
    protected $purchaseServices;
    protected $productServices;
    protected $reportServices;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        ExpensesService $expensesService,
        InvoiceService $invoiceService,
        PurchaseServices $purchaseServices,
        ProductService $productServices,
        ReportServices $reportServices
    )
    {
        $this->expensesService      = $expensesService;
        $this->invoiceService       = $invoiceService;
        $this->purchaseServices     = $purchaseServices;
        $this->productServices      = $productServices;
        $this->reportServices       = $reportServices;
    }

    public function expenseReport(Request $request)
    {
            $total              = 0;
            $data               = [];
            $report_range       = '';
            $start              = $request->from_date;
            $end                = $request->to_date;

            if ($start && $end) {
                $report_range   = $start . ' - ' . $end;
                $data           = $this->expensesService->filterByDateRange($start, $end, ['category:id,name']);
            }

            if (isset($request->q) && $request->q = 'all-time') {
                $report_range   = 'All Time';
                $data           = $this->expensesService->get(null, ['category:id,name']);
            }

            // Calculate total
            if ($data instanceof Collection) {
                $total          = $data->sum('total');
            }
            return $this->responseWithSuccess('Expense Report', [
                'data'          => $data,
                'total'         => $total,
                'report_range'  => $report_range
            ]);
    }
    public function sales(Request $request)
    {
        $gross_total        = 0;
        $total_paid         = 0;

        $data               = [];
        $report_range       = '';
        $start              = $request->from_date;
        $end                = $request->to_date;

        if ($start && $end) {
            $report_range   = $start . ' - ' . $end;
            $data           = $this->invoiceService->filterByDateRange($start, $end);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range   = 'All Time';
            $data           = $this->invoiceService->filterWareHouseWiseAll(['warehouse:id,name']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $gross_total    = $data->sum('total');
            $total_paid     = $data->sum('total_paid');
        }
        return $this->responseWithSuccess('Sales Report', [
            'data'          => $data,
            'gross_total'   => $gross_total,
            'total_paid'    => $total_paid,
            'report_range'  => $report_range,
        ]);
    }

    public function purchases(Request $request)
    {
        $total              = 0;
        $data               = [];
        $report_range       = '';
        $start              = $request->from_date;
        $end                = $request->to_date;

        if ($start && $end) {
            $report_range   = $start . ' - ' . $end;
            $data           = $this->purchaseServices->filterByDateRange($start, $end, ['purchaseItems', 'warehouse:id,name']);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range   = 'All Time';
            $data           = $this->purchaseServices->allTime(['purchaseItems', 'warehouse:id,name']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total          = $data->sum('total');
        }
        return $this->responseWithSuccess('Purchase Report', [
            'data'          => $data,
            'total'         => $total,
            'report_range'  => $report_range
        ]);
    }
    public function payments(Request $request)
    {
        $total          = 0;
        $data           = [];
        $report_range   = '';
        $start          = $request->from_date;
        $end            = $request->to_date;


        if ($start && $end) {
            $report_range   = $start . ' - ' . $end;
            $data           = $this->invoiceService->filterPaymentByDateRange($start, $end, ['invoice.warehouse:id,name']);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range   = 'All Time';
            $data           = $this->invoiceService->getAllPayments(['invoice.warehouse:id,name']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total          = $data->sum('amount');
        }

        return $this->responseWithSuccess('Payment Report', [
            'data'          => $data,
            'total'         => $total,
            'report_range'  => $report_range
        ]);
    }
    public function warehouseStock(Request $request)
    {
        $products = $this->productServices->productAllWarehouseStock();
        return $this->responseWithSuccess('Warehouse Stock Report', $products);
    }
    public function lossProfit(Request $request)
    {
        try {
            $report_range       = '';
            $start              = $request->from_date;
            $end                = $request->to_date;

            if ($start && $end) {
                $report_range   = $start . ' - ' . $end;
            }

            if (isset($request->q) && $request->q = 'all-time') {
                $report_range   = 'All Time';
            }

            $loss_profit_data = $this->reportServices->lossProfitCalculation($request);

            return $this->responseWithSuccess('Loss Profit Report', [
                'data'          => $loss_profit_data,
                'report_range'  => $report_range,
            ]);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(),[], 500);
        }
    }
}
