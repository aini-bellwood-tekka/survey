<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SvSurvey;
use App\SvSurveyOptions;
use App\SvSurveyTag;
use App\SvUserIdSurveyRelation;
use App\SvUser;

class SurveyController {
    
    private function _webError($request,$msg){
        $data = array(
            'user_id' => $request->session()->get('id'),
         );
        return view('logon', ['message' => $msg,'data' => $data]); 
    }
    private function _apiError($request,$msg){
        $data = array(
            'user_id' => $request->session()->get('id'),
            'error' =>'',
            'message' => $msg,
         );
        return $data; 
    }
    
    public function webTextSearch(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        $sort = '&sort=' . $request->sort;
        $order = '&order=' . $request->order;
        $search = (($request->text == '')?(''):('&search='.$request->search));
        $text = (($request->text == '')?(''):('&text='.urlencode($request->text)));
                
        return redirect('search?page=1' . $sort . $order . $search . $text);
    }
    public function webGetSurveyList(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        $data = $this->_getSurveyList($request);
        
        if($data['count'] > 0 ){
            return view('search', ['message' => $data['message'],'data' => $data]);
        }
        else{
            return view('searchempty', ['message' => $data['message'],'data' => $data]);
        }
    }
    public function apiGetSurveyList(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return _apiError($request, 'ログインしてくだい。'); }
        
