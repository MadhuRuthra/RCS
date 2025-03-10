<?php
$servername = "localhost";
$username = "admin";
$password = "Admin@123";
$dbname = "whatsapp_messenger";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "UPDATE whatsapp_config SET sent_count = 0 WHERE whatspp_config_status = 'Y'";

if ($conn->query($sql) === TRUE) {
  site_log_generate(
    "Auto Increment page : To update the record On Record updated successfully on " .
    date("Y-m-d H:i:s")
  );
  echo "Record updated successfully";
} else {
  site_log_generate(
    "Auto Increment page : Error updating record: [$conn->error] on " .
    date("Y-m-d H:i:s")
  );
  echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
