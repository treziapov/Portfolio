<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>CS 242 Portfolio</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css" />

<!-- Syntax Highlighter Code -->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.collapsible.js"></script>
<script type="text/javascript" src="js/shCore.js"></script>
<script type="text/javascript" src="js/shBrushJava.js"></script>
<script type="text/javascript" src="js/shBrushPython.js"></script>
<script type="text/javascript" src="js/shBrushRuby.js"></script>
<script type="text/javascript" src="js/shBrushXML.js"></script>
<link href="css/shCore.css" rel="stylesheet" type="text/css" />
<link href="css/shThemeDefault.css" rel="stylesheet" type="text/css" />

<!-- Some java script functions for displaying html element contents-->
<script type="text/javascript">

  // Trigger the collapsible feature when the document is loaded
  $(document).ready(function() {
    $(".collapsible").collapsible();
  });

  /*
    Javascript function that is activated whenever any file button is clicked.
    The file is identified by the id parameter. The function loads the contents of the
    file from subversion to the page's only iframe.
  */
  function showFileFrame(id) {
    /* 
    Non jquery version

    // Set up an AJAX post request for subversion files
    var xmlhttp;

    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    }
    else {
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    // On successful request, place the file content into file_frame div and highlight it
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        document.getElementById("file_frame").innerHTML = "";
        document.getElementById("file_frame").innerHTML = "<pre id='file' class='brush: java'></pre>";
        document.getElementById("file").innerHTML = xmlhttp.responseText.replace(/<(?:.|\n)*?>/gm, '');
        SyntaxHighlighter.highlight();
      }
    }

    xmlhttp.open( "POST", "scripts/svn_file_content.php", true );
    xmlhttp.setRequestHeader( "Content-type","application/x-www-form-urlencoded" );
    xmlhttp.send("path=" + id);
    */

    $('#file_frame').html("<pre id='file' class='brush: java'></pre>");
    $('#file').load("scripts/svn_file_content.php", { path: id }, 
      function() {
        SyntaxHighlighter.highlight();
      });


    // Scroll to the top of the page to show the frame
    $('html, body').animate({ scrollTop: 0 }, 'slow');

  }


  /*
    Shows a form with input fields whenever a Reply button is clicked.
  */
  function showReplyForm(name) {
    document.getElementsByName(name)[0].style.display="block";
  }


  /*
    Hides the form and input fields whenever a Cancel button is clicked.
  */
  function hideReplyForm(name) {
    document.getElementsByName(name)[0].style.display="none";
  }

  /*
    This function will be called whenever a comment is posted. 
    Loads the comment into the page without refreshing and runs a php script
    to insert the comment into the database.
  */
  function postCommentAJAX(form) {

    // Stop the form from sending the request and refreshing the page
    event.preventDefault();

    // Get the comment related values of form elements
    var author = form.elements["name"].value;
    var content = form.elements["comment"].value;
    var parent_id = form.elements["parent_post_id"].value;
    var project = document.getElementById("project_title").innerHTML;

    // Set up an AJAX post request to post the form
    var xmlhttp;

    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
      }
    else {
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

        // Display the entered comment without reloading
        $( "#" + parent_id ).prepend( xmlhttp.responseText );
      }
    }

    xmlhttp.open( "POST", "scripts/post.php", true );
    xmlhttp.setRequestHeader( "Content-type","application/x-www-form-urlencoded" );
    xmlhttp.send( "name=" + author + "&comment=" + content + 
                  "&parent_post_id=" + parent_id + "&project=" + project );
    
    /* Problem: rewrites comment on non-reply comment
    $("#"+parent_id).load("scripts/post.php", 
      { 
        name: author, 
        comment: content,
        parent_post_id: parent_id, 
        project: project 
      }
      );
    */
  }

</script>

</head>

