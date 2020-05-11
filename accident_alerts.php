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

$GLOBALS['CURRENT_PAGE'] = "Accident Alerts";
$date = date('Y-m-d');

/*Query for the notification view*/
$username = $_SESSION["username"];
$sql = "SELECT a.RD_NO, COMMUNITY_AREA, CRASH_DATE, WEATHER, STREET_NO, STREET_NAME, DAMAGE, INJURIES_TOTAL".
    " FROM notification n, accidents_view a".
    " WHERE username='".$_SESSION["username"]."'".
	" AND n.RD_NO = a.RD_NO".
    " AND status = 0";
$result = $conn->query($sql);

if ($result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $record_no = $row["RD_NO"];
        $alert_table .= '<tr>'.
			'<td><a href="view_accident.php?editrdno='.$row["RD_NO"].'">'.$row["RD_NO"].'</a></td>'.
            '<td>'.$row["COMMUNITY_AREA"].'</td>'.
            '<td>'.$row["CRASH_DATE"].'</td>'.
            '<td>'.$row["WEATHER"].'</td>'.
            '<td>'.$row["STREET_NO"].' '.$row["STREET_NAME"].'</td>'.
            '<td>'.$row["DAMAGE"].'</td>'.
            '<td>'.$row["INJURIES_TOTAL"].'</td>'.
            '<td><a href="dismiss.php?RD_NO='.$record_no.'&username='.$username.'">Dismiss</a></td>'.
            '</tr>';
    }
}
else
{
    $alert_table = '<tr><td  colspan="10" align="center">No new notifications</td></tr>';
}

$result->free();
$conn->close();

?>



<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="">
    <meta name="keywords" content="" />
    
    <title>User Profile</title>
    
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
        .container-fluid{
        min-height: calc(100vh-11em);
        }
        #footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        }
    </style>
</head>

<body>
    <!-- header -->
    <?php include "header.php"; ?>
<main>
    <div class="container-fluid">
        <!-- side bar -->
        <?php include "sidebar.php"; ?>

        <!-- main -->
        <div class="col-md-9 ml-sm-auto col-lg-10 px-4">
    	    <h2>New Alerts</h2>
    		<div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Record No</th>
    						<th scope="col">Community</th>
    						<th scope="col">Crash Date</th>
    						<th scope="col">Weather</th>
    						<th scope="col">Street Adress</th>
    						<th scope="col">Damage</th>
    						<th scope="col">Injuries_Total</th>
                            <th scope="col">Operation</th>
                        </tr>
                    </thead>
    
                    <tbody>
                    <?php echo $alert_table; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <div/>
</main>
    <!-- Footer -->
	<?php include "footer.php"; ?>
</body>

</html>
