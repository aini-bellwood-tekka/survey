
    <br>
<?php
if ( $userdata['logon'] ){
    echo '<a href="/">マイページ</a>　<a href="/logoff">ログアウト</a>';
}else{
    echo '<a href="/login/twitter">Twitter連携でログイン</a>';
}
?>
