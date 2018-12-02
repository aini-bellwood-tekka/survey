@extends('layouts.base')
@section('title', 'Sample')
@section('stylesheet')
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
    <form method="post" action="/login">
        {{ csrf_field() }}
        <table>
            <tr><td>ユーザーID:</td><td><input type="text" name="user_name"></td></tr>
            <tr><td>パスワード:</td><td><input type="text" name="pass"></td></tr>
        </table>
        <input type="submit" value="ログイン">
    </form>
    </br>
    <form method="get" action="/signup">
        <input type="submit" value="アカウント作成">
    </form>
    <a class="btn btn-default" href="/login/twitter">
        twitter
    </a>
@endsection