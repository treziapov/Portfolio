<?php

    /*
        Provide a function to recursively display all comments for a predetermined project.
        @param prepared_query - an SQL query prepared before the function is called, assumed to be valid
        @id - the current parent comment id
        @colorhex - the current color for reply background
        NOTE: Database contents are trusted
    */
	function display_all_comments_html($prepared_query, $id, $colorhex) {

        // Prepare the query with the current parent comment id
        $prepared_query->bindValue(":parent_id", $id);

        // Return if either the query failed or it returned no results
	   	if ( !$prepared_query->execute() ) {
			return;
		}
		$rows = $prepared_query->fetchAll();

		if ( sizeof($rows) == 0 ) {
			return;
		}

		foreach ($rows as $row) {

            $formatted_date = date("D, d M Y H:i", strtotime( $row['date'] ) );

            // Hex arithmetic to determine next comments color
            $r = hexdec(substr($colorhex,0,2));
		    $g = hexdec(substr($colorhex,2,2));
		    $b = hexdec(substr($colorhex,4,2));
		    $new_r = dechex( max($r - 50, 0) );
		    if ( strlen($new_r) < 2 ) {
		    	$new_r = "0" . $new_r;
		    }
		    $new_g = dechex( min($g + 1, 255) );
		    $new_b = dechex( min($b + 15, 255) );
		    $new_colorhex = $new_r . $new_g . $new_b;

            // Display html code for current comment
            echo "<div class=\"comment_block\"> ";
            echo "<div class=\"comment\" style=\"background-color:rgb($r,$g,$b);\">";
            echo "<b> &nbsp;&nbsp;" . $row["author"] . "&nbsp;</b>";
            echo "commented on " . $formatted_date . "<br> <hr> ";
            echo $row["content"] . "<br>";
            echo "<div id=" . $row["id"] . ">";

            // Recurse into with this comment as parent comment
            display_all_comments_html($prepared_query, $row["id"], $new_colorhex);

            // This button will allow to show the hidden reply form
            echo "</div>";
            echo "<button type=\"button\" onclick=\"showReplyForm(" . $row["id"] . ")\" 
                  style=\"float:right; margin-right:10px; margin-top:3px;\"> Reply </button> <br>";
            echo "</div> </div>";

            // Setup the initially hiding reply form
            echo "<form name=" . $row["id"] . " onsubmit=\"postCommentAJAX(this);\" 
                    style=\"margin:5px; padding:5px; display:none;\">";
            echo "<label for=\"name\" 
                    style=\"font:normal 12px Arial, Helvetica, sans-serif; color:#c1c1c1\"> 
                    Name: 
                 </label> ";
            echo "<input type=\"text\" name=\"name\" id=\"name\" pattern=\"[a-zA-z0-9_\s\.]{0,25}\"> <br>"; 
            echo "<textarea name=\"comment\" required maxlength=\"65000\" placeholder=\"Type here...\"
                   style=\"width:400px; height:60px;\"></textarea><br>";

            // This particular text box will never be seen by the viewer, it is only used to pass the parent id
            echo "<input type=\"text\" name=\"parent_post_id\" value=\"" . $row["id"] . "\" style=\"display:none;\">";

            echo "<input type=\"submit\" value=\"Submit\">";

            // This button will allow to cancel a comment reply by rehiding the reply form
            echo "<button type=\"button\" onclick=\"hideReplyForm(" . $row["id"] . ")\"> Cancel </button>";
            echo "</form>";

		}


		return;
	}


    /*
        This function replaces red flags from database in the given string.
    */
    function filter_out_flags( $string ) {

        include "db_connect.php";

        // Set up an array of flagged words to replace from the database
        // and an array containing each respective word's replacement in the same order
        $flags = array();
        $replacements = array();

        $flagged_words_query = $connection->prepare( "SELECT * FROM RedFlags" );

        if ( $flagged_words_query->execute() ) {
          while ( $row = $flagged_words_query->fetch() ) {
            array_push( $flags, $row['actual'] );
            array_push( $replacements, $row['filter'] );
          }
        }

        // Also replace newline characters with html tag <br>
        array_push($flags, "\n");
        array_push($replacements, "<br>");

        $result = str_replace( $flags, $replacements, $string );
        
        return $result;
    }

	/*
		This function adds the passed parameters as a Comment to a MySQL database.
	*/
	function post_comment(&$author, &$reply, $parent_id, $project_title) {

		include "db_connect.php";

	    // Check if comment is empty
	    if ( strcmp($reply, "") == 0 || strlen($reply) == 0 ) {
	      echo "<h2>Empty comments not allowed!</h2>";
	      die();
	    }

	    // Check if author is empty
	    if ( strlen($author) == 0 ) {
	      $author = "Anonymous";
	    }

		// Escape html code and actually modify the original values
	    $author = htmlspecialchars($author);
	    $reply = htmlspecialchars($reply);
	    
	    // Filter the red flags from database
        $author = filter_out_flags($author);
        $reply = filter_out_flags($reply);
		
		$insert_statement = $connection->prepare(
			"INSERT INTO $table (author, content, parent_comment_id, parent_project)
			 VALUES (:author, :content, :parent_id, :title)
		");

		// Better practice of committing changes to database, auto-reverts on error
		$connection->beginTransaction();

		$insert_statement->bindParam(":author", $author);
		$insert_statement->bindParam(":content", $reply);
		$insert_statement->bindParam(":parent_id", $parent_id );		
		$insert_statement->bindParam(":title", $project_title);

		$insert_statement->execute();

		$connection->commit();
	}

?>