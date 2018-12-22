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
    <h1>あんけーと</h1
    <p><?php echo $message; ?></p>
    <p><?php echo 'ユーザー名：'.$data['screen_name']; ?></p>
    <p><?php echo 'user_id：'.$data['user_id']; ?></p>
    <p><?php echo 'user_name：'.$data['user_name']; ?></p>
    <a href="/surveycreate">アンケートを作る</a><br>
    <br>
    <a href="/search?page=1&sort=d&order=n">アンケートを探す</a><br>
    <br>
    <a href="/search?page=1&sort=ms&order=n">自分の作ったアンケートを探す</a><br>
    <br>
    <a href="/search?page=1&sort=ma&order=n">自分の回答したアンケートを探す</a><br>
    <br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection