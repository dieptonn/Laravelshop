<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function save_cart(Request $request){
        // $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        // $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        $productId = $request->productid_hidden;
        $quantity = $request->qty;
        $product_info = DB::table('tbl_product')->where('product_id', $productId)->first(); // sao deo get() duoc nhi?
        // $data;
        // Cart::add('293ad', 'Product 1', 1, 9.99, 550);
        $data['id'] = $productId;
        $data['qty'] = $quantity;
        $data['name'] = $product_info->product_name;
        $data['price'] = $product_info->product_price;
        $data['weight'] = '123';
        $data['options']['image'] = $product_info->product_image;
        Cart::setGlobalTax(5);
        Cart::add($data);
        // Cart::destroy();
        return Redirect::to('/show-cart');
        
    }

    public function show_cart(){
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        return view('pages.cart.show_cart',[
            'cate_product' => $cate_product,
            'brand_product' => $brand_product,
        ]);
    }

    public function delete_to_cart($rowId){
        Cart::update($rowId,0);
        return Redirect::to('/show-cart');
    }
    public function update_cart_quantity(Request $request){
        $rowId = $request->rowId_cart;
        $qty = $request -> quantity;
        Cart::update($rowId,$qty);
        return Redirect::to('/show-cart');
    }
    public function add_cart_ajax(Request $request ){
        $data = $request->all();
        // print_r($data);
        $session_id = substr(md5(microtime()),rand(0,26),5);
        $cart  = Session::get('cart');
        if($cart == true){
            $is_avaiable = 0;
            foreach($cart as $key => $val){
                if($val['product_id']==$data['cart_product_id']){
                    $is_avaiable++;
                    $cart[$key] = array(
                    'session_id' => $val['session_id'],
                    'product_name' => $val['product_name'],
                    'product_id' => $val['product_id'],
                    'product_image' => $val['product_image'],
                    'product_quantity' => $val['product_quantity'],
                    'product_qty' => $val['product_qty']+ $data['cart_product_qty'],
                    'product_price' => $val['product_price'],
                    );
                    Session::put('cart',$cart);
                }
            }
            if($is_avaiable == 0){
                $cart[] = array(
                'session_id' => $session_id,
                'product_id' => $data['cart_product_id'],
                'product_name' => $data['cart_product_name'],
                'product_image' => $data['cart_product_image'],
                'product_qty' => $data['cart_product_qty'],
                'product_price' => $data['cart_product_price'],
            );
                Session::put('cart',$cart);
            }  
        }else{
            $cart[] = array(
                'session_id' => $session_id,
                'product_id' => $data['cart_product_id'],
                'product_name' => $data['cart_product_name'],
                'product_image' => $data['cart_product_image'],
                'product_qty' => $data['cart_product_qty'],
                'product_price' => $data['cart_product_price'],
            );
        }
        Session::put('cart',$cart);
        Session::save();
    }
    
    public function show_cart_ajax(){
        $cate_product = DB::table('tbl_category_product')->where('category_status','1')->orderby('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1')->orderby('brand_id','desc')->get();

        return view('pages.cart.cart_ajax',[
            'cate_product' => $cate_product,
            'brand_product' => $brand_product,
        ]);
    }
}
