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
    
    <?php foreach ($data['survey'] as $sv){ ?>
    <a href=<?= 'survey?id='.$sv['id'] ?>><?php echo $sv['text']; ?></a><br>
    <?php } ?>
    <br>
    
    <?php if($data['page'] > 1) { ?>
    <a href=<?= 'search?page='.($data['page'] - 1) ?>>←</a>
    <?php } ?>
    <?php for($i = -3; $i <= 3; $i++ ) { ?>
        <?php if( $data['page'] + $i > 0 ) { ?>
            <?php if( $i == 0 ) { ?>
                <?php echo $data['page'] ?>
            <?php }else{ ?>
                <a href=<?= 'search?page='.($data['page'] + $i) ?>><?php echo ($data['page'] + $i) ?></a>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <a href=<?= 'search?page='.($data['page'] + 1) ?>>→</a>
    <br>
    
    <br>
    <a href="/logoff">ログアウト</a>
@endsection