<?php
include "config.php";

try {
	$dbh = new PDO('mysql:host='.$dbhost.';dbname='.$dbname.';charset=utf8', $dbusername, $dbpassword);
}
catch(PDOException $e) {
	http_response_code(403);
	die();
}
if(empty($_POST['name'])) {
	http_response_code(403);
	die();
}
try {
	$qUpdate = "UPDATE users SET live_status=0 WHERE stream_key = :stream_key";
	$qUpdatePrep = $dbh->prepare($qUpdate);
	$qUpdatePrep->execute(array(':stream_key' => $_POST['name']));
}
catch(PDOException $e) {
	http_response_code(403);
	die();
}
?>
