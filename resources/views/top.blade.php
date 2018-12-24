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
    <?php
        echo $message; 
        if ( $userdata['logon'] ){
            echo 'ユーザー名：'.$userdata['screen_name'].'<br><br>';
            echo '<a href="/surveycreate">アンケートを作る</a><br><br>';
            echo '<a href="/search?page=1&sort=d&order=n">アンケートを探す</a><br><br>';
            echo '<a href="/search?page=1&sort=ms&order=n">自分の作ったアンケートを探す</a><br><br>';
            echo '<a href="/search?page=1&sort=ma&order=n">自分の回答したアンケートを探す</a><br><br>';
        }else{
            echo 'アンケートの作成はログイン後に行えます<br><br>';
            echo '<a href="/search?page=1&sort=d&order=n">アンケートを探す</a><br><br>';
        }
    ?>
    @include('layouts.loginbutton')
@endsection