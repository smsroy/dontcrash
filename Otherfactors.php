<?php
$GLOBALS['CURRENT_PAGE'] = "Other Factor";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Chart</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/init.js"></script>
        <!--script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script -->	
		<link href="https://api.mapbox.com/mapbox-gl-js/v1.10.0/mapbox-gl.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<noscript>
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
	</head>
	<body>

		<!-- Header -->
			<?php include "header.php"; ?>
    

<BODY>
    
	<header class="major">
	    <div class="container">
	        <h1>Other factors</h1><br>
	    </div>
	</header>

    <!-- Main -->
    <main id="main">
    	<div class="container">
  
    
    <?php
    
	/* Database connection settings */
	require 'connection.php';

	
	//for weather pie
	$sqlw = "SELECT w.weather, count(*) as count FROM accidents as a,weather as w where a.WID = w. WID and w.weather <> 'UNKNOWN'".
	" and a.PRIM_CONTRIBUTORY_CAUSE = 992 group by w.weather";
    $resultw = $conn->query($sqlw);

	//loop through the returned data
	while ($row = mysqli_fetch_array($resultw)) {

		$countw = $countw . '"'. $row['count'].'",';
		$weather = $weather . '"'. $row['weather'].'",';	
	}
	$countw = "[" . rtrim($countw,","). "]";
	$weather = "[" . rtrim($weather,","). "]";
	$resultw -> free();

	//Data prep for Light chart--
	$sqli = "SELECT count(*) as count FROM accidents group by LID order by LID";
    $resulti = $conn->query($sqli);

	//loop through the returned data
	while ($row = mysqli_fetch_array($resulti)) {

		$countl = $countl . '"'. $row['count'].'",';
		
	}
	$countl = "[" . rtrim($countl,","). "]";
	$resulti -> free();
	
	//for day of week
	$sqld = "SELECT CRASH_DAY_OF_WEEK, count(*) as countd from accidents where CRASH_DAY_OF_WEEK is not null group by CRASH_DAY_OF_WEEK order by CRASH_DAY_OF_WEEK";
	$resultd = $conn->query($sqld);
	while ($row = mysqli_fetch_array($resultd)) {

		$countd = $countd . '"'. $row['countd'].'",';
		$day =  $day . '"'. $row['CRASH_DAY_OF_WEEK'].'",';
		
	}
	$countd = "[" . rtrim($countd,","). "]";
	$day = "[" . rtrim($day,","). "]";
	$resultd -> free();
	
	//for month
	$sqlm = "SELECT CRASH_MONTH, count(*) as countm from accidents where CRASH_MONTH is not null group by CRASH_MONTH order by CRASH_MONTH";
	$resultm = $conn->query($sqlm);
	while ($row = mysqli_fetch_array($resultm)) {

		$countm = $countm . '"'. $row['countm'].'",';
		$month =  $month . '"'. $row['CRASH_MONTH'].'",';
		
	}
	$countm = "[" . rtrim($countm,","). "]";
	$month = "[" . rtrim($month,","). "]";
	$resultm -> free();
	
    
    ?>
			<div class="row">
			<div class = "col-md-12 col-lg-12"><?php echo "" ?></div>
			<div id="maptest" class = "col-md-12 col-lg-12"></div>
			<div class = "col-md-12 col-lg-12"><?php echo "" ?></div>
			</div>
			<div class="row">
            <div id="weatherpie" class = "col-md-6 col-lg-6"></div>
			<div id="lighpie" class = "col-md-6 col-lg-6"></div>
            </div>
			<div class="row align-items-center align-self-center">
			</div>
			<div class="row align-self-center">
			<div id="dayofweek" class = "col-md-6 col-lg-6"></div>
            <div id="month" class = "col-md-6 col-lg-6"></div>
			</div>
			

        
		</div>
    </main>

    <script>
	
//This is Weather pie
    	
var data = [{
  values: <?php echo $countw; ?>,
  labels: <?php echo $weather; ?>,
  type: 'pie'
}];

var layout = {
	title:"The weather condition when primary contibutory cause is weather",
  height: 500
  //width: 500
};

Plotly.newPlot('weatherpie', data, layout);
			
			
//This is light pie
var data = [{
  values: <?php echo $countl; ?>,
  labels: ['DARKNESS, LIGHTED ROAD','DAYLIGHT','DAWN','UNKNOWN','DUSK','DARKNESS'],
  type: 'pie'
}];

var layout = {
	title:"The light condition",
  height: 500
  //width: 500
};

Plotly.newPlot('lighpie', data, layout);	

//This is Day of week
var data = [{
  y: <?php echo $countd; ?>,
  x: <?php echo $day; ?>,
  type: 'bar'
}];

var layout = {
	title:"The day of week",
	xaxis: {title: 'Day of week'},
    yaxis: {title: 'Total Crash'},
  height: 500
  //width: 500
};

Plotly.newPlot('dayofweek', data, layout);

//This is Month
var data = [{
  y: <?php echo $countm; ?>,
  x: <?php echo $month; ?>,
  type: 'bar'
}];

var layout = {
	title:"Month",
	xaxis: {title: 'Month'},
    yaxis: {title: 'Total Crash'},
  height: 500
  //width: 500
};

Plotly.newPlot('month', data, layout);

</script>
    	
			
	<!-- Footer -->
		<?php include "footer.php"; ?>		
    	

</BODY>
</HTML>