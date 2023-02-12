<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show_info($customer_id){
        $cate_product = DB::table('tbl_category_product')->orderBy('category_id','desc')->get();
        $brand_product = DB::table('tbl_brand')->orderby('brand_id','desc')->get();
        $user_info = DB::table('tbl_customers')->where('customer_id',$customer_id)->get();
        return view('pages.user.user',[
            'customer_id'=>$customer_id,
            'cate_product' => $cate_product,
            'brand_product' => $brand_product,
            'user_info'=>$user_info
        ]);
    }
}
