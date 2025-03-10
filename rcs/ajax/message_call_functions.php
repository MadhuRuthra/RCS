<?php
/*
This page has some functions which is access from Frontend.
This page is act as a Backend page which is connect with Node JS API and PHP Frontend.
It will collect the form details and send it to API.
After get the response from API, send it back to Frontend.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
*/
//phpinfo();
session_start(); // start session
error_reporting(E_ALL); // The error reporting function
include_once('../api/configuration.php'); // Include configuration.php
include_once('site_common_functions.php'); // include sitecommon functions
extract($_REQUEST); // Extract the request

$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // To get bearertoken
$current_date = date("Y-m-d H:i:s"); // To get currentdate function
$milliseconds = round(microtime(true) * 1000); // milliseconds in time
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

// Compose Whatsapp Page validateMobno - Start
if (isset($_POST['validateMobno']) == "validateMobno") {
  // This function is used to validate the mobile numbers
  // Get data
  $mobno = str_replace('"', '', htmlspecialchars(strip_tags(isset($_POST['mobno']) ? $conn->real_escape_string($_POST['mobno']) : "")));
  $dup = htmlspecialchars(strip_tags(isset($_POST['dup']) ? $conn->real_escape_string($_POST['dup']) : ""));
  $inv = htmlspecialchars(strip_tags(isset($_POST['inv']) ? $conn->real_escape_string($_POST['inv']) : ""));

  $mobno = str_replace('\n', ',', $mobno);
  $newline = explode('\n', $mobno);
  // validate the mobile numbers
  $correct_mobno_data = [];
  $return_mobno_data = '';
  $issu_mob = '';
  $cnt_vld_no = 0;
  $max_vld_no = 1000;
  // Looping the i is less than count of newline. if the condition is true to continue the process.if the condition is false to stop the process
  for ($i = 0; $i < count($newline); $i++) {
    $expl = explode(",", $newline[$i]);
    // Looping with in the another loop the i is less than count of expl. if the condition is true to continue the process.if the condition is false to stop the process
    for ($ij = 0; $ij < count($expl); $ij++) {

      if ($inv == 1) {
        $vlno = validate_phone_number($expl[$ij]);
      } else {
        $vlno = $newline[$i];
      }

      if ($vlno == true) {
        if ($dup == 1) {
          if (!in_array($expl[$ij], $correct_mobno_data)) {
            if ($expl[$ij] != '') {
              $cnt_vld_no++;
              if ($cnt_vld_no <= $max_vld_no) {
                $correct_mobno_data[] = $expl[$ij];
                $return_mobno_data .= $expl[$ij] . ",\n";
              } else {
                $issu_mob .= $expl[$ij] . ",";
              }
            } else {
              $issu_mob .= $expl[$ij] . ",";
            }
          } else {
            $issu_mob .= $expl[$ij] . ",";
          }
        } else {
          if ($expl[$ij] != '') {
            $cnt_vld_no++;
            if ($cnt_vld_no <= $max_vld_no) {
              $correct_mobno_data[] = $expl[$ij];
              $return_mobno_data .= $expl[$ij] . ",\n";
            } else {
              $issu_mob .= $expl[$ij] . ", ";
            }
          } else {
            $issu_mob .= $expl[$ij] . ", ";
          }
        }
      } else {
        $issu_mob .= $expl[$ij] . ",";
      }
    }
  }

  $return_mobno_data = rtrim($return_mobno_data, ",\n");
  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " validated Mobile Nos ($return_mobno_data||$issu_mob) on " . date("Y-m-d H:i:s"), '../');
  $json = array("status" => 1, "msg" => $return_mobno_data . "||" . $issu_mob);
}
// Compose Whatsapp Page validateMobno - End

// Compose Whatsapp Page delete_senderid - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "delete_senderid") {
  // Get data
  $whatspp_config_id1 = htmlspecialchars(strip_tags(isset($_REQUEST['whatspp_config_id']) ? $conn->real_escape_string($_REQUEST['whatspp_config_id']) : ""));
  $approve_status1 = htmlspecialchars(strip_tags(isset($_REQUEST['approve_status']) ? $conn->real_escape_string($_REQUEST['approve_status']) : ""));
  // To Send the request  API
//"user_id" : "'.$_SESSION['yjwatsp_user_id'].'",
  $replace_txt = '{
    "whatspp_config_id" : "' . $whatspp_config_id1 . '",
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "delete_sender_id" API to verify, can we access for the delete_sender_id details 
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/sender_id/delete_sender_id',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'

      ),
    )
  );
  // Send the data into API and execute 
  site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
  // To get the one by one data
  if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg);
  } else if ($header->response_status == 204) {
    site_log_generate("Compose Whatsapp Delete Sender ID Page  : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 2, "msg" => $header->response_msg);
  } else {
    if ($header->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => "Failed"];
  }
}
// Compose Whatsapp Page delete_senderid - Start


// Compose Whatsapp Page delete_user - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $delete_user_functions == "delete_users") {
  //echo "!!!!!!";
  // Get data
  $delete_user_id = htmlspecialchars(strip_tags(isset($_REQUEST['user_id']) ? $conn->real_escape_string($_REQUEST['user_id']) : ""));

  $replace_txt = '{
    "user_id" : "'.$_SESSION['yjwatsp_user_id'].'",
    "delete_user_id" : "'.$delete_user_id.'"
}';
  //echo $replace_txt;
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "delete_sender_id" API to verify, can we access for the delete_sender_id details 
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/delete_users',
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
  site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  //echo $response;
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
  // To get the one by one data
  if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg);
  } else if ($header->response_status == 204) {
    site_log_generate("Compose Whatsapp Delete Sender ID Page  : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 2, "msg" => $header->response_msg);
  } else {
    if ($header->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => "Failed"];
  }
}
// Compose Whatsapp Page delete_user - End



