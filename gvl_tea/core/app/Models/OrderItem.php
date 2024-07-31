<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    const VNP_URL = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    const VNP_HASH_SECRET = "YVVVDXXUGTGPFEVRUBWEXKIIYNNFUUTZ";
    const VNP_TMN_CODE = "B6D7F86K";

    protected $fillable = [
        "product_order_id", 
        "product_id", 
        "user_id", 
        "title", 
        "sku", 
        "category", 
        "image", 
        "summary", 
        "description", 
        "price", 
        "previous_price", 
  
       ];


}
