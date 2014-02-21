<?php 

  // get the input json object
  if( !empty($_POST) )
  { 
    $json = $_POST['json']; 
  
  //$json = '{"username":"srager13","coupons":[{"event_id":"7","was_favorite":"true","date_used":66}]}';
    $obj = json_decode($json);
    //echo var_dump($obj);
  }
  else
  {
    //echo "POST was empty";
    $json = $_SERVER['HTTP_JSON'];
    $obj = json_decode($json);
//    echo var_dump($json);
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_error($db))
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    exit();
  }

  $username = $obj->username;
  // echo "username = ".$username."\n";
  foreach( $obj->coupons as $coupon )
  {
    //echo "coupon: ".$coupon->date_used."\n";
    $sql = "UPDATE coupon_event SET synced_to_phone='1' WHERE event_id='".$coupon->event_id."'\n";
    //echo "Executing select statement:  ".$sql;

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

?>
