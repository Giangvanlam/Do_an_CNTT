@extends('front.layout')


@section('content')

    <!--   hero area start   -->
    <section class="page-title-area d-flex align-items-center" style="background-image:url('{{asset('assets/front/img/'.$bs->breadcrumb)}}')">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-title-item text-center">
                        <h2 class="title">{{__("Errors")}}</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('front.index')}}"><i class="flaticon-home"></i>{{__('Home')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{__("Errors")}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--====== PAGE TITLE PART ENDS ======-->

    <div class="checkout-message">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="checkout-success">
                        <div class="icon text-danger"><i class="fa-solid fa-xmark-large"></i></div>
                        <h2>{{__('Errors')}}!</h2>
                        <p>Rất tiếc đã xảy ra lỗi trong quá trình thanh toán</p>
                        <p>Thanh toán không thành công</p>
                        <p class="mt-4">{{__('Thank you.')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
