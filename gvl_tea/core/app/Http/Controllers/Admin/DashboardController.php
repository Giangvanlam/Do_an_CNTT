<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TableBook;
use App\Models\ProductOrder;
use App\Models\Language;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('setlang');
    }
    public function dashboard() {
        $data['table_books'] = TableBook::orderby('id','desc')->take(10)->get();
        $data['orders'] = ProductOrder::orderby('id','desc')->take(10)->get();

        $day = date('d');
        $month = date('m');
        $year = date('Y');

        $data['total_day'] = ProductOrder::whereDay('created_at', $day)->whereMonth('created_at',
            $month)->whereYear('created_at', $year)
            ->where('payment_status', '!=', 'Pending')->sum('total');

        $data['total_month'] = ProductOrder::whereMonth('created_at',
            $month)->whereYear('created_at', $year)
            ->where('payment_status', '!=', 'Pending')->sum('total');

        $data['total_year'] = ProductOrder::whereYear('created_at', $year)
            ->where('payment_status', '!=', 'Pending')->sum('total');


      return view('admin.dashboard', $data);
    }
}
