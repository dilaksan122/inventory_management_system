<!doctype html>
<html lang="en">

<head>
  @include('admin.auth.head')
</head>

<body class="body_color">
<!--================Login Form Area =================-->
<!-- Begin page -->
{{--<div class="accountbg ic-accountbg"></div>--}}

{{--<div class="wrapper-page account-page-full">--}}

{{--    <div class="card shadow-none">--}}
{{--        <div class="card-block">--}}

{{--            <div class="account-box">--}}

{{--                <div class="card-box shadow-none ic_main_form_inner p-4 w-auto">--}}
{{--                    <div class="p-2">--}}
{{--                        <div class="text-center mt-4">--}}
{{--                            @if(config('site_logo'))--}}
{{--                                <img class="ic-login-img img-fluid" src="{{ config('site_logo') }}" alt="logo">--}}
{{--                            @else--}}
{{--                                <img class="img-fluid ic-login-img" src="{{ config('site_logo') ?? static_asset('admin/images/logo.png') }}" alt="imgs">--}}

{{--                            @endif--}}
{{--                            --}}{{--                                <a href="index.html"><img src="assets/images/logo-dark.png" height="22" alt="logo"></a>--}}
{{--                        </div>--}}

{{--                        <h4 class="font-size-18 mt-5 text-center">Login</h4>--}}
{{--                        <p class="text-muted text-center">Manage your business with our automated Inventory Management System</p>--}}
{{--                        @if(session()->get('success'))--}}
{{--                            <div class="alert alert-success">{{ session()->get('success') }}</div>--}}
{{--                        @elseif(session()->get('error'))--}}
{{--                            <div class="alert alert-danger">{{ session()->get('error') }}</div>--}}
{{--                        @endif--}}

{{--                        <form class="mt-4 ic_main_form_inner w-auto shadow-none p-0" action="{{ url('/admin/login') }}" method="post">--}}
{{--                            @csrf--}}
{{--                            <div class="form_box">--}}
{{--                                <div class="login_form text-left">--}}
{{--                                    <label class="form-label" for="username">Email</label>--}}
{{--                                    <div class="mb-3 form-group">--}}
{{--                                        <input type="email" name="email" class="form-control ic_control_form" id="username" placeholder="Enter username">--}}
{{--                                    </div>--}}
{{--                                    <label class="form-label" for="userpassword">Password</label>--}}
{{--                                    <div class="mb-3 form-group">--}}
{{--                                        <input type="password" name="password" class="form-control ic_control_form" id="userpassword" placeholder="Enter password">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="mb-3 row">--}}
{{--                                <div class="col-sm-12">--}}
{{--                                    <button class="btn submit_btn form-control" type="submit">Log In</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}

{{--                        <div class="text-center login-form-footer">© {{ date('Y') }} {{--}}
{{--                            __('custom.all_right_reserved') }} | {{ __('custom.design_and_developed') }} <a class="ic-main-color" href="https://itclanbd.com/"><span class="d-block">ITclan--}}
{{--                                    BD</span></a></div>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}

{{--</div>--}}

    <section class="ic_main_form_area">
        <div class="container">
            @include('flash::message')
            <div class="row align-items-center">
                <div class="col-lg-7 d-none d-lg-block">
                    <div class="ic-fxied-image">
                        <div class="login-img-slider-heads">
                            <div class="img-items">
                                <img src={{ config('login_slider_image_1') ?? static_asset('admin/images/1.png') }} class="img-fluid" alt="slider-img">
                            </div>
                            <div class="img-items">
                                <img src={{ config('login_slider_image_2') ?? static_asset('admin/images/2.png') }} class="img-fluid" alt="slider-img">
                            </div>
                            <div class="img-items">
                                <img src={{ config('login_slider_image_3') ?? static_asset('admin/images/3.png') }} class="img-fluid" alt="slider-img">
                            </div>
                        </div>
                        <div class="mobile-img-slider-heads">
                            <div class="img-items">
                                <img src={{ config('login_slider_image_m_1') ?? static_asset('admin/images/M1.png') }} class="img-fluid" alt="slider-img">
                            </div>
                            <div class="img-items">
                                <img src={{ config('login_slider_image_m_2') ?? static_asset('admin/images/M2.png') }} class="img-fluid" alt="slider-img">
                            </div>
                            <div class="img-items">
                                <img src={{ config('login_slider_image_m_3') ?? static_asset('admin/images/M3.png') }} class="img-fluid" alt="slider-img">
                            </div>
                        </div>
                        <img class="img-fluid w-100" src="{{ static_asset('admin/images/Slider_Frame.png') }}" alt="slider-img">
                    </div>
                </div>
                <div class="col-lg-5 col-md-7 m-auto ml-lg-auto">
                    <div class="ic_main_form_inner">
                        <div class="form_box">
                            <div class="col-lg-12">
                                @if(config('site_logo'))
                                <img class="ic-login-img img-fluid" src="{{ config('site_logo') }}" alt="logo">
                                @else
                                <img class="img-fluid ic-login-img" src="{{ config('site_logo') ?? static_asset('admin/images/logo.png') }}" alt="imgs">

                                @endif

                                <h2>{{__('custom.login')}}</h2>
                                <p>{{ config('login_message_system') ?? __('custom.login_message') }}</p>
                            </div>

                            @if(session()->has('loginFail'))
                            <p class="alert alert-danger text-center">
                                {{ session()->get('loginFail') }}
                            </p>
                            @endif


                            <form class="row login_form justify-content-center" action="{{ url('/admin/login') }}" method="post" id="loginForm" novalidate="novalidate">

                                @csrf

                                <div class="form-group col-lg-12">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="demo@app.com">
                                    <i class="fa fa-user"></i>
                                </div>
                                @if ($errors->has('email'))
                                <p class="ic-error-massage">{{ $errors->first('email') }}</p>
                                @endif
                                <div class="form-group col-lg-12">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>

                                </div>
                                @if ($errors->has('password'))
                                <p class="ic-error-massage">{{ $errors->first('password') }}</p>
                                @endif
                                <div class="form-group col-lg-12">
                                    <button type="submit" value="submit" class="btn submit_btn form-control">{{
                                        __('custom.login') }}</button>
                                </div>
                                @if(\Config::get('app.demo_mode'))
                                <div class="form-group col-lg-12">
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="javascript:void(0)" data-value="admin" class="btn btn-primary btn-oneclick-login form-control">{{ __('custom.admin_login') }} </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="javascript:void(0)" data-value="customer" class="btn btn-primary btn-oneclick-login form-control">{{ __('custom.customer_login') }} </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </form>
                        </div>
                        <div class="text-center login-form-footer">© {{ date('Y') }} {{
                            __('custom.all_right_reserved') }} | {{ __('custom.design_and_developed') }} <a class="ic-main-color" href="https://itclanbd.com/"><span class="d-block">ITclan
                                    BD</span></a></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!--================End Login Form Area =================-->

@include('admin.auth.script')

</body>

</html>
