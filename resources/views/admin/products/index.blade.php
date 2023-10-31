@extends('admin.layouts.master')

@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.products') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('custom.products') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <h4 class="header-title">{{ __('custom.product_list') }}</h4>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 text-right">
                            <a class="btn btn-sm btn-primary mb-4"
                               href="javascript:void(0)" id="download_barcode">{{
                            __('custom.download_all_barcode') }}</a>

                            <form action="{{ route('admin.products.barcode.download.zip') }}" method="post" id="download_form" style="display: none">
                                @csrf
                                <input type="text" name="product_ids" id="product_ids">
                            </form>
                        </div>
                    </div>

                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    @include('includes.styles.datatable')
@endpush

@push('script')
    @include('includes.scripts.datatable')
    <script src="{{ static_asset('admin/js/bulk_barcode_download.js') }}"></script>
@endpush
