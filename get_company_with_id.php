<?php 

  $LOG = true;

  if( !empty($_POST)  ) 
  {
    $companyId = $_POST['companyId'];
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

  if( $LOG )
  {
    $log_string = "Date: ".date('m-d-Y, g:i A', time())."\ncompanyId: ".$companyId."\n\n\n";
    //echo $log_string;
    $log_success = file_put_contents( "/home/scott/gnt_logs/get_company_with_id.log", $log_string, FILE_APPEND );
//    var_dump( $log_success );
  }

  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_errno($db))
  {
    die("Error connecting to MySQL database:".mysqli_connect_error()."\n");
  }
  
  $sql3 = "SELECT company_name,file_url from company WHERE company_id='".$companyId."'\n";
  if( $q = $db->query($sql3) ) 
  {
    while($row = $q->fetch_assoc())
    {
      $json_array["company_name"] = $row["company_name"];
      $json_array["file_url"] = $row["file_url"];
     //array_push($json_array["companyInfo"], $companyInfo);
    }
  }
  else
  {
    $json_array["error"] = "select_unsynced_fail";
    echo json_encode($json_array);
    exit();
  }
  $db->close();
  $json_array["error"] = "none";
  echo json_encode($json_array);
 //var_dump($json_array); 
 //var_dump($json_companys); 
?>