// Compose Whatsapp Page activate_user - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $activate_user_functions == "activate_users") {
  //echo "!!!!!!";
  // Get data
  $activate_user_id = htmlspecialchars(strip_tags(isset($_REQUEST['user_id']) ? $conn->real_escape_string($_REQUEST['user_id']) : ""));

  $replace_txt = '{
    "user_id" : "'.$_SESSION['yjwatsp_user_id'].'",
    "activate_user_id" : "'.$activate_user_id.'"
}';
  //echo $replace_txt;
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "delete_sender_id" API to verify, can we access for the delete_sender_id details 
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/activate_users',
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
  site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  //echo $response;
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
  // To get the one by one data
  if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg);
  } else if ($header->response_status == 204) {
    site_log_generate("Compose Whatsapp Delete Sender ID Page  : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 2, "msg" => $header->response_msg);
  } else {
    if ($header->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Compose Whatsapp Delete Sender ID Page : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => "Failed"];
  }
}
// Compose Whatsapp Page activate_user - End


// Compose Whatsapp Page generate_contacts - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "generate_contacts") {
  // Get data
  $txt_list_mobno = htmlspecialchars(strip_tags(isset($_REQUEST['txt_list_mobno']) ? $conn->real_escape_string($_REQUEST['txt_list_mobno']) : ""));

  $expld = explode(",", $txt_list_mobno); // explode function
  $mblno = '';
  for ($i = 0; $i < count($expld); $i++) {
    // Looping the i is less than count of expld. if the condition is true to continue the process.if the condition is false to stop the process
    $mblno .= '"' . $expld[$i] . '", ';
  }
  $mblno = rtrim($mblno, ", ");
  // To Send the request API
  $replace_txt = '{
    "mobile_number" : [' . $mblno . '],
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
  //add bearer_token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "create_csv" API to verify, can we can we allow to view the create_csv list
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/create_csv',
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
  site_log_generate("Compose Whatsapp Generate Contacts Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp Generate Contacts Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($header->response_code == 1) {// If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $site_url . $header->file_location);
  } else if ($header->response_status == 204) {
    site_log_generate("Compose Whatsapp Generate Contacts Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 2, "msg" => $header->response_msg);
  } else {    //otherwise
    if ($header->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Compose Whatsapp Generate Contacts Page  : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => "Failed to Generate Contact CSV. Kindly try again!!"];
  }
}
// Compose Whatsapp Page generate_contacts - Start



// generate Report List Whatsapp Page generate_report_list - Start
if($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "generate_report") {
  // Get data

  $compose_whatsapp_id = htmlspecialchars(strip_tags(isset($_REQUEST['compose_whatsapp_id']) ? $conn->real_escape_string($_REQUEST['compose_whatsapp_id']) : ""));
  $user_id = htmlspecialchars(strip_tags(isset($_REQUEST['user_id']) ? $conn->real_escape_string($_REQUEST['user_id']) : ""));


      // File moved successfully
      $replace_txt = '{
                "user_id" : "'.$_SESSION['yjwatsp_user_id'].'",
                "compose_user_id" : "'.$user_id.'",
                "compose_id" : "'.$compose_whatsapp_id.'"
            }';

//echo $replace_txt;

    // To Get Api URL
  $bearer_token = 'Authorization: '.$_SESSION['yjwatsp_bearer_token'].'';
  // It will call "approve_reject_template" API to verify, can we use approve_reject_template
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url.'/list/generate_report',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$replace_txt,
    CURLOPT_HTTPHEADER => array(
      $bearer_token,
      'Content-Type: application/json'

    ),
  ));
 // Send the data into API and execute
  site_log_generate("Compose Whatsapp approve_template Page : ".$_SESSION['yjwatsp_user_name']." Execute the service [$replace_txt] on ".date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  //echo $response;
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);

  site_log_generate("Compose Whatsapp approve_template Page : ".$_SESSION['yjwatsp_user_name']." get the Service response [$response] on ".date("Y-m-d H:i:s"), '../');

