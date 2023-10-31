<?php

namespace App\Http\Controllers\Admin\Brand;

use App\DataTables\BrandDataTable;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use App\Services\Brand\BrandService;

class BrandsController extends Controller
{
    protected $brandService;

    /**
     * __construct
     *
     * @param  mixed $brandService
     * @return void
     */
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;

        $this->middleware(['permission:List Brand'])->only(['index']);
        $this->middleware(['permission:Add Brand'])->only(['create']);
        $this->middleware(['permission:Edit Brand'])->only(['edit']);
        $this->middleware(['permission:Delete Brand'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BrandDataTable $dataTable)
    {
        set_page_meta(__('custom.brand'));
        return $dataTable->render('admin.brands.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta(__('custom.add_brand'));
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $request)
    {
        $data = $request->validated();

        if ($this->brandService->createOrUpdateWithFile($data, 'image')) {
            flash(__('custom.brand_create_successful'))->success();
        } else {
            flash(__('custom.brand_create_failed'))->error();
        }

        return redirect()->route('admin.brands.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = $this->brandService->get($id);

        set_page_meta(__('custom.edit_brand'));
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->brandService->createOrUpdateWithFile($data, 'image', $id)) {
            flash(__('custom.brand_updated_successful'))->success();
        } else {
            flash(__('custom.brand_updated_failed'))->error();
        }

        return redirect()->route('admin.brands.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->brandService->delete($id)) {
            flash(__('custom.brand_deleted_successful'))->success();
        } else {
            flash(__('custom.brand_deleted_failed'))->error();
        }

        return redirect()->route('admin.brands.index');
    }
}
