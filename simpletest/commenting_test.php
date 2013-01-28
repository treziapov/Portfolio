<?php
	
	require_once "simpletest/autorun.php";
	require_once "scripts/db_comment_module.php";
	
	class CommentingTest extends UnitTestCase {

		/*
			This test cheks if a valid comment can be posted via the portfolio website's scripts.
		*/
		function testAddingComment() {

			require "scripts/db_connect.php";

			// Setup  a valid comment
			$author = "TestBot";
			$content = "Automated Test";
			$parent_id = -1;
			$project = "shopping";

			post_comment($author, $content, $parent_id, $project);

			// Check if comment was stored on the database
			$statement = $connection->prepare(
				"SELECT * FROM $table 
				 WHERE author = ? AND content = ?
				 ORDER BY date
				 LIMIT 1"
				 
			);

			// Assert that query for the posted comment returns TRUE - its successful
			$this->assertTrue( $statement->execute( array($author, $content) ) );

			// Assert that query contains exactly one row which is the comment
			$result = $statement->fetch();
			$this->assertEqual( $result["parent_comment_id"], $parent_id );
			$this->assertEqual( $result["parent_project"], $project );

		}

		/*
			This test cheks if a valid reply can be posted via the portfolio website's scripts.
			Very similar to posting a comment.
		*/
		function testAddingReply() {

			require "scripts/db_connect.php";

			// Setup  a valid comment
			$author = "TestBot";
			$content = "Automated Test";
			$parent_id = -1;
			$project = "shopping";

			post_comment($author, $content, $parent_id, $project);

			// Check if comment was stored on the database
			$statement = $connection->prepare(
				"SELECT * FROM $table 
				 WHERE author = ? AND content = ?
				 ORDER BY date DESC
				 LIMIT 1"
			);

			// Assert that query for the posted comment returns TRUE - its successful
			$this->assertTrue( $statement->execute( array($author, $content) ) );

			$row = $statement->fetch();
			$comment_id = $row["id"];

			// Setup  a valid reply to previous comment
			$author = "TestBotReplier";
			$content = "Automated Reply";
			$parent_id = $comment_id;
			$project = "shopping";

			post_comment($author, $content, $parent_id, $project);

			// Assert that query for the posted comment returns TRUE - its successful
			$this->assertTrue( $statement->execute( array($author, $content) ) );

			$result = $statement->fetch();

			$this->assertEqual( $result["parent_comment_id"], $comment_id );			

		}

		/*
			This test checks that a comment added with red flagged words is filtered when displaying.
		*/
		function testFiltering() {

			require "scripts/db_connect.php";

			// Setup a comment that should be filtered
			$flag = "hell";
			$replacement = "oops";

			$author = "TestBadBot";
			$content = "Automated Test with Flagged Words such as: " . $flag;
			$parent_id = -1;
			$project = "shopping";

			post_comment($author, $content, $parent_id, $project);

			// Check if comment was stored on the database
			$statement = $connection->prepare(
				"SELECT * FROM $table 
				 WHERE author = ? AND content = ?
				 ORDER BY date
				 LIMIT 1"
			);

			// Assert that query for the posted comment returns TRUE - its successful
			$this->assertTrue( $statement->execute( array($author, $content) ) );

			$result = $statement->fetch();

			// Assert that flag isn't contained and replacement is in the content
			$this->assertFalse( strpos( $result["content"], $flag ) );
			$this->assertTrue( strpos( $result["content"], $replacement) >= 0 );

		}

		/*
			Tests is SQL injection is prevented from user inputs.
		*/
		function testSqlInjection() {

			require "scripts/db_connect.php";

			// Get the original number of comments to check against later
			$statement= $connection->prepare( "SELECT * FROM $table ");
			$statement->execute();
			$num_comments = sizeof( $statement->fetchAll() );

			// Setup a comment with attempted SQL injection
			$author = "BadBot";
			$content = "a' OR  '1'='1' -- '; DROP * FROM $table";
			$parent_id = -1;
			$project = "shopping";

			post_comment($author, $content, $parent_id, $project);


			// Check if comment was stored on the database as it should be properly sanitized
			$statement = $connection->prepare(
				"SELECT * FROM $table 
				 WHERE author = ? AND content = ?
				 ORDER BY date
				 LIMIT 1"
			);

			// Assert that query for the posted comment returns TRUE - its successful
			$this->assertTrue( $statement->execute( array($author, $content) ) );

			$result = $statement->fetch();

			// Assert that the attempted SQL command is actually stored in comment text
			$this->assertFalse( strpos( $result["content"], $content ) );

			$statement= $connection->prepare( "SELECT * FROM $table ");
			$statement->execute();
			
			// Assert that no comments were deleted, and number only increased by 1
			$results = $statement->fetchAll();

			$this->assertNotEqual( sizeof($results), 0 );
			$this->assertEqual( sizeof($results), $num_comments + 1 );

		}






	}

?>