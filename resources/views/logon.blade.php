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
    <a href="/surveycreate">アンケートを作る</a><br>
    <br>
    <a href="/search">アンケートを探す</a><br>
    <br>
    
       
    <br>
    <a href="/logoff">ログアウト</a>
@endsection