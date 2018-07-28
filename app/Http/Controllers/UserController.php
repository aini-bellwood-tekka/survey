<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvUser;
use App\SvUserAuth;

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
        $pass = encrypt($request->pass);
        
        $userpost = array(
            'screen_name' => $id,
        );
        if(!SvUser::create($userpost)){
            return view('signup', ['message' => 'ユーザーがID重複しています。']);
        }
        $authpost = array(
            'user_name' => $id,
            'password' => $pass
        );
        if(SvUserAuth::create($authpost)){
            $request->session()->put('id',$id);
            $request->session()->put('logon',true);
            $data['user_id'] = $id;
            return redirect('');
            //return view('logon', ['message' => 'ユーザー登録に成功しました。','data' => $data]);
        }else{
            SvUser::where('screen_name', $id)->delete();
            return view('signup', ['message' => 'アカウント登録に失敗しました。']);
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
        $user = SvUserAuth::where('user_name', $id)->first();
        
        if( !empty($user) && decrypt($user->password) == $pass ){
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
