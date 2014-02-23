<?php 
  $LOG = false;

  if( !empty($_POST) )
  { 
    $seller_id = $_POST['seller_id']; 
    $cash_code = $_POST['cash_code'];
    $book_cost = intval($_POST['book_cost']);
   
    //echo "seller_id = ".$seller_id."\n";
    //echo "cash_code = ".$cash_code."\n";
    //echo "book_cost = ".$book_cost."\n";
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
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\nsellerId: ".$seller_id."\ncashCode = ".$cash_code."\nbook_cost = ".$book_cost."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/validate_cash_code.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }

  $sql = "SELECT * from sellers,cash_codes WHERE sellers.username='".$seller_id."' AND sellers.sellers_id=cash_codes.sellers_id AND cash_codes.cash_codes='".$cash_code."' AND book_cost IS NULL\n";

  //echo $sql;

  $json_array = array();
  if( $q = $db->query($sql) ) 
  {
    //echo "returned a value\n";
    if( $q->num_rows == 0 )
    {
      $json_array["success"] = false;
    }
    else
    {
      $row = $q->fetch_assoc();
      $seller_id_num = $row["sellers_id"];
      //echo "seller_id_num = ".$seller_id_num."\n";
      $newDate = date('Y-m-d');
      //echo $newDate;
      //echo "\n";
      $sql2 = "UPDATE cash_codes SET book_cost='".$book_cost."', date_used='".$newDate."' WHERE sellers_id='".$seller_id_num."' AND cash_codes='".$cash_code."'\n";

      //echo $sql2;
      if( mysqli_query($db, $sql2) )
      {
        $json_array["success"] = true;
      }
      else
      {
        $json_array["success"] = false;
      }
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
