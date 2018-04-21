<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvUser;

class UserController {
    
    public function getTopPage(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == true){
            return view('logon', ['message' => 'ログインに成功しました。']);
        }else{
            return view('logoff',['message' => '']);
        }
    }

    public function userCrate(Request $request)
    {
        $id = $request->id;
        $pass = $request->pass;
        $post = array(
            'screen_name' => $id,
            'password' => $pass
        );
        if(SvUser::create($post)){
            $request->session()->put('logon',true);
            return view('logon', ['message' => 'success!']);
        }else{
            return view('signup', ['message' => 'ユーザーがID重複しています。']);
        }
    }
    
    public function getSignUp(Request $request)
    { 
        return view('signup', ['message' => 'ユーザーIDを登録してください。']); 
    }
    
    public function userLogin(Request $request)
    { 
        $id = $request->id;
        $pass = $request->pass;
        $user = SvUser::where('screen_name', $id)->first();
        
        if( 
            !empty($user) &&
            $user->password == $pass 
        ){
            $request->session()->put('logon',true);
            return view('logon', ['message' => 'ログインに成功しました。']);
        }else{
            return view('logoff',['message' => 'IDもしくはパスワードに誤りがあります。']);
        }
        
    }
    
    public function userLogoff(Request $request)
    { 
        $request->session()->put('logon',false);
        return view('logoff',['message' => '']);
    }
    
}
