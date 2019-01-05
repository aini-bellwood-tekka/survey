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
    <?php if( $data['is_my_survey'] ) { ?>
        <?php echo 'あなたが作成した質問です。'; ?><br>
    <?php }elseif($data['my_vote_num'] == -1){ ?>
        <?php echo 'あなたは投票しませんでした。'; ?><br>
    <?php } ?>
    <?php echo (($data['is_end'])?'投票は締め切っています。':('残り時間：'.$data['remaining_time'])); ?><br>
    <?php echo '総投票数：'.$data['all_vote_count'].'票'; ?><br>
    <br>
    
    タグ一覧：
    <?php foreach ($data['tag'] as $tag){ ?>
        <form method="post" action="/tagerase" style="display:inline">
            {{ csrf_field() }}
            <a href=<?= "/search?page=1&sort=d&order=n&search=tag&text=".$tag['name'] ?>><?php echo $tag['name']; ?></a>
            <input type="hidden" name="survey_id" value=<?= $data['survey_id'] ?>>
            <input type="hidden" name="tag_id" value=<?= $tag['tag_id'] ?>>
            <input type="hidden" name="lock_type" value=<?= $tag['lock_type'] ?>>
            <input type="submit" value="削除">
            &emsp;
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
    
    <?php echo '作成者：'.$data['create_user_screen_name']; ?><br>
    <?php echo '開始日時：'.$data['start_at']; ?><br>
    <?php echo '終了日時：'.$data['end_at']; ?><br>
    <br>
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    @include('layouts.loginbutton')
@endsection