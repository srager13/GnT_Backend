<?php 

  $LOG = true; 

  $json_array = array();
  // get the input json object
  if( !empty($_POST) )
  { 
    $json = $_POST['json']; 
    $obj = json_decode($json);
  }
  else
  {
    $json = $_SERVER['HTTP_JSON'];
    $obj = json_decode($json);
  }
  
  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_error($db))
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    if( $LOG )
    {
      $log_success = file_put_contents( "/home/scott/gnt_logs/update_used_coupons.log", "Database Connect Error\n", FILE_APPEND );
    }
    exit();
  }

  $username = $obj->username;
  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\nUsername: ".$username."\n";
    $log_success = file_put_contents( "/home/scott/gnt_logs/update_used_coupons.log", $log_string, FILE_APPEND );
  }
 
  foreach( $obj->coupons as $coupon )
  {
    //echo "coupon: ".$coupon->date_used."\n";
    if( $LOG )
    {
      $log_string = "coupon date_used = ".$coupon->date_used."\ncoupon event_id = ".$coupon->event_id."\n";
      //echo $log_string;
      $log_success = file_put_contents( "/home/scott/gnt_logs/update_used_coupons.log", $log_string, FILE_APPEND );
    }

    $sql = "UPDATE coupon_event SET date_used='".$coupon->date_used."' WHERE event_id='".$coupon->event_id."'\n";

    if( !($q = $db->query($sql)) )
    {
      $json_array["error"] = "db_error";
      echo json_encode($json_array);
      exit();
      //die("ERROR executing the query: ".$sql."\n");
    }
  }

  $db->close();

  $json_array["error"] = "none";
  echo json_encode($json_array);

  if( $LOG )
  {
    $log_string = "json response = ".json_encode($json_array)."\n\n\n";
    $log_success = file_put_contents( "/home/scott/gnt_logs/update_used_coupons.log", $log_string, FILE_APPEND );
  }

?>
