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
    <?php //echo $message; ?><br>
    <form method="get" action="/">
        {{ csrf_field() }}
        <?php echo $data['question']; ?><br>
        
        <?php foreach ($data['option'] as $op){ ?>
        <input type="radio" name="option" value=<?= $op['var'] ?> <?php if($op['checked']) {print 'checked';} ?>><?php echo $op['text']; ?><br>
        <?php } ?>
        <br>
        <input type="hidden" name="id" value=<?= $data['survey_id'] ?>>
        <input type="submit" value="投票する！">
    </form>
    <br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection