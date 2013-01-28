<?php

	/* 
		Script to connect to MySQL database through PDO.
		Helps avoid duplication and separates access details.
	*/

	$username = "reziapo1_visitor";
    $password = 'DTn[4a$A}[5p';
    $database = "reziapo1_cs242";
    $table = "Comments";
    $host = "engr-cpanel-mysql.engr.illinois.edu";

	try {
	  $connection = new PDO ("mysql:host=$host;dbname=$database", $username, $password);
	  $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} 
	catch (PDOException $e) {
      echo "Error!: " . $e->getMessage() . "<br>";
      die();
    }

?>