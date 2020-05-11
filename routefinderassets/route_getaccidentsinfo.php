<?php
require '../connection.php';
$start_lats = $_POST['start_lats'];
$start_longs = $_POST['start_longs'];
$end_lats = $_POST['end_lats'];
$end_longs = $_POST['end_longs'];
$streets = $_POST['streets'];

$result_array = array();
$each_result_arr = array(); 

for($i = 0; $i < count($streets); $i++){
    $curr_start_lat = $start_lats[$i];
    $curr_start_long = $start_longs[$i];
    $curr_end_lat = $end_lats[$i];
    $curr_end_long = $end_longs[$i];
    $curr_street = $streets[$i];
    
$sql = "SELECT latitude,longitude,street_name,severity FROM dontcrash_db.accident_severity WHERE (CAST(latitude AS DOUBLE(7,3)) BETWEEN '".$curr_start_lat."' AND '".$curr_end_lat."' AND CAST(longitude AS DOUBLE(8,3)) BETWEEN '".$curr_start_long."' AND '".$curr_end_long."')" ;

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        array_push($each_result_arr, $row);
	    }
	    $result->free();
	    array_push($result_array, $each_result_arr);
    }
    else {
        array_push($result_array,'');
    }
}
/* send a JSON encded array to client */
header('Content-type: application/json');
echo json_encode($result_array);

$conn->close();
?>	
