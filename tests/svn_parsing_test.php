<?php

require_once '../simpletest/autorun.php';
require_once "../scripts/parser.php";

class TestOfLogging extends UnitTestCase {

	/*
		Test illegal usage: non-existent or non-xml files, non-file arguments, etc.
	*/
	function testIllegal() {

		$list_arg = NULL;
		$log_arg = NULL;

		// Test NULL file args
		$caught = false;
		try {
			parse_svn_list($list_arg);
		}
		catch (Exception $e) {
			$caught = true;
		}
		$this->assertTrue($caught);

		$caught = false;
		try {
			parse_svn_list($log_arg, NULL);
		}
		catch (Exception $e) {
			$caught = true;
		}
		$this->assertTrue($caught);

		$list_arg = "b";
		$log_arg = "";
		$projects = array();

		// Test invalid-file arguments
		$caught = false;
		try {
			parse_svn_list($list_arg);
		}
		catch (Exception $e) {
			$caught = true;
		}
		$this->assertTrue($caught);

		$caught = false;
		try {
			parse_svn_list($log_arg, $projects);
		}
		catch (Exception $e) {
			$caught = true;
		}
		$this->assertTrue($caught);

	}

	/*
		Tests single entry svn xml files. 
	*/
	function testSimpleXml() {

		$test_list = "test_xml/simple_list.xml";
		$test_log = "test_xml/simple_log.xml";

		try {
			$projects = parse_svn_list($test_list);
		}
		catch (Exception $e) {
			$this->fail();
		}

		// Check project contents against expeced values
		$this->assertTrue( array_key_exists("Test", $projects) );

		$project = $projects["Test"];

		$this->assertEqual( $project->title, "Test" );
		$this->assertEqual( $project->version, 1000 );
		$this->assertEqual( $project->summary, "" );
		$this->assertEqual( $project->date, "Thu, 11 Oct 2012 14:37" );
		$this->assertEqual( sizeof( $project->files ), 1 );
		$this->assertTrue( array_key_exists("Test/test.java", $project->files) );

		$file = $project->files["Test/test.java"];

		// Check the project's only file contents, which shouldn't have any versions yet
		$this->assertEqual( $file->path, "Test/test.java" );
		$this->assertEqual( $file->type, "code" );
		$this->assertEqual( $file->size, 100 );
		$this->assertEqual( sizeof($file->versions), 0 );

		// Add log information
		try {
			parse_svn_log($test_log, $projects);
		}
		catch (Exception $e) {
			$this->fail();
		}

		// Check that the file's only version information was added and stored
		$this->assertEqual( sizeof($file->versions), 1 );

		// Check that the project's summary is set to latest commit - the only commit message
		$this->assertEqual( $project->summary, "Message" );

		$version = $file->versions[0];

		$this->assertEqual( $version->message, $project->summary );
		$this->assertEqual( $version->author, "author" );
		$this->assertEqual( $version->date, "Thu, 11 Oct 2012 14:37" );

	}

	/*
		Tests a one project with one multiple revisions file.
	*/
	function testSingleMultirevisionFileXml() {

		$test_list = "test_xml/simple_list.xml";
		$test_log = "test_xml/simple_multirevision_file_log.xml";

		try {
			$projects = parse_svn_list($test_list);
		}
		catch (Exception $e) {
			$this->fail();
		}

		// Check project contents against expeced values
		$this->assertTrue( array_key_exists("Test", $projects) );

		$project = $projects["Test"];

		$this->assertEqual( $project->title, "Test" );
		$this->assertTrue( array_key_exists("Test/test.java", $project->files) );

		$file = $project->files["Test/test.java"];

		// Check the project's only file contents, which shouldn't have any versions yet
		$this->assertEqual( $file->path, "Test/test.java" );
		$this->assertEqual( sizeof($file->versions), 0 );

		// Add log information
		try {
			parse_svn_log($test_log, $projects);
		}
		catch (Exception $e) {
			$this->fail();
		}

		// Check that the file's version information was added and stored
		$this->assertEqual( sizeof($file->versions), 2 );

		// Check that the project's summary is set to latest commit - the later revision
		$this->assertNotEqual( $project->summary, "Earlier Message" );
		$this->assertEqual( $project->summary, "Message" );

		$earlier_version = $file->versions[1];

		// Check that earlier revision information is stored
		$this->assertEqual( $earlier_version->version_number, 999 );
		$this->assertEqual( $earlier_version->message, "Earlier Message" );
		$this->assertEqual( $earlier_version->author, "author" );
		$this->assertEqual( $earlier_version->date, "Tue, 09 Oct 2012 14:37" );

	}

	/*
		Tests a one project with multiple files with multiple revisions.
	*/
	function testMultirevisionFilesXml() {

		$test_list = "test_xml/multi_file_list.xml";
		$test_log = "test_xml/multi_file_multirevision_log.xml";

		try {
			$projects = parse_svn_list($test_list);
		}
		catch (Exception $e) {
			$this->fail();
		}

		$project = $projects["Test"];


		// Check the number of files in the project
		$this->assertEqual( sizeof( $project->files ), 3 );

		// Add log information
		try {
			parse_svn_log($test_log, $projects);
		}
		catch (Exception $e) {
			$this->fail();
		}

		$file3 = $project->files["Test/test3.java"];
		$file2 = $project->files["Test/test2.java"];
		$file1 = $project->files["Test/test1.java"];

		// Check the number of revisions in each file, which is just the number in file name
		$this->assertEqual( sizeof( $file3->versions), 3 );
		$this->assertEqual( sizeof( $file2->versions), 2 );
		$this->assertEqual( sizeof( $file1->versions), 1 );

	}

	/*
		Test svn information with deleted files;
	*/
	function testDeletedFile() {
		$test_list = "test_xml/multi_file_list.xml";
		$test_log = "test_xml/multi_file_multirevision_deleted_log.xml";

		try {
			$projects = parse_svn_list($test_list);
		}
		catch (Exception $e) {
			$this->fail();
		}

		$project = $projects["Test"];

		// Check the number of files in the project, shouldn't include the deleted one
		$this->assertEqual( sizeof( $project->files ), 3 );

		// Add log information
		try {
			parse_svn_log($test_log, $projects);
		}	
		catch (Exception $e) {
			$this->fail();
		}

		// Check that the deleted file doesn't exists in project's files
		$this->assertFalse( array_key_exists("Test/deleted.java", $project->files) );
	}

}

?>
