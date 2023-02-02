<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;

class CheckoutController extends Controller
{
    public function login_checkout(){
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        return view('pages.checkout.login_checkout',[
            'cate_product'=> $cate_product,
            'brand_product'=>$brand_product,
        ]);
    }

    public function add_customer(Request $request){
        $data = array();
        $data['customer_name'] = $request->customer_name;
        $data['customer_email'] = $request->customer_email;
        $data['customer_password'] = md5($request->customer_password);
        $data['customer_phone'] = $request->customer_phone;

        $customer_id= DB::table('tbl_customers')->insertGetId($data);
        Session::put('customer_id', $customer_id);
        Session::put('customer_name',$request->customer_name);

        return Redirect::to('/checkout');
    }

    public function checkout(){
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        return view('pages.checkout.checkout',[
            'cate_product'=> $cate_product,
            'brand_product'=>$brand_product,
        ]);
    }

    public function save_checkout_customer(Request $request){
        $data = array();
        $data['shipping_name'] = $request->shipping_name;
        $data['shipping_email'] = $request->shipping_email;
        $data['shipping_address'] = $request->shipping_address;
        $data['shipping_phone'] = $request->shipping_phone;
        $data['shipping_notes'] = $request->shipping_notes;

        $shipping_id= DB::table('tbl_shipping')->insertGetId($data);
        Session::put('shipping_id', $shipping_id);
        // Session::put('customer_name',$request->customer_name);

        return Redirect::to('/payment');
    }
    public function payment(){
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();
        return view('pages.checkout.payment',[
            'cate_product'=> $cate_product,
            'brand_product'=>$brand_product,
        ]);
    }
    public function logout_checkout(){
        Session::flush(); // xóa dữ liệu phiên
        return Redirect::to('/login-checkout');
    }
    public function login_customer(Request $request){
        $customer_email = $request->email_account;
        $customer_password = md5($request->password_account);
        $result = DB::table('tbl_customers')->where('customer_email', $customer_email)->where('customer_password', $customer_password)->first();

        if($result){
            Session::put('customer_id',$result->customer_id);
            return Redirect::to('checkout');
        }else{
            return Redirect::back(); // nên back hay nên trả về trang chủ hoặc giỏ hàng hơn?
        }

    }
}
