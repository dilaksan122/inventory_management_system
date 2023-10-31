<?php

namespace App\Http\Controllers\Customer\Invoice;

use App\DataTables\CustomerDraftInvoiceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\DraftInvoiceRequest;
use App\Http\Requests\InvoiceRequest;
use App\Services\Customer\CustomerService;
use App\Services\Invoice\DraftInvoiceService;
use App\Services\Invoice\InvoiceService;
use App\Services\Product\ProductCategoryService;
use App\Services\Product\ProductService;
use App\Services\Warehouse\WarehouseService;

class DraftInvoiceController extends Controller
{
    protected $draftInvoiceService;
    protected $customerService;
    protected $productService;
    protected $categoryService;
    protected $warehouseService;
    protected $invoiceService;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        DraftInvoiceService $draftInvoiceService,
        CustomerService $customerService,
        ProductService $productService,
        ProductCategoryService $categoryService,
        WarehouseService $warehouseService,
        invoiceService $invoiceService
    )
    {
        $this->draftInvoiceService      = $draftInvoiceService;
        $this->customerService          = $customerService;
        $this->productService           = $productService;
        $this->categoryService          = $categoryService;
        $this->warehouseService         = $warehouseService;
        $this->invoiceService           = $invoiceService;
    }
        public function index(CustomerDraftInvoiceDataTable $dataTable)
    {
        set_page_meta(__('custom.invoice'));
        return $dataTable->render('customer.invoice.draft.index');
    }
    public function store(DraftInvoiceRequest $request)
    {
        $data = $request->validated();

        // Customer
        if (!$data['customer_id']) {
            $data['customer'] = $data['walkin_customer'];
        } else {
            $customer = $this->customerService->get($data['customer_id']);
            $data['customer'] = $customer;
        }

        try {
            $draftInvoice = $this->draftInvoiceService->storeOrUpdate($data);
            flash(__('custom.draft_invoice_created_successful'))->success();

            return response()->json(['success' => true, 'invoice' => $draftInvoice->id], 200);
        } catch (Throwable $th) {
            flash(__('custom.draft_invoice_created_failed'))->success();

            return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
        }
    }
    public function update(DraftInvoiceRequest $request,$id)
    {
        $data = $request->validated();

        // Customer
        if (!$data['customer_id']) {
            $data['customer'] = $data['walkin_customer'];
        } else {
            $customer = $this->customerService->get($data['customer_id']);
            $data['customer'] = $customer;
        }

        try {
            $draftInvoice = $this->draftInvoiceService->storeOrUpdate($data,$id);
            flash(__('custom.draft_invoice_updated_successful'))->success();

            return response()->json(['success' => true, 'invoice' => $draftInvoice->id], 200);
        } catch (Throwable $th) {
            flash(__('custom.draft_invoice_updated_failed'))->success();

            return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $draftInvoice = $this->draftInvoiceService->get($id, ['items']);
        if(!$draftInvoice) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('customer.invoices.index');
        }
        if($draftInvoice->customer_id != auth()->guard('customer')->user()->id) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('customer.invoices.index');
        }

        set_page_meta(__('custom.show_invoice'));
        return view('customer.invoice.draft.show', ['invoice' => $draftInvoice]);
    }
    public function destroy($id)
    {
        $draftInvoice = $this->draftInvoiceService->get($id, ['items']);

        if ($draftInvoice->delete()) {
            flash(__('custom.draft_invoice_deleted_successful'))->success();
        } else {
            flash(__('custom.draft_invoice_deleted_failed'))->error();
        }

        return redirect()->back();
    }
    public function draftToInvoice($id){
        $invoice        = $this->draftInvoiceService->get($id, ['items']);
        if(!$invoice) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('customer.draft-invoices.index');
        }

        $wareHouseId    = $invoice->warehouse_id ?? $this->warehouseService->defaultWareHouse()->id;
        $warehouse      = $this->warehouseService->get($wareHouseId);
        $all_status     = $this->draftInvoiceService->getAllStatus();
        $customers      = $this->customerService->getActiveData(null, ['b_country_data', 'b_state_data', 'b_city_data']);
//        $products       = $this->productService->wareHouseWiseProducts('warehouseStockQty', $warehouse);//$this->productService->get(null, [], 20);
        $product_stocks = $this->productService->wareHouseWiseAllProductStocks(['product','attribute','attributeItem'], $warehouse);

        $categories     = $this->categoryService->get()->toArray();
        array_unshift($categories, ['id' => 'all', 'text' => 'All Categories']);

        set_page_meta(__('custom.edit_invoice'));
        return view('customer.invoice.edit', compact('invoice', 'all_status', 'customers', 'product_stocks', 'categories', 'warehouse'));

    }
    public function storeDraftToInvoice(InvoiceRequest $request, $id){
        $draft_invoice        = $this->draftInvoiceService->get($id, ['items']);

        $data = $request->validated();
        // Customer
        if (!$data['customer_id']) {
            $data['customer'] = $data['walkin_customer'];
        } else {
            $customer = $this->customerService->get($data['customer_id']);
            $data['customer'] = $customer;
        }

        try {
            $invoice = $this->invoiceService->storeOrUpdate($data);
            flash(__('custom.invoice_created_successful'))->success();

            $draft_invoice->delete();
            return response()->json(['success' => true, 'invoice' => $invoice->id], 200);
        } catch (Throwable $th) {
            flash(__('custom.invoice_created_failed'))->success();

            return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
        }

    }
}
