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
session_start(); //start session
error_reporting(E_ALL); // The error reporting function
include_once('../api/configuration.php'); // Include configuration.php
extract($_REQUEST); // Extract the request
$current_date = date("Y-m-d H:i:s"); // Today date function
$milliseconds = round(microtime(true) * 1000);  // milliseconds in time
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // add bearer token
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

// Manage Sender ID Page save_mobile_api - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "save_mobile_api") {
  site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"), '../');
  // Get data
  $slt_service_category = htmlspecialchars(strip_tags(isset($_REQUEST['slt_service_category']) ? $conn->real_escape_string($_REQUEST['slt_service_category']) : ""));
  $exp1 = htmlspecialchars(strip_tags(isset($_REQUEST['txt_country_code']) ? $conn->real_escape_string($_REQUEST['txt_country_code']) : "101"));
  $mobile_number = htmlspecialchars(strip_tags(isset($_REQUEST['mobile_number']) ? $conn->real_escape_string($_REQUEST['mobile_number']) : ""));
  $txt_display_name = htmlspecialchars(strip_tags(isset($_REQUEST['txt_display_name']) ? $conn->real_escape_string($_REQUEST['txt_display_name']) : ""));
  site_log_generate("Manage Sender ID Page : Username => " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');

  $exp2 = explode("~~", $exp1);
  $txt_country_code = $exp2[0];
  $country_code = $exp2[1];
  $filename = '';
  if ($_FILES['fle_display_logo']['name'] != '') {
    $path_parts = pathinfo($_FILES["fle_display_logo"]["name"]);
    $extension = $path_parts['extension'];
    $filename = $_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $extension;

    /* Location */
    $location = "../uploads/whatsapp_images/" . $filename;
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
    }

    /* Valid extensions */
    $valid_extensions = array("jpg", "jpeg", "png");

    $rspns = '';
    if (move_uploaded_file($_FILES['fle_display_logo']['tmp_name'], $location)) {
      site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
    }
  } else {
    $filename = '';
  }

  if ($_SESSION['yjwatsp_user_master_id'] == 1 or $_SESSION['yjwatsp_user_master_id'] == 2) {
    $qr_code_allowed = 'A';
  } else {
    $qr_code_allowed = 'U';
  }
  // To Send the request  API
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
    "country_code" : "' . $country_code . '",
    "mobile_no" : "' . $mobile_number . '",
    "profile_name" : "' . $txt_display_name . '",
    "profile_image" : "' . $filename . '",
    "service_category" : "' . $slt_service_category . '",
    "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "add_sender_id" API to verify, can we access for the add_sender_id
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url . '/sender_id/add_sender_id',
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
  site_log_generate("Manage Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " logged in send it to Service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $sql = json_decode($response, false);
  site_log_generate("Manage Sender ID Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query reponse [$response] on " . date("Y-m-d H:i:s"), '../');
  if ($sql->response_code == 1) {
    site_log_generate("Manage Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " new mobile no added successfully on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $sql->response_msg);
  } else {
    if ($sql->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    site_log_generate("Manage Sender ID Page : " . $_SESSION['yjwatsp_user_name']["$sql->response_msg "] . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => $sql->response_msg);

  }
  // if ($response->errno) {
  //   site_log_generate("Manage Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " mobile no creation Failed [Invalid Inputs] on " . date("Y-m-d H:i:s"), '../');
  //   $json = array("status" => 0, "msg" => "Invalid Inputs. Kindly try again with the correct Inputs!");
  // }
}
// Manage Sender ID Page save_mobile_api - End

// Message Credit Page message_credit - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "message_credit") {
  site_log_generate("Message Credit Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"), '../');
  // Get data
  $txt_parent_user = htmlspecialchars(strip_tags(isset($_REQUEST['txt_parent_user']) ? $conn->real_escape_string($_REQUEST['txt_parent_user']) : ""));
  $txt_receiver_user = htmlspecialchars(strip_tags(isset($_REQUEST['txt_receiver_user']) ? $conn->real_escape_string($_REQUEST['txt_receiver_user']) : ""));
  $txt_message_count = htmlspecialchars(strip_tags(isset($_REQUEST['txt_message_count']) ? $conn->real_escape_string($_REQUEST['txt_message_count']) : ""));
  $hid_usrsmscrd_id = htmlspecialchars(strip_tags(isset($_REQUEST['hid_usrsmscrd_id']) ? $conn->real_escape_string($_REQUEST['hid_usrsmscrd_id']) : ""));
  site_log_generate("Message Credit Page : Username => " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');

  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
  }'; // exit;
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';

  // It will call "add_message_credit" API to verify, can we access for the add_message_credit list  
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url . '/list/check_available_msg',
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
  site_log_generate("Message Credit Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service 0 [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  // print_r($header);
  site_log_generate("Message Credit Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response 0 [$response] on " . date("Y-m-d H:i:s"), '../');

  $available_messages = 0;
  if ($header->response_status == 200) {
    for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
      // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
      $available_messages = $header->report[$indicator]->available_messages;
    }
  } else if ($header->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
  <? }
  // echo "==".$available_messages."==".$txt_message_count."=="; // exit;

  if ($available_messages < $txt_message_count) {
    site_log_generate("Message Credit Page : User : " . $_SESSION['yjwatsp_user_name'] . " Message Credit failed [Message credit exceeds] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Message credit exceeds. Kindly try again after refill your message Credit!!");
  } else {
    // echo "CAME"; exit;
    // To Send the request  API
    if ($hid_usrsmscrd_id != '') {
      $replace_txt = '{
        "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
        "parent_user" : "' . $txt_parent_user . '",
        "receiver_user" : "' . $txt_receiver_user . '",
        "message_count" : "' . $txt_message_count . '",
        "credit_raise_id" : "' . $hid_usrsmscrd_id . '",
        "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
      }'; // exit;
    } else {
      $replace_txt = '{
        "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
        "parent_user" : "' . $txt_parent_user . '",
        "receiver_user" : "' . $txt_receiver_user . '",
        "message_count" : "' . $txt_message_count . '",
        "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
      }'; // exit;
    }

    //add bearer token
    $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';

    // It will call "add_message_credit" API to verify, can we access for the add_message_credit list  
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $api_url . '/list/add_message_credit',
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
    site_log_generate("Message Credit Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);
    // print_r($header);
    site_log_generate("Message Credit Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

    if ($header->response_status == 200) {
      site_log_generate("Message Credit Page : " . $user_name . " Message Credit updation Success on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 1, "msg" => "Message Credit updated.");
    } else if ($header->response_status == 201) {
      site_log_generate("Message Credit Page : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 2, "msg" => $header->response_msg);
    } else {
      if ($header->response_status == 403 || $response == '') { ?>
          <script>
            window.location = "index"
          </script>
      <? }
      site_log_generate("Message Credit Page : " . $user_name . " Message Credit updation Failed [Invalid Inputs] on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 0, "msg" => "Message Credit updation failed [Invalid Inputs]. Kindly try again with the correct Inputs!");
    }
    // else {
    //   site_log_generate("Message Credit Page : " . $user_name . " Message Credit updation Failed [Invalid Inputs] on " . date("Y-m-d H:i:s"), '../');
    //   $json = array("status" => 0, "msg" => "Message Credit updation failed [Invalid Inputs]. Kindly try again with the correct Inputs!");
    // }
  }
}
// Message Credit Page message_credit - End

// Mobile number sending - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $store_call_function == "mobile_qrcode") {
  // To get
  $mobile_number = htmlspecialchars(strip_tags(isset($_REQUEST['mobile_number']) ? $conn->real_escape_string($_REQUEST['mobile_number']) : ""));
  $txt_country_code = htmlspecialchars(strip_tags(isset($_REQUEST['txt_country_code']) ? $conn->real_escape_string($_REQUEST['txt_country_code']) : ""));

  $exp1 = explode("||", $txt_country_code);
  $country_code = $exp1[1];
  $country_id = $exp1[0];

  site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query [SELECT * FROM whatsapp_config where whatspp_config_status in ('L', 'Y') and mobile_no = " . $mobile_number . " and user_id = " . $_SESSION['yjwatsp_user_id'] . " ORDER BY whatspp_config_id Asc] on " . date("Y-m-d H:i:s"), '../');
  // add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "select_query" API to verify, can we access for the select_query details 
  $curl2 = curl_init();
  curl_setopt_array(
    $curl2,
    array(
      CURLOPT_URL => $api_url . '/select_query',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
      "query":"SELECT * FROM whatsapp_config where whatspp_config_status in (\'M\') and mobile_no = \'' . $mobile_number . '\' and user_id = \'' . $_SESSION['yjwatsp_user_id'] . '\' ORDER BY whatspp_config_id Asc"
      }',
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );
  // Send the data into API and execute 
  $response2 = curl_exec($curl2);
  curl_close($curl2);
  // After got response decode the JSON result
  $obj2 = json_decode($response2);
  site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " got the response ($response2) on " . date("Y-m-d H:i:s"), '../');

  if ($obj2->num_of_rows > 0) {
    $json = array("status" => 0, "msg" => "Given Mobile No and Scanned Mobile No Mismatched!");
  } else { //otherwise
    if ($obj2->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query [SELECT * FROM whatsapp_config where whatspp_config_status in ('L', 'Y') and mobile_no = " . $mobile_number . " and user_id = " . $_SESSION['yjwatsp_user_id'] . " ORDER BY whatspp_config_id Asc] on " . date("Y-m-d H:i:s"), '../');
    //add bearer token
    $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
    // It will call "select_query" API to verify, can we access for the select_query
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $api_url . '/select_query',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
        "query":"SELECT * FROM whatsapp_config where whatspp_config_status in (\'L\', \'Y\') and mobile_no = \'' . $mobile_number . '\' and user_id = \'' . $_SESSION['yjwatsp_user_id'] . '\' ORDER BY whatspp_config_id Asc"
        }',
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'

      ),
    )
    );
    // Send the data into API and execute 
    $response = curl_exec($curl);
    curl_close($curl);
    site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query response [$response] on " . date("Y-m-d H:i:s"), '../');
    // After got response decode the JSON result
    $obj = json_decode($response);
    if ($obj->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    $vlu = 0;

    if ($obj->num_of_rows <= 0) {
      $vlu = 0;
    } else {
      $vlu = 1;
    }

    if ($vlu == 0) {
      // It will call "qrcode url" API to verify, can we access for the qrcode url
      $curl1 = curl_init();
      curl_setopt_array(
        $curl1,
        array(
          CURLOPT_URL => $qrcode_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => '{
              "mobile_number":"' . $mobile_number . '",
              "store_id":"1",
              "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
              "country_code":"' . $country_code . '",
              "country_id":"' . $country_id . '"
            }',
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'

          ),
        )
      );
      // Send the data into API and execute 
      $response1 = curl_exec($curl1);
      curl_close($curl1);

      site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query2 [{
          \"mobile_number\":" . $mobile_number . ",
          \"store_id\":\"1\",
          \"user_id\":" . $_SESSION['yjwatsp_user_id'] . ",
          \"country_code\":" . $country_code . ",
          \"country_id\":" . $country_id . "
        }] on " . date("Y-m-d H:i:s"), '../');
      // After got response decode the JSON result
      $obj1 = json_decode($response1);

      site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query2 response [$response1] on " . date("Y-m-d H:i:s"), '../');

      $_SESSION['qrcode'] = $obj1->qr_code;

      if ($obj1->response_code == 200) {
        $json = array("status" => 1, "msg" => $obj1->response_msg, "qrcode" => $obj1->qr_code);
      } else if ($obj1->response_code == 201) {
        $json = array("status" => 0, "msg" => $obj1->response_msg);
      } else if ($obj1->response_code == 203) {
        $json = array("status" => 3, "msg" => "Invalid User");
      } else {
        if ($obj1->response_status == 403 || $response == '') { ?>
              <script>
                window.location = "index"
              </script>
        <? }
        $json = array("status" => 2, "msg" => "loading");
      }
    } else {
      // It will call "select_query" API to verify, can we access for the select_query
      $curl2 = curl_init();
      curl_setopt_array(
        $curl2,
        array(
          CURLOPT_URL => $api_url . '/select_query',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => '{
          "query":"SELECT * FROM whatsapp_config where whatspp_config_status in (\'L\', \'Y\') and mobile_no = \'' . $mobile_number . '\' and user_id = \'' . $_SESSION['yjwatsp_user_id'] . '\' ORDER BY whatspp_config_id Asc"
          }',
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute 
      site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query3 [SELECT * FROM whatsapp_config where whatspp_config_status in ('L', 'Y') and mobile_no = " . $mobile_number . " and user_id = " . $_SESSION['yjwatsp_user_id'] . " ORDER BY whatspp_config_id Asc] on " . date("Y-m-d H:i:s"), '../');
      $response2 = curl_exec($curl2);
      curl_close($curl2);
      // After got response decode the JSON result
      $obj2 = json_decode($response2);
      site_log_generate("Mobile number QR Code Scan Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query3 response [$response2] on " . date("Y-m-d H:i:s"), '../');

      if ($obj2->num_of_rows > 0) { // If the response is success to execute this condition
        $json = array("status" => 1, "msg" => "QRCODE Already Scanned!");
      } else {
        if ($obj2->response_status == 403 || $response == '') { ?>
          <script>
            window.location = "index"
          </script>
        <? }
        $json = array("status" => 0, "msg" => "Kindly rescan!!");
      }
    }
  }
}

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with JSON Response
header('Content-type: application/json');
echo json_encode($json);