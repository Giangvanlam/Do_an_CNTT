<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Session;
use App\Models\Language;
use Config;
use App\Models\BasicSetting as BS;
use App\Models\BasicExtended as BE;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'userLogout']]);
        $this->middleware('setlang');
        $bs = BS::first();
        $be = BE::first();

        Config::set('captcha.sitekey', $bs->google_recaptcha_site_key);
        Config::set('captcha.secret', $bs->google_recaptcha_secret_key);
    }
    // map ra dữ dữ liệu phần login
    public function showLoginForm()
    {

        $url = url()->previous();
        $url = (explode('/', $url));
        if (in_array('checkout', $url)) {
            session(['link' => url()->previous()]);
        }
        // $viewDB = [
        //     'check_name' => ' Gửi từ thư mục user trong LoginController'
        // ];

        return view('user.login');
    }

    public function login(Request $request)
    {

        if (Session::has('link')) {
            $redirectUrl = Session::get('link');
            Session::forget('link');
        } else {
            $redirectUrl = route('user-dashboard');
        }


        //--- Validation Section
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;



        $rules = [
            'number'   => 'required',
            'password'   => 'required',
        ];

        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }
        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];
        $request->validate($rules, $messages);

        $phone = request('number');
        $password = request('password');
        if (!is_numeric($phone) || strlen($phone) != 10) {
            return back()->with('err', "Please enter phone with 10 number")->withInput();
        }

        $user = User::where('number', $phone)->first();
        if (empty($user)) {
            $user = new User();
            $user->number = $phone;
            $user->status = 1;
            $user->save();
        }
        if (Auth::guard('web')->attempt(['number' => $phone, 'password' => $password])) {
            return redirect($redirectUrl);
        }
        return back()->with('err', "Tài khoản không tồn tại");
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect('/');
    }
}
