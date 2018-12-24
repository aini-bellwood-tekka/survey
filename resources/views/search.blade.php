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
    <form method="post" action="/search">
        {{ csrf_field() }}
        <select name="search">
        <option value="tag" <?php if($data['searchOption']['search'] == 'tag'){echo("selected");}?>>タグ検索</option>
        <option value="text" <?php if($data['searchOption']['search'] == 'text'){echo("selected");}?>>キーワード検索</option>
        </select>
        <input type="text" name="text" size="40" value=<?= $data['searchOption']['text'] ?>>
        <input type="hidden" name="sort" value=<?= $data['sort'] ?>>
        <input type="hidden" name="order" value=<?= $data['order'] ?>>
        <input type="submit" value="検索">
    </form>
    <br>
    <p><?php echo $message; ?></p>
    <br>
    <?php foreach ($data['survey'] as $sv){ ?>
    <a href=<?= 'survey?id='.$sv['id'] ?>><?php echo $sv['text'];?></a><br>
    <?php echo (($sv['is_end'])?'　投票は締め切っています。':('　残り時間：'.$sv['remaining_time'])).'　作成者：'.$sv['screen_name'].'　作成日時：'.$sv['created_at']; ?><br>
    <br>
    <?php } ?>
    <br>
    
    <?php if($data['page'] > 1) { //1ページ目には前のページが存在しない ?>
    <a href=<?= 'search?page='.($data['page'] - 1).($data['url_option']) //前のページ ?>>←</a>
    <?php } ?>
    <?php for($i = -3; $i <= 3; $i++ ) { //ページ送り ?>
        <?php if( $data['page'] + $i > 0 ) { //ページ数が0を下回る場合は非表示 ?>
            <?php if( $i == 0 ) { ?>
                <?php echo $data['page'] //いまいるページ。なのでリンクは張らない。 ?>
            <?php }elseif( $i < 0 || ($i > 0 && $data['count'] > ($data['page'] + $i) * 10)){ ?>
                <a href=<?= 'search?page='.($data['page'] + $i).($data['url_option']) ?>><?php echo ($data['page'] + $i) ?></a>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php if($data['count'] > ($data['page'] + 1) * 10) { //取得した質問件数が10個未満の場合は次のページへのリンクを張らない ?>
    <a href=<?= 'search?page='.($data['page'] + 1).($data['url_option']) //次のページ ?>>→</a>
    <?php } ?>
    <br>
    <br>
    <a href="/">トップに戻る</a><br>
    <br>
    @include('layouts.loginbutton')
@endsection