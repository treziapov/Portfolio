<?php
	/* 
		This script sends a curl request to download the posted file from svn repository.
	*/
	include "../svn_user_info.php";
	$path = $_POST["path"];

	$ch = curl_init();
	$timeout = 10;
	$url = "https://subversion.ews.illinois.edu/svn/fa12-cs242/reziapo1/" . $path;

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRASFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

	$data = curl_exec($ch);
	curl_close($ch);

	// We want to treat the whole file as string and ignore any potential html, 
	// must replace all '<' with '&lt;' for SyntaxHighlighter to work
	$data = html_entity_decode($data);
	$data = htmlspecialchars($data, ENT_QUOTES);

	// Send the data as response to jquery
	echo $data;
?>