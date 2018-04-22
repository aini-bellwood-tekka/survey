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
    <form method="get" action="/">
        {{ csrf_field() }}
        <?php echo $data->question; ?><br>
        <input type="radio" name="option" value="0" checked><?php echo $data->option[0]; ?><br>
        <input type="radio" name="option" value="1"><?php echo $data->option[1]; ?><br>
        <input type="radio" name="option" value="2"><?php echo $data->option[2]; ?><br>
        <input type="radio" name="option" value="3"><?php echo $data->option[3]; ?><br>
        <br>
        <input type="submit" value="投票する！">
    </form>
    <br>
    <form method="post" action="/logoff">
        {{ csrf_field() }}
        <input type="submit" value="ログアウト">
    </form>
@endsection