if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg);
  } else if($header->response_status == 204){ //otherwise
    site_log_generate("Compose Whatsapp approve_template Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $header->response_msg);

  }else {
    site_log_generate("Compose Whatsapp approve_template Page  : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => $header->response_msg, "pjfile" => $header->pj_file_path, "yjfile" => $header->yj_file_path];
    //echo $json;
  }
}
// Generate Report List Page generate_report_list - End




// Approve Page save_phbabt - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "save_phbabt") {
  // Get data
  $whatspp_config_id = htmlspecialchars(strip_tags(isset($_REQUEST['whatspp_config_id']) ? $conn->real_escape_string($_REQUEST['whatspp_config_id']) : ""));
  $fieldname = htmlspecialchars(strip_tags(isset($_REQUEST['fieldname']) ? $conn->real_escape_string($_REQUEST['fieldname']) : ""));
  $fieldvalue = htmlspecialchars(strip_tags(isset($_REQUEST['fieldvalue']) ? $conn->real_escape_string($_REQUEST['fieldvalue']) : ""));
  $whatspp_config_id1 = htmlspecialchars(strip_tags(isset($_REQUEST['whatspp_config_id']) ? $conn->real_escape_string($_REQUEST['whatspp_config_id']) : ""));
  $approve_status1 = htmlspecialchars(strip_tags(isset($_REQUEST['approve_status']) ? $conn->real_escape_string($_REQUEST['approve_status']) : ""));
  $phone_number_id = htmlspecialchars(strip_tags(isset($_REQUEST['phone_number_id']) ? $conn->real_escape_string($_REQUEST['phone_number_id']) : ""));
  $whatsapp_business_acc_id = htmlspecialchars(strip_tags(isset($_REQUEST['whatsapp_business_acc_id']) ? $conn->real_escape_string($_REQUEST['whatsapp_business_acc_id']) : ""));
  $bearer_token_value = htmlspecialchars(strip_tags(isset($_REQUEST['bearer_token_value']) ? $conn->real_escape_string($_REQUEST['bearer_token_value']) : ""));
  $mobile_no = htmlspecialchars(strip_tags(isset($_REQUEST['mobile_number']) ? $conn->real_escape_string($_REQUEST['mobile_number']) : ""));
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "username_approve_usergenerate" API to verify, can we Auto generate the Add the approve_user option
  $curl = curl_init();  // To Get Api URL
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . "/approve_user",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
          "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
          "mobile_number": "' . $mobile_no . '",
          "phone_number_id": "' . $phone_number_id . '",
          "whatsapp_business_acc_id": "' . $whatsapp_business_acc_id . '",
          "bearer_token": "' . $bearer_token_value . '",
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
        }',
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  $usr = '{
    "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
    "mobile_number": "' . $mobile_no . '",
    "phone_number_id": "' . $phone_number_id . '",
    "whatsapp_business_acc_id": "' . $whatsapp_business_acc_id . '",
    "bearer_token": "' . $bearer_token_value . '",
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
  site_log_generate("Approve Whatsappno Page : " . $_SESSION['yjwatsp_user_name'] . " executed the query API ($usr) on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $yjresponseobj = json_decode($response, false);

  site_log_generate("Approve Whatsappno Page : " . $_SESSION['yjwatsp_user_name'] . " executed the query API response ($response) on " . date("Y-m-d H:i:s"), '../');
  if ($yjresponseobj->response_msg == 'Error occurred') { // If the response is success to execute this condition
    $json = array("status" => 2, "msg" => $yjresponseobj->data[0]);
  } else if ($yjresponseobj->response_status == 204) { //otherwise
    site_log_generate("Approve Whatsappno Page  : " . $user_name . "get the Service response [$yjresponseobj->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $yjresponseobj->response_msg);
  } else {
    if ($yjresponseobj->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Approve Whatsappno Page  : " . $user_name . " get the Service response [$yjresponseobj->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => $yjresponseobj->response_msg];
  }
}
// Approve Page save_phbabt - Start


// Compose Whatsapp Page approve_template - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "approve_template") {
  // Get data
  $template_id1 = htmlspecialchars(strip_tags(isset($_REQUEST['unique_template_id']) ? $conn->real_escape_string($_REQUEST['unique_template_id']) : ""));
  $approve_status1 = htmlspecialchars(strip_tags(isset($_REQUEST['template_status']) ? $conn->real_escape_string($_REQUEST['template_status']) : ""));
  $reject_reason = htmlspecialchars(strip_tags(isset($_REQUEST['reject_reason']) ? $conn->real_escape_string($_REQUEST['reject_reason']) : ""));
  // $template_id = htmlspecialchars(strip_tags($data['templateid'] ?? ''));
  // $media_url = htmlspecialchars(strip_tags($data['media_url'] ?? ''));
  $template_id = htmlspecialchars(strip_tags(isset($_REQUEST['templateid']) ? $conn->real_escape_string($_REQUEST['templateid']) : ""));
  $media_url = htmlspecialchars(strip_tags(isset($_REQUEST['media_url']) ? $conn->real_escape_string($_REQUEST['media_url']) : ""));
// echo($template_id);
  // To Send the request API
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
    "change_status" : "' . $approve_status1 . '",
    "template_id" : "' . $template_id1 . '"';

// If the template is rejected, add the reject reason
if ($approve_status1 == "R") {
    $replace_txt .= ',"reject_reason" : "' . $reject_reason . '"';
} else {
    // If not rejected, include templateid and media_url
    $template_id = htmlspecialchars(strip_tags(isset($_REQUEST['templateid']) ? $conn->real_escape_string($_REQUEST['templateid']) : ""));
    $media_url = htmlspecialchars(strip_tags(isset($_REQUEST['media_url']) ? $conn->real_escape_string($_REQUEST['media_url']) : ""));
    
    $replace_txt .= ',"templateid" : "' . $template_id . '",
                      "media_url" : "' . $media_url . '"';
}

// Close the JSON object
$replace_txt .= '}';
  // To Get Api URL
  // print_r($replace_txt);
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "approve_reject_template" API to verify, can we use approve_reject_template
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/template/approve_reject_template',
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
  site_log_generate("Compose Whatsapp approve_template Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result  
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp approve_template Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg);
  } else if ($header->response_status == 204) { //otherwise
    site_log_generate("Compose Whatsapp approve_template Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    echo json_encode(array("status" => 1, "msg" => $header->response_msg));
  } else {
    if ($header->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Compose Whatsapp approve_template Page  : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => $header->response_msg];
  }
}
// Compose Whatsapp Page approve_template - Start

//  approve page sender_id reject approve_qr_whatsappno - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "approve_qr_whatsappno") {
  // Get data
  $whatspp_config_id1 = htmlspecialchars(strip_tags(isset($_REQUEST['whatspp_config_id']) ? $conn->real_escape_string($_REQUEST['whatspp_config_id']) : ""));
  $approve_status1 = htmlspecialchars(strip_tags(isset($_REQUEST['approve_status']) ? $conn->real_escape_string($_REQUEST['approve_status']) : ""));
  // To Send the request API
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
    "whatspp_config_status" : "' . $approve_status1 . '",
    "whatspp_config_id" : "' . $whatspp_config_id1 . '"
  }';
  // Add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "approve_whatsappno" API to verify, can we use approve_whatsappno
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/reject_campaign',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  site_log_generate("approve page sender_id reject Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $sms = json_decode($response, false);
  site_log_generate("approve page sender_id reject Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($sms->response_msg > 0) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $sms->response_msg);
  } else if ($sms->response_status == 204) { //otherwise
    site_log_generate("approve page sender_id reject Page   : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $sms->response_msg);
  } else {
    if ($sms->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("approve page sender_id reject Page   : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => "Save option Failed!!. Must fill all fields!!"];
  }
}
// approve page sender_id reject approve_qr_whatsappno - End

// Compose Whatsapp Page approve_whatsappno - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "approve_whatsappno") {
  // Get data
  $compose_message_id = htmlspecialchars(strip_tags(isset($_REQUEST['compose_message_id']) ? $conn->real_escape_string($_REQUEST['compose_message_id']) : ""));
  $approve_status1 = htmlspecialchars(strip_tags(isset($_REQUEST['approve_status']) ? $conn->real_escape_string($_REQUEST['approve_status']) : ""));
 $selected_userid = htmlspecialchars(strip_tags(isset($_REQUEST['selected_userid']) ? $conn->real_escape_string($_REQUEST['selected_userid']) : ""));
  $reason = htmlspecialchars(strip_tags(isset($_REQUEST['reason']) ? $_REQUEST['reason'] : ""));

  // To Send the request API
  $replace_txt = '{
      "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
      "campaign_status" : "' . $approve_status1 . '",
      "compose_message_id" : "' . $compose_message_id . '",
      "selected_userid" : "'. $selected_userid .'",
      "reason" : "'.$reason.'"
    }';
  // Add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "approve_whatsappno" API to verify, can we use approve_whatsappno
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/reject_campaign',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'PUT',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  site_log_generate("Approve Whatsappno Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);

  // After got response decode the JSON result
  $sms = json_decode($response, false);
  site_log_generate("Approve Whatsappno Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($sms->response_msg == 'Success') { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $yjresponseobj->reponse_msg);
  } else if ($sms->response_status == 204) { //otherwise
    site_log_generate("Approve Whatsappno Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $sms->response_msg);
  } else {
    if ($sms->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Approve Whatsappno Page   : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => $sms->response_msg];
  }
}

// Compose Whatsapp Page approve_whatsappno - Start

// All Page Footer find_blocked_senderid - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "find_blocked_senderid") {
  // To Send the request API
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
  }';
  //add bearertoken
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "find_blocked_senderid" API to verify, can we find_blocked_senderid
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/find_blocked_senderid',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  site_log_generate("All Footer Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $sms = json_decode($response, false);
  site_log_generate("All Footer Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  $sent_count_msg = '';
  $ii = 0;
  if ($sms->num_of_rows > 0) {
    for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
      // Looping the indicator is less than num_of_rows. if the condition is true to continue the process.if the condition is false to stop the process
      $ii++;
      $wht_bearer_token = $sms->report[$indicator]->mobile_no;
      $sent_count_msg .= $ii . ") Sender ID : " . $sms->report[$indicator]->mobile_no . ", <br>";
    }
  } else if ($sms->response_status == 204) { // If the response is success to execute this condition
    site_log_generate("All Page Footer find_blocked_senderid : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $sms->response_msg);
  } else { //otherwise
    if ($sms->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("All Page Footer find_blocked_senderid : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => $sms->response_msg];
  }
  $sent_count_msg = rtrim($sent_count_msg, ",  <br>");
  if ($sent_count_msg != '') {
    $json = array("status" => 1, "msg" => $sent_count_msg);
  } else {
    $json = array("status" => 0, "msg" => "0");
  }
}
// All Page Footer find_blocked_senderid - Start


// Messenger Reply Page messenger_reply - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "messenger_reply") {
  //  generate log file
  site_log_generate("Messenger Reply Page : User : " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');
  // Get data
  $txt_reply = htmlspecialchars(strip_tags(isset($_REQUEST['txt_reply']) ? $conn->real_escape_string($_REQUEST['txt_reply']) : ""));
  $receiver_mobile = htmlspecialchars(strip_tags(isset($_REQUEST['message_to']) ? $conn->real_escape_string($_REQUEST['message_to']) : ""));
  $sender_id = htmlspecialchars(strip_tags(isset($_REQUEST['sender_id']) ? $conn->real_escape_string($_REQUEST['sender_id']) : ""));
  $message_from = htmlspecialchars(strip_tags(isset($_REQUEST['message_from']) ? $conn->real_escape_string($_REQUEST['message_from']) : ""));
  // To Send the request API
  $send_reply = '{
    "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
    "reply_msg":"' . $txt_reply . '",
    "sender_id" : "' . $sender_id . '",
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
  site_log_generate("Messenger Reply Page : User : " . $_SESSION['yjwatsp_user_name'] . " reply [$send_reply] on " . date("Y-m-d H:i:s"), '../');
  //add bearer_token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "reply_message" API to verify, can we use reply_message 
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . "/message/reply_message",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
      "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
      "reply_msg":"' . $txt_reply . '",
       "sender_id" : "' . $sender_id . '",
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
    }',
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  $response = curl_exec($curl);
  site_log_generate("Messenger Reply Page : User : " . $_SESSION['yjwatsp_user_name'] . " reply reseponse [$response] on " . date("Y-m-d H:i:s"), '../');
  curl_close($curl);
  // After got response decode the JSON result
  $yjresponseobj = json_decode($response, false);

  if ($yjresponseobj->response_status == 200) {// If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $yjresponseobj->response_msg);
  } else if ($yjresponseobj->response_status == 204) { //otherwise
    site_log_generate("Messenger Reply Page : " . $user_name . "get the Service response [$yjresponseobj->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $yjresponseobj->response_msg);
  } else {
    if ($yjresponseobj->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Messenger Reply Page : " . $user_name . " get the Service response [$yjresponseobj->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Failed while saving the Reply!!");
  }
}
// Messenger Reply Page messenger_reply - Start


// Compose Whatsapp Page compose_whatsapp - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "compose_whatsapp") {
  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');

  // Get data
  $txt_list_mobno = htmlspecialchars(strip_tags(isset($_REQUEST['txt_list_mobno']) ? $_REQUEST['txt_list_mobno'] : ""));

  $chk_remove_duplicates = htmlspecialchars(strip_tags(isset($_REQUEST['chk_remove_duplicates']) ? $_REQUEST['chk_remove_duplicates'] : ""));
  $chk_remove_invalids = htmlspecialchars(strip_tags(isset($_REQUEST['chk_remove_invalids']) ? $_REQUEST['chk_remove_invalids'] : ""));
  $id_slt_contgrp = htmlspecialchars(strip_tags(isset($_REQUEST['id_slt_contgrp']) ? $_REQUEST['id_slt_contgrp'] : "0"));
  $txt_sms_type = htmlspecialchars(strip_tags(isset($_REQUEST['txt_sms_type']) ? $_REQUEST['txt_sms_type'] : "TEXT"));
  $txt_sms_type = strtoupper($txt_sms_type);
  $country_code = '';
  $mime_type = '';

  $filename = '';
  if ($_FILES['txt_media']['name'] != '') {
    $path_parts = pathinfo($_FILES["txt_media"]["name"]);
    $extension = $path_parts['extension'];

    $filename = $_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $extension;
    /* Location */
    $location = "../uploads/whatsapp_media/" . $filename;
    $send_location = $full_pathurl . "/uploads/whatsapp_media/" . $filename;
    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);

    switch ($imageFileType) {
      case 'jpg':
      case 'jpeg':
        $mime_type = "image/jpeg";
        break;
      case 'png':
        $mime_type = "image/png";
        break;
      case 'gif':
        $mime_type = "image/gif";
        break;

      case 'pdf':
        $mime_type = "application/pdf";
        break;
      case 'mp4':
        $mime_type = "video/mp4";
        break;
      case 'webm':
        $mime_type = "video/webm";
        break;
    }

    /* Valid extensions */
    $valid_extensions = array("jpg", "jpeg", "png", "pdf", "gif", "mp4", "webm");

    $rspns = '';
    /* Check file extension */
    if (move_uploaded_file($_FILES['txt_media']['tmp_name'], $location)) {
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_media file moved into Folder on " . date("Y-m-d H:i:s"), '../');
    }
  } else {
    $filename = '';
  }
  // To Get Data
  $txt_sms_content = htmlspecialchars(strip_tags(isset($_REQUEST['txt_sms_content']) ? $_REQUEST['txt_sms_content'] : ""));
  $txt_caption = htmlspecialchars(strip_tags(isset($_REQUEST['txt_caption']) ? $_REQUEST['txt_caption'] : "Media"));
  $txt_char_count = htmlspecialchars(strip_tags(isset($_REQUEST['txt_char_count']) ? $_REQUEST['txt_char_count'] : "1"));
  $txt_sms_count = htmlspecialchars(strip_tags(isset($_REQUEST['txt_sms_count']) ? $_REQUEST['txt_sms_count'] : "1"));
  $txt_rcscard_title = htmlspecialchars(strip_tags(isset($_REQUEST['txt_rcscard_title']) ? $_REQUEST['txt_rcscard_title'] : ""));

  $chk_save_contact_group = htmlspecialchars(strip_tags(isset($_REQUEST['chk_save_contact_group']) ? $_REQUEST['chk_save_contact_group'] : ""));

  $expl_wht = explode("~~", $txt_whatsapp_mobno[0]);
  $storeid = $expl_wht[0];
  $confgid = $expl_wht[1];

  $txt_caption = str_replace("'", "\'", $txt_caption);
  $txt_caption = str_replace('"', '\"', $txt_caption);

  // Receiver Mobile Numbers
  $newline1 = explode("\n", $txt_list_mobno);
  $receive_mobile_nos = '';
  $cnt_mob_no = count($newline1);
  for ($i1 = 0; $i1 < count($newline1); $i1++) {
    // Looping the i1 is less than the count of newline1. if the condition is true to continue the process.if the condition is false to stop the process
    $expl1 = explode(",", $newline1[$i1]);
    for ($ij1 = 0; $ij1 < count($expl1); $ij1++) {
      // Looping with in the another loop the ij1 is less than count of expl1. if the condition is true to continue the process.if the condition is false to stop the process
      if (validate_phone_number($expl1[$ij1])) {
        $mblno[] = $expl1[$ij1];
        $receive_mobile_nos .= $expl1[$ij1] . ',';
      }
    }
  }
  $receive_mobile_nos = rtrim($receive_mobile_nos, ",");

  // // Sender Mobile Numbers
  // $sender_mobile_nos = '';
  // for ($i1 = 0; $i1 < count($txt_whatsapp_mobno); $i1++) {
  //   // Looping the i1 is less than the count of txt_whatsapp_mobno. if the condition is true to continue the process.if the condition is false to stop the process
  //   $ex1 = explode('~~', $txt_whatsapp_mobno[$i1]);
  //   $sender_mobile_nos .= $ex1[2] . ',';
  // }
  // $sender_mobile_nos = rtrim($sender_mobile_nos, ",");

$txt_sms_content = substr($txt_sms_content, 0, 1000);
/* $txt_char_count = mb_strlen($txt_sms_content, 'utf-8');
if ($txt_char_count != strlen($txt_sms_content)) {
    $txt_sms_count = ceil($txt_char_count / 70);
} else { */
    $txt_sms_count = ceil($txt_char_count / 160);
// }

  $usr_id = $_SESSION['yjwatsp_user_id'];
  // To Send the request API
  $replace_txt = '{
      "user_id" : "' . $usr_id . '"
    }';
  //  add bearer_token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "username_generate" API to verify, can we use available_credits
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . "/list/available_credits",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_POSTFIELDS => '{
        "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
        "sender_mobile":"' . $sender_id . '",
        "receiver_mobile":"' . $receiver_mobile . '",
        "reply_msg":"' . $txt_reply . '"
      }',
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  site_log_generate("Compose Whatsapp Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($header->num_of_rows > 0) {
    for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
      // Looping the indicator is less than the num_of_rows. if the condition is true to continue the process.if the condition is false to stop the process
      $alotsms = $header->report[$indicator]->available_messages;
      $expdate = date("Y-m-d H:i:s", strtotime($header->report[$indicator]->expiry_date));
    }
  } else {
    if ($header->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    $alotsms = 0;
    $expdate = '';
  }

  $ttlmsgcnt = 0;
  if ($txt_sms_content != '') {
    $ttlmsgcnt++;
  }
  if ($filename != '') {
    $ttlmsgcnt++;
  }
  if ($txt_open_url != '' or $txt_call_button != '' or (count($txt_reply_buttons) > 0 and $txt_reply_buttons[0] != '')) {
    $ttlmsgcnt++;
  }
  if (count($txt_option_list) > 0 and $txt_option_list[0] != '') {
    $ttlmsgcnt++;
  }

  $ttl_sms_cnt = count($mblno);
  $txt_sms_content = str_replace("'", "\'", $txt_sms_content);
  $txt_sms_content = str_replace('"', '\"', $txt_sms_content);

  if ($alotsms == 0 and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Whatsapp Credits are not available..] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Whatsapp Credits are not available. Kindly verify!!");
  } elseif ($alotsms < $ttl_sms_cnt and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Whatsapp Credits are not available.] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Whatsapp Credits are not available. Kindly verify!!");
  } elseif ($txt_char_count > 700) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Morethan 700 characters are not allowed for Whatsapp] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Morethan 700 characters are not allowed for Whatsapp. Kindly verify!!");
  } elseif ($expdate == '' and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Validity Period Expired.] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Validity Period Expired. Kindly verify!");
  } elseif (strtotime($expdate) < strtotime($current_date) and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Validity Period Expired..] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Validity Period Expired. Kindly verify!!");
  } else {


    $update_txt = '';
    // Send Whatsapp Message - Start
    // Media Messages - Start
    if ($filename != '') {
      $update_txt .= '{
                "msg_type" : "media",
                "media_file" : "' . $send_location . '",
                "file_name" : "' . $filename . '",
                "media_caption" : "' . $txt_caption . '"
              },';
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the text [$update_txt] on " . date("Y-m-d H:i:s"), '../');
    }
    // Media Messages - End

    // TEXT Messages - Start
    $txt_sms_content = str_replace("\'", "'", $txt_sms_content);
    $update_txt .= '{
              "msg_type" : "text",
              "text_msg" : "' . base64_encode($txt_sms_content) . '"
            },';
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the text [$update_txt] on " . date("Y-m-d H:i:s"), '../');
    // TEXT Messages - End

    // Button Messages (URL & CALL & REPLY) - Start
    if ($txt_open_url != '' and $txt_call_button != '' and (count($txt_reply_buttons) > 0 and $txt_reply_buttons[0] != '')) {
      $update_txt .= '{
                "msg_type" : "button",';
      $update_txt .= '
                "btn_content" : "' . $txt_open_url_data . '",
                "url_btn" : "' . $txt_open_url . '",
                "url_text" : "' . $txt_open_url_data . '",';

      $update_txt .= '
                "call_btn" : "+91' . $txt_call_button . '",
                "call_text" : "' . $txt_call_button_data . '",';

      $opt = '';
      $tsr_ii = 0;
      for ($tsr_i = 0; $tsr_i < count($txt_reply_buttons); $tsr_i++) {
        // Looping the tsr_i is less than the count of txt_reply_buttons. if the condition is true to continue the process.if the condition is false to stop the process
        $tsr_ii++;
        $opt .= '"reply_btn' . $tsr_ii . '" : "' . $txt_reply_buttons[$tsr_i] . '",';
      }
      $opt = rtrim($opt, ",");

      $update_txt .= '{
                "msg_type" : "button",
                "btn_content" : "Reply",
                ' . $opt . '
              },';

      $update_txt = rtrim($update_txt, ",");
      $update_txt .= '},';

      // Button Messages (URL & CALL & REPLY) - End
    } else {
      // Button Messages (URL) - Start
      if ($txt_open_url != '' or $txt_call_button != '') {
        $update_txt .= '{
                  "msg_type" : "button",';
      }
      if ($txt_open_url != '') {
        $update_txt .= '
                  "btn_content" : "' . $txt_open_url_data . '",
                  "url_btn" : "' . $txt_open_url . '",
                  "url_text" : "' . $txt_open_url_data . '",';
      }
      // Button Messages (URL) - End

      // Button Messages (CALL) - Start
      if ($txt_call_button != '') {
        $update_txt .= '
                  "call_btn" : "+91' . $txt_call_button . '",
                  "call_text" : "' . $txt_call_button_data . '",';
      }
      if ($txt_open_url != '' or $txt_call_button != '') {
        $update_txt = rtrim($update_txt, ",");

        $update_txt .= '},';
        site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the text [$update_txt] on " . date("Y-m-d H:i:s"), '../');
      }
      // Button Messages (CALL) - Start

      // Button Messages (REPLY) - Start
      if (count($txt_reply_buttons) > 0 and $txt_reply_buttons[0] != '') {
        $opt = '';
        $tsr_ii = 0;
        for ($tsr_i = 0; $tsr_i < count($txt_reply_buttons); $tsr_i++) {
          // Looping the tsr_i is less than the count of txt_reply_buttons. if the condition is true to continue the process.if the condition is false to stop the process
          $tsr_ii++;
          $opt .= '"reply_btn' . $tsr_ii . '" : "' . $txt_reply_buttons[$tsr_i] . '",';
        }
        $opt = rtrim($opt, ",");

        $update_txt .= '{
                  "msg_type" : "button",
                  "btn_content" : "Reply",
                  ' . $opt . '
                },';
        site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the text [$update_txt] on " . date("Y-m-d H:i:s"), '../');
      }
      // Button Messages (REPLY) - End
    }

    // List Messages - Start
    if (count($txt_option_list) > 0 and $txt_option_list[0] != '') {
      $opt1 = '';
      for ($tsr_i = 0; $tsr_i < count($txt_option_list); $tsr_i++) {
        // Looping the tsr_i is less than the count of txt_option_list. if the condition is true to continue the process.if the condition is false to stop the process
        $opt1 .= '{
                  "id": "' . strtolower($txt_option_list[$tsr_i]) . '", "title": "' . $txt_option_list[$tsr_i] . '"
                },';
      }
      $opt1 = rtrim($opt1, ",");

      $update_txt .= '{
                "msg_type" : "list",
                "title" : "Select",
                "body" : "Deals",
                "btn_text" : "View",
                "list_title" : "List", 
                "list_items" : [
                    ' . $opt1 . '
                ]
              },';
    }
    $update_txt = rtrim($update_txt, ",");
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the text [$update_txt] on " . date("Y-m-d H:i:s"), '../');
    // List Messages - End

    $sendto_api = '{
              "api_key":"' . $_SESSION['yjwatsp_api_key'] . '",
              "sender_numbers":[' . $sender_mobile_nos . '],
              "mobile_numbers":[' . $receive_mobile_nos . '],
              "messages" : [
                  ' . $update_txt . '
                ]
              }';
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " api send text [$sendto_api] on " . date("Y-m-d H:i:s"), '../');
    // Add bearer token
    $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
    // It will call "send_msg" API to verify, can we use send_msg
    $curl = curl_init();
    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => $watsp_msg_url . '/send_msg',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => $sendto_api,
        CURLOPT_HTTPHEADER => array(
          $bearer_token,
          "cache-control: no-cache",
          'Content-Type: application/json;charset=utf-8'
        ),
      )
    );
    // Send the data into API and execute 
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $respobj = json_decode($response);
    if ($response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " api response [$response] on " . date("Y-m-d H:i:s"), '../');

    $rsp_id = $respobj->response_status;
    if ($rsp_id == 203) {// If the response is success to execute this condition
      $json = array("status" => 1, "msg" => $respobj->response_msg);
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [$respobj->response_msg] on " . date("Y-m-d H:i:s"), '../');
    }
    if ($rsp_id == 403) {// If the response is success to execute this condition
      ?>
      <script>
        window.location = "index"
      </script>
      <?
      $json = array("status" => 2, "msg" => "Invalid User, Kindly try with valid User!!");
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [Invalid User, Kindly try with valid User!!] on " . date("Y-m-d H:i:s"), '../');
    } elseif ($rsp_id == 201) { //otherwise
      $json = array("status" => 0, "msg" => "Failure");
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [Failure] on " . date("Y-m-d H:i:s"), '../');
    } else {
      $json = array("status" => 1, "msg" => "Success");
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [Success] on " . date("Y-m-d H:i:s"), '../');
    }
  }
  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " newconn new db connection closed on " . date("Y-m-d H:i:s"), '../');
}
// Compose Whatsapp Page compose_whatsapp - End

