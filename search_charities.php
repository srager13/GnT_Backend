<?php 

  $json_array = array();

//  $json = $_SERVER['HTTP_JSON'];
  //$obj = json_decode($json);
  //$searchString = $obj->searchString;
  $searchString = "Girl Scout";

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    exit();
  }

  // get up to 10 charities close to the input string searching on
  $sql1 = "SELECT * from charity WHERE name LIKE '%".$searchString."%' LIMIT 0,10\n";

  //echo $sql1;
  if( $q1 = $db->query($sql1) ) 
  {
    $json_array["charities"] = array();
    while($row = $q1->fetch_assoc())
    {
      //$charities = array();
      //$charities["name"] = $row["name"];
 
   //   echo $row["name"];

      array_push($json_array["charities"], $row["name"]);
    }
  }
  else
  {
    $json_array["error"] = "searching_db_failed";
    echo json_encode($json_array);
    exit();
  }

  $db->close();
  $json_array["error"] = "none";
  echo json_encode($json_array);
 //var_dump($json_array); 
 //var_dump($json_coupons); 
?>
