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
	$qInfo = "SELECT private, private_key FROM users WHERE username = :username";
	$qInfoPrepared = $dbh->prepare($qInfo);
	$qInfoPrepared->execute(array(':username' => $_POST['name']));
	$qInfoResults = $qInfoPrepared->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
	http_response_code(403);
	die();
}
if($qInfoResults['private'] == '1') {
	if(empty($_POST['key'])){
		http_response_code(403);
		die();
	}
		elseif($_POST['key'] == $qInfoResults['private_key']) {
	        http_response_code(200);
        	die();
	}
	else {
		http_response_code(403);
		die();
	}
}
?>
