<?php 

  $json_array = array();

  $json = $_SERVER['HTTP_JSON'];
  $obj = json_decode($json);
  $couponBookId = $obj->couponBookId;
/*
  if( $_GET ) 
  {
    // used with http as in http://mygiveandtake.com/get_coupons_for_book.php?coupon_book_id=1&other=value
    $couponBookId = $_GET['couponBookId'];
  }
  else
  {
    $json_array["error"] = "bad_args";
    echo json_encode($json_array);
    exit();
  }
*/

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    exit();
  }

  // get all coupons in the coupon book being bought
  $sql1 = "SELECT * from coupon_book,coupon,company WHERE coupon_book_id='".$couponBookId."' AND coupon_book.coupon_id=coupon.coupon_id AND company.company_id=coupon.company_id\n";

  if( $q1 = $db->query($sql1) ) 
  {
    $json_array["coupons"] = array();
    while($row = $q1->fetch_assoc())
    {
      $coupons = array();
//      $coupons["event_id"] = $row["event_id"];
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
    $json_array["error"] = "get_coupons_failed";
    echo json_encode($json_array);
    exit();
  }

  $db->close();
  $json_array["error"] = "none";
  echo json_encode($json_array);
 //var_dump($json_array); 
 //var_dump($json_coupons); 
?>
