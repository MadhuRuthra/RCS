<?php
/*
This page is used to setup the Configuration file
and controls the whole site.
It will create the common log file function to monitor the process
of this site, including Frontend, backend.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 03-Jul-2023
*/

set_time_limit(0); // Assign unlimited time limit
// Live server Credentials
/* $servername = "localhost";
$username = "sms_portal";
$password = "CM_sms@123";
$dbname = "whatsapp_report"; */

$servername = "localhost";
$username = "rcs_messenger";
$password = "RCS-Ms@YJ_626101";
$dbname = "rcs";

// Assign the Setup confifuration for this site
$site_title = "RCS";
$site_url   = "http://139.167.144.37/rcs/";
$api_url    = "http://localhost:10056";
$template_get_url = "http://localhost:10056/create_template";
$full_pathurl = "/var/www/html/rcs/";
$site_socket_url    ="http://yeejai.in:10056/";

// Whatsapp Connection - Start
$monthly_allowed_qty    = 800;
$whatsapp_mobno         = "8610110464"; // 8610110464 - Shanthini 2 Mobile No
$whatsapp_wabaid        = "100175206060494"; // 8610110464 - Shanthini 2 Mobile No
$whatsapp_phone_id      = "103741605696935"; // 8610110464 - Shanthini 2 Mobile No
$whatsapp_bearer_token  = 'EAAlaTtm1XV0BANV3Lc8mA5kEO4BqWsCKudO6lNWGcVyl6O6wIK7mJqXCtPtpyjhO36ZA1eEGLra4Q21T7aEWns1VxqwcOFVR4BtQsxShdMB9zBIPjN4gaj3KTz5ZBHnEtO3WVkC26UdLpM75vIZBIZCw8eCRVus4NcZC7FZC3NhBFqpF3ntmGh13ZAZBdUcVtwJ9Mcout3A1ZCwZDZD';
$whatsapp_tmpl_url      = "https://graph.facebook.com/v15.0/".$whatsapp_wabaid;             // Collect the Template URL
$whatsapp_tmplsend_url  = "https://graph.facebook.com/v15.0/".$whatsapp_phone_id;           // Send Whatsapp message URL
$whatsapp_tmplate_url   = "https://graph.facebook.com/v15.0/";
// Whatsapp Connection - End

// Razorpay TEST Configuration
/* $rp_keyid       = "rzp_test_3d14kxnIjpcKIz";
$rp_keysecret   = "kSusodeEnSRcjdDLmtZEe0ud"; */

// Razorpay LIVE Configuration
$rp_keyid       = "rzp_live_pWIs8WdU8DslrS";
$rp_keysecret   = "YZ9n7AxPKNjAifkMZWr0XOOc";

// Create connection in Mysql DB
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the Mysql sql mode for unwanted termination while running the query
mysqli_query($conn, "SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
date_default_timezone_set("Asia/Kolkata"); // Setup the Asia/Kolkata timezone in the whole site

include_once('ajax/site_common_functions.php'); // Setup the Common functions which is used in the whole site


// To Generate the Log File and store into log/site_log folder
function site_log_generate($log_msg, $location = '')
{
    $max_size = 10485760; // 10 MB allowed
    
// Log File Generation with Current URL
$log_base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$log_url = $log_base_url . $_SERVER["REQUEST_URI"]." : IP Address : ".$_SERVER['SERVER_ADDR']." ==> ";

    $log_filename = "site_log";
    if (!file_exists($location."log/".$log_filename)) 
    {
        // create the directory/folder
        mkdir($location."log/".$log_filename, 0777, true);
    }
    $log_file_data1 = $location."log/".$log_filename.'/log_'.date('d-M-Y');
    $log_file_data  = $log_file_data1.'.log';

    clearstatcache();
    $size = filesize($log_file_data);

    // If size exceeds rename the file and move
    if($size > $max_size)
    {
        shell_exec("mv ".$log_file_data." ".$log_file_data1."-".date('YmdHis').".log");
    }

    // Append the data into log file
    file_put_contents($log_file_data, $log_url.$log_msg . "\n", FILE_APPEND);
}
