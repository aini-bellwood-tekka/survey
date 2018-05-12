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
    <table>
    <?php foreach ($data['option'] as $op){ ?>
         <tr><td><?php echo $op['vote_count'].'票：'.(($op['vote_count'] < 1 or $data['all_vote_count'] <1 )?('0'):(($op['vote_count'] /$data['all_vote_count'])*100.0)).'％'; ?></td>
             <td><?php echo $op['text']; ?></td>
             <td><?php echo ($op['my_voted'])?'あなたが投票しました！':''; ?></td></tr>
    <?php } ?>
    </table>
    <?php if( $data['my_survey'] ) { ?>
        <?php echo 'あなたが作成した質問です。'; ?><br>
    <?php }elseif($data['my_vote_num'] == -1){ ?>
        <?php echo 'あなたは投票しませんでした。'; ?><br>
    <?php } ?>
    <?php echo '総投票数：'.$data['all_vote_count'].'票'; ?><br>
    <br>
    <?php echo '作成者：'.$data['author_id']; ?><br>
    <br>
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    <a href="/logoff">ログアウト</a>
@endsection