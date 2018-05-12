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
            $data['user_id'] = $request->session()->get('id');
            return view('logon', ['message' => 'ログイン中です。','data' => $data]);
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
            $request->session()->put('id',$id);
            $request->session()->put('logon',true);
            $data['user_id'] = $id;
            return view('logon', ['message' => 'ユーザー登録に成功しました。','data' => $data]);
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
        
        if( !empty($user) && $user->password == $pass ){
            $request->session()->put('id',$id);
            $request->session()->put('logon',true);
            $data['user_id'] = $id;
            return view('logon', ['message' => 'ログインに成功しました。','data' => $data]);
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
