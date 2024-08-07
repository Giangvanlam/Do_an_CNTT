@extends('admin.layout')

@section('content')
  <div class="mt-2 mb-4">
    <h2 class="text-white pb-2">{{__('Welcome back')}}, {{Auth::guard('admin')->user()->first_name}} {{Auth::guard('admin')->user()->last_name}}!</h2>
  </div>
  <div class="row">
		<div class="col-sm-6 col-md-4">
			<div class="card card-stats card-primary card-round">
				<div class="card-body">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="flaticon-users"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Team Members')}}</p>
								<h4 class="card-title">{{$currentLang->members()->count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-md-4">
			<div class="card card-stats card-info card-round">
				<div class="card-body">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="flaticon-interface-6"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Subscribers')}}</p>
								<h4 class="card-title">{{App\Models\Subscriber::count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="col-sm-6 col-md-4">
			<div class="card card-stats card-warning card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="fab fa-blogger-b"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Blogs')}}</p>
								<h4 class="card-title">{{$currentLang->blogs()->count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-md-4">
			<div class="card card-stats card-primary card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="fab fa-product-hunt"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Products')}}</p>
								<h4 class="card-title">{{$currentLang->products()->count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-md-4">
			<div class="card card-stats card-info card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="fas fa-users"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Users')}}</p>
								<h4 class="card-title">{{App\Models\User::count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-md-4">
			<div class="card card-stats card-success card-round">
				<div class="card-body ">
					<div class="row">
						<div class="col-5">
							<div class="icon-big text-center">
								<i class="far fa-file"></i>
							</div>
						</div>
						<div class="col-7 col-stats">
							<div class="numbers">
								<p class="card-category">{{__('Custom Pages')}}</p>
								<h4 class="card-title">{{$currentLang->pages()->count()}}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-6 col-md-4">
		  <div class="card card-stats card-danger card-round">
			  <div class="card-body ">
				  <div class="row">
					  <div class="col-3">
						  <div class="icon-big text-center">
							  <i class='far fa-dollar-sign'></i>
						  </div>
					  </div>
					  <div class="col-9 col-stats">
						  <div class="numbers">
							  <p class="card-category">{{__('Doanh thu hôm nay')}}</p>
							  <h4 class="card-title">{{ number_format($total_day) }} đ</h4>
						  </div>
					  </div>
				  </div>
			  </div>
		  </div>
		</div>
		<div class="col-sm-6 col-md-4">
		  <div class="card card-stats card-danger card-round">
			  <div class="card-body ">
				  <div class="row">
					  <div class="col-3">
						  <div class="icon-big text-center">
							  <i class='far fa-dollar-sign'></i>
						  </div>
					  </div>
					  <div class="col-9 col-stats">
						  <div class="numbers">
							  <p class="card-category">{{__('Doanh thu tháng này')}}</p>
							  <h4 class="card-title">{{ number_format($total_month) }}</h4>
						  </div>
					  </div>
				  </div>
			  </div>
		  </div>
		</div>
	  <div class="col-sm-6 col-md-4">
		  <div class="card card-stats card-danger card-round">
			  <div class="card-body ">
				  <div class="row">
					  <div class="col-3">
						  <div class="icon-big text-center">
							  <i class='far fa-dollar-sign'></i>
						  </div>
					  </div>
					  <div class="col-9 col-stats">
						  <div class="numbers">
							  <p class="card-category">{{__('Doanh thu năm nay')}}</p>
							  <h4 class="card-title">{{ number_format($total_year) }}</h4>
						  </div>
					  </div>
				  </div>
			  </div>
		  </div>
	  </div>
	</div>



	<div class="row">
		<div class="col-lg-6">
		  <div class="row row-card-no-pd">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="card-head-row">
								<h4 class="card-title">{{__('Recent Table Book')}}</h4>
							</div>
							<p class="card-category">
							{{__('Top 10 latest table book request')}}</p>
						</div>
						<div class="card-body">
				  <div class="row">
					  <div class="col-lg-12">
						  @if (count($table_books) == 0)
						  <h3 class="text-center">{{__('NO TABLE BOOKING REQUEST FOUND')}}</h3>
						  @else
						  <div class="table-responsive">
							<table class="table table-striped mt-3">
								<thead>
								  <tr>
									<th scope="col">{{__('Name')}}</th>
									<th scope="col">{{__('Email')}}</th>
									<th scope="col">{{__('Phone')}}</th>
									<th scope="col">{{__('Date')}}</th>
									<th scope="col">{{__('Time')}}</th>
									<th scope="col">{{__('Person')}}</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach ($table_books as $key => $table)
									<tr>
									  <td>{{convertUtf8($table->name)}}</td>
									  <td>{{convertUtf8($table->email)}}</td>
									  <td>{{convertUtf8($table->phone)}}</td>
									  <td>{{convertUtf8($table->date)}}</td>
									  <td>{{convertUtf8($table->time)}}</td>
									  <td>{{convertUtf8($table->person)}}</td>
									</tr>

								  @endforeach
								</tbody>
							  </table>
						  </div>
						  @endif
					  </div>
				  </div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
		  <div class="row row-card-no-pd">
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<div class="card-head-row">
								<h4 class="card-title">{{__('Recent Orders')}}</h4>
							</div>
							<p class="card-category">
							{{__('Top 10 latest orders')}}</p>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
                                    @if (count($orders) > 0)
                                        <div class="table-responsive table-hover table-sales">
                                            <table class="table table-striped mt-3">
                                                <thead>
                                                <tr>
                                                    <th scope="col">{{__('Order Number')}}</th>
                                                    <th scope="col">{{__('Date')}}</th>
                                                    <th scope="col">{{__('Total')}}</th>
                                                    <th scope="col">{{__('Payment Status')}}</th>
                                                    <th scope="col">{{__('Details')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($orders as $key => $order)
                                                    <tr>
                                                    <td>{{$order->order_number}}</td>
                                                    <td>{{convertUtf8($order->created_at->format('d-m-Y'))}}</td>
                                                    <td>{{$order->currency_symbol_position == 'left' ? $order->currency_symbol : ''}} {{number_format($order->total,0,',','.')}} {{$order->currency_symbol_position == 'right' ? $order->currency_symbol : ''}}</td>
                                                    <td>
                                                        @if ($order->payment_status == 'Pending' || $order->payment_status == 'pending')

                                                        <p class="badge badge-danger">{{__('UnPaid')}}</p>
                                                        @else
                                                            <p class="badge badge-success">{{__('Paid')}}</p>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <a href="{{route('admin.product.details',$order->id)}}" target="_blank" class="btn btn-primary btn-sm "><i class="fas fa-eye"></i> {{__('Details')}}</a>

                                                    </td>
                                                    </tr>

                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <h2 class="text-center">{{__('NO ORDER FOUND')}}</h2>
                                    @endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	  </div>




@endsection
