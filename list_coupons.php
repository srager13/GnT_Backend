<?php 

  if( $_GET ) 
  {
    // used with http as in http://mygiveandtake.com/list_coupons.php?customerId=1&other=value
    $customerId = $_GET['customerId'];
  }
  else
  {
    $customerId = 1;
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }

  $sql = "SELECT * from coupon_event WHERE customer_id='".$customerId."' AND date_used IS NULL\n";

//  echo $sql;

  $json_array = array();
  $json_coupons = array(); 
  if( $q = $db->query($sql) ) 
  {

    //echo "<table border='1'>";

    $json_array["customer_id"] = $customerId;
    $json_array["coupons"] = array();
    $filename = "./customer_data_files/customer_".$customerId."_coupons.json";
    $json_file = fopen( $filename, 'w' );
    if( !$json_file )
    {
      print_r(error_get_last());
      die("ERROR opening file ".$filename."\n");
    }
    else
    {
      while($row = $q->fetch_assoc())
      {
        $coupons = array();
        $coupons["coupon_id"] = $row["coupon_id"];
        $coupons["event_id"] = $row["event_id"];
        $coupons["date_bought"] = $row["date_bought"];
        $coupons["date_used"] = $row["date_used"];

        array_push($json_array["coupons"], $coupons);
      }
        $json_coupons[] = json_encode($json_array);
        fwrite( $json_file, json_encode($json_array) );
        echo json_encode($json_array);
    }
    //echo "</table>";
  }
  $db->close();
 //var_dump($json_array); 
 //var_dump($json_coupons); 
?>
