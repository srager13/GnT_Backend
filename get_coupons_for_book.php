<?php 

 $LOG = true;

  if( !empty($_POST) )
  { 
    $couponBookId = $_POST['couponBookId'];
    $username = $_POST['username'];
  }
  else
  {
    //echo "POST was empty";
    $json = $_SERVER['HTTP_JSON'];
    $obj = json_decode($json);
//    echo var_dump($json);
    $json_array = array();

    $couponBookId = $obj->couponBookId;
    $username = $obj->username;
  }
  
  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\nusername: ".$username."\ncouponBookId: ".$couponBookId."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/get_coupons_for_book.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }
  
  // get customer id that matches username
  $sql = "SELECT customer_id from customer WHERE name='".$username."'\n";
  if( $q = $db->query($sql) )
  {
    if( $q->num_rows != 1 )
    {
      $json_array["error"] = "find_customer_failed";
      echo json_encode($json_array);
      exit();
    }
    else
    {
      $r = $q->fetch_assoc(); 
      $customerId = $r["customer_id"];
//      echo  "customerId = ".$customerId."\n";
    }
  }
  else
  {
    $json_array["error"] = "find_customer_failed";
    echo json_encode($json_array);
    exit();
  }

  // first get all coupons in the coupon book being bought
  $sql1 = "SELECT coupon_id from coupon_book WHERE coupon_book_id='".$couponBookId."'\n";
  
  $coupon_ids = array();
  if( $q1 = $db->query($sql1) ) 
  {
    $i = 0;
    while($row1 = $q1->fetch_assoc())
    {
      $coupon_ids[$i] = $row1["coupon_id"];
      $i+=1;
    }
  }
  else
  {
    $json_array["error"] = "find_coupons_failed";
    echo json_encode($json_array);
    exit();
  }

  $date = date('Y-m-d');
  // now insert these into coupon_event
  foreach( $coupon_ids as $coup_id )
  {
    $sql2 = $db->prepare("INSERT INTO coupon_event VALUES( '".$customerId."','".$coup_id."','NULL','".$date."',NULL,'0')");
    if( !$sql2->execute() )
    {
      $json_array["error"] = "insert_fail";
      echo json_encode($json_array);
      exit();
    }
  }

  // TODO::Need to fix for multiple locations
  $sql3 = "SELECT * from coupon_event,coupon,company,locations WHERE coupon_event.customer_id='".$customerId."' AND synced_to_phone='0' AND coupon_event.coupon_id=coupon.coupon_id AND coupon.company_id=company.company_id AND company.company_id=locations.company_id AND coupon_event.date_used IS NULL \n";
  //$sql3 = "SELECT * from coupon_event,coupon,company WHERE coupon_event.customer_id='".$customerId."' AND synced_to_phone='0' AND coupon_event.coupon_id=coupon.coupon_id AND coupon.company_id=company.company_id AND coupon_event.date_used IS NULL \n";
 $json_coupons = array(); 
  if( $q = $db->query($sql3) ) 
  {

    //echo "<table border='1'>";

//    $json_array["customer_id"] = $couponBookId;
    $json_array["coupons"] = array();
    while($row = $q->fetch_assoc())
    {
      $coupons = array();
      $coupons["event_id"] = $row["event_id"];
      $coupons["company_id"] = $row["company_id"];
      $coupons["location_id"] = $row["location_id"];
      $coupons["coupon_id"] = $row["coupon_id"];

      array_push($json_array["coupons"], $coupons);
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
