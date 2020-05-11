<?php

require_once 'connection.php';
$GLOBALS['CURRENT_PAGE'] = "Vehicles";


?>
<?php

//Data prep for Treemap
$sql1 = "SELECT MAKE, COUNT(DISTINCT VID) AS cVID 
    FROM vehicles 
    WHERE MAKE <> 'UNKNOWN' 
    GROUP BY MAKE 
    HAVING CVID > 100 
    ORDER BY CVID DESC 
    LIMIT 15";
$result_tree = $conn->query($sql1);
while ($row = mysqli_fetch_array($result_tree))
{
	$MAKE .= '"'. $row['MAKE'].'",';
	$cVID_tree .= ($row['cVID']).',';
	$parents .= ' ,';
}
$MAKE = "[" . rtrim($MAKE,","). "]";
$cVID_tree = "[" . rtrim($cVID_tree,","). "]";
$parents = "[" . rtrim($parents,","). "]";

$map_title = "Accidents in ".$weather." weather";
$result_tree -> free();


//Data prep for Bar
$sql_bar = "SELECT VEHICLE_DEFECT AS DEFECT, COUNT(DISTINCT VID) AS cVID".
    " FROM vehicles".
    " WHERE VEHICLE_DEFECT NOT IN ('NONE','UNKNOWN')".
    " AND VEHICLE_DEFECT IS NOT NULL".
    " GROUP BY DEFECT".
    " ORDER BY cVID".
    " LIMIT 10";

$result_bar = $conn->query($sql_bar);
while ($row = mysqli_fetch_array($result_bar))
{
	$defect .= '"'. $row['DEFECT'].'",';
	$CVID_bar .= ($row['cVID']).',';
}
$defect = "[" . rtrim($defect,","). "]";
$CVID_bar = "[" . rtrim($CVID_bar,","). "]";
$result_bar -> free();


//Data prep for Stack
$st_condition = array("\$500 OR LESS", "\$501 - \$1,500", "OVER \$1,500");
for ($i=0; $i<=2; $i++)
{
    $sql_st = "SELECT FIRST_CONTACT_POINT AS CONTACT, COUNT(DISTINCT VID) AS cVID".
    " FROM vehicles AS v LEFT JOIN accidents AS a".
    " ON v.RD_NO = a.RD_NO".
    " WHERE a.DAMAGE = '".$st_condition[$i]."'".
    " AND FIRST_CONTACT_POINT NOT IN ('UNKNOWN','NONE')".
    " AND FIRST_CONTACT_POINT IS NOT NULL".
    " GROUP BY CONTACT";
    $result_st = $conn->query($sql_st);
    $cont = "";
    $CVID_st = "";
    while ($row = mysqli_fetch_array($result_st))
    {
    	$cont .= '"'. $row['CONTACT'].'",';
    	$CVID_st .= ($row['cVID']).',';
    }
    $contact[$i] = "[" . rtrim($cont,","). "]";
    $CVID_stack[$i] = "[" . rtrim($CVID_st,","). "]";
}
$result_st -> free();
unset($cont);
unset($CVID_st);


//Data prep for Pie
$sql_pie = "SELECT VEHICLE_TYPE AS TYPE, COUNT(DISTINCT VID) AS cVID".
    " FROM vehicles".
    " WHERE VEHICLE_TYPE NOT IN ('UNKNOWN/NA')".
    " AND VEHICLE_TYPE IS NOT NULL".
    " GROUP BY TYPE".
    " ORDER BY cVID";
$result_pie = $conn->query($sql_pie);
while ($row = mysqli_fetch_array($result_pie))
{
	$type .= '"'. $row['TYPE'].'",';
	$CVID_pie .= ($row['cVID']).',';
}
$type = "[" . rtrim($type,","). "]";
$CVID_pie = "[" . rtrim($CVID_pie,","). "]";
$result_pie -> free();

$conn->close();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content="" />
    
    <title>Vehicles</title>
    
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    
    <noscript>
        <link rel="stylesheet" href="css/skel.css" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/style-xlarge.css" />
    </noscript>
    
    <style>
    
    </style>
</head>
    
<body>
    <!-- Header -->
    <?php include "header.php"; ?>

    <!-- Main -->
    <main id="main"  class="wrapper">
        <div class="container">
            <header class="major">
                <h1 >Oberservation with data of Vehicles</h1><br>
            </header>
            
	    </div>
        
    	<div class="container">

            <div id="treetest" class = "col-md-6 col-lg-6"></div>
            <div id="bartest" class = "col-md-6 col-lg-6"></div>
            <div id="stacktest" class = "col-md-6 col-lg-6"></div>
            <div id="pietest" class = "col-md-6 col-lg-6"></div>
            <br>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include "footer.php"; ?>
    

    <!-- Script -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <script>
    	//Plot Treemap
    	var datatree = [{
            type: "treemap",
            labels: <?php echo $MAKE; ?>,
            parents: <?php echo $parents; ?>,
            values: <?php echo $cVID_tree; ?>,
        }];
        var layout = {
            title: "Crashed Vehicles by Maker",
    // 		width: 500,
    		height: 500,
    	};
        Plotly.newPlot('treetest', datatree, layout);
        
        
        //Plot Bar Chart
        var databar = [{
            y: <?php echo $defect; ?>,
            x: <?php echo $CVID_bar; ?>,
            orientation: 'h',
            type: 'bar',
            }];
        var layout = {
            title: "Most Common Defects",
    // 		width: 500,
    		height: 500,
    	};
        Plotly.newPlot('bartest', databar, layout);
    	
    	
    	//Plot Percentage Stacked Chart
    	var datast = [
        	{x: <?php echo $contact[0]; ?>, y: <?php echo $CVID_stack[0]; ?>, name: "$500 OR LESS", stackgroup: 'one', groupnorm:'percent'},
        	{x: <?php echo $contact[1]; ?>, y: <?php echo $CVID_stack[1]; ?>, name: "$501 - $1,500", stackgroup: 'one'},
        	{x: <?php echo $contact[2]; ?>, y: <?php echo $CVID_stack[2]; ?>, name: "OVER $1,500", stackgroup: 'one'}
        ];
        var layout = {
            title: "First Hit Point vs Damage Value",
    // 		width: 500,
    		height: 500,
    	};
        Plotly.newPlot('stacktest', datast, layout);
    	
    	
    	//Plot Pie Chart
    	var datapie = [{
            values: <?php echo $CVID_pie; ?>,
            labels: <?php echo $type; ?>,
            type: 'pie',
            textinfo: 'none',
            hole: 0.4,
            
        }];
        var layout = {
            title: "Vehicle Type Distribution",
            height: 500,
        };
        Plotly.newPlot('pietest', datapie, layout);
    	
    </script>

</body>
</html>