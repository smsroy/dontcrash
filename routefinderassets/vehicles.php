<!DOCTYPE html>
<!--
	Transit by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Vehicles</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
        <!--script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script -->	
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
	</head>
	<body>

		<!-- Header -->
			<header id="header">
				<h1><a href="index.php">Accident Analysis</a></h1>
				<nav id="nav">
					<ul>
						<li><a href="index.html">Home</a></li>
						<li><a href="generic.html">Generic</a></li>
						<li><a href="elements.html">Elements</a></li>
						<li><a href="#" class="button special">Sign Up</a></li>
					</ul>
				</nav>
			</header>

		<!-- Main -->
			<section id="main" class="wrapper">
				<div class="container">

					<header class="major">
						<h2>Vehicle Details</h2>
						<p>Details of the vehicles involved in accidents</p>
					</header>
<?php
require 'connection.php';
$sql = "SELECT distinct model FROM vehicles ORDER BY model ASC";
$result = $conn->query($sql);
echo '<div class="content">';
echo '<table border="1" cellspacing="2" cellpadding="2" class="content"> <tr>';
echo '<td align="left"><b>Please select the car model to get details on vehicles involved in accidents: </b></td>';
if ($result->num_rows > 0) {
	// output data of each row
	echo '<td td align="left"><select id="model" name="model" onchange="getCauses(this.value)"><option selected>-select model-</option>'; 
    /*for ($x = 0; $x <= 20; $x++) {
        $row = $result->fetch_assoc();
        $model = $row["model"];
	    echo '<option>'.$model.'</option>';
    }*/
	while($row = $result->fetch_assoc()) {
		$model = $row["model"];
	    echo '<option>'.$model.'</option>';
	}
	$result->free();
	echo '</select></td>'; 	
}
echo '</table> </tr>';

?>

<!--<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>-->
<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
<script type="text/javascript"> 
	function getCauses(selectedVal){ 
      $.ajax({ 
        method: "GET", 
		dataType: "json",        
        url: "getvehicleinfo.php",
		data: {model : selectedVal},
		success: function(JSONObject) {
        var string='<table border="1" cellspacing="2"  cellspacing="2" cellpadding="2" cellpadding="2" cellpadding="2" cellpadding="2" cellpadding="2" cellpadding="2" class="content"><tr><td> <b> <font face="Arial">VID</font> </b></td><td> <b> <font face="Arial">Crash Date</font> </b></td> <td> <b> <font face="Arial">Maneuver</font> </b></td><td> <b> <font face="Arial">First Contact Point</font> </b></td> <td> <b> <font face="Arial">Vehicle Type</font> </b></td> <td> <b> <font face="Arial">Vehicle Use</font> </b></td> <td> <b> <font face="Arial">Unit Type</font> </b></td> <td> <b> <font face="Arial">Model Year</font> </b></td></tr>';			
		  var vehicleHTML = "";

		  // Loop through Object and create maneuverHTML
		  for (var key in JSONObject) {
			if (JSONObject.hasOwnProperty(key)) {
			  vehicleHTML += "<tr>";
				vehicleHTML += "<td>" + JSONObject[key]["vid"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["crash_date"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["maneuver"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["first_contact_point"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["vehicle_type"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["vehicle_use"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["unit_type"] + "</td>";
				vehicleHTML += "<td>" + JSONObject[key]["vehicle_year"] + "</td>";
			  vehicleHTML += "</tr>";
			}
		  }

		 string += vehicleHTML;
		 string += '</table>'; 
          $("#records").html(string); 
		}
		});	
    }; 
</script> 	
<div id="records">
records will show here
</div> 				

				</div>
			</section>
		<!-- Footer -->
			<footer id="footer">
				<div class="container">
					<section class="links">
						<div class="row">
							<section class="3u 6u(medium) 12u$(small)">
								<h3>Lorem ipsum dolor</h3>
								<ul class="unstyled">
									<li><a href="#">Lorem ipsum dolor sit</a></li>
									<li><a href="#">Nesciunt itaque, alias possimus</a></li>
									<li><a href="#">Optio rerum beatae autem</a></li>
									<li><a href="#">Nostrum nemo dolorum facilis</a></li>
									<li><a href="#">Quo fugit dolor totam</a></li>
								</ul>
							</section>
							<section class="3u 6u$(medium) 12u$(small)">
								<h3>Culpa quia, nesciunt</h3>
								<ul class="unstyled">
									<li><a href="#">Lorem ipsum dolor sit</a></li>
									<li><a href="#">Reiciendis dicta laboriosam enim</a></li>
									<li><a href="#">Corporis, non aut rerum</a></li>
									<li><a href="#">Laboriosam nulla voluptas, harum</a></li>
									<li><a href="#">Facere eligendi, inventore dolor</a></li>
								</ul>
							</section>
							<section class="3u 6u(medium) 12u$(small)">
								<h3>Neque, dolore, facere</h3>
								<ul class="unstyled">
									<li><a href="#">Lorem ipsum dolor sit</a></li>
									<li><a href="#">Distinctio, inventore quidem nesciunt</a></li>
									<li><a href="#">Explicabo inventore itaque autem</a></li>
									<li><a href="#">Aperiam harum, sint quibusdam</a></li>
									<li><a href="#">Labore excepturi assumenda</a></li>
								</ul>
							</section>
							<section class="3u$ 6u$(medium) 12u$(small)">
								<h3>Illum, tempori, saepe</h3>
								<ul class="unstyled">
									<li><a href="#">Lorem ipsum dolor sit</a></li>
									<li><a href="#">Recusandae, culpa necessita nam</a></li>
									<li><a href="#">Cupiditate, debitis adipisci blandi</a></li>
									<li><a href="#">Tempore nam, enim quia</a></li>
									<li><a href="#">Explicabo molestiae dolor labore</a></li>
								</ul>
							</section>
						</div>
					</section>
					<div class="row">
						<div class="8u 12u$(medium)">
							<ul class="copyright">
								<li>&copy; Untitled. All rights reserved.</li>
								<li>Design: <a href="http://templated.co">TEMPLATED</a></li>
								<li>Images: <a href="http://unsplash.com">Unsplash</a></li>
							</ul>
						</div>
						<div class="4u$ 12u$(medium)">
							<ul class="icons">
								<li>
									<a class="icon rounded fa-facebook"><span class="label">Facebook</span></a>
								</li>
								<li>
									<a class="icon rounded fa-twitter"><span class="label">Twitter</span></a>
								</li>
								<li>
									<a class="icon rounded fa-google-plus"><span class="label">Google+</span></a>
								</li>
								<li>
									<a class="icon rounded fa-linkedin"><span class="label">LinkedIn</span></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</footer>

	</body>
</html>			

