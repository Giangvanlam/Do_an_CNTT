<?php

namespace App\Http\Controllers\Payment\product;

use App\BasicSetting;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\Language;
use Illuminate\Support\Str;
use App\Models\ProductOrder;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Carbon\Carbon;
use App\Models\PaymentGateway;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use Mail;
use PDF;
use Auth;



class PaymentCODController extends Controller
{

    private $_api_context;

    public function store(Request $request)
    {

        if (!Session::has('cart')) {
            return view('errors.404');
        }
        Carbon::now()->format('Y-m-d');

        $cart = Session::get('cart');

        $total = 0;
        foreach ($cart as $id => $item) {
            $product = Product::findOrFail($id);
            if ($product->stock < $item['qty']) {
                Session::flash('stock_error', $product->title . ' stock not available');
                return back();
            }
            $total  += $product->current_price * $item['qty'];
        }
        $shipping_method = '';
        $shippig_charge = in_array(request('billing_city'), [1, 79]) ? env('FEE_SHIP_CITY', 20000) : env('FEE_SHIP_DEFAULT', 35000);
        $total = round($total + $shippig_charge, 2);
        $total_pay = $total;

        $request->validate([
            'billing_fname' => 'required',
            'billing_lname' => 'required',
            'billing_address' => 'required',
            'billing_city' => 'required',
            'billing_country' => 'required',
            'billing_number' => 'required',
            'billing_email' => 'required',
            'shpping_fname' => 'present', //present
            'shpping_lname' => 'present',
            'shpping_address' => 'present',
            'shpping_city' => 'present',
            'shpping_country' => 'present',
            'shpping_number' => 'present',
            'shpping_email' => 'present',
        ]);

        $input = $request->all();
        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }


        $be = $currentLang->basic_extended;

        $title = 'Product Checkout';
        $success_url = action('Payment\product\PaymentCODController@payreturn');

        $order['order_amount'] = round($total, 2);
        $total = round(($total / $be->base_currency_rate), 2);

        if($request->method == 'delivery'){
            $order['order_number'] = "COD_" . Str::random(4) . now()->format('dmYHis');
            $txnid = "COD_" . substr(hash('sha256', mt_rand() . microtime()), 0, 17);
            $charge = "COD_" . substr(hash('sha256', mt_rand() . microtime()), 0, 22);
        }else if($request->method == 'atm'){
            $order['order_number'] = "ATM_" . Str::random(4) . now()->format('dmYHis');
            $txnid = "ATM_" . substr(hash('sha256', mt_rand() . microtime()), 0, 17);
            $charge = "ATM_" . substr(hash('sha256', mt_rand() . microtime()), 0, 22);
        } else if ($request->method == 'vnpay') {
            $order['order_number'] = "VNPAY_" . Str::random(4) . now()->format('dmYHis');
            $txnid = "VNPAY_" . substr(hash('sha256', mt_rand() . microtime()), 0, 17);
            $charge = "VNPAY_" . substr(hash('sha256', mt_rand() . microtime()), 0, 22);
        };

        $order_data_obj = json_decode (json_encode ($order), FALSE);


        $charge_id = strtoupper($charge);

            $province = Province::where('id', $request->billing_city)->first();
            $order = new ProductOrder;
            $order->billing_fname = $request->billing_fname;
            $order->billing_lname = $request->billing_lname;
            $order->billing_email = $request->billing_email;
            $order->billing_address = $request->billing_address;
            $order->billing_city = !empty($province->name) ? $province->name : '';
            $order->billing_district = $request->billing_district;
            $order->billing_town = $request->billing_town;
            $order->billing_country = $request->billing_country;
            $order->billing_number = $request->billing_number;
            $order->shpping_fname = $request->shpping_fname;
            $order->shpping_lname = $request->shpping_lname;
            $order->shpping_email = $request->shpping_email;
            $order->shpping_address = $request->shpping_address;
            $order->shpping_city = $request->shpping_city;
            $order->shpping_district = $request->shpping_district;
            $order->shpping_town = $request->shpping_town;
            $order->shpping_country = $request->shpping_country;
            $order->shpping_number = $request->shpping_number;
            // var_dump($order_data);


            $order->total = round($order_data_obj->order_amount, 2);
            // var_dump($order->total);
            $order->shipping_method = $shipping_method; //Home Delivery
            $order->shipping_charge = round($shippig_charge, 2);
            // var_dump($order->shipping_charge);
            $order->method = $request->method;
            // var_dump($request->method);
            $order->currency_code = $be->base_currency_text; // USD
            $order->currency_code_position = $be->base_currency_text_position;// right
            $order->currency_symbol = $be->base_currency_symbol; //$
            $order->currency_symbol_position = $be->base_currency_symbol_position; //right

            $order['order_number'] = $order_data_obj->order_number;


            $order['payment_status'] = "Pending";
            $order['txnid'] = $txnid;

            $order['charge_id'] = $charge_id;

            $order['user_id'] = Auth::user()->id;

            if($request->method == 'delivery'){
                $order['method'] = 'Thanh toán khi nhận hàng';
            }else if($request->method == 'atm'){
                $order['method'] = 'Chuyển khoản';
            } else if ($request->method == 'vnpay') {
                $order['method'] = 'Thanh tóa qua vnpay';
            }

