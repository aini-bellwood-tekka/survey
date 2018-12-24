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
    <p><?php echo $message; ?></p>    
    <form method="post" action="/surveycreate">
        {{ csrf_field() }}
        どんなことを聞いてみる？<br>
        <textarea name="question" rows="4" cols="40">例：今夜の夕食はどれがいい？</textarea><br>
        <br>
        選択肢を設定しよう<br>
        <textarea name="option[]" rows="1" cols="40">例：カレー</textarea><br>
        <textarea name="option[]" rows="1" cols="40">例：寿司</textarea><br>
        <textarea name="option[]" rows="1" cols="40">例：焼き肉</textarea><br>
        <textarea name="option[]" rows="1" cols="40">例：ラーメン</textarea><br>
        <br>
        締め切りまでの時間を選ぼう<br>
        <input type="radio" name="timelimit" value="1h" checked>1時間　
        <input type="radio" name="timelimit" value="1d">1日<br>
        <input type="radio" name="timelimit" value="3h">3時間　
        <input type="radio" name="timelimit" value="3d">3日<br>
        <input type="radio" name="timelimit" value="6h">6時間　
        <input type="radio" name="timelimit" value="7d">7日<br>
        <br>
        <input type="submit" value="アンケートを作る！">
    </form>
    <br>
    <br>
    <a href="/">トップに戻る</a>
    <br>
    @include('layouts.loginbutton', array('logon'=>$data['logon']))
@endsection