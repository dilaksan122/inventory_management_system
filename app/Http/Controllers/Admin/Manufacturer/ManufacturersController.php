<?php

namespace App\Http\Controllers\Admin\Manufacturer;

use App\Http\Controllers\Controller;
use App\DataTables\ManufacturerDataTable;
use App\Http\Requests\ManufacturerRequest;
use App\Services\Manufacturer\ManufacturerService;

class ManufacturersController extends Controller
{
    protected $manufacturerService;

    /**
     * __construct
     *
     * @param  mixed $manufacturerService
     * @return void
     */
    public function __construct(ManufacturerService $manufacturerService)
    {
        $this->manufacturerService = $manufacturerService;

        $this->middleware(['permission:List Manufacturer'])->only(['index']);
        $this->middleware(['permission:Add Manufacturer'])->only(['create']);
        $this->middleware(['permission:Edit Manufacturer'])->only(['edit']);
        $this->middleware(['permission:Delete Manufacturer'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ManufacturerDataTable $dataTable)
    {
        set_page_meta(__('custom.manufacturer'));
        return $dataTable->render('admin.manufacturers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta(__('custom.add_manufacturer'));
        return view('admin.manufacturers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ManufacturerRequest $request)
    {
        $data = $request->validated();

        if ($this->manufacturerService->createOrUpdateWithFile($data, 'image')) {
            flash(__('custom.manufacturer_created_successfully'))->success();
        } else {
            flash(__('custom.manufacturer_create_failed'))->error();
        }

        return redirect()->route('admin.manufacturers.index');
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
        $manufacturer = $this->manufacturerService->get($id);

        set_page_meta(__('custom.edit_manufacturer'));
        return view('admin.manufacturers.edit', compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ManufacturerRequest $request, $id)
    {
        $data = $request->validated();

        if ($this->manufacturerService->createOrUpdateWithFile($data, 'image', $id)) {
            flash(__('custom.manufacturer_updated_successfully'))->success();
        } else {
            flash(__('custom.manufacturer_update_failed'))->error();
        }

        return redirect()->route('admin.manufacturers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->manufacturerService->delete($id)) {
            flash(__('custom.manufacturer_deleted_successfully'))->success();
        } else {
            flash(__('custom.manufacturer_deleted_failed'))->error();
        }

        return redirect()->route('admin.manufacturers.index');
    }
}
