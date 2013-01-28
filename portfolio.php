<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>CS 242 Portfolio</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

  <script type="text/javascript" src="js/jquery.js"></script>

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
  <div class="header_blog">
    <div class="header_blog_resize">
      <h2>My Work</h2>
      <div class="menu2">
        <ul>
          <li><a href="#" class="active">All</a></li>
        </ul>
        <div class="clr"></div>
      </div>
      <div class="clr"></div>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
  <div class="body">
    <div class="body_resize">
      
      <!-- Loop through all the projects and display an html block of code for each -->
      <?php
        include "scripts/svn_parsing_script.php";
        foreach ($projects_hash as $title=>$project) {
          echo "<div class=\"right_port\" style=\"display:none;\">
                  <p><a href=\"project.php?name=$title\">
                    <h2><font color=\"FFFFFF\">$title</font></h2>
                    <img src=\"images/file_icon.jpg\" alt=\"picture\" width=\"100\" height=\"100\" /><br>
                  </a></p>
                </div>";   
          }

      ?>

      <script>
        $(".right_port").slideToggle("slow");
      </script>

      <div class="clr"></div>
    </div>
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