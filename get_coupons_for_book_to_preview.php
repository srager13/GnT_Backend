<?php 

  if( !empty($_POST) )
  {
    $couponBookId = $_POST['couponBookId'];
  }  
  else
  {
    //echo "POST was empty";
    $json_array = array();    
    $json_array["error"] = "invalid_args";
    echo json_encode($json_array);
    exit;
  }

  $json_array = array();

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
      $coupons["coupon_name"] = $row["company_name"];
      $coupons["coupon_details"] = $row["coupon_details"];
      $coupons["exp_date"] = $row["exp_date"];
      $coupons["file_url"] = $row["file_url"];

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
