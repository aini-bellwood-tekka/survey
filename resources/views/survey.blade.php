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
    <?php echo '作成者：'.$data['author_id']; ?><br>
    <br>
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection