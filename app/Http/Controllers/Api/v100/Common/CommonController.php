<?php

namespace App\Http\Controllers\Api\v100\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\CitiesResource;
use App\Http\Resources\CountriesResource;
use App\Http\Resources\WarehouseResource;
use App\Models\SystemSettings;
use App\Services\Product\ProductCategoryService;
use App\Services\Warehouse\WarehouseService;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;
use DB;

class CommonController extends Controller
{
    use ApiReturnFormatTrait;
    protected $warehouseService;
    protected $productCategoryService;

    public function __construct(WarehouseService $warehouseService,ProductCategoryService $productCategoryService){
        $this->warehouseService = $warehouseService;
        $this->productCategoryService = $productCategoryService;
    }

    public function getSettings(){
        $generalSettings = SystemSettings::where('settings_key','general')->first();
        $settings = [
            'general_settings'  => $generalSettings->settings_value,
        ];
        return $this->responseWithSuccess(__('Settings'), $settings);
    }
    public function getCountries(){
        $countries = CountriesResource::collection(DB::table('system_countries')->get());
        return $this->responseWithSuccess(__('Country List'), $countries);
    }
    public function getStateByCountry(Request $request){
        if($request->country_id == '' || $request->country_id == null){
            return $this->responseWithError(__('Country not found'), [], 404);
        }
        $states = CitiesResource::collection(DB::table('system_states')->where('country_id', $request->country_id)->get());
        return $this->responseWithSuccess(__('State List'), $states);
    }
    public function getCitiesByState(Request $request){
        if($request->state_id == '' || $request->state_id == null){
            return $this->responseWithError(__('State not found'), [], 404);
        }
        $cities = CitiesResource::collection(DB::table('system_cities')->where('state_id', $request->state_id)->get());
        return $this->responseWithSuccess(__('City List'), $cities);
    }
    public function warehouses(){
        $warehouses = WarehouseResource::collection($this->warehouseService->get());
        return $this->responseWithSuccess(__('Warehouse List'), $warehouses);
    }
    public function categories(){
        $categories = CategoriesResource::collection($this->productCategoryService->getActiveData(null,'subCategory')->where('parent_id', null));
        return $this->responseWithSuccess(__('Category List'), $categories);
    }
}
