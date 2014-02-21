<?php 
  $LOG = true; 

  $json_array = array();
  if( !empty($_POST)  ) 
  {
    $locationId = $_POST['locationId'];
    $companyId = $_POST['companyId'];
  }
  else
  {
    $json = $_SERVER['HTTP_JSON'];
    $obj = json_decode($json);
    $locationId = $obj->locationId;
    $companyId = $obj->companyId;
  }
  
  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."locationId: ".$locationId."\ncompanyId: ".$companyId."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/get_location_with_id.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }
  

  $sql3 = "SELECT * from locations WHERE location_id='".$locationId."' AND company_id='".$companyId."'\n";
  if( $q = $db->query($sql3) ) 
  {

    while($row = $q->fetch_assoc())
    {
      $json_array["company_id"] = $row["company_id"];
      $json_array["addr_line_1"] = $row["address_line_1"];
      $json_array["addr_line_2"] = $row["address_line_2"];
      $json_array["latitude"] = $row["latitude"];
      $json_array["longitude"] = $row["longitude"];
    }
  }
  else
  {
    $json_array["error"] = "select_unsynced_fail";
    echo json_encode($json_array);
    exit();
  }
  $db->close();
  $json_array["error"] = "none";
  echo json_encode($json_array);
 //var_dump($json_array); 
 //var_dump($json_locations); 
?>