// purchase_sms_credit Page purchase_sms_credit - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "purchase_sms_credit") {
  site_log_generate("Purchase SMS Credit Page : User : " . $_SESSION['yjwatsp_user_name'] . " Purchase SMS Credit - access this page on " . date("Y-m-d H:i:s"), '../');
  // Get data
  $txt_pricing_plan = htmlspecialchars(strip_tags(isset($_REQUEST["txt_pricing_plan"]) ? $conn->real_escape_string($_REQUEST["txt_pricing_plan"]) : ""));
  $txt_message_amount = htmlspecialchars(strip_tags(isset($_REQUEST["txt_message_amount"]) ? $conn->real_escape_string($_REQUEST["txt_message_amount"]) : ""));
  $usrcrdbt_comments = htmlspecialchars(strip_tags(isset($_REQUEST["usrcrdbt_comments"]) ? $conn->real_escape_string($_REQUEST["usrcrdbt_comments"]) : "-"));

  $cnt_insrt = 0;
  $slt_expiry_date = 12; // 12 Months
  $exp_date = date("Y-m-d H:i:s", strtotime('+' . $slt_expiry_date . ' month'));
  $expl = explode("~~", $txt_pricing_plan);

  $paid_status = 'A';
  $paid_status_cmnts = "Direct Approval. Collect the Money from them, before Credit the Messages";
  if ($_SESSION['yjwatsp_user_master_id'] == 2) {
    $paid_status = 'W';
    $paid_status_cmnts = 'NULL';
  }

  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
    "parent_id" : "' . $_SESSION['yjwatsp_parent_id'] . '",
    "pricing_slot_id" : "' . $expl[1] . '",
    "exp_date" : "' . $exp_date . '",
    "slt_expiry_date" : "' . $slt_expiry_date . '",
    "raise_sms_credits" : "' . $expl[3] . '",
    "sms_amount" : "' . $hdsms . '",
    "paid_status_cmnts" : "' . $paid_status_cmnts . '",
    "paid_status" : "' . $paid_status . '",
    "usrcrdbt_comments" : "' . $usrcrdbt_comments . '"
  }';

  // To Get Api URL
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/user_sms_credit_raise',
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
  site_log_generate("Approve Whatsappno Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service (user_sms_credit_raise) [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  $sms = json_decode($response, false);
  site_log_generate("Approve Whatsappno Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response (user_sms_credit_raise) [$response] on " . date("Y-m-d H:i:s"), '../');
  if ($sms->response_status == 200) {
    $cnt_insrt++;
  } else if ($sms->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
  <? }
  if ($cnt_insrt > 0) {
    site_log_generate("Purchase SMS Credit Page : User : " . $_SESSION['yjwatsp_user_name'] . " Purchase SMS Credit Success on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => "Success");
  } else {
    site_log_generate("Purchase SMS Credit Page : User : " . $_SESSION['yjwatsp_user_name'] . " Purchase SMS Credit failed [Data not inserted] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Data not inserted. Kindly try again!!");
  }
}
// purchase_sms_credit Page purchase_sms_credit - End


// Compose rcs Page compose_message - Start
if($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "compose_message") {
  // Get data
  $compose_id			= htmlspecialchars(strip_tags(isset($_REQUEST['compose_id']) ? $conn->real_escape_string($_REQUEST['compose_id']) : ""));
  $user_id      = htmlspecialchars(strip_tags(isset($_REQUEST['user_id']) ? $conn->real_escape_string($_REQUEST['user_id']) : ""));

 // To Send the request API
 $replace_txt = '{
  "user_id" : "'.$_SESSION['yjwatsp_user_id'].'",
  "selected_user_id" : "'.$user_id.'",
  "campaign_id" : "'.$compose_id.'",
  "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
}';
    // To Get Api URL
  $bearer_token = 'Authorization: '.$_SESSION['yjwatsp_bearer_token'].''; 
  // It will call "approve_reject_template" API to verify, can we use approve_reject_template
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url.'/approve_rcs',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$replace_txt,
    CURLOPT_HTTPHEADER => array(
      $bearer_token,
      'Content-Type: application/json'
     
    ),
  ));
 // Send the data into API and execute 
  site_log_generate("Compose rcs approve_template Page : ".$_SESSION['yjwatsp_user_name']." Execute the service [$replace_txt] on ".date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result  
  $header = json_decode($response, false);

  site_log_generate("Compose rcs approve_template Page : ".$_SESSION['yjwatsp_user_name']." get the Service response [$response] on ".date("Y-m-d H:i:s"), '../');
  
  if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg, "pjfile" => $header->pj_file_path);

  } else if($header->response_code == 0){ //otherwise
    site_log_generate("Compose rcs approve_template Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => $header->response_msg);
  }else {
    site_log_generate("Compose rcs approve_template Page  : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
    $json = ["status" => 0, "msg" => $header->response_msg, "pjfile" => $header->pj_file_path];
    echo $json;
  } 
}
// Compose rcs Page compose_message - End

// manual Whatsapp Page manual_report - Start
if($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "manual_report") {
  // Get data
  $compose_whatsapp_id = $_POST['compose_whatsapp_id'];
 $report_group = $_POST['report_group'];
 $user_id = $_POST['user_id'];
  $file = $_FILES['file'];
 $upload_info = $_POST['upload_info'];
  $fileName = $file['name'];
  $firstTenChars = substr($fileName, 0, 10);
  $filename = $firstTenChars."_".$_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $extension;
  $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/rcs/uploads/pj_report_file/';
  // Append the file name to the upload directory to create the full path
  $uploadPath = $uploadDirectory . $filename;

  //$uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/rcs/uploads/pj_report_file/';

  // Append the file name to the upload directory to create the full path
  //$uploadPath = $uploadDirectory . $fileName;

  // Move the uploaded file to the specified path
  if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
      // File moved successfully
      $replace_txt = '{
                "user_id" : "'.$_SESSION['yjwatsp_user_id'].'",
	            	"selected_user_id" : "'.$user_id.'",
                "compose_id" : "'.$compose_whatsapp_id.'",
                "csvFilePath" : "'.$filename.'"                     
            }';
  } else {
    $json = array("status" => 0, "msg" => "Failed to upload file.");
  }
