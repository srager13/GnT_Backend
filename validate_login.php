<?php 
 $LOG = true;

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
  $user = $obj->user;
  $pwHash = $obj->pwHash;
  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\nUsername: ".$user."\nPassword: ".$pwHash."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/validate_login.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }

  $sql = "SELECT * from customer WHERE name='".$user."' AND password_hash='".$pwHash."'\n";

  //echo $sql;

  $json_array = array();
  if( $q = $db->query($sql) ) 
  {
//    echo "returned a value";
    if( $q->num_rows == 0 )
    {
      $json_array["success"] = false;
    }
    else
    {
      $json_array["success"] = true;
    }
  }
  else
  {
//    echo "returned false";
    $json_array["success"] = false;
  }
  $db->close();

  echo json_encode($json_array);
 //var_dump($json_array); 
?>
