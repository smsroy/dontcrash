<?php
$GLOBALS['CURRENT_PAGE'] = "Accidents By People Type";

if(!isset($_SESSION))
{
	session_start();
}
?>
<!DOCTYPE html>
<!--
	Transit by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Peoples Page</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>			
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
	</head>
	<body>

		<!-- Header -->
		<?php include "header.php"; ?>

		<!-- Main -->
			<section id="main" class="wrapper">
				<div class="container">
				<div align="right"><a href="accidents_search.php">Return to Accident Search</a></div>
				<br/>

<?php
require 'connection.php';
$sql = "SELECT distinct PERSON_TYPE FROM people";
$result = $conn->query($sql);
echo '<div class="content">';
echo '<table border="1" cellspacing="2" cellpadding="2" class="content"> <tr>';
echo '<td align="left"><b>Please select the type of person to get accident information: </b></td>';
if ($result->num_rows > 0) {
	// output data of each row
	echo '<td td align="left"><select id="PERSON_TYPE" name="PERSON_TYPE" onchange="getpeopleList(this.value)"><option selected>-select type of person-</option>'; 
 
	while($row = $result->fetch_assoc()) {
		$PERSON_TYPE = $row["PERSON_TYPE"];
	    echo '<option>'.$PERSON_TYPE.'</option>';
	}
	$result->free();
	echo '</select></td>'; 	
}
echo '</table> </tr>';

?>

<!--<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>-->
<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
<script type="text/javascript"> 
	function getpeopleList(selectedVal){ 
      $.ajax({ 
        method: "GET", 
		dataType: "json",        
        url: "getpeopleList.php",
		data: {PERSON_TYPE : selectedVal},
		success: function(JSONObject) {
        var string='<table border="1" cellspacing="2" cellpadding="2" cellpadding="2" cellpadding="2" cellpadding="2" cellpadding="2" cellpadding="2" class="content"><tr><td> <b> <font face="Arial">Record No</font> </b></td> <td> <b> <font face="Arial">Sex</font> </b></td><td> <b> <font face="Arial">Age</font> </b></td> <td> <b> <font face="Arial">Safety Equipment</font> </b></td> <td> <b> <font face="Arial">Action</font> </b></td> <td> <b> <font face="Arial">Vision</font> </b></td></tr>';			
		  var peopleHTML = "";

		  // Loop through Object and create peopleHTML
		  var i = 0;
		  for (var key in JSONObject) {
			if (JSONObject.hasOwnProperty(key)) {
				i = i + 1;
				var age = JSONObject[key]["AGE"];
				if (age == null) { age = '-'};
				var sex = JSONObject[key]["SEX"];
				if (sex == null) { sex = '-'};	
				var safety = JSONObject[key]["SAFETY_EQUIPMENT"];
				if (safety == null) { safety = '-'};	
				var action = JSONObject[key]["DRIVER_ACTION"];
				if (action == null) { action = '-'};
				var vision =  JSONObject[key]["DRIVER_VISION"];
				if (vision == null) { vision = '-'};
			  peopleHTML += "<tr>";
				peopleHTML += "<td> <a href='view_accident.php?editrdno=" + JSONObject[key]["RD_NO"] + "'> " + JSONObject[key]["RD_NO"] + "</a></td>";
				peopleHTML += "<td>" + sex + "</td>";
				peopleHTML += "<td>" + age + "</td>";
				peopleHTML += "<td>" + safety + "</td>";
				peopleHTML += "<td>" + action + "</td>";
				peopleHTML += "<td>" + vision + "</td>";
				
			  peopleHTML += "</tr>";
			}
		  }

		 string += peopleHTML;
		 string += '</table>';
		 if (i<7) {
			string = "<div style='height:400px;'>" + string + "</div>"
		 }		 
          $("#records").html(string); 
		}
		});	
    }; 
</script> 	
<div id="records">
<div style="height:400px;"></div>
</div> 				

				</div>
			</section>
		<!-- Footer -->
		<?php include "footer.php"; ?>

	</body>
</html>	