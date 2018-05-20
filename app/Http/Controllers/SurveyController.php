<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvSurvey;
use App\SvSurveyOptions;
use App\SvSurveyTag;
use App\SvUserIdSurveyRelation;

class SurveyController {
    
    public function textSearch(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
        if($request->search_type == "tag"){
            $text = '&tag='.urlencode($request->search_text);
        }else{
            $text = '&text='.urlencode($request->search_text);
        }
        
        return redirect('search?page=1&sort='.$request->sort.'&order='.$request->order.$text);
    }
    
    public function getSurveyList(Request $request) {
        
        $searchOption = array(
            'sort' => $request->sort,
            'order'=> $request->order,
            'target_user_id' => $request->session()->get('id'),
            'search_text' => urldecode($request->text),
            'search_tag' => urldecode($request->tag),
        );
        $data = $this->_getSurveyList($searchOption,$request->page,10);
        
        //受け取ったDataに検索オプションを引き継ぐ
        $data['sort'] = $request->sort;
        $data['order'] = $request->order;
        $data['url_option'] = '&sort='.($request->sort).'&order='.($request->order).(($request->text == '')?(''):('&text='.$request->text)).(($request->tag == '')?(''):('&tag='.$request->tag));
        
        return view('search', ['message' => '','data' => $data]);
    }
    private function _getSurveyList($searchOption,$page,$count){
        
        $page = ( $page < 1 )? 1: $page;
        if($searchOption['search_tag'] != ''){
            
            //survey_tagとsurveyを内部結合。
            //この時点でsurvey_tagとsurvey間で同じ名前の列はsurveyの値で上書きされるので注意（現状、id,created_at,updated_atが該当）
            $tags = DB::table('survey_tag')->join('survey','survey_tag.survey_id','=','survey.id');
            
            //完全一致するタグを検索
            $tags = $tags->where('name',$searchOption['search_tag'])->get();
            
            if($searchOption['order'] == 'o'){
                $surveys = $tags->sortBy('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
            }else{
                $surveys = $tags->sortByDesc('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
            }

            /* LaravelのクエリビルダでJoinしようとするとうまくいかない。
            if($searchOption['order'] == 'o'){
                $surveys = DB::table('survey_tag')->join('survey',function ($join) use ($searchOption) {
                    $join->on('survey_tag.survey_id','=','survey.id')
                            ->where('survey_tag.name',$searchOption['search_tag'])
                            ->sortBy('survey.updated_at')->slice( ( $page - 1 ) * $count, $count+1); //メソッドがないと言われる
                    })->get();
            }else{
                $surveys = DB::table('survey_tag')->join('survey',function ($join) use ($searchOption) {
                    $join->on('survey_tag.survey_id','=','survey.id')
                            ->where('survey_tag.name',$searchOption['search_tag'])
                            ->sortByDesc('survey.updated_at')->slice( ( $page - 1 ) * $count, $count+1); //メソッドがないと言われる
                    })->get();
            }
            //var_dump($data);
             */
            
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
                    'author_id' => $sv->author_id,
                );
                $data['survey'][] = $op_var;
            }
            $data['count'] = $surveys->count();

            return $data;
        }
        elseif($searchOption['sort'] == 'ms'){
            //自分の投稿した質問
            $baseSurveys = SvSurvey::where('author_id', $searchOption['target_user_id'])->get();
            
            //キーワード検索
            if($searchOption['search_text'] != ''){
                $baseSurveys = $baseSurveys->where('description','like', '%'.$searchOption['search_text'].'%');
            }
            
        }elseif($searchOption['sort'] == 'ma'){
            //自分の回答した質問
            
            //選択肢DBからユーザIDをキーにしてcount件取得。
            if($searchOption['order'] == 'o'){
                $answers = SvUserIdSurveyRelation::where('user_id', $searchOption['target_user_id'])->get();
                
                //キーワード検索
                if($searchOption['search_text'] != ''){
                    $answers = $answers->where('description','like', '%'.$searchOption['search_text'].'%');
                }
                $answers = $answers->sortBy('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
                
            }else{
                $answers = SvUserIdSurveyRelation::where('user_id', $searchOption['target_user_id'])->get();
                
                //キーワード検索
                if($searchOption['search_text'] != ''){
                    $answers = $answers->where('description','like', '%'.$searchOption['search_text'].'%');
                }
                $answers = $answers->sortByDesc('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
            }
            
            //選択肢から紐づく質問IDをまとめて質問DBから検索
            $answerIdArray = Array();
            foreach($answers as $an){
                $answerIdArray[] = $an->survey_id;
            }
            $baseSurveys = SvSurvey::whereIn('id', $answerIdArray);
            
        }else{
            //全部取ってくる
            $baseSurveys = SvSurvey::all();
            
            //キーワード検索
            if($searchOption['search_text'] != ''){
                $baseSurveys = $baseSurveys->where('description',$searchOption['search_text']);
            }
        }
        
        //ページ数を元に必要な件数を絞り込み
        if($searchOption['order'] == 'o'){
            $surveys = $baseSurveys->sortBy('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
        }else{
            $surveys = $baseSurveys->sortByDesc('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
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
                'author_id' => $sv->author_id,
            );
            $data['survey'][] = $op_var;
        }
        $data['count'] = $baseSurveys->count();
        
        return $data;
    }
    
    public function getSurvey(Request $request) {
        
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
       if($request->error == 1){
            $message = 'タグの登録に失敗しました。(E004)';
        }
        else if($request->vote == 0){
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
        //質問view取得関数
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->where('user_id', $user_id)->first();
        $survey = SvSurvey::where('id', $survey_id)->first();
        
        $voted = (!empty($votes) or $survey->author_id == $user_id);
        $data =  $this->_getSurvey($voted,$user_id,$survey_id);
        
        return view(($voted)? 'votedsurvey':'survey', ['message' => $massage,'data' =>$data]);
    }
    private function _getSurvey($voted,$user_id,$survey_id){
        //質問取得関数
        $survey = SvSurvey::where('id', $survey_id)->first();
        $options = SvSurveyOptions::where('survey_id', $survey_id)->get();
        $tags = SvSurveyTag::where('survey_id', $survey_id)->get();
        
        if(empty($survey) || empty($options) || empty($tags)) { return view('logon', ['message' => '質問の取得に失敗しました。(E002)']); }
        
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->get();
        $all_vote_count = $votes->count() - $votes->where('number', -1)->count();
        $my_vote = $votes->where('user_id', $user_id)->first();
        $my_vote_num = (empty($my_vote))? -1:$my_vote->number;
                
        $data = array(
            'voted' => !empty($voted),
            'survey_id' => $survey_id,
            'question' => $survey->description,
            'all_vote_count' => $all_vote_count,
            'option' => array(),
            'tag' => array(),
            'author_id' => $survey->author_id,

            'my_vote_num' => $my_vote_num,
            'my_survey' => ($user_id == $survey->author_id),
         );
        
        $i = 0;
        foreach($options as $op){
            $op_var = array(
                'var' => $op->number,
                'text' => $op->description,
                'my_voted' => ($i == $my_vote_num),
            );
            if($voted){
                $op_var['vote_count'] = $votes->where('number', $op->number)->count();
            }
            $data['option'][] = $op_var;
            $i++;
        }
        //$data['option'][0]['checked'] = true;
        //var_dump($data);
        
        foreach($tags as $tag){
            $tag_var = array(
                'name' => $tag->name,
                'tag_id' => $tag->id,
                'lock_type' => $tag->lock_type,
            );
            $data['tag'][] = $tag_var;
        }
        
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
    
    public function createTag(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
        $id = $request->session()->get('id');
        
        //質問の登録
        $postSurvey = array(
            'name' => $request->name,
            'lock_type' => $request->lock_type,
            'survey_id' => $request->survey_id,
        );
        $survey = SvSurveyTag::create($postSurvey);
        if(!empty($survey)){
            return redirect('survey?id='.$request->survey_id);
        }else{
            return redirect('survey?id='.$request->survey_id.'&error=1');
        }
    }
    public function eraseTag(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return view('logoff',['message' => 'ログインしてくだい。']); }
        
        $survey = SvSurveyTag::destroy($request->tag_id);
        return redirect('survey?id='.$request->survey_id);
    }
}
