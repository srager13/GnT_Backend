<?php 

  $LOG = true;

  $json_array = array();
  if( !empty($_POST) ) 
  {
    // used with http as in http://mygiveandtake.com/get_coupon_with_id.php?coupon_id=1
    $json = $_POST['json'];
    $obj = json_decode($json);
  }
  else
  {
    $json = $_SERVER['HTTP_JSON'];
    $obj = json_decode($json);
  }

  $couponId = $obj->couponId;
  
  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\ncouponId: ".$couponId."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/get_coupon_with_id.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }
  

  // TODO::Need to fix for multiple locations
  //$sql3 = "SELECT * from coupon_event,coupon,company,locations WHERE coupon_event.customer_id='".$customerId."' AND synced_to_phone='0' AND coupon_event.coupon_id=coupon.coupon_id AND coupon.company_id=company.company_id AND company.company_id=locations.company_id AND coupon_event.date_used IS NULL \n";
  $sql3 = "SELECT company_id,coupon_details,exp_date,favorite from coupon WHERE coupon_id='".$couponId."'\n";
 $json_coupons = array(); 
  if( $q = $db->query($sql3) ) 
  {

    //echo "<table border='1'>";

    //$json_array["couponInfo"] = array();
    while($row = $q->fetch_assoc())
    {
      //$couponInfo = array();
//      $couponInfo["company_id"] = $row["company_id"];
 //     $couponInfo["exp_date"] = $row["exp_date"];
  //    $couponInfo["favorite"] = $row["favorite"];
      $json_array["company_id"] = $row["company_id"];
      $json_array["coupon_details"] = $row["coupon_details"];
      $json_array["exp_date"] = $row["exp_date"];
      $json_array["favorite"] = $row["favorite"];

      //array_push($json_array["couponInfo"], $couponInfo);
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
 //var_dump($json_coupons); 
?>
