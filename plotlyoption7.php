<?php
$GLOBALS['CURRENT_PAGE'] = "Map";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Map of the Chicago Crash</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
        <!--script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script -->	
		<link href="https://api.mapbox.com/mapbox-gl-js/v1.10.0/mapbox-gl.css" rel="stylesheet" />
		<style>
		body { margin: 0; padding: 0; }
		#map { width: 100%; height: 80%}
		</style>
		<noscript>
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
	</head>
	<style>
	.mapcontainer {
		height:100%;
	}
    </style>
	<body>

		<!-- Header -->
			<?php include "header.php"; ?>

		<!-- Main -->
			<section id="main" class="wrapper">
				<div class="container">
			
				<header class="major">
					<h2>Distribution of Chicago Crash</h2>

					
<?php
	/* Database connection settings */
	require 'connection.php';
	$zoom="";
	$center="";
	$info="";
	$opacity=0.7;
	$size=8;
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$sql = "SELECT ac.LATITUDE,ac.LONGITUDE,ac.RD_NO,lc.community_side, c.SEVERITY, c.CAUSE_DESC FROM accidents ac, location lc, people p, contributory_cause c where 1=1".
		" AND ac.location_id = lc.location_id AND p.RD_NO = ac.RD_NO AND c.CID = ac.PRIM_CONTRIBUTORY_CAUSE".
		" AND ac.location_id <> 78 AND ac.LONGITUDE IS NOT NULL AND ac.LATITUDE IS NOT NULL AND c.SEVERITY IS NOT NULL";
		$addWhere = "";
		$location="";
		$i = 0;
		$checkedc = 0;
		if(!empty($_POST['location'])){
			$checkedc++;
			$info = "Crash in ";
			$selectedlocationCount = count($_POST['location']);
			while ($i < $selectedlocationCount) {
				if ($i == 0)
				{$addWhere = $addWhere.' AND (lc.community_area = "'.$_POST['location'][$i].'"';}
				else
				{$addWhere = $addWhere.' OR lc.community_area = "'.$_POST['location'][$i].'"';}
				if ($i == $selectedlocationCount-1) {
					$addWhere = $addWhere.")";
					if($i == 0){$info = $info.$_POST['location'][$i];}
					else {$info = $info.'and '.$_POST['location'][$i];}
				}
				else if ($i == $selectedlocationCount-2){$info = $info.$_POST['location'][$i].' ';}
				else
				{
					if($i%6 == 0&&$i!=0){$info = $info."<br>";}
					$info = $info.$_POST['location'][$i].', ';
				}
				$i ++;
			}
			//$info = rtrim($info,",")；
			$sql = $sql.$addWhere;
			//echo "l   ".$sql;
			$result = $conn->query($sql);
			$communitiesside = [];
			$lats = [];
			$longs = []; 
			while ($row = mysqli_fetch_array($result)) {
				$lat =  $row['LATITUDE'];
				$lon =  $row['LONGITUDE'];
				array_push($communitiesside, $row['community_side']);
				array_push($lats, $lat);
				array_push($longs, $lon);
			}
			//setting zoom and center
			$communitiesside = array_unique($communitiesside);
			if ($selectedlocationCount<3 && count($communitiesside)==1)
			{$zoom = "13";}
			else if (count($communitiesside)==1)
			{$zoom = "12";}
			else{$zoom = "10";}
			
			//echo "max".max($lats);
			//echo min($lats);
			$lats = array_diff($lats, array(null));
			$longs = array_diff($longs, array(null));
			$lat = (max($lats)+ min($lats))/2;
			$lon = (max($longs)+ min($longs))/2;
			$center = "lon:".$lon.",lat:".$lat;
			$result -> free();
		}
		if($checkedc == 0)
		{
			$info = "Crash in all communities";
			$center = "lon: -87.623177, lat:41.858132";
			$zoom = "10";
		}
		if(!empty($_POST['nonv'])){
			$addWhere = "";
			$i = 0;
			$checkedc++;
			$info = $info."<br />";
			$selectedptypeCount = count($_POST['nonv']);
			while ($i < $selectedptypeCount) {
				if ($i == 0)
				{$addWhere = $addWhere.' AND (p.PERSON_TYPE = "'.$_POST['nonv'][$i].'"';}
				else
				{$addWhere = $addWhere.' OR p.PERSON_TYPE = "'.$_POST['nonv'][$i].'"';}
				if ($i == $selectedptypeCount-1) {
					$addWhere = $addWhere.")";
					if($i == 0){$info = $info.$_POST['nonv'][$i]." involed";}
					else {$info = $info.'and '.$_POST['nonv'][$i]." involed";}
				}
				else if ($i == $selectedptypeCount-2){$info = $info.$_POST['nonv'][$i].' ';}
				else
				{
					$info = $info.$_POST['nonv'][$i].', ';
				}
				$i ++;
			}
			$sql = $sql.$addWhere;
			//echo $sql;
		}
		if(!empty($_POST['cause'])){
			$addWhere = "";
			$i = 0;
			$checkedc++;
			$info = $info."<br />Primary cause:";
			$selectedcauseCount = count($_POST['cause']);
			while ($i < $selectedcauseCount) {
				if ($i == 0)
				{$addWhere = $addWhere.' AND (c.CAUSE_DESC = "'.$_POST['cause'][$i].'"';}
				else
				{$addWhere = $addWhere.' OR c.CAUSE_DESC = "'.$_POST['cause'][$i].'"';}
				if ($i == $selectedcauseCount-1) {
					$addWhere = $addWhere.")";
					if($i == 0){$info = $info.$_POST['cause'][$i];}
					else {$info = $info.'and '.$_POST['cause'][$i];}
				}
				else if ($i == $selectedcauseCount-2){$info = $info.$_POST['cause'][$i].' ';}
				else
				{
					if($i%3 == 0){$info = $info."<br>";}
					$info = $info.$_POST['cause'][$i].', ';
				}
				$i ++;
			}
			$sql = $sql.$addWhere;
			//echo $sql;
		}
		if($checkedc == 0)
		{
		$info = "Crash in all communities";
		//query to get data from the table
		$sql = "SELECT ac.LATITUDE,ac.LONGITUDE,ac.RD_NO,c.SEVERITY FROM accidents ac,contributory_cause c where c.CID = ac.PRIM_CONTRIBUTORY_CAUSE AND CRASH_DATE > '2019-01-01' ORDER BY LATITUDE,LONGITUDE";
		$count = 0;
		$lattemp = "";
		$longtemp = "";
		$result = $conn->query($sql);
		$SER = "";
		$i = 0;
		//loop through the returned data
		while ($row = mysqli_fetch_array($result)) {
			$lat = $row['LATITUDE'];
			$long = $row['LONGITUDE'];
			if($lat == $lattemp&&$long == $longtemp)
			{
				$count++;
			}
			else
			{
				if($SER == 'LOW' && $i!=0)
				{
					$COUNT1 =  '"count: '. $count.'",'.$COUNT1 ;
				}
				if($SER == 'MEDIUM' && $i!=0)
				{
					$COUNT2 =  '"count: '. $count.'",'.$COUNT2 ;
				}
				if($SER == 'CRITICAL' && $i!=0)
				{
					$COUNT3 =  '"count: '. $count.'",'.$COUNT3 ;
				}
				
				$lattemp = $lat;
				$longtemp = $long;
				$SER = $row['SEVERITY'];
			
				if($SER == 'LOW')
				{
				$LATITUDE1 = '"'. $lat.'",'. $LATITUDE1;
				$LONGITUDE1 =  '"'. $long.'",'.$LONGITUDE1 ;
				//$RD_NO1 = '"'. $row['RD_NO'].'",'.$RD_NO1 ;
				}
				if($SER == 'MEDIUM')
				{
				$LATITUDE2 = '"'. $lat.'",'. $LATITUDE2;
				$LONGITUDE2 =  '"'. $long.'",'.$LONGITUDE2 ;
				//$RD_NO2 = '"'. $row['RD_NO'].'",'.$RD_NO2 ;
				}
				if($SER == 'CRITICAL')
				{
				$LATITUDE3 = '"'. $lat.'",'. $LATITUDE3;
				$LONGITUDE3 =  '"'. $long.'",'.$LONGITUDE3 ;
				//$RD_NO3 = '"'. $row['RD_NO'].'",'.$RD_NO3 ;
				}
				$count = 1;
			}
			$i++;
		}
		if($SER == 'LOW')
		{
			$COUNT1 =  '"count: '. $count.'",'.$COUNT1 ;
		}
		if($SER == 'MEDIUM')
		{
			$COUNT2 =  '"count: '. $count.'",'.$COUNT2 ;
		}
		if($SER == 'CRITICAL')
		{
			$COUNT3 =  '"count: '. $count.'",'.$COUNT3 ;
		}
		$LATITUDE1 = "[" . rtrim($LATITUDE1,","). "]";
		$LONGITUDE1 = "[" . rtrim($LONGITUDE1,","). "]";
		$COUNT1 = "[" . rtrim($COUNT1,","). "]";
		$LATITUDE2 = "[" . rtrim($LATITUDE2,","). "]";
		$LONGITUDE2 = "[" . rtrim($LONGITUDE2,","). "]";
		$COUNT2 = "[" . rtrim($COUNT2,","). "]";
		$LATITUDE3 = "[" . rtrim($LATITUDE3,","). "]";
		$LONGITUDE3 = "[" . rtrim($LONGITUDE3,","). "]";
		$COUNT3 = "[" . rtrim($COUNT3,","). "]";
		$center = "lon: -87.623177, lat:41.87812";
		$zoom = "10";
		}
		else
		{
		//echo "f   ".$sql;
		$result = $conn->query($sql);
		$i=0;
		$count = 0;
		$lattemp = "";
		$longtemp = "";
		$SER = "";
		while ($row = mysqli_fetch_array($result)) {
			$lat = $row['LATITUDE'];
			$long = $row['LONGITUDE'];
			if($lat == $lattemp&&$long == $longtemp)
			{
				$count++;
			}
			else
			{
				if($SER == 'LOW' && $i!=0)
				{
					$COUNT1 =  '"count: '. $count.'",'.$COUNT1 ;
				}
				if($SER == 'MEDIUM' && $i!=0)
				{
					$COUNT2 =  '"count: '. $count.'",'.$COUNT2 ;
				}
				if($SER == 'CRITICAL' && $i!=0)
				{
					$COUNT3 =  '"count: '. $count.'",'.$COUNT3 ;
				}
				
				$lattemp = $lat;
				$longtemp = $long;
				$SER = $row['SEVERITY'];
			
				if($SER == 'LOW')
				{
				$LATITUDE1 = '"'. $lat.'",'. $LATITUDE1;
				$LONGITUDE1 =  '"'. $long.'",'.$LONGITUDE1 ;
				//$RD_NO1 = '"'. $row['RD_NO'].'",'.$RD_NO1 ;
				}
				if($SER == 'MEDIUM')
				{
				$LATITUDE2 = '"'. $lat.'",'. $LATITUDE2;
				$LONGITUDE2 =  '"'. $long.'",'.$LONGITUDE2 ;
				//$RD_NO2 = '"'. $row['RD_NO'].'",'.$RD_NO2 ;
				}
				if($SER == 'CRITICAL')
				{
				$LATITUDE3 = '"'. $lat.'",'. $LATITUDE3;
				$LONGITUDE3 =  '"'. $long.'",'.$LONGITUDE3 ;
				//$RD_NO3 = '"'. $row['RD_NO'].'",'.$RD_NO3 ;
				}
				$count = 1;
			}
			$i++;
		}
		if($SER == 'LOW')
		{
			$COUNT1 =  '"count: '. $count.'",'.$COUNT1 ;
		}
		if($SER == 'MEDIUM')
		{
			$COUNT2 =  '"count: '. $count.'",'.$COUNT2 ;
		}
		if($SER == 'CRITICAL')
		{
			$COUNT3 =  '"count: '. $count.'",'.$COUNT3 ;
		}
		if ($i<50){$opacity=0.9;$size=12;}
		$info = $info."<br>Total result is ".$i;
		$LATITUDE1 = "[" . rtrim($LATITUDE1,","). "]";
		$LONGITUDE1 = "[" . rtrim($LONGITUDE1,","). "]";
		$COUNT1 = "[" . rtrim($COUNT1,","). "]";
		$LATITUDE2 = "[" . rtrim($LATITUDE2,","). "]";
		$LONGITUDE2 = "[" . rtrim($LONGITUDE2,","). "]";
		$COUNT2 = "[" . rtrim($COUNT2,","). "]";
		$LATITUDE3 = "[" . rtrim($LATITUDE3,","). "]";
		$LONGITUDE3 = "[" . rtrim($LONGITUDE3,","). "]";
		$COUNT3 = "[" . rtrim($COUNT3,","). "]";
		}
		
	}

	else{
		$info = "Crash in all communities";
		//query to get data from the table
		$sql = "SELECT ac.LATITUDE,ac.LONGITUDE,ac.RD_NO,c.SEVERITY FROM accidents ac,contributory_cause c where c.CID = ac.PRIM_CONTRIBUTORY_CAUSE AND CRASH_DATE > '2019-01-01' ORDER BY LATITUDE,LONGITUDE";
		$count = 0;
		$lattemp = "";
		$longtemp = "";
		$result = $conn->query($sql);
		$SER = "";
		$i = 0;
		//loop through the returned data
		while ($row = mysqli_fetch_array($result)) {
			$lat = $row['LATITUDE'];
			$long = $row['LONGITUDE'];
			if($lat == $lattemp&&$long == $longtemp)
			{
				$count++;
			}
			else
			{
				if($SER == 'LOW' && $i!=0)
				{
					$COUNT1 =  '"count: '. $count.'",'.$COUNT1 ;
				}
				if($SER == 'MEDIUM' && $i!=0)
				{
					$COUNT2 =  '"count: '. $count.'",'.$COUNT2 ;
				}
				if($SER == 'CRITICAL' && $i!=0)
				{
					$COUNT3 =  '"count: '. $count.'",'.$COUNT3 ;
				}
				
				$lattemp = $lat;
				$longtemp = $long;
				$SER = $row['SEVERITY'];
			
				if($SER == 'LOW')
				{
				$LATITUDE1 = '"'. $lat.'",'. $LATITUDE1;
				$LONGITUDE1 =  '"'. $long.'",'.$LONGITUDE1 ;
				//$RD_NO1 = '"'. $row['RD_NO'].'",'.$RD_NO1 ;
				}
				if($SER == 'MEDIUM')
				{
				$LATITUDE2 = '"'. $lat.'",'. $LATITUDE2;
				$LONGITUDE2 =  '"'. $long.'",'.$LONGITUDE2 ;
				//$RD_NO2 = '"'. $row['RD_NO'].'",'.$RD_NO2 ;
				}
				if($SER == 'CRITICAL')
				{
				$LATITUDE3 = '"'. $lat.'",'. $LATITUDE3;
				$LONGITUDE3 =  '"'. $long.'",'.$LONGITUDE3 ;
				//$RD_NO3 = '"'. $row['RD_NO'].'",'.$RD_NO3 ;
				}
				$count = 1;
			}
			$i++;
		}
		if($SER == 'LOW')
		{
			$COUNT1 =  '"count: '. $count.'",'.$COUNT1 ;
		}
		if($SER == 'MEDIUM')
		{
			$COUNT2 =  '"count: '. $count.'",'.$COUNT2 ;
		}
		if($SER == 'CRITICAL')
		{
			$COUNT3 =  '"count: '. $count.'",'.$COUNT3 ;
		}
		$LATITUDE1 = "[" . rtrim($LATITUDE1,","). "]";
		$LONGITUDE1 = "[" . rtrim($LONGITUDE1,","). "]";
		$COUNT1 = "[" . rtrim($COUNT1,","). "]";
		$LATITUDE2 = "[" . rtrim($LATITUDE2,","). "]";
		$LONGITUDE2 = "[" . rtrim($LONGITUDE2,","). "]";
		$COUNT2 = "[" . rtrim($COUNT2,","). "]";
		$LATITUDE3 = "[" . rtrim($LATITUDE3,","). "]";
		$LONGITUDE3 = "[" . rtrim($LONGITUDE3,","). "]";
		$COUNT3 = "[" . rtrim($COUNT3,","). "]";
		$center = "lon: -87.623177, lat:41.87812";
		$zoom = "10";
	}

