<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvSurvey;
use App\SvSurveyOptions;

class SurveyController {
    public function getSurvey(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
        return view('logon', ['message' => '']);
    }

    public function surveyCrate(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
        $id = $request->session()->get('id');

        //質問の登録
        $question = $request->question;
        $timelimit = $request->timelimit;
        $postSurvey = array(
            'author_id' => $id,
            'title' => '',
            'description' => $question
        );
        $survey = SvSurvey::create($postSurvey);
        if(empty($survey)){ return view('logon', ['message' => '質問の作成に失敗しました。(E002)']); }

        //選択肢の登録
        $postSuccess = true;
        for ($count = 0; $count < 4; $count++){
            $option = $request->option[$count];
            $postOption = array(
                'survey_id' => $survey->id,
                'number' => $count,
                'description' => $option
            );
            if(empty(SvSurveyOptions::create($postOption))){ 
                $postSuccess = false;
                break;
            }
        }
        
        if($postSuccess){
            return view('survey', ['message' => 'success!','data' => $request]);
        }else{
            return view('logon', ['message' => '質問の作成に失敗しました。(E002)']);
        }
    }

}
