<!-- 
  PHP script that will run whenever a commenting form is posted.
  Possibly hazardous content will be sanitized in function calls. 
-->
<?php

  // If any comment form was submitted, save it to database
  if ( isset( $_POST["comment"] ) ) {

    include "db_comment_module.php";
    include "db_connect.php";

    // Get filled reply form fields
    $author = $_POST["name"];
    $reply = $_POST["comment"];
    $parent_post_id = $_POST["parent_post_id"];
    $project_title = $_POST["project"];

    // Parameters are sanitized in the function call
    post_comment($author, $reply, $parent_post_id, $project_title);

    // Format html code for the posted comment
    // Note: We won't allow the user to reply his own comment
    echo "<div class=\"comment_block\"> ";
    echo "<div class=\"comment\" style=\"background-color:green;\">";
    echo "<b> &nbsp;&nbsp;" . $author . "&nbsp;</b>";
    echo "commented on " . date( "D, d M Y H:i", time() ) . "<br> <hr> ";
    echo $reply . "<br>";
    echo "</div></div>";

  }

?>