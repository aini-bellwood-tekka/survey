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
    <?php echo $message; ?><br>
    <?php echo $data['question']; ?><br>
    <form method="post" action="/vote">
        {{ csrf_field() }}
        
        <?php foreach ($data['option'] as $op){ ?>
        <input type="radio" name="option" value=<?= $op['var'] ?>><?php echo $op['text']; ?><br>
        <?php } ?>
        <br>
        <input type="radio" name="option" value='-1' checked>投票しないけど結果は見たい
        <br>
        <br>
        <input type="hidden" name="id" value=<?= $data['survey_id'] ?>>
        <input type="submit" value="投票する！">
    </form>
    <?php echo '総投票数：'.$data['all_vote_count'].'票'; ?><br>
    <br>
    
    <?php foreach ($data['tag'] as $tag){ ?>
        <form method="post" action="/tagerase" style="display:inline">
            {{ csrf_field() }}
            &emsp;
            <a href=<?= "/search?page=1&sort=d&order=n&tag=".$tag['name'] ?>><?php echo $tag['name']; ?></a>
            <input type="hidden" name="survey_id" value=<?= $data['survey_id'] ?>>
            <input type="hidden" name="tag_id" value=<?= $tag['tag_id'] ?>>
            <input type="hidden" name="lock_type" value=<?= $tag['lock_type'] ?>>
            <input type="submit" value="削除">
        </form>
    <?php } ?>
    
    <form method="post" action="/tagcreate">
        {{ csrf_field() }}
        <input type="text" name="name">
        <input type="hidden" name="survey_id" value=<?= $data['survey_id'] ?>>
        <input type="hidden" name="lock_type" value=<?= 0 ?>>
        <input type="submit" value="タグ追加">
    </form>
    <br>
    
    <?php echo '作成者：'.$data['author_id']; ?><br>
    <br>
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection