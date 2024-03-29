<?php 
  $LOG = true;
  if( $LOG )
  {
    file_put_contents( "/home/scott/gnt_logs/register_new_user.log", "Date: ".date('m-d-Y, g:i A', time())."\n", FILE_APPEND);
  }
  if( !empty($_POST) )
  { 
    $seller_id = $_POST['seller_id']; 
    $username = $_POST['username'];
    $password = $_POST['password'];
    $date = date('Y-m-d');
    $firstname = $_POST['firstName'];
    $lastname = $_POST['lastName'];
    $email = $_POST['email'];
    $phonenum = "xxxxxxxxxx";
//    $phonenum = $_GET['phonenum'];
    $null_val = 'NULL';
  }
  else
  {
    $json_array = array();
    $json_array["error"] = "invalid_args";
    echo json_encode($json_array);
    exit;
  }
      
  if( $LOG )
  {
    $log_string = "First name: ".$firstname."\nLast name: ".$lastname."\nUsername: ".$username."\nPassword: ".$password."\nEmail: ".$email."\nPhone number: ".$phonenum."\n";
    $log_success = file_put_contents( "/home/scott/gnt_logs/register_new_user.log", $log_string, FILE_APPEND );
  }
  
  $db = new mysqli("localhost", "scott", "scott", "gntdb");
  if(mysqli_connect_error($db))
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    if( $LOG )
    {
      $log_success = file_put_contents( "/home/scott/gnt_logs/register_new_user.log", "Database Error\n", FILE_APPEND );
    }
    exit();
  }

  $sql = "SELECT * from customer where name='".$username."'\n";

  if( !($q = $db->query($sql)) )
  {
    $json_array["error"] = "db_error";
    echo json_encode($json_array);
    exit();
    //die("ERROR executing the query: ".$sql."\n");
  }
  if( $q->num_rows != 0 )
  {
    $json_array["error"] = "username_taken";
    echo json_encode($json_array);
    exit();
  }
  else
  {  
    // customer_id, name, pw-hash, date_join, first-name, last-name, email, phone-num 
    $sql2 = $db->prepare("INSERT INTO customer VALUES( 'NULL', '".$username."','". $password."','". $date."','". $firstname."','". $lastname."','". $email."','". $phonenum."' )");
    //echo "INSERT INTO customer VALUES( 'NULL', $username, $password, $date, $firstname, $lastname, $email, $phonenum )";
    //$sql2 = $db->prepare("INSERT INTO customer VALUES(?,?,?,?,?,?,?,?)");

    //$sql2->bind_param( 'ssssssss', $null_val, $username, $password, $date, $firstname, $lastname, $email, $phonenum );

    //echo "Executing insert statement:  ".$sql2;

    if( !$sql2->execute() )
    {
      die("Error inserting customer into MySQL database:"."$sql2->error()"."\n");
    }
    else
    {
      $json_array["error"] = "none";
      echo json_encode($json_array);
    }

  }
  $db->close();

  if($LOG)
  {
    $log_success = file_put_contents( "/home/scott/gnt_logs/register_new_user.log", "\n\n", FILE_APPEND );
  }
?>
