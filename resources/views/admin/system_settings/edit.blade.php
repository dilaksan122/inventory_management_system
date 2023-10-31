@extends('admin.layouts.master')
@php
    $timezones = all_timezones() ? all_timezones() : [];
@endphp
@section('content')
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">{{__('custom.general')}} {{ __('custom.settings') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="ic-javascriptVoid">{{ __('custom.settings') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{__('custom.general')}} {{ __('custom.settings') }}</li>
                </ol>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.system-settings.update') }}" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                   role="tab" aria-controls="pills-home" aria-selected="true">{{__('custom.general')}} {{
                                __('custom.info') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-login-tab" data-toggle="pill" href="#pills-login"
                                   role="tab"
                                   aria-controls="pills-login" aria-selected="true">{{ __('custom.login') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                   role="tab" aria-controls="pills-profile" aria-selected="false">{{
                                __('custom.payment_method') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact"
                                   role="tab" aria-controls="pills-contact" aria-selected="false">{{ __('custom.smtp_mail')
                                }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-product_setting-tab" data-toggle="pill"
                                   href="#pills-product_setting"
                                   role="tab" aria-controls="product_setting" aria-selected="false">{{ __('custom.product_setting')
                                }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-api_key-tab" data-toggle="pill"
                                   href="#pills-api_key"
                                   role="tab" aria-controls="api_key" aria-selected="false">{{ __('custom.api_key')
                                }}</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                 aria-labelledby="pills-home-tab">
                                <h5 class="card-title text-muted">{{__('custom.general')}} {{ __('custom.info') }}</h5>
                                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.site_title') }}</label>
                                            <input type="text" name="general[site_title]" class="form-control"
                                                   value="{{ $settings['general']['site_title'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.timezone') }}</label>
                                            <select name="general[timezone]" class="select2 form-control">
                                                @foreach($timezones as $key => $value)
                                                    <option value="{{ $key }}" {{ config('app.timezone') ? (config('app.timezone') == $key ? 'selected' : '') : '' }}>{{ $value }}( {{ $key }}) </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.site_logo') }} <small class="text-muted">(105px x
                                                    30px)</small></label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[site_logo]" class="form-control image_pick" data-image-for="site_logo">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_site_logo"
                                                         src="{{ $settings['general']['site_logo'] ?? static_asset('images/default-64.png') }}"
                                                         alt="avatar"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.favicon') }} <small class="text-muted">(16px x
                                                    16px)</small></label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[favicon]" class="form-control image_pick" data-image-for="site_favicon">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_site_favicon"
                                                         src="{{ $settings['general']['favicon'] ?? static_asset('images/default-64.png') }}"
                                                         alt="favicon"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.primary_color') }}</label>
                                            <input type="color" name="general[primary_color]" class="form-control"
                                                   value="{{ @$settings['general']['primary_color'] ?? '#28aaa9' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.secondary_color') }}</label>
                                            <input type="color" name="general[secondary_color]" class="form-control"
                                                   value="{{ @$settings['general']['secondary_color'] ?? '#2b2d5d' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.currency_symbol') }} <small class="text-muted">(Ex.
                                                    $)</small></label>
                                            <input type="text" name="general[currency_symbol]" class="form-control"
                                                   value="{{ $settings['general']['currency_symbol'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.currency_exchange_rate') }} <small class="text-muted">(Base on USD.Your currency = USD?)</small></label>
                                            <input type="number" step="any" name="general[currency_exchange_rate]" class="form-control"
                                                   value="{{ $settings['general']['currency_exchange_rate'] ?? 1 }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.currency_exchange_from') }} <small class="text-muted">(Input Currency code. Exchange to USD)</small></label>
                                            <input type="text" name="general[currency_exchange_from]" class="form-control"
                                                   value="{{ $settings['general']['currency_exchange_from'] ?? 'USD' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.default_tax') }} (%)</label>
                                            <input min="0" type="number" name="general[default_tax]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['default_tax'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <label class="d-block mb-3">{{ __('custom.currency_convert_form_api') }} <small>(You need to Setup cron job on server)</small> </label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="currency_convert_form_api_yes" value="yes"
                                                   {{ array_key_exists('general', $settings) && @$settings['general']['currency_convert_form_api'] == 'yes' ? 'checked' : '' }}
                                                   name="general[currency_convert_form_api]"
                                                   class="custom-control-input">
                                            <label for="currency_convert_form_api_yes" class="custom-control-label">Yes</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="currency_convert_form_api_no" value="no"
                                                   {{ array_key_exists('general', $settings) && @$settings['general']['currency_convert_form_api'] == 'no' ? 'checked' : '' }}
                                                   name="general[currency_convert_form_api]"
                                                   checked="checked"
                                                   class="custom-control-input">
                                            <label for="currency_convert_form_api_no" class="custom-control-label">No</label>
                                        </div>
                                    </div>

                                    <div class="col-6" hidden>
                                        <div class="form-group">
                                            <label>{{ __('custom.default_language') }}</label>
                                            <select name="general[default_language]" class="form-control">
                                                <option
                                                    {{ array_key_exists('general', $settings) && array_key_exists('default_language', $settings['general']) && $settings['general']['default_language'] == 'en' ? 'selected' : '' }} value="en">
                                                    English
                                                </option>
                                                <option
                                                    {{ array_key_exists('general', $settings) && array_key_exists('default_language', $settings['general']) && $settings['general']['default_language'] == 'bn' ? 'selected' : '' }} value="bn">
                                                    বাংলা
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <label class="d-block mb-3">{{ __('custom.is_logo_show_in_invoice') }}</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="is_logo_show_in_invoice_yes" value="yes"
                                                   {{ array_key_exists('general', $settings) && @$settings['general']['is_logo_show_in_invoice'] == 'yes' ? 'checked' : '' }}
                                                   name="general[is_logo_show_in_invoice]"
                                                   checked="checked" class="custom-control-input">
                                            <label for="is_logo_show_in_invoice_yes" class="custom-control-label">Yes</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="is_logo_show_in_invoice_no" value="no"
                                                   {{ array_key_exists('general', $settings) && @$settings['general']['is_logo_show_in_invoice'] == 'no' ? 'checked' : '' }}
                                                   name="general[is_logo_show_in_invoice]"
                                                   class="custom-control-input">
                                            <label for="is_logo_show_in_invoice_no" class="custom-control-label">No</label>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="card-title text-muted">{{__('custom.store')}} {{ __('custom.info') }}</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.store_name') }}</label>
                                            <input type="text" name="general[store_name]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['store_name'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.brand_slogan_or_motto') }}</label>
                                            <input type="text" name="general[brand_slogan]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['brand_slogan'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.store_address') }}</label>
                                            <input type="text" name="general[store_address]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['store_address'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.store_mobile') }}</label>
                                            <input type="text" name="general[store_mobile]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['store_mobile'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.store_website') }}</label>
                                            <input type="text" name="general[store_website]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['store_website'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.invoice_footer') }}</label>
                                            <input type="text" name="general[invoice_footer]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['invoice_footer'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.tin') }}</label>
                                            <input type="text" name="general[tin]"
                                                   class="form-control"
                                                   value="{{ $settings['general']['tin'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.terms_and_conditions') }}</label>
                                            <textarea type="text" name="general[terms_and_conditions]"
                                                      class="form-control summernote" id="summernote"
                                            >
                                                {{ $settings['general']['terms_and_conditions'] ?? '' }}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade show" id="pills-login" role="tabpanel"
                                 aria-labelledby="pills-login-tab">
                                <h5 class="card-title text-muted">{{__('custom.login')}} {{ __('custom.info') }}</h5>
                                @csrf
                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_background') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_background]"
                                                           class="form-control image_pick" data-image-for="login_background">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_background"
                                                         src="{{ $settings['general']['login_background'] ?? static_asset('images/default-64.png') }}"
                                                         alt="image"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_message_system') }}</label>
                                            <input type="text" name="general[login_message_system]" class="form-control"
                                                   value="{{ $settings['general']['login_message_system'] ?? '' }}"
                                                   maxlength="100">
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="" class="text-muted">{{ __('custom.login_slider_pc') }}</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_slider_image_1') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_slider_image_1]"
                                                           class="form-control image_pick" data-image-for="login_slider_1">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_slider_1"
                                                         src="{{ $settings['general']['login_slider_image_1'] ?? static_asset('images/default-64.png') }}"
                                                         alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_slider_image_2') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_slider_image_2]"
                                                           class="form-control image_pick" data-image-for="login_slider_2">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_slider_2"
                                                         src="{{ $settings['general']['login_slider_image_2'] ?? static_asset('images/default-64.png') }}"
                                                         alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_slider_image_3') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_slider_image_3]"
                                                           class="form-control image_pick" data-image-for="login_slider_3">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_slider_3"
                                                         src="{{ $settings['general']['login_slider_image_3'] ?? static_asset('images/default-64.png') }}"
                                                         alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="" class="text-muted">{{ __('custom.login_slider_mobile') }}</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_slider_image_m_1') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_slider_image_m_1]"
                                                           class="form-control image_pick" data-image-for="login_slider_m_1">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_slider_m_1"
                                                         src="{{ $settings['general']['login_slider_image_m_1'] ?? static_asset('images/default-64.png') }}"
                                                         alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_slider_image_m_2') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_slider_image_m_2]"
                                                           class="form-control image_pick" data-image-for="login_slider_m_2">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_slider_m_2"
                                                         src="{{ $settings['general']['login_slider_image_m_2'] ?? static_asset('images/default-64.png') }}"
                                                         alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.login_slider_image_m_3') }}</label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="general[login_slider_image_m_3]"
                                                           class="form-control image_pick" data-image-for="login_slider_m_3">
                                                </div>
                                                <div class="col-lg-4">
                                                    <img class="mt-1 m-sm-0 img-fluid default-image-size" id="img_login_slider_m_3"
                                                         src="{{ $settings['general']['login_slider_image_m_3'] ?? static_asset('images/default-64.png') }}"
                                                         alt=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                 aria-labelledby="pills-profile-tab">
                                <h5 class="card-title text-muted">{{ __('custom.paypal') }}</h5>
                                @csrf
                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.base_url') }}</label>
                                            <input type="text" name="paypal[paypal.baseUrl]" class="form-control"
                                                   value="{{ $settings['paypal']['paypal.baseUrl'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.client_id') }}</label>
                                            <input type="text" name="paypal[paypal.clientId]" class="form-control"
                                                   value="{{ $settings['paypal']['paypal.clientId'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.client_secret') }}</label>
                                            <input type="text" name="paypal[paypal.secret]" class="form-control"
                                                   value="{{ $settings['paypal']['paypal.secret'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <h5 class="card-title text-muted">{{ __('custom.stripe') }}</h5>
                                @csrf
                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.public_key') }}</label>
                                            <input type="text" name="stripe[stripe.public_key]" class="form-control"
                                                   value="{{ $settings['stripe']['stripe.public_key'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.secret_key') }}</label>
                                            <input type="text" name="stripe[stripe.secret_key]" class="form-control"
                                                   value="{{ $settings['stripe']['stripe.secret_key'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                 aria-labelledby="pills-contact-tab">
                                <h5 class="card-title text-muted">{{ __('custom.smtp_mail') }}</h5>
                                @csrf
                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.host') }}</label>
                                            <input type="text" name="mail[mail.mailers.smtp.host]" class="form-control"
                                                   value="{{ $settings['mail']['mail.mailers.smtp.host'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.port') }}</label>
                                            <input type="text" name="mail[mail.mailers.smtp.port]" class="form-control"
                                                   value="{{ $settings['mail']['mail.mailers.smtp.port'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.encryption') }}</label>
                                            <select name="mail[mail.mailers.smtp.encryption]" class="form-control">
                                                <option value="">- Select encryption -</option>
                                                <option {{ array_key_exists('mail', $settings) && array_key_exists('mail.mailers.smtp.encryption', $settings['mail']) && $settings['mail']['mail.mailers.smtp.encryption'] == 'tls' ? 'selected' : '' }} value="tls">TLS</option>
                                                <option {{ array_key_exists('mail', $settings) && array_key_exists('mail.mailers.smtp.encryption', $settings['mail']) && $settings['mail']['mail.mailers.smtp.encryption'] == 'ssl' ? 'selected' : '' }} value="ssl">SSL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.username') }}</label>
                                            <input type="text" name="mail[mail.mailers.smtp.username]"
                                                   class="form-control"
                                                   value="{{ $settings['mail']['mail.mailers.smtp.username'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.password') }}</label>
                                            <input type="text" name="mail[mail.mailers.smtp.password]"
                                                   class="form-control"
                                                   value="{{ $settings['mail']['mail.mailers.smtp.password'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.from_address') }}</label>
                                            <input type="text" name="mail[mail.from.address]" class="form-control"
                                                   value="{{ $settings['mail']['mail.from.address'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.from_name') }}</label>
                                            <input type="text" name="mail[mail.from.name]" class="form-control"
                                                   value="{{ $settings['mail']['mail.from.name'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="pills-product_setting" role="tabpanel"
                                 aria-labelledby="pills-product_setting-tab">
                                <h5 class="card-title text-muted">{{ __('custom.sku_setting') }}</h5>
                                @csrf
                                <div class="">
                                    <div class="col-sm-6">
                                        <label class="d-block mb-3">{{ __('custom.auto') }}</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="auto_yes" value="yes"
                                                   {{ array_key_exists('product_setting', $settings) && $settings['product_setting']['sku.auto'] == 'yes' ? 'checked' : '' }}
                                                   name="product_setting[sku.auto]"
                                                   checked="checked" class="custom-control-input">
                                            <label for="auto_yes" class="custom-control-label">Yes</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="auto_no" value="no"
                                                   {{ array_key_exists('product_setting', $settings) && $settings['product_setting']['sku.auto'] == 'no' ? 'checked' : '' }}
                                                   name="product_setting[sku.auto]"
                                                   class="custom-control-input">
                                            <label for="auto_no" class="custom-control-label">No</label>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="d-block mb-3">{{ __('custom.editable') }}</label>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="editable_yes" value="yes"
                                                       {{ array_key_exists('product_setting', $settings) && $settings['product_setting']['sku.editable'] == 'yes' ? 'checked' : '' }}
                                                       name="product_setting[sku.editable]"
                                                       checked="checked" class="custom-control-input">
                                                <label for="editable_yes" class="custom-control-label">Yes</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="editable_no" value="no"
                                                       {{ array_key_exists('product_setting', $settings) && $settings['product_setting']['sku.editable'] == 'no' ? 'checked' : '' }}
                                                       name="product_setting[sku.editable]"
                                                       class="custom-control-input">
                                                <label for="editable_no" class="custom-control-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.prefix') }}</label>
                                            <input type="text" name="product_setting[sku.prefix]" class="form-control"
                                                   value="{{ array_key_exists('product_setting', $settings) ? $settings['product_setting']['sku.prefix'] : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.suffix') }}</label>
                                            <input type="text" name="product_setting[sku.suffix]" class="form-control"
                                                   value="{{ array_key_exists('product_setting', $settings) ? $settings['product_setting']['sku.suffix'] : '' }}">
                                        </div>
                                    </div>
                                </div>


                                <h5 class="card-title text-muted">{{ __('custom.product_stock_email_config') }}</h5>
                                @csrf
                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.roles') }}</label>
                                            <select name="stock_alert_mail_getter[]" class="form-control product-setting-select2 select2" multiple>
                                                <option value="">- {{ __('custom.select_role') }} -</option>
                                                @if(array_key_exists('stock_alert_mail_getter', $settings))
                                                    @foreach($roles as $id => $role)
                                                        <option {{ in_array($id, $settings['stock_alert_mail_getter']) ? 'selected' : '' }} value="{{ $id }}">{{ $role }}</option>
                                                    @endforeach
                                                @else
                                                    @foreach($roles as $id => $role)
                                                        <option value="{{ $id }}">{{ $role }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="pills-api_key" role="tabpanel"
                                 aria-labelledby="pills-api_key-tab">
                                <h5 class="card-title text-muted">{{ __('custom.api_key') }}</h5>
                                @csrf

                                <div class="">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.api_url') }}<em>({{__t('this_is_base_url_for_api')}})</em></label>
                                            <input type="text" readonly class="form-control"
                                                   value="{{ url('/').'/api/v100' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>{{ __('custom.api_key') }}</label>
                                            <input type="text" name="api_key" class="form-control"
                                                   value="{{ $settings['api_key'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ __('custom.save')
                        }}</button>
                        <button type="reset" class="btn btn-secondary"><i
                                class="fa fa-refresh"></i>{{ __('custom.reset') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('style')
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            tinymce.init({
                selector: '#tinytextarea'
            });
        });
    </script>
@endpush
