@extends('layouts.base')
@section('title', 'Sample')
@section('stylesheet')
  <link rel="stylesheet" href="css/toiawase.css"/>
    <title>あんけーと</title>
    <style>
    body { color:gray; }
    h1 { font-size:18pt; font-weight:bold; }
    th { color:white; background:#999; }
    td { color:black; background:#eee; padding:5px 10px; }
    </style>
@endsection
@section('content')
    <h1>あんけーと</h1>
    
    <script src="js/jquery-3.3.1.min.js"></script>
    <script>
        $(function(){ // ページ取得時に実行しないように遅延させる。
            $('#id').on('click',function(){
                $.ajax({
                  'type': 'GET',
                  'url': 'http://survey/apisearch', //このパスはViewを返しているのでfailする
                  'data': {
                      'page' : '1',
                      'sort' : 'ms',
                      'order' : 'n'
                  },
                  'dataType': 'json'
                })
                .done(function(res, statusText, jqXHR) {  
                   alert(JSON.stringify(res));
                })
                .fail(function(jqXHR, textStatus, errorThrown){
                    alert("idTestFail");
                });
            });
        });

        function api_search(){
            $.ajax({
              'type': 'GET',
              'url': 'http://survey/api/search',
              'data': {
                  'page' : '1',
                  'sort' : 'ms',
                  'order' : 'n'
              },
              'dataType': 'json'
            })
            .done(function(res, statusText, jqXHR) { alert(JSON.stringify(res)); })
            .fail(function(jqXHR, textStatus, errorThrown){ alert("fail"); });
        }
        function api_survey(){
            $.ajax({
              'type': 'GET',
              'url': 'http://survey/api/survey',
              'data': {
                  'id' : '1',
              },
              'dataType': 'json'
            })
            .done(function(res, statusText, jqXHR) { alert(JSON.stringify(res)); })
            .fail(function(jqXHR, textStatus, errorThrown){ alert("fail"); });
        }
        function api_surveycreate(){
            $.ajax({
              'type': 'POST',//TokenMismatchException https://sota1235.hatenablog.com/entry/2015/10/11/213000
              'url': 'http://survey/api/surveycreate',
              'data': {
                    'question':'apitestQuestion',
                    'option':JSON.stringify(['apitestOption0','apitestOption1','apitestOption2','apitestOption3']),
                    'timelimit':'1h'
              },
              'dataType': 'json'
            })
            .done(function(res, statusText, jqXHR) { alert(JSON.stringify(res)); })
            .fail(function(jqXHR, textStatus, errorThrown){ alert("fail"); });
        }
        function api_tagcreate(){
            $.ajax({
              'type': 'GET',
              'url': 'http://survey/api/tagcreate',
              'data': {
                  'survey_id' : '1',
                  'name' : 'testtag',
                  'lock_type' : 'lock_type'
              },
              'dataType': 'json'
            })
            .done(function(res, statusText, jqXHR) { alert(JSON.stringify(res)); })
            .fail(function(jqXHR, textStatus, errorThrown){ alert("fail"); });
        }
        function api_vote(){
            $.ajax({
              'type': 'GET',
              'url': 'http://survey/api/vote',
              'data': {
                  'survey_id' : '1',
                  'number' : '1'
              },
              'dataType': 'json'
            })
            .done(function(res, statusText, jqXHR) { alert(JSON.stringify(res)); })
            .fail(function(jqXHR, textStatus, errorThrown){ alert("fail"); });
        }
    </script>
    
    <input type="button" value="idTest" id="id"><br>
    <input type="button" value="api_search" onclick="api_search()"><br>
    <input type="button" value="api_survey" onclick="api_survey()"><br>
    <input type="button" value="api_surveycreate" onclick="api_surveycreate()"><br>
    <input type="button" value="api_tagcreate" onclick="api_tagcreate()"><br>
    <input type="button" value="api_tagerase" onclick="api_tagerase()"><br>
    <input type="button" value="api_vote" onclick="api_vote()"><br>
    
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection
