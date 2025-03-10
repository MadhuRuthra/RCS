<?php
include_once('api/configuration.php');// Include configuration.php
header('Cache-Control: no cache'); // no cache // This is for avoid failure in submit form  pagination form details page
session_cache_limiter('private_no_expire, must-revalidate'); // works // This is for avoid failure in submit form  pagination form details page

session_start();// start session
site_log_generate("Logout Page : User : '" . $_SESSION['yjwatsp_user_name'] . "' logged out successfully on " . date("Y-m-d H:i:s"));
$current_date = date("Y-m-d H:i:s"); // get date and time
// Step 1: Get the current date
$todayDate = new DateTime();
// Step 2: Convert the date to Julian date
$baseDate = new DateTime($todayDate->format('Y-01-01'));
$julianDate = $todayDate->diff($baseDate)->format('%a') + 1; // Adding 1 since the day of the year starts from 0
// Step 3: Output the result in 3-digit format
// echo "Today's Julian date in 3-digit format: " . str_pad($julianDate, 3, '0', STR_PAD_LEFT);
$year = date("Y");
$julian_dates = str_pad($julianDate, 3, '0', STR_PAD_LEFT);
$hour_minutes_seconds = date("His");
$random_generate_three = rand(100, 999);
$replace_txt = '{
  "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
}';

// To get the logout api
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
// add bearer token 
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $api_url . '/logout',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $replace_txt,
  CURLOPT_HTTPHEADER => array(
    $bearer_token,
    'Content-Type: application/json'

  ),
)
);
// Send the data into API and execute
site_log_generate("Logout Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');

session_destroy();
$response = curl_exec($curl);
curl_close($curl);
// After got response decode the JSON result
$header = json_decode($response, false);
if ($header->response_status == 403 || $response == '') { /* ?>
  <script>
    window.location = ""
  </script>
<? */ }
site_log_generate("Logout Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
// to clear the all sessions

/*$_SESSION['yjwatsp_parent_id'] = '';
$_SESSION['yjwatsp_user_id'] = '';
$_SESSION['yjwatsp_user_master_id'] = '';
$_SESSION['yjwatsp_user_name'] = '';
$_SESSION['yjwatsp_api_key'] = '';
$_SESSION['yjwatsp_user_permission'] = '';
$_SESSION['yjwatsp_login_id'] = '';
$_SESSION['yjwatsp_user_email'] = '';
$_SESSION['yjwatsp_user_mobile'] = '';
$_SESSION['yjwatsp_price_per_sms'] = '';
$_SESSION['yjwatsp_netoptid'] = '';
$_SESSION['yjwatsp_usraprstat'] = '';
$_SESSION['yjwatsp_login_time'] = '';

$_SESSION = array();
// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

// Use this too
ini_set('session.gc_max_lifetime', 0);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
// close and destory the session
session_write_close();
session_unset();
//session_destroy();*/

// Unset specific session variables
unset($_SESSION["yjwatsp_parent_id"]);
unset($_SESSION["yjwatsp_user_id"]);
unset($_SESSION["yjwatsp_user_master_id"]);
unset($_SESSION["yjwatsp_user_name"]);
unset($_SESSION["yjwatsp_user_short_name"]);
unset($_SESSION["yjwatsp_api_key"]);
unset($_SESSION["yjwatsp_user_permission"]);
unset($_SESSION["yjwatsp_bearer_token"]);
unset($_SESSION["yjwatsp_login_id"]);
unset($_SESSION["yjwatsp_user_email"]);
unset($_SESSION["yjwatsp_user_mobile"]);
unset($_SESSION["yjwatsp_price_per_sms"]);
unset($_SESSION["yjwatsp_netoptid"]);
unset($_SESSION["yjwatsp_usraprstat"]);
unset($_SESSION["yjwatsp_usr_mgt_status"]);
unset($_SESSION["yjwatsp_login_time"]);

site_log_generate("Logout Page : All sessions destroyed successfully on " . date("Y-m-d H:i:s"));
?>
<script>window.location = "index";</script>