            $order->save();
            $order_id = $order->id;
            $carts = Session::get('cart');
            $products = [];
            $qty = [];
            foreach ($carts as $id => $item) {
                $qty[] = $item['qty'];
                $products[] = Product::findOrFail($id);
            }



            foreach ($products as $key => $product) {
                if (!empty($product->category)) {
                    $category = $product->category->name;
                } else {
                    $category = '';
                }
                OrderItem::insert([
                    'product_order_id' => $order->id,
                    'product_id' => $product->id,
                    'user_id' => Auth::user()->id,
                    'title' => $product->title,
                    'qty' => $qty[$key],
                    'category' => $category,
                    'price' => $product->current_price,
                    'previous_price' => $product->previous_price,
                    'image' => $product->feature_image,
                    'summary' => $product->summary,
                    'description' => $product->description,
                    'created_at' => Carbon::now()
                ]);
            }


            foreach ($cart as $id => $item) {
                $product = Product::findOrFail($id);
                $stock = $product->stock - $item['qty'];
                Product::where('id', $id)->update([
                    'stock' => $stock
                ]);
            }
            // xuất ra file pdf hóa đơn
            $fileName = Str::random(4) . time() . '.pdf';
            $path = public_path() . '/assets/front/invoices/product/' . $fileName;
            $data['order']  = $order;
            $pdf = PDF::loadView('pdf.product', $data);
            $pdf->save($path);


            ProductOrder::where('id', $order_id)->update([
                'invoice_number' => $fileName
            ]);



            // Send Mail to Buyer
            $mail = new PHPMailer(true);
            $user = Auth::user();

            if ($be->is_smtp == 1) {
                try {

                    $mail->isSMTP();
                    $mail->Host       = $be->smtp_host;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $be->smtp_username;
                    $mail->Password   = $be->smtp_password;
                    $mail->SMTPSecure = $be->encryption;
                    $mail->Port       = $be->smtp_port;

                    //Recipients
                    $mail->setFrom($be->from_mail, $be->from_name);
                    $mail->addAddress($user->email, $user->fname);

                    // Attachments
                    $mail->addAttachment('assets/front/invoices/product/' . $fileName);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = "Order placed for Product";
                    $mail->Body    = 'Hello <strong>' . $user->fname . '</strong>,<br/>Your order has been placed successfully. We have attached an invoice in this mail.<br/>Thank you.';

                    $mail->send();
                } catch (Exception $e) { }
            } else {
                try {

                    //Recipients
                    $mail->setFrom($be->from_mail, $be->from_name);
                    $mail->addAddress($user->email, $user->fname);

                    // Attachments
                    $mail->addAttachment('assets/front/invoices/product/' . $fileName);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = "Order placed for Product";
                    $mail->Body    = 'Hello <strong>' . $user->fname . '</strong>,<br/>Your order has been placed successfully. We have attached an invoice in this mail.<br/>Thank you.';

                    $mail->send();
                } catch (Exception $e) { }
            }


            if ($request->method == 'vnpay') {

                $startTime = date("YmdHis");
                $expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));

                $totalMoney = $total_pay;
                $vnp_TxnRef = $this->randString(15);
                $vnp_Amount = $totalMoney * 100;
                $vnp_Locale = 'vn';
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];


                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => OrderItem::VNP_TMN_CODE,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => $startTime,
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => "Thanh toan GD:" . $vnp_TxnRef,
                    "vnp_OrderType" => "other",
                    "vnp_ReturnUrl" => route('front.payreturn.success', $order_id),
                    "vnp_TxnRef" => $vnp_TxnRef,
                    "vnp_ExpireDate"=>$expire
                );

                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }

                ksort($inputData);

                $query = "";
                $i = 0;
                $hashdata = "";

                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = OrderItem::VNP_URL . "?" . $query;
                if (OrderItem::VNP_HASH_SECRET) {

                    $vnpSecureHash =  hash_hmac('sha512', $hashdata, OrderItem::VNP_HASH_SECRET); //
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }
                // Session::forget('paypal_data');
                Session::forget('order_data');
                // Session::forget('paypal_payment_id');
                Session::forget('cart');
                return redirect($vnp_Url);

            }
            // Session::forget('paypal_data');
            Session::forget('order_data');
            // Session::forget('paypal_payment_id');
            Session::forget('cart');
            return redirect($success_url);
        // }
        // return redirect($cancel_url);



    }


    public function payreturn(Request $request, $order_id = 0)
    {
        if(isset($order_id) && $order_id != 0) {
            $order = ProductOrder::find($order_id);

            $inputData = array();
            foreach ($_GET as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }
            if ($inputData['vnp_ResponseCode'] == '00') {
                $order->payment_status = 'Paid';

                $order->save();

                $order = ProductOrder::find($order_id);

                $fileName = Str::random(4) . time() . '.pdf';
                $path = public_path() . '/assets/front/invoices/product/' . $fileName;
                $data['order']  = $order;
                $pdf = PDF::loadView('pdf.product', $data);
                $pdf->save($path);

                ProductOrder::where('id', $order_id)->update([
                    'invoice_number' => $fileName
                ]);
                return view('front.product.success');
            } else {
                return view('front.product.errors');
            }
        } else {
            return view('front.product.errors');
        }


    }

    public function randString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }
}