        return $this->_getSurveyList($request);
    }
    private function _getSurveyList(Request $request){
        
        $searchOption = array(
            'target_user_id' => $request->session()->get('user_id'),
            
            'sort' => $request->sort,
            'order'=> $request->order,
            'search' => $request->search,
            'text' => urldecode($request->text),
        );
        
        $data = $this->_search($request,$searchOption,$request->page,10);
        
        if($data['error'] != ''){ return $this->_error($request,$data['error']); }
        
        //受け取ったDataに検索オプションを引き継ぐ
        $data['sort'] = $request->sort;
        $data['order'] = $request->order;
        
        $sort = '&sort='.($request->sort);
        $order = '&order='.($request->order);
        $search = (($request->text == '')?(''):('&search='.$request->search));
        $text = (($request->text == '')?(''):('&text='.urlencode($request->text)));
        
        $data['url_option'] = $sort . $order. $search . $text;
        
        return $data;
    }
    private function _searchTag($searchOption,$page,$count){
        
        //survey_tagとsurveyを内部結合。
        //この時点でsurvey_tagとsurvey間で同じ名前の列はsurveyの値で上書きされるので注意（現状、id,created_at,updated_atが該当）
        $tags = DB::table('survey_tag')->join('survey','survey_tag.survey_id','=','survey.id');

        //完全一致するタグを検索
        $tags = $tags->where('name',$searchOption['text'])->get();

        if($searchOption['order'] == 'o'){
            $surveys = $tags->sortBy('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
        }else{
            $surveys = $tags->sortByDesc('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
        }

        /* LaravelのクエリビルダでJoinしようとするとうまくいかない。
        if($searchOption['order'] == 'o'){
            $surveys = DB::table('survey_tag')->join('survey',function ($join) use ($searchOption) {
                $join->on('survey_tag.survey_id','=','survey.id')
                        ->where('survey_tag.name',$searchOption['text'])
                        ->sortBy('survey.updated_at')->slice( ( $page - 1 ) * $count, $count+1); //メソッドがないと言われる
                })->get();
        }else{
            $surveys = DB::table('survey_tag')->join('survey',function ($join) use ($searchOption) {
                $join->on('survey_tag.survey_id','=','survey.id')
                        ->where('survey_tag.name',$searchOption['text'])
                        ->sortByDesc('survey.updated_at')->slice( ( $page - 1 ) * $count, $count+1); //メソッドがないと言われる
                })->get();
        }
        //var_dump($data);
         */
        $result = array(
            'count' => $tags->count(),
            'surveys' => $surveys,
         );
        return $result;
    }
    private function _searchText($searchOption,$page,$count){
        
        $isEmpty = false;
        if($searchOption['sort'] == 'ms'){
            //自分の投稿した質問
            //質問DBからユーザIDをキーにしてcount件取得
            $baseSurveys = SvSurvey::where('author_user_id', $searchOption['target_user_id'])->get();
            
            //キーワード検索
            if($searchOption['text'] != ''){
                $baseSurveys = $baseSurveys->where('description','like', '%'.$searchOption['text'].'%');
            }
            
        }elseif($searchOption['sort'] == 'ma'){
            //自分の回答した質問
            //回答DBからユーザIDをキーにしてcount件取得。
            
            $answers = SvUserIdSurveyRelation::where('user_id', $searchOption['target_user_id'])->get();

            //キーワード検索
            if($searchOption['text'] != ''){
                $answers = $answers->where('description','like', '%'.$searchOption['text'].'%');
            }
            
            if($searchOption['order'] == 'o'){
                $answers = $answers->sortBy('updated_at')->slice( ( $page - 1 ) * $count, $count+1);               
            }else{
                $answers = $answers->sortByDesc('updated_at')->slice( ( $page - 1 ) * $count, $count+1);
            }
            
            //選択肢から紐づく質問IDをまとめて質問DBから検索
            $answerIdArray = Array();
            foreach($answers as $an){
                $answerIdArray[] = $an->survey_id;
            }
            if(empty($answerIdArray)){
                // 空のCollectionを返すいい手が思いつかない…。
                $baseSurveys = SvSurvey::all();
                $isEmpty = true;
            }
            else{
                $baseSurveys = SvSurvey::whereIn('id', $answerIdArray);
            }
        }else{
            //全部取ってくる
            $baseSurveys = SvSurvey::all();
            
            //キーワード検索
            if($searchOption['text'] != ''){
                $baseSurveys = $baseSurveys->where('description',$searchOption['text']);
            }
        }
        
        //ページ数を元に必要な件数を絞り込み
        if($searchOption['order'] == 'o'){
            $surveys = $baseSurveys->sortBy('created_at')->slice( ( $page - 1 ) * $count, $count+1);
        }else{
            $surveys = $baseSurveys->sortByDesc('created_at')->slice( ( $page - 1 ) * $count, $count+1);
        }
        
        $result = array(
            'count' => (($isEmpty)? 0:$baseSurveys->count()),
            'surveys' => $surveys,
         );
        return $result;
    }  
    private function _search($request,$searchOption,$page,$count){
        
        $page = ( $page < 1 )? 1: $page;
        
        $message = '';
        if($searchOption['search'] == 'tag'){
            $result = $this->_searchTag($searchOption,$page,$count);
            if($result['count'] > 0){
                $message = ($searchOption['text'] == '')? '':'タグ「'.$searchOption['text'].'」が設定された質問の一覧です。';
            }else{
                $message = 'タグ「'.$searchOption['text'].'」が設定された質問は見つかりませんでした。';
            }
        }
        else{
            $result = $this->_searchText($searchOption,$page,$count);
            if($searchOption['sort'] == 'ms'){
                if($result['count'] > 0){
                    $message = 'あなたが作成した質問の一覧です。';
                }
                else{
                    $message = 'あなたはまだ質問を作成していません。';
                }
            }
            elseif($searchOption['sort'] == 'ma'){
                if($result['count'] > 0){
                    $message = 'あなたが回答した質問の一覧です。';
                }
                else{
                    $message = 'あなたはまだ質問に回答していません。';
                }
            }else{
                if($result['count'] > 0){
                    $message = ($searchOption['text'] == '')? '':'キーワード「'.$searchOption['text'].'」に一致する質問の一覧です。';
                }
                else{
                    $message = 'キーワード「'.$searchOption['text'].'」に一致する質問は見つかりませんでした。';
                }
            }
        }
        
        $data = array(
            'searchOption' => $searchOption,
            'page' => $page,
            'survey' => array(),
            'error' =>'',
            'message' => $message,
         );
        if(empty($result['surveys'])) { 
            $data['error'] = '質問の取得に失敗しました。(SVL000)';
            return $data; 
        }
        foreach($result['surveys'] as $sv){
            $user = SvUser::where('id', $sv->author_user_id)->first();
            
            $op_var = array(
                'id' => $sv->id,
                'text' => $sv->description,
                'screen_name' => $user->screen_name,
                
                'start_at' => $sv->start_at,
                'end_at' => $sv->end_at,
                'is_end' => Carbon::parse($sv->end_at)->isPast(),
                'remaining_time' => Carbon::now()->diff(Carbon::parse($sv->end_at))->format('%d日 %h時間 %i分 %s秒'),
                
                'created_at' => $sv->created_at,
            );
            $data['survey'][] = $op_var;
        }
        $data['count'] = $result['count'];
        
        return $data;
    }  
    
    public function webGetSurvey(Request $request) {
        
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
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
        else if($request->vote == 3){
            $message = '質問が見つかりませんでした。(E004)';
        }
        else if($request->vote == 4){
            $message = '締切が過ぎています。(E005)';
        }
        
        return $this->_getSurveyView($message,$request->session()->get('user_id'),$request->id);
    }
    public function apiGetSurvey(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return _apiError($request, 'ログインしてくだい。'); }
        
        $survey_id = $request->id;
        $user_id = $request->session()->get('id');
        
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->where('user_id', $user_id)->first();
        $survey = SvSurvey::where('id', $survey_id)->first();
        
        $voted = !empty($votes);
        $voted = $voted or ($survey->author_user_id == $user_id);
        $voted = $voted or Carbon::parse($survey->end_at)->isPast();
        
        $data =  $this->_getSurvey($voted,$user_id,$survey_id);
        
        return $data;
    }
    private function _getSurveyView($massage,$user_id,$survey_id){
        //質問view取得関数
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->where('user_id', $user_id)->first();
        $survey = SvSurvey::where('id', $survey_id)->first();
        
        $voted = (!empty($votes) or $survey->author_user_id == $user_id or Carbon::parse($survey->end_at)->isPast());
        $data =  $this->_getSurvey($voted,$user_id,$survey_id);
        
        return view(($voted)? 'votedsurvey':'survey', ['message' => $massage,'data' =>$data]);
    }
    private function _getSurvey($voted,$user_id,$survey_id){
        //質問取得関数
        $survey = SvSurvey::where('id', $survey_id)->first();
        $options = SvSurveyOptions::where('survey_id', $survey_id)->get();
        $tags = SvSurveyTag::where('survey_id', $survey_id)->get();
        
        if(empty($survey) || empty($options) || empty($tags)) { return $this->_error($request, '質問の取得に失敗しました。(E002)'); }
        
        $votes = SvUserIdSurveyRelation::where('survey_id', $survey_id)->get();
        $all_vote_count = $votes->count() - $votes->where('number', -1)->count();
        $my_vote = $votes->where('user_id', $user_id)->first();
        $my_vote_num = (empty($my_vote))? -1:$my_vote->number;
        $user = SvUser::where('id', $user_id)->first();
        
        $data = array(
            'voted' => !empty($voted),
            'survey_id' => $survey_id,
            'question' => $survey->description,
            'screen_name' => $user->screen_name,
            
            'all_vote_count' => $all_vote_count,
            'option' => array(),
            'tag' => array(),
            
            'start_at' => $survey->start_at,
            'end_at' => $survey->end_at,
            'is_end' => Carbon::parse($survey->end_at)->isPast(),
            'remaining_time' => Carbon::now()->diff(Carbon::parse($survey->end_at))->format('%d日 %h時間 %i分 %s秒'),
            
            'my_vote_num' => $my_vote_num,
            'my_survey' => ($user_id == $survey->author_user_id),
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
    
    public function webSurveyCreate(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        $data = $this->_surveyCreate($request);
        
        if($data['success']){
            return redirect('survey?id='.$data['survey_id']);
        }else{
            return $this->_webError($request, $data['message']);
        }
    }
    public function apiSurveyCreate(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return _apiError($request, 'ログインしてくだい。'); }

        $request['option'] = json_decode($request->jsonoption);
        
        if($data['success']){
            return $data;
        }else{
            return $this->apiError($request, $data['message']);
        }
    }
    private function _surveyCreate(Request $request){
        $id = $request->session()->get('user_id');

        /*
        $limit = $request->timelimit;
        if(     $limit == '1h'){    $timelimit = Carbon::now()->addSecond(10);  }
        elseif( $limit == '3h'){    $timelimit = Carbon::now()->addSecond(30);  }
        elseif( $limit == '6h'){    $timelimit = Carbon::now()->addSecond(60);  }
        elseif( $limit == '1d'){    $timelimit = Carbon::now()->addSecond(2*60);  }
        elseif( $limit == '3d'){    $timelimit = Carbon::now()->addSecond(3*60);  }
        elseif( $limit == '7d'){    $timelimit = Carbon::now()->addSecond(7*60);  }
        else{$timelimit = Carbon::now();}
        */
        
        $limit = $request->timelimit;
        if(     $limit == '1h'){    $timelimit = Carbon::now()->addHour(1);  }
        elseif( $limit == '3h'){    $timelimit = Carbon::now()->addHour(3);  }
        elseif( $limit == '6h'){    $timelimit = Carbon::now()->addHour(6);  }
        elseif( $limit == '1d'){    $timelimit = Carbon::now()->addDay(1);  }
        elseif( $limit == '3d'){    $timelimit = Carbon::now()->addDay(3);  }
        elseif( $limit == '7d'){    $timelimit = Carbon::now()->addDay(7);  }
        else{$timelimit = Carbon::now();}
        
        $data = array(
            'success' => false,
            'survey_id' => 1,
            'message' =>''
        );
        
        //質問の登録
        $question = $request->question;
        $postSurvey = array(
            'author_user_id' => $id,
            'title' => '',
            'description' => $question,
            'start_at' => Carbon::now(),
            'end_at' => $timelimit,
        );
        $survey = SvSurvey::create($postSurvey);
        if(empty($survey)){ $data['message'] = '質問の作成に失敗しました。(E002)'; return $data; }

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
        
        $data['success'] = $postSuccess;
        $data['survey_id'] = $survey->id;
        
        return $data;
    }
    
    public function webGetSurveyCreateForm(Request $request) {
        
        return view('surveycreate', ['message' => '']);
    }
    
    public function webCreateTag(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        $data = $this->_createTag($request);
        
        if($data['success']){
            return redirect('survey?id='.$request->survey_id);
        }else{
            return redirect('survey?id='.$request->survey_id.'&error=1');
        }
    }
    public function apiCreateTag(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return _apiError($request, 'ログインしてくだい。'); }
        
        $data = $this->_createTag($request);
        
        return $data;
    }
    public function _createTag(Request $request) {
        
        $postSurvey = array(
            'name' => $request->name,
            'lock_type' => $request->lock_type,
            'survey_id' => $request->survey_id
        );
                
        $data = array(
            'success' => !(empty(SvSurveyTag::create($postSurvey)))
        );
        return $data;
    }
    
    public function webEraseTag(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        $data = $this->_eraseTag($request);
        
        return redirect('survey?id='.$request->survey_id);
    }
    public function apiEraseTag(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return _apiError($request, 'ログインしてくだい。'); }
        
        $data = $this->_eraseTag($request);
        
        return $data;
    }
    public function _eraseTag(Request $request) {
        $survey = SvSurveyTag::destroy($request->tag_id);
        
        $data = array(
            'success' => true,
        );
        return $data;
    }
    
    public function webVote(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        $data = $this->_vote($request);
        return redirect('survey?id='.$request->id.'&vote='.$data['vote']);
    }
    public function apiVote(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return _apiError($request, 'ログインしてくだい。'); }
        
        return $this->_vote($request);
    }
    private function _vote(Request $request) {
        $user_id = $request->session()->get('user_id');
        $survey_id = $request->id;
        
        $data = array(
            'success' => false,
            'vote' => '1'
        );
        
        $votes = SvUserIdSurveyRelation::where('user_id', $user_id)->where('survey_id', $survey_id)->first();
        if(!empty($votes)){ $data['vote'] = '2'; return $data; }
        
        $survey = SvSurvey::where('id', $survey_id)->first();
        if(empty($survey)){ $data['vote'] = '3'; return $data; }
        
        if(Carbon::parse($survey->end_at)->isPast()){ $data['vote'] = '4'; return $data; }
        
        $postVote = array(
            'user_id' => $user_id,
            'survey_id' => $survey_id,
            'number' => (int)$request->option
        );
        $vote = SvUserIdSurveyRelation::create($postVote);
        
        $data['success'] = !empty($vote);
        return $data;
    }
    
    public function getDebugView(Request $request) {
        $logon = $request->session()->get('logon',false);
        if($logon == false){ return $this->_webError($request, 'ログインしてくだい。'); }
        
        return view('debug', ['message' => '']);
    }
    
}
