<?php

	require "parser.php";
	$root = $_SERVER["SERVER_NAME"];
	$projects_hash = parse_svn_list("svn_list.xml");
	parse_svn_log("svn_log.xml", $projects_hash);

?>