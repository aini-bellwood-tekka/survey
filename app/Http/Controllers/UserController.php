<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvUser;
use App\SvUserAuth;
use App\Http\Controllers\Auth;

class UserController {
    
    public function getTopPage(Request $request) {
        $logon = $request->session()->get('logon',false);
        
        if($logon == true){
            $data['screen_name'] = $request->session()->get('screen_name');
            
            $data['user_id'] = $request->session()->get('user_id');
            $data['user_name'] = $request->session()->get('user_name');
            
            return view('logon', ['message' => 'ログイン中です。','data' => $data]);
        }else{
            return view('logoff',['message' => '']);
        }
    }

    public function userCrate(Request $request){
        $user_name = $request->user_name;
        $screen_name = $request->screen_name;
        $pass = encrypt($request->pass);
        
        if(empty($user_name) || empty($screen_name) || empty($pass)){
            return view('signup', ['message' => '空欄の項目があります。']);
        }
        
        $userFind = SvUser::where('user_name', $user_name)->first();
        if($userFind){
            return view('signup', ['message' => 'ユーザーIDが重複しています。']);
        }
        
        //userテーブルとuser_authテーブルに情報登録
        $userpost = array(
            'user_name' => $user_name,
            'screen_name' => $screen_name,
        );
        $user = SvUser::create($userpost);
        if(!$user){
            return view('signup', ['message' => 'ユーザーIDが重複しています。']);
        }
        
        $authpost = array(
            'user_id' => $user->id,
            'password' => $pass
        );
        if(!SvUserAuth::create($authpost)){
            SvUser::where('user_name', $user_name)->delete();
            return view('signup', ['message' => 'アカウント登録に失敗しました。']);
        }
        //ここまできたら登録成功
        
        $request->session()->put('user_id',$user->id);
        $request->session()->put('user_name',$user_name);
        $request->session()->put('screen_name',$screen_name);
        $request->session()->put('logon',true);
        $data['screen_name'] = $screen_name;
        return redirect('');
        //return view('', ['message' => 'ユーザー登録に成功しました。','data' => $data]);
    }
    
    public function getSignUp(Request $request)
    { 
        return view('signup', ['message' => 'ユーザーIDを登録してください。']); 
    }
    
    public function userLogin(Request $request)
    { 
        $user_name = $request->user_name;
        $pass = $request->pass;
        $user = SvUser::where('user_name', $user_name)->first();
        if(empty($user)){
            return view('logoff',['message' => 'IDもしくはパスワードに誤りがあります。']);
        }
        
        $userAuth = SvUserAuth::where('user_id', $user->id)->first();
        if( !empty($userAuth) && decrypt($userAuth->password) == $pass ){
            $request->session()->put('user_id',$user->id);
            $request->session()->put('user_name',$user_name);
            $request->session()->put('screen_name',$user->screen_name);
            $request->session()->put('logon',true);
            $data['screen_name'] =$user->screen_name;
            return view('logon', ['message' => 'ログインに成功しました。','data' => $data]);
        }else{
            return view('logoff',['message' => 'IDもしくはパスワードに誤りがあります。']);
        }
    }
    
    public function userLogoff(Request $request)
    { 
        $request->session()->put('logon',false);
        Auth::logout();
        return view('logoff',['message' => '']);
    }
    
}
