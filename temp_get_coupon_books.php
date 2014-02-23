<?php 

  
  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_error($db))
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    fwrite( $log_file, json_encode($json_array) );
    exit();
  }

  $charity_name = 'Pittsburgh Charity X';
  //$charity_name = $obj->charity_name;
  echo "charity_name = ".$charity_name."\n";
    
  $sql = "SELECT DISTINCT coupon_book_id,cost,value from coupon_book,charity WHERE  coupon_book.charity_id=charity.charity_id AND charity.name='".$charity_name."' ORDER BY value LIMIT 5\n";
  echo "Executing select statement:  ".$sql;

  if( !($q = $db->query($sql)) )
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    fwrite( $log_file, json_encode($json_array) );
    exit();
    //die("ERROR executing the query: ".$sql."\n");
  }
  else
  {
    $json_array["coupon_books"] = array();
    while( $row = $q->fetch_assoc() )
    {
      $coupon_books = array();
      $coupon_books["coupon_book_id"] = $row["coupon_book_id"];
      $coupon_books["cost"] = $row["cost"];
      $coupon_books["value"] = $row["value"];

      array_push($json_array["coupon_books"], $coupon_books);
    }

  }

  $db->close();

  $json_array["error"] = "none";
  echo json_encode($json_array);

?>