?>

			</header>

<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/v1.9.0/mapbox-gl.js"></script>

<div id="maptest" class="mapcontainer"></div>

			
			
<?php
//Get location
require 'connection.php';
$sql = "SELECT location_id, community_area, community_side FROM location WHERE location_id <> 78 Order By 3";
$result = $conn->query($sql);
$location_select = "";

if ($result->num_rows > 0) {
	// output data of each row
	$location_select = ''; 
	$i=0;
	$temp = 'Central';
	while($row = $result->fetch_assoc()) {
		if($i==0){
			$location_select=$location_select.'<table class="table" id="getlocation"><tbody><tr><td colspan="9"><b>Central:    <input type="checkbox" name="Central" id="Central" onClick="toggle(this);" /><label for="Central">Select All</label></td></tr><tr>';
			$i++;}
		$lid = $row["location_id"];
		$area = $row["community_area"];
		$community = $row["community_side"];
		if($community!=$temp){
			if(($i-1)%5 != 0)
			{
				$col = 9-2*(($i-1)%5);
				$location_select=$location_select."<td colspan=".$col.">&nbsp;</td>";
			}
			$location_select=$location_select.'</tr><tr><td colspan="9"><b>'.$community.':    <input type="checkbox" name="'.$community.'" id="'.$community.'" onClick="toggle(this);" /><label for="'.$community.'">Select All</label></td></tr><tr>';
			$temp=$community;
			$i=1;
		}
		$location_select = $location_select.'<td><input type="checkbox" class="'.$community.'" name="location[]" value="'.$area.'" id="'.$lid.'"><label for="'.$lid.'">'.$area.'</label></td>';
		if($i%5 == 0){
			$location_select=$location_select."</tr><tr>";
		}
		else
		{
			$location_select=$location_select."<td>&nbsp;</td>";
		}
		$i++;
	}
	$location_select=$location_select."</tr></tbody></table>";
	$result->free();
}

