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
        
        return $this->_getSurveyView($request->id);
    }
    
    private function _getSurveyView($id){
        
        return view('survey', ['message' => 'success!','data' => $this->_getSurvey($id)]);
    }
    
    private function _getSurvey($id){
  
        $survey = SvSurvey::where('id', $id)->first();
        $options = SvSurveyOptions::where('survey_id', $id)->get();
        
        if(empty($survey) || empty($options)) { return view('logon', ['message' => '質問の取得に失敗しました。(E002)']); }
        
        $data = array(
            'survey_id' => $id,
            'question' => $survey->description,
            'option' => array(),
         );
        
        $i = 0;
        foreach($options as $op){
            $op_var = array(
                'var' => $i,
                'text' => $op->description,
                'checked' => false,
            );
            $i++;
            $data['option'][] = $op_var;
        }
        //$data['option'][0]['checked'] = true;
        //var_dump($data);
         
        return $data;
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
            return redirect('survey?id='.$survey->id);
            //return $this->_getSurveyView($survey->id);
        }else{
            return view('logon', ['message' => '質問の作成に失敗しました。(E002)']);
        }
    }

}