<body>
<div class="main">
  <div class="header_resize">
    <div class="header">
      <div class="menu">
        <ul>
          <li><h2>TIMUR REZIAPOV<h2></li>
          <li><a href="index.html">Home</a></li>
          <li><a href="portfolio.php" class="active">Portfolio</a></li>
        </ul>
      </div>
      <div class="search">
        <form id="form1" name="form1" method="post" action="">
          <span>
          <input name="q" type="text" class="keywords" id="textfield" maxlength="50" value="Search...to be added!" />
          </span>
          <input name="b" type="image" src="images/search.gif" class="button" />
        </form>
      </div>
      <div class="clr"></div>
      <div class="clr"></div>
    </div>
  </div>

  <!-- Get the passed project title from this url and locate the project object from the included script. 
        Also startup the connection to database for retrieving and potentially posting comments -->
	<?php
		include "scripts/svn_parsing_script.php";
    include "scripts/db_connect.php";
    include "scripts/db_comment_module.php";

		$title = $_GET["name"];

    // $title is NOT TRUSTED, $project->title is TRUSTED
		$project = $projects_hash[$title];
    if ( is_null($project) ) {
      echo "<h2> No such project! </h2>";
      die();
    }
    $comments_limit = 15;
	?>

  <div class="header_blog">
    <div class="header_blog_resize">

      <?php 
        echo "<h2 id=\"project_title\">$project->title</h2>"; 
      ?>

      <div class="menu2">
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <div class="body">
    <div class="body_resize">

      <!-- Display all the information on the project in formatted html. -->
      <?php

        echo "<div style=\"float:left; width:300px;\">
          <ul>
          <li><p><font size=\"3.5\"><strong>Date: </strong>$project->date</font></p></li>
          <li><p><font size=\"3.5\"><strong>Version: </strong>$project->version</font></p></li>
          <li><p><font size=\"3.5\"><strong>Last Commit Message: </strong>$project->summary</font><p></li>";
        foreach ($project->files as $file) {
          echo "
            <p><strong><u>FILE: </strong>$file->path</u></p>
            <ul>
            <li><p><strong>Size: </strong>$file->size Bytes</p></li>
            <li><p><strong>Type: </strong>$file->type</p></li>";
          if (strcmp($file->type, "image") != 0) {
            echo "<p><button type=\"button\" id=\"$file->path\" onclick=\"showFileFrame(this.id);\">Load latest revision</button></p>
              <p><a href=\"https://subversion.ews.illinois.edu/svn/fa12-cs242/reziapo1/$file->path\">View file</a></p>";
          }
          foreach ($file->versions as $version) {
            echo "
              <p><strong>VERSION Number: </strong>$version->version_number</p>
              <ul>
              <li><p><strong>Author: </strong>$version->author</p></li>
              <li><p><strong>Message: </strong>$version->message</p></li>
              <li><p><strong>Date Committed: </strong>$version->date</p></li>
              </ul><br>";
          }
          echo "</ul><br>";
        }
        echo "</ul></div>";

      ?>

      <!--
      <iframe src="" name="file_iframe" id="file_iframe" width="500" height="550" scrolling="yes"
      style="float:right; background-color:#FFFFFF; margin:10px; padding:10px;"></iframe>
      -->

      <pre id="file_frame" style="width:500px; height:550px; float:right; background-color:#FFFFFF; 
        margin:10px; padding:20px; border-radius:10px; border: 1px solid black; overflow:auto;">
         <pre class="brush: java"> 
          /*
            Choose a file to load here!
          */
          </pre>
      </pre>

        <script type="text/javascript">
          SyntaxHighlighter.all();
        </script>

      <!-- Commenting form under the iframe -->
      <form onsubmit="postCommentAJAX(this);" style="float:right; margin:10px; padding:10px;">
        <label for="name" 
          style="font:normal 16px Arial, Helvetica, sans-serif; color:#c1c1c1"> 
          Name: 
        </label> 
        <input type="text" name="name" id="name" pattern="[a-zA-z0-9_\s\.]{0,25}"> 
        <label for="name" 
          style="font:normal 12px Arial, Helvetica, sans-serif; color:#c1c1c1"> 
          (Up to 25 leters, numbers, underscores, etc) 
        </label> <br> <br>
        <label for="comment" 
          style="font:normal 16px Arial, Helvetica, sans-serif; color:#c1c1c1"> 
          Your Comment: 
        </label> <br>
        <textarea name="comment" id="comment" required style="width:500px; height:100px;" placeholder="Don't try anything funny"></textarea> <br>
        <input type="text" name="parent_post_id" value="-1" style="display:none;">
        <input type="submit" value="Submit">
      </form>

      <!-- Display existing comments for the project from MySQL database -->
      <?php

        // Prepare query statement, parameters are automatically quoted
        $project_comments_query = $connection->prepare(
          "SELECT * FROM $table
           WHERE parent_project = :title AND parent_comment_id = :parent_id
           ORDER BY date DESC
           LIMIT $comments_limit"
        );
        
        // Project title is specifically binded because $title is taken from the GET request,
        // potentially dangerous
        $project_comments_query->bindParam(":title", $project->title);
        $current_parent_id = -1;
        $base_color_hex = "801e14";

        echo "<div class=\"comments\" id=\"-1\">";
        display_all_comments_html($project_comments_query, $current_parent_id, $base_color_hex);
        echo "</div>";
        
        // Close the PDO connection
        $connection = NULL;

      ?>

		<div class="clr"></div>
  	</div>
  <div class="clr"></div>
  <div class="FBG">
    <div class="FBG_resize">
      <div class="left">
        <h2>About</h2>
          <p><strong>Timur Reziapov<br /></strong>
            Junior in Computer Science<br />
            CS 242<br />
          </p>
      </div>
      <div class="left">
        <h2>Contact</h2>
        <p><strong>reziapo1@illinois.edu</strong><br />
          University of Illinois at Urbana-Champaign<br />
        </p>
        <a href="#"><img src="images/rss_1.gif" alt="picture" width="18" height="16" border="0" /></a> <a href="#"><img src="images/rss_2.gif" alt="picture" width="18" height="16" border="0" /></a> <a href="#"><img src="images/rss_3.gif" alt="picture" width="18" height="16" border="0" /></a> </div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <div class="footer">
    <div class="footer_resize">
      <p class="right">(Blue) <a href="http://www.bluewebtemplates.com">Website Templates</a></p>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  </div>
</div>
</body>
</html>


