<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RouteFinder - Accident Type Prediction</title>
    <!--meta name="viewport" content="initial-scale=1.0"-->
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
			<link rel="stylesheet" href="css/customStyle.css" />			
		</noscript>
	<link rel="stylesheet" type="text/css" href="routefinderassets/routefinder.css">

  </head>
  <body>
    <!--div class="InputParams">
  <h1 class="Title">Find Accident Statistics For Your Route</h1-->
  
  <!--div class="Input">
      <input id="start_addr" class="Input-text" type="text" name="start" placeholder="Starting Address"/>
      <label for="start_addr" class="Input-label">Starting Address</label>
    </div>
    <div class="Input">
      
      <input id="end_addr" class="Input-text" type="text" name="end" placeholder="Destination Address"/>
      <label for="end_addr" class="Input-label">Destination Address</label>
      </div>
      <button class="submit" onclick="javascript:saveRouteParams();"> Submit</button>
  </div-->
  
  	<!-- Header -->
	<?php include "header.php"; ?>
	<div id="map-container-google-1" class="z-depth-1-half map-container" style="height: 500px">
   <!--div class="container-fluid"-->
      <div style="display: none">
        <input id="origin-input" class="controls" type="text"
            placeholder="Enter an origin location">

        <input id="destination-input" class="controls" type="text"
            placeholder="Enter a destination location">

        <div id="mode-selector" class="controls">
          <input type="radio" name="type" id="changemode-walking" checked="checked">
          <label for="changemode-walking">Walking</label>

          <input type="radio" name="type" id="changemode-driving" checked="checked">
          <label for="changemode-driving">Driving</label>
        </div>
    </div>
  
    <div id="map"></div>
      </div>
    <script src="jquery/jquery.js"></script>
    <!--"position:absolute;bottom:0px;height:50%;width:100%;"-->
    <script src="routefinderassets/routefinder.js" type="text/javascript"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCnDD1rNUQA_hXanSo4W6vXye8zw3Z0M7U&libraries=places&callback=initMap"
    type="text/javascript"> import { initMap } from 'routefinderassets/routefinder.js'; initMap();</script>
        		<!-- Footer -->
	<?php include "footer.php"; ?>
  </body>
</html>