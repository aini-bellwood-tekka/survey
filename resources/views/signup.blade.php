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
    <form method="post" action="/usercrate">
        {{ csrf_field() }}
        <table>
            <tr><td>ID:</td><td><input type="text" name="id"></td></tr>
            <tr><td>PASS:</td><td><input type="text" name="pass"></td></tr>
        </table>
        <input type="submit" value="アカウント作成">
    </form>
@endsection