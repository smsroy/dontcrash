<?php
$GLOBALS['CURRENT_PAGE'] = "People";
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
	        <h1>Driver</h1><br>
	    </div>
	</header>

    <!-- Main -->
    <main id="main">
    	<div class="container">
  
    
    <?php
    
    //Data prep for Weather chart--
	/* Database connection settings */
	require 'connection.php';

	//for driver action bar chart
	$sqlac = "SELECT p.DRIVER_ACTION, count(*) as count FROM accidents as a, people as p".
	" where a.RD_NO = p.RD_NO and a.PRIM_CONTRIBUTORY_CAUSE not in (991,992,993,1002,1007,1010)".
	" and p.DRIVER_ACTION not in ('UNKNOWN','NONE')".
	" and p.DRIVER_ACTION is not NULL".
	" GROUP BY p.DRIVER_ACTION ORDER BY count";

    $resultac = $conn->query($sqlac);
	while ($row = mysqli_fetch_array($resultac))
        {
        	$countac = $countac . '"'. $row['count'].'",';
			$action = $action . '"'. $row['DRIVER_ACTION'].'",';
        }
        $countac = "[" . rtrim($countac,","). "]";
        $action = "[" . rtrim($action,","). "]";
        $resultac -> free();

    //for speed line chart
	$sqlsp = "SELECT POSTED_SPEED_LIMIT, count(*) as count FROM accidents WHERE PRIM_CONTRIBUTORY_CAUSE= 1013 and POSTED_SPEED_LIMIT BETWEEN 1 AND 200 group by POSTED_SPEED_LIMIT ORDER BY POSTED_SPEED_LIMIT ";
    $resultsp = $conn->query($sqlsp);
	while ($row = mysqli_fetch_array($resultsp))
        {
        	$countsp = $countsp . '"'. $row['count'].'",';
			$speed = $speed . '"'. $row['POSTED_SPEED_LIMIT'].'",';
        }
        $countsp = "[" . rtrim($countsp,","). "]";
        $speed = "[" . rtrim($speed,","). "]";
        $resultsp -> free();
		
	$sqlex = "SELECT a.POSTED_SPEED_LIMIT, count(*) as countp FROM accidents as a, people as p".
	" where a.RD_NO = p.RD_NO and p.DRIVER_ACTION = 'TOO FAST FOR CONDITIONS'".
	" AND POSTED_SPEED_LIMIT BETWEEN 1 AND 200".
	" GROUP BY a.POSTED_SPEED_LIMIT ORDER BY a.POSTED_SPEED_LIMIT";

    $resultex = $conn->query($sqlex);
	while ($row = mysqli_fetch_array($resultex))
        {
        	$countex = $countex . '"'. $row['countp'].'",';
			$sl = $sl . '"'. $row['POSTED_SPEED_LIMIT'].'",';
        }
        $countex = "[" . rtrim($countex,","). "]";
        $sl = "[" . rtrim($sl,","). "]";
        $resultex -> free();

    
    $conn->close();
	
	//for safty
	
    
    ?>
			<div class="row">
			<div class = "col-md-12 col-lg-12"><?php echo "" ?></div>
			<div id="maptest" class = "col-md-12 col-lg-12"></div>
			<div class = "col-md-12 col-lg-12"><?php echo "" ?></div>
			</div>
			<div class="row align-items-start align-self-center">
            <div id="weatherpie" class = "col-md-6 col-lg-6"></div>
			<div id="lighpie" class = "col-md-6 col-lg-6"></div>
            </div>
			<div class="row align-items-center align-self-center">
			<div class = "col-md-12 col-lg-12"><?php echo "" ?></div>
			</div>
			<div class="row align-self-center">
			<div id="driveraction" class = "col-md-6 col-lg-6"></div>
            <div id="speedscatter" class = "col-md-6 col-lg-6"></div>
			</div>
			
            </div>
        
		</div>
    </main>

    <script>

//This is for driver action 	
var data = [
{
	x: <?php echo $action; ?>,
	y: <?php echo $countac; ?>,
	type: 'bar'
	}
];
var layout = {
	xaxis:{visible:false,title:{text:'Driver Action'}},
	title:"Different driver action when crash caused by driver",
  height: 500
  //width: 500
};
		Plotly.newPlot('driveraction', data,layout);	

//This is for speed scatter

var data = [{
  x: <?php echo $speed; ?>,
  y: <?php echo $countsp; ?>,
  mode: 'lines+markers',
  name: 'total number of crash caused by exceeding speed limit',
  type: 'scatter',
  marker: { size: 10 }
},
{x: <?php echo $sl; ?>,
  y: <?php echo $countex; ?>,
  mode: 'lines+markers',
  name: 'total number of driver who drove too fast',
  type: 'scatter',
  marker: { size: 10 }
}];
var layout = {
	title:"Different speed limit",
	legend:{yanchor:"bottom"},
    height: 500
  //width: 500
};		

Plotly.newPlot('speedscatter', data,layout);	
</script>
    	
			
	<!-- Footer -->
		<?php include "footer.php"; ?>		
    	

</BODY>
</HTML>