//Get causes
$sql = "SELECT DISTINCT CAUSE_DESC FROM contributory_cause";
$result = $conn->query($sql);
$cause_select = "";
if ($result->num_rows > 0) {
	// output data of each row
	$cause_select = '<table class="table"><tbody><tr>'; 
	$i=1;
	while($row = $result->fetch_assoc()) {
		$cause = $row["CAUSE_DESC"];
		$cause_select = $cause_select.'<td><input type="checkbox" name="cause[]" value="'.$cause.'" id="'.$cause.'"><label for="'.$cause.'">'.$cause.'</label></td>';
		if($i%2 == 0){
			$cause_select=$cause_select."</tr><tr>";
		}
		else
		{
			$cause_select=$cause_select."<td>&nbsp;</td>";
		}
		$i++;
	}
	$cause_select=$cause_select."</tr></tbody></table>";
	$result->free();
}

 
?>
        	
					
				<form action="" method="post">  
<?php				
    if(!empty($_GET['error'])){
		echo '<p color="red">'.$_GET['error'].'</p>';
    }
?>					
				
					 <h4>select community:</h4>
					  <?php echo $location_select; ?>
					  <br />
					  <br />
					  <h4>check the people type involed crash:</h4>
					  <table>
					  <tbody>
						<tr>
							<td><input type="checkbox" name="nonv[]" value="DRIVER" id="DRIVER"><label for="DRIVER">Driver</label></td>
							<td><input type="checkbox" name="nonv[]" value="BICYCLE" id="BICYCLE"><label for="BICYCLE">Bicycle</label></td>
							<td><input type="checkbox" name="nonv[]" value="PEDESTRIAN" id="PEDESTRIAN"><label for="PEDESTRIAN">Pedestrian</label></td>
							<td><input type="checkbox" name="nonv[]" value="NON-MOTOR VEHICLE" id="NON-MOTOR VEHICLE"><label for="NON-MOTOR VEHICLE">other non-motor vehicle</label></td>
						</tr>
						</tbody>
						</table>
						
					<h4>select primary cause:</h4>
					  <?php echo $cause_select; ?>
					  
				<header class="major">					
					<div class="form-group">
						<input type="submit" class="button medium" value="Update Map">
						<input type="reset" class="button medium" value="Clear All">
					</div>					
				</header>				
				</form>

				</div>
			</section>
		
		<!-- Footer -->
		<?php include "footer.php"; ?>

	</body>
	<script>
