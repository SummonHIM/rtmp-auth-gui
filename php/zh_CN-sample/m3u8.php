<?php
include "config.php";
header("Content-Type: text/plain");
try {
    $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname . ';charset=utf8', $dbusername, $dbpassword);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    echo '数据库出错！';
    echo '错误信息： ' . $e->getMessage();
    die();
}

$qGetUsersInfo = "SELECT * FROM users";
$qGetUsersResult = $dbh->query('select id_user,username,stream_key,private,private_key,live_status from users');

echo "#EXTM3U\n";
while ($loopUsersResult = $qGetUsersResult->fetch()) {
    if ($loopUsersResult['username'] != null && $loopUsersResult['stream_key'] != null) {
        echo "#EXTINF:-1 group-title=\"网络用户直播\"," . $loopUsersResult['username'] . " 的直播间\n";
        echo $rtmpurl . $loopUsersResult['username'] . "\n";
    }
}
?>