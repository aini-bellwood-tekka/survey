<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvSurvey;
use App\SvSurveyOptions;
use App\SvUserIdSurveyRelation;

class SurveyController {
    
    public function getSurveyList(Request $request) {
        
        $searchOption = array(
            'sort' => 'neworder',
            'refine'=> 'none',
            'target_user_id' => $request->session()->get('id'),
        );
        $data = $this->_getSurveyList($searchOption,$request->page,10);
        
        return view('search', ['message' => '','data' => $data]);
    }
    private function _getSurveyList($searchOption,$page,$count){
        
        $page = ( $page < 1 )? 1: $page;
        if($searchOption['refine'] == 'mysurvey'){
            $baseSurveys = SvSurvey::where('author_id', $searchOption['target_user_id'])->get();
        }else{
            $baseSurveys = SvSurvey::all();
        }
        if($searchOption['sort'] == 'order'){
            $surveys = $baseSurveys->sortBy('updated_at')->slice( ( $page - 1 ) * $count, $count);
        }else{
            $surveys = $baseSurveys->sortByDesc('updated_at')->slice( ( $page - 1 ) * $count, $count);
        }
        
        
        if(empty($surveys)) { return view('logon', ['message' => '質問の取得に失敗しました。(SVL000)']); }
        
        $data = array(
            'searchOption' => $searchOption,
            'page' => $page,
            'survey' => array(),
         );
        foreach($surveys as $sv){
            $op_var = array(
                'id' => $sv->id,
                'text' => $sv->description,
            );
            $data['survey'][] = $op_var;
        }
        
        return $data;
    }
    
    public function getSurvey(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
       
        if($request->vote == 0){
            $message = '';
        }
        else if($request->vote == 1){
            $message = '投票しました！';
        }
        else if($request->vote == 2){
            $message = '投票に失敗しました。(E003)';
        }
        
        return $this->_getSurveyView($message,$request->session()->get('id'),$request->id);
    }
    private function _getSurveyView($massage,$user_id,$survey_id){
        
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->where('user_id', $user_id)->first();
        $survey = SvSurvey::where('id', $survey_id)->first();
        
        $voted = (!empty($votes) or $survey->author_id == $user_id);
        return view(($voted)? 'votedsurvey':'survey', ['message' => $massage,'data' => $this->_getSurvey($voted,$survey_id)]);
    }
    private function _getSurvey($voted,$survey_id){
  
        $survey = SvSurvey::where('id', $survey_id)->first();
        $options = SvSurveyOptions::where('survey_id', $survey_id)->get();
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->get();
        $all_vote_count = $votes->count() - $votes->where('number', -1)->count();
        
        if(empty($survey) || empty($options)) { return view('logon', ['message' => '質問の取得に失敗しました。(E002)']); }
        
        $data = array(
            'voted' => !empty($voted),
            'survey_id' => $survey_id,
            'question' => $survey->description,
            'all_vote_count' => $all_vote_count,
            'option' => array(),
         );
        
        $i = 0;
        foreach($options as $op){
            $op_var = array(
                'var' => $op->number,
                'text' => $op->description,
            );
            if($voted){
                $op_var['vote_count'] = $votes->where('number', $op->number)->count();
            }
            $data['option'][] = $op_var;
            $i++;
        }
        //$data['option'][0]['checked'] = true;
        //var_dump($data);
         
        return $data;
    }

    public function surveyCreate(Request $request) {
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
        }else{
            return view('logon', ['message' => '質問の作成に失敗しました。(E002)']);
        }
    }
    public function getSurveyCreateForm(Request $request) {
        
        return view('surveycreate', ['message' => '']);
    }

    public function vote(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
        $user_id = $request->session()->get('id');
        $survey_id = $request->id;
        $votes = SvUserIdSurveyRelation::where('user_id', $user_id)->where('survey_id', $survey_id)->first();
        if(!empty($votes)){ return redirect('survey?id='.$request->id.'&vote=2'); }
        
        $postVote = array(
            'user_id' => $user_id,
            'survey_id' => $survey_id,
            'number' => $request->option
        );
        
        $vote = SvUserIdSurveyRelation::create($postVote);
        if(empty($vote)){ 
            return redirect('survey?id='.$request->id.'&vote=2');
        }
        else{
            return redirect('survey?id='.$request->id.'&vote=1');
        }
        
        
    }
}