var trace1 = {
		type:'scattermapbox',
		lon: <?php echo $LONGITUDE1; ?>,
		lat: <?php echo $LATITUDE1; ?>,
		text:  <?php echo $COUNT1; ?>,
		mode: 'markers',
		name: 'LOW',
		marker: {
			size: <?php echo $size; ?>,
			opacity: <?php echo $opacity; ?>,
			symbol: 'circle',
			color: 'rgb(255, 212, 82)'
		}
	};
	
var trace2 = {
		type:'scattermapbox',
		lon: <?php echo $LONGITUDE2; ?>,
		lat: <?php echo $LATITUDE2; ?>,
		text:  <?php echo $COUNT2; ?>,
		mode: 'markers',
		name: 'MEDIUM',
		marker: {
			size: <?php echo $size; ?>,
			opacity: <?php echo $opacity; ?>,
			symbol: 'circle',
			color: 'rgb(245, 144, 144)'
		}
	};
	
var trace3 = {
		type:'scattermapbox',
		lon: <?php echo $LONGITUDE3; ?>,
		lat: <?php echo $LATITUDE3; ?>,
		text:  <?php echo $COUNT3; ?>,
		mode: 'markers',
		name: 'CRITICAL',
		marker: {
			size: <?php echo $size; ?>,
			opacity: <?php echo $opacity; ?>,
			symbol: 'circle',
			color: 'rgb(111, 219, 103)'
		}
	};
var data = [trace3, trace1, trace2];
var layout = {
		title:'<?php echo $info; ?>',
		legend:{
			title:{text:"Severity:"}
		},
		mapbox:{
			center: {<?php echo $center; ?>},
			zoom:<?php echo $zoom; ?>
		},
		height: 900
	};
var config = {responsive: true, mapboxAccessToken: "pk.eyJ1Ijoic2hlaWxhMDEyMWoiLCJhIjoiY2s4a3hycWdmMDNpcTNlbDMzeHBxaGthcSJ9.MVWI71kfFxtL_hfw3Z1Ntw"};
Plotly.newPlot('maptest', data, layout,config);

</script>

<script language="JavaScript">
	//for check all box
	function toggle(source) {
		var community = source.name;
		var t = document.getElementById("getlocation");
		var checkboxes = t.getElementsByClassName(community);
		for(var i=0, n=checkboxes.length;i<n;i++) 
		{
			checkboxes[i].checked = source.checked;
		}
	}
</script>

</html>