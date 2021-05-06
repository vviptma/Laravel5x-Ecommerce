<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Http\Requests;
use App\Slider;
use App\CatePost;
use Illuminate\Support\Facades\Redirect;
use Cart;
use Carbon\Carbon;
use App\Coupon;
session_start();
class CartController extends Controller
{
    public function check_coupon(Request $request){
        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
        $data = $request->all();
        if(Session::get('customer_id')){
           $coupon = Coupon::where('coupon_code',$data['coupon'])->where('coupon_status',1)->where('coupon_date_end','>=',$today)->where('coupon_used','LIKE','%'.Session::get('customer_id').'%')->first();
           if($coupon){
            return redirect()->back()->with('error','Mã giảm giá đã sử dụng,vui lòng nhập mã khác');
        }else{

           $coupon_login = Coupon::where('coupon_code',$data['coupon'])->where('coupon_status',1)->where('coupon_date_end','>=',$today)->first();
                if($coupon_login){
                    $count_coupon = $coupon_login->count();
                    if($count_coupon>0){
                        $coupon_session = Session::get('coupon');
                        if($coupon_session==true){
                            $is_avaiable = 0;
                            if($is_avaiable==0){
                                $cou[] = array(
                                    'coupon_code' => $coupon_login->coupon_code,
                                    'coupon_condition' => $coupon_login->coupon_condition,
                                    'coupon_number' => $coupon_login->coupon_number,

                                );
                                Session::put('coupon',$cou);
                            }
                        }else{
                            $cou[] = array(
                                'coupon_code' => $coupon_login->coupon_code,
                                'coupon_condition' => $coupon_login->coupon_condition,
                                'coupon_number' => $coupon_login->coupon_number,

                            );
                            Session::put('coupon',$cou);
                        }
                        Session::save();
                        return redirect()->back()->with('message','Thêm mã giảm giá thành công');
                    }


                }else{
                    return redirect()->back()->with('error','Mã giảm giá không đúng - hoặc đã hết hạn');
                }
        }
        //neu chua dang nhap
    }else{
        $coupon = Coupon::where('coupon_code',$data['coupon'])->where('coupon_status',1)->where('coupon_date_end','>=',$today)->first();
        if($coupon){
            $count_coupon = $coupon->count();
            if($count_coupon>0){
                $coupon_session = Session::get('coupon');
                if($coupon_session==true){
                    $is_avaiable = 0;
                    if($is_avaiable==0){
                        $cou[] = array(
                            'coupon_code' => $coupon->coupon_code,
                            'coupon_condition' => $coupon->coupon_condition,
                            'coupon_number' => $coupon->coupon_number,

                        );
                        Session::put('coupon',$cou);
                    }
                }else{
                    $cou[] = array(
                        'coupon_code' => $coupon->coupon_code,
                        'coupon_condition' => $coupon->coupon_condition,
                        'coupon_number' => $coupon->coupon_number,

                    );
                    Session::put('coupon',$cou);
                }
                Session::save();
                return redirect()->back()->with('message','Thêm mã giảm giá thành công');
            }


        }else{
            return redirect()->back()->with('error','Mã giảm giá không đúng - hoặc đã hết hạn');
        }

    }

}   
public function gio_hang(Request $request){
         //category post
    $category_post = CatePost::orderBy('cate_post_id','DESC')->get();
         //seo 
         //slide
    $slider = Slider::orderBy('slider_id','DESC')->where('slider_status','1')->take(4)->get();

    $meta_desc = "Giỏ hàng của bạn"; 
    $meta_keywords = "Giỏ hàng Ajax";
    $meta_title = "Giỏ hàng Ajax";
    $url_canonical = $request->url();
        //--seo
    $cate_product = DB::table('tbl_category_product')->where('category_status','0')->orderby('category_id','desc')->get(); 
    $brand_product = DB::table('tbl_brand')->where('brand_status','0')->orderby('brand_id','desc')->get(); 

    return view('pages.cart.cart_ajax')->with('category',$cate_product)->with('brand',$brand_product)->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)->with('meta_title',$meta_title)->with('url_canonical',$url_canonical)->with('slider',$slider)->with('category_post',$category_post);
}
public function add_cart_ajax(Request $request){
        // Session::forget('cart');
    $data = $request->all();
    $session_id = substr(md5(microtime()),rand(0,26),5);
    $cart = Session::get('cart');
    if($cart==true){
        $is_avaiable = 0;
        foreach($cart as $key => $val){
            if($val['product_id']==$data['cart_product_id']){
                $is_avaiable++;
            }
        }
        if($is_avaiable == 0){
            $cart[] = array(
                'session_id' => $session_id,
                'product_name' => $data['cart_product_name'],
                'product_id' => $data['cart_product_id'],
                'product_image' => $data['cart_product_image'],
                'product_quantity' => $data['cart_product_quantity'],
                'product_qty' => $data['cart_product_qty'],
                'product_price' => $data['cart_product_price'],
            );
            Session::put('cart',$cart);
        }
    }else{
        $cart[] = array(
            'session_id' => $session_id,
            'product_name' => $data['cart_product_name'],
            'product_id' => $data['cart_product_id'],
            'product_image' => $data['cart_product_image'],
            'product_quantity' => $data['cart_product_quantity'],
            'product_qty' => $data['cart_product_qty'],
            'product_price' => $data['cart_product_price'],

        );
        Session::put('cart',$cart);
    }

    Session::save();

}   
public function delete_product($session_id){
    $cart = Session::get('cart');
        // echo '<pre>';
        // print_r($cart);
        // echo '</pre>';
    if($cart==true){
        foreach($cart as $key => $val){
            if($val['session_id']==$session_id){
                unset($cart[$key]);
            }
        }
        Session::put('cart',$cart);
        return redirect()->back()->with('message','Xóa sản phẩm thành công');

    }else{
        return redirect()->back()->with('message','Xóa sản phẩm thất bại');
    }

}
public function update_cart(Request $request){
    $data = $request->all();
    $cart = Session::get('cart');
    if($cart==true){
        $message = '';

        foreach($data['cart_qty'] as $key => $qty){
            $i = 0;
            foreach($cart as $session => $val){
                $i++;

                if($val['session_id']==$key && $qty<$cart[$session]['product_quantity']){

                    $cart[$session]['product_qty'] = $qty;
                    $message.='<p style="color:blue">'.$i.') Cập nhật số lượng :'.$cart[$session]['product_name'].' thành công</p>';

                }elseif($val['session_id']==$key && $qty>$cart[$session]['product_quantity']){
                    $message.='<p style="color:red">'.$i.') Cập nhật số lượng :'.$cart[$session]['product_name'].' thất bại</p>';
                }

            }

        }

        Session::put('cart',$cart);
        return redirect()->back()->with('message',$message);
    }else{
        return redirect()->back()->with('message','Cập nhật số lượng thất bại');
    }
}
public function delete_all_product(){
    $cart = Session::get('cart');
    if($cart==true){
            // Session::destroy();
        Session::forget('cart');
        Session::forget('coupon');
        return redirect()->back()->with('message','Xóa hết giỏ thành công');
    }
}
public function save_cart(Request $request){
    $productId = $request->productid_hidden;
    $quantity = $request->qty;
    $product_info = DB::table('tbl_product')->where('product_id',$productId)->first(); 

    
        // Cart::add('293ad', 'Product 1', 1, 9.99, 550);
        // Cart::destroy();
    $data['id'] = $product_info->product_id;
    $data['qty'] = $quantity;
    $data['name'] = $product_info->product_name;
    $data['price'] = $product_info->product_price;
    $data['weight'] = $product_info->product_price;
    $data['options']['image'] = $product_info->product_image;
    Cart::add($data);
        // Cart::destroy();
    return Redirect::to('/show-cart');
     //Cart::destroy();

}
public function show_cart(Request $request){
        //seo 
    $meta_desc = "Giỏ hàng của bạn"; 
    $meta_keywords = "Giỏ hàng";
    $meta_title = "Giỏ hàng";
    $url_canonical = $request->url();
        //--seo
    $cate_product = DB::table('tbl_category_product')->where('category_status','0')->orderby('category_id','desc')->get(); 
    $brand_product = DB::table('tbl_brand')->where('brand_status','0')->orderby('brand_id','desc')->get(); 
    return view('pages.cart.show_cart')->with('category',$cate_product)->with('brand',$brand_product)->with('meta_desc',$meta_desc)->with('meta_keywords',$meta_keywords)->with('meta_title',$meta_title)->with('url_canonical',$url_canonical);
}
public function delete_to_cart($rowId){
    Cart::update($rowId,0);
    return Redirect::to('/show-cart');
}
public function update_cart_quantity(Request $request){
    $rowId = $request->rowId_cart;
    $qty = $request->cart_quantity;
    Cart::update($rowId,$qty);
    return Redirect::to('/show-cart');
}

}
