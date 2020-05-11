<?php

require 'connection.php';

if(!isset($_SESSION))
{
	session_start();
}

if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true)
{
    header("location: login.php");
    exit;
}

$GLOBALS['CURRENT_PAGE'] = "User Profile";
?>
<!DOCTYPE html>

<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="">
    <meta name="keywords" content="" />
    <title>Profile</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="routefinderassets/profile.css">
    <noscript>
      <link rel="stylesheet" href="css/skel.css" />
      <link rel="stylesheet" href="css/style.css" />
      <link rel="stylesheet" href="css/style-xlarge.css" />
    </noscript>
    </head>
  <body>

	<!-- Header -->
	<?php include "header.php"; ?>
	
  <main>
  <div class="container-fluid">
        <!-- side bar -->
        <?php include "sidebar.php"; ?>

      <div class="col-md-9 ml-sm-auto col-lg-10 px-4">

        <div class="row textformat">
          <div class="column">
              <h2>Reported Accidents</h2>
              <p class = 'psize' id = 'numAccidents'>0</p>
          </div>
          <div class="column">
             <h2 id = 'mostRecent'>Most Recent Post</h2>
             <p class = 'psize' id = 'strRecent'>Most Recent Street</p>
          </div>
        </div>
        <!--h2>Current Alerts</h2-->
        <div id="map"></div>
        
        <script src="jquery/jquery.js"></script>
        <!--"position:absolute;bottom:0px;height:50%;width:100%;"-->
        <script src="routefinderassets/profile.js" type="text/javascript"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCnDD1rNUQA_hXanSo4W6vXye8zw3Z0M7U&libraries=places&callback=initMap"
        type="text/javascript">initMap()</script>
        
      </div>
    </div>
  </main>

    <!--script>initMap()</script-->
	<!-- Footer -->
	<?php include "footer.php"; ?>
  </body>
</html>




