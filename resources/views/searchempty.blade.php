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
    
    <form method="post" action="/search">
        {{ csrf_field() }}
        <select name="search_type">
        <option value="tag" <?php if($data['searchOption']['search_type'] == 'tag'){echo("selected");}?>>タグ検索</option>
        <option value="text" <?php if($data['searchOption']['search_type'] == 'text'){echo("selected");}?>>キーワード検索</option>
        </select>
        <input type="text" name="search_text" size="40" value=<?= $data['searchOption']['search_text'] ?>>
        <input type="hidden" name="sort" value=<?= $data['sort'] ?>>
        <input type="hidden" name="order" value=<?= $data['order'] ?>>
        <input type="submit" value="検索">
    </form>
    <br>
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection