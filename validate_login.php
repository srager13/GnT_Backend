<?php 
 $LOG = true;

  if( !empty($_POST) )
  { 
    $user = $_POST['username'];
    $pwHash = $_POST['pwHash'];
  }
  else
  {
    //echo "POST was empty";
    $json_array = array();    
    $json_array["success"] = false;
    echo json_encode($json_array);
    exit;
  }
  
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
