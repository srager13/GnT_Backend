<?php 

  $json_array = array();

  $couponBookId = 1;// $_GET['couponBookId'];
  $username = 'srager13';
  //$customerId = 1; //$_GET['customerId'];

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
      echo  "customerId = ".$customerId."\n";
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
      //echo "coupon_ids[".$i."] = ".$coupon_ids[$i]."\n";
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

  echo "date = ".$date."\n";
  // now insert these into coupon_event
  foreach( $coupon_ids as $coup_id )
  {
    echo "coup_id = ".$coup_id."\n";
    $sql2 = $db->prepare("INSERT INTO coupon_event VALUES( '".$customerId."','".$coup_id."','NULL','".$date."',NULL,'0')");
    if( !$sql2->execute() )
    {
      $json_array["error"] = "insert_fail";
      echo json_encode($json_array);
      exit();
    }
  }

  $sql3 = "SELECT * from coupon_event,coupon,company WHERE coupon_event.customer_id='".$customerId."' AND synced_to_phone='0' AND coupon_event.coupon_id=coupon.coupon_id AND coupon.company_id=company.company_id AND coupon_event.date_used IS NULL \n";
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
      $coupons["coupon_name"] = $row["company_name"];
      $coupons["coupon_details"] = $row["coupon_details"];
      $coupons["exp_date"] = $row["exp_date"];
      $coupons["file_url"] = $row["file_url"];
//      $coupons["latitude"] = $row["latitude"];
//      $coupons["longitude"] = $row["longitude"];

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