// "upload_info" : "' . $upload_info . '"
    // To Get Api URL
  $bearer_token = 'Authorization: '.$_SESSION['yjwatsp_bearer_token'].''; 
  // It will call "approve_reject_template" API to verify, can we use approve_reject_template
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url.'/update_report',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$replace_txt,
    CURLOPT_HTTPHEADER => array(
      $bearer_token,
      'Content-Type: application/json'
     
    ),
  ));
 // Send the data into API and execute 
  site_log_generate("Compose Whatsapp approve_template Page : ".$_SESSION['yjwatsp_user_name']." Execute the service [$replace_txt] on ".date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
 // echo $response;
  curl_close($curl);
  // After got response decode the JSON result  
 if ($response == '') {
    ?>
    <script>
      window.location = "index"
    </script>
    <?
  }
  // After got response decode the JSON result  
  $header = json_decode($response, false);

  site_log_generate("Compose Whatsapp approve_template Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($header->response_code == 1) { // If the response is success to execute this condition
    $json = array("status" => 1, "msg" => $header->response_msg, "pjfile" => $header->pj_file_path, "yjfile" => $header->yj_file_path);
  } else if ($header->response_status == 204) { //otherwise
    site_log_generate("Compose Whatsapp approve_template Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => $header->response_msg);

  }else if ($header->response_status == 201) { //otherwise
    site_log_generate("Compose Whatsapp approve_template Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => $header->response_msg);

  }  else if ($header->response_status == 403) { ?>
        <script>
          window.location = "index"
        </script>
  <? }
}
// manual Whatsapp Page manual_report - End


// generate_auto_report Page save_generate_auto_report - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "save_generate_auto_report") {
  site_log_generate("Purchase SMS Credit Page : User : " . $_SESSION['yjwatsp_user_name'] . " Purchase SMS Credit - access this page on " . date("Y-m-d H:i:s"), '../');
  // Get data
  $compose_user = htmlspecialchars(strip_tags(isset($_REQUEST["compose_user"]) ? $conn->real_escape_string($_REQUEST["compose_user"]) : ""));
  $db_name = htmlspecialchars(strip_tags(isset($_REQUEST["db_name"]) ? $conn->real_escape_string($_REQUEST["db_name"]) : ""));
  $table_name = htmlspecialchars(strip_tags(isset($_REQUEST["table_name"]) ? $conn->real_escape_string($_REQUEST["table_name"]) : ""));
  $compose_whatsapp_id = htmlspecialchars(strip_tags(isset($_REQUEST["compose_whatsapp_id"]) ? $conn->real_escape_string($_REQUEST["compose_whatsapp_id"]) : ""));
  $report_group = htmlspecialchars(strip_tags(isset($_REQUEST["report_group"]) ? $conn->real_escape_string($_REQUEST["report_group"]) : "-"));

  $replace_txt = '{
    "compose_user" : "' . $compose_user . '",
    "database" : "' . $db_name . '",
    "table_name" : "' . $table_name . '",
    "compose_whatsapp_id" : "' . $compose_whatsapp_id . '",
    "report_group" : "' . $report_group . '"
  }';

  // To Get Api URL
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  $curl = curl_init();
  
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/report/report_generation',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        // $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  site_log_generate("Generate Auto Report Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service (save_generate_auto_report) [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  $sms = json_decode($response, false);
  site_log_generate("Generate Auto Report Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response (save_generate_auto_report) [$response] on " . date("Y-m-d H:i:s"), '../');
  if ($sms->response_status == 200) {
    $cnt_insrt++;
  } else if ($sms->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
  <? }

  if ($cnt_insrt > 0) {
    site_log_generate("Generate Auto Report Page : User : " . $_SESSION['yjwatsp_user_name'] . " save_generate_auto_report Success on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => "Success");
  } else {
    site_log_generate("Generate Auto Report Page : User : " . $_SESSION['yjwatsp_user_name'] . " save_generate_auto_report failed [Data not inserted] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Data not inserted. Kindly try again!!");
  }
}
// generate_auto_report Page save_generate_auto_report - End

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with JSON Response
header('Content-type: application/json');
echo json_encode($json);
