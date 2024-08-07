@extends('front.layout')

@section('content')
<!--====== PAGE TITLE PART START ======-->

<section class="page-title-area d-flex align-items-center" style="background-image: url('{{asset('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-title-item text-center">
                    <h2 class="title">{{__('Login')}}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('front.index')}}"><i class="flaticon-home"></i>{{__('Home')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('Login')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== PAGE TITLE PART ENDS ======-->


<!--   hero area start    -->
<div class="login-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="login-content">
                    <div class="login-title">
                        <h3 class="title">{{__('Login')}}</h3>
                    </div>
                    <form id="loginForm" action="{{route('user.login')}}" method="POST">
                        @csrf
                        <div class="input-box">
                            <span>Phone *</span>
                            <input type="text" name="number" required value="{{Request::old('number')}}">
                            @if(Session::has('err'))
                                <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                            @endif
                            @error('phone')
                            <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="input-box">
                            <span>{{__('Password')}}</span>
                            <input type="password" name="password" value="{{Request::old('password')}}">
                            @error('password')
                            <p class="text-danger mb-2 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="input-box">
                            @if ($bs->is_recaptcha == 1)
                            <div class="d-block mb-4">
                                <div data-sitekey="{{$bs->google_recaptcha_site_key}}" class="g-recaptcha d-inline-block"></div>
                                @if ($errors->has('g-recaptcha-response'))
                                @php
                                    $errmsg = $errors->first('g-recaptcha-response');
                                @endphp
                                <p class="text-danger mb-0 mt-2">{{__("$errmsg")}}</p>
                                @endif
                            </div>
                        @endif
                        </div>

                        <div class="input-btn">
                            <button type="submit" class="main-btn">{{__('LOG IN')}}</button><br>
                            <a href="{{route('user-register')}}" class="mr-3">{{__("Don't have an account")}}?</a>
                            <a href="{{route('user-forgot')}}">{{__('Lost your password')}}?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--   hero area end    -->
@endsection
