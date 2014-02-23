<?php 
 $LOG = false;

  if( !empty($_POST) )
  { 
//    $json = $_POST['json']; 
  
//    $obj = json_decode($json);
    $seller_id = $_POST['seller_id']; 
//    echo $seller_id;
  }
  else
  {
    $json_array = array();
    $json_array["success"] = false;
    echo json_encode($json_array);
    exit;
  }
  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\nUsername: ".$seller_id."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/validate_seller_id.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }

  $sql = "SELECT * from sellers WHERE username='".$seller_id."'\n";

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
