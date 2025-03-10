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
session_start(); // start session
error_reporting(E_ALL); // The error reporting function

include_once('../api/configuration.php'); // Include configuration.php
include_once('site_common_functions.php'); // Include site_common_functions.php

extract($_REQUEST); // Extract the request

$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // add bearer token
$current_date = date("Y-m-d H:i:s"); // to get the today date
$milliseconds = round(microtime(true) * 1000); // milliseconds in time
$default_variale_msg = '-'; // default msg

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

// Template List Page tmpl_call_function remove_template - Start
if (isset($_GET['tmpl_call_function']) == "remove_template") {
  // Get data
  $template_response_id = htmlspecialchars(strip_tags(isset($_REQUEST['template_response_id']) ? $conn->real_escape_string($_REQUEST['template_response_id']) : ""));
  $change_status = htmlspecialchars(strip_tags(isset($_REQUEST['change_status']) ? $conn->real_escape_string($_REQUEST['change_status']) : ""));
  // To Send the request  API
  $replace_txt = '{
    "template_id" : "' . $template_response_id . '",
    "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
  // add bearertoken
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "delete_template" API to verify, can we access for the delete_template
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/template/delete_template',
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
  site_log_generate("Template List Page : User : " . $_SESSION['yjwatsp_user_name'] . " send it to Service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $state1 = json_decode($response, false);
  site_log_generate("Template List Page : User : " . $_SESSION['yjwatsp_user_name'] . " get Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($state1->response_code == 1) {
    site_log_generate("Template List Page : User : " . $_SESSION['yjwatsp_user_name'] . " delete template success on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $state1->response_msg);
  } else if ($state1->response_status == 204) {
    site_log_generate("Template List Page : " . $user_name . "get the Service response [$state1->response_status] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 2, "msg" => $state1->response_msg);
  } else {
    if ($state1->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    site_log_generate("Template List Page: " . $user_name . " Template List Page [Invalid Inputs] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Template delete failure.");
  }
}
// Template List Page remove_template - End

// Compose SMS Page getSingleTemplate_meta - Start
if (isset($_GET['getSingleTemplate_meta']) == "getSingleTemplate_meta") {

  $tmpl_name = explode('!', $tmpl_name);
  $replace_txt = '';
  // Get data
  $replace_txt = '{"template_name" : "' . $tmpl_name[0] . '","template_lang" : "' . $tmpl_name[1] . '"}';
  // It will call "message_templates" API to verify, can we access for the message_templates
  $curl_get = $api_url . "/template/get_single_template";
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $curl_get,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );

  // Send the data into API and execute 
  $yjresponse = curl_exec($curl);
  curl_close($curl);

  site_log_generate("Single template Page: " . $yjresponse . "Page on " . date("Y-m-d H:i:s"), '../');

  // Decode the JSON string
  $data = json_decode($yjresponse, true);
  if ($data->response_status == 403 || $yjresponse == '') { ?>
    <script>
      window.location = "index"
    </script>
  <? }
  // Define the pattern to match the dynamic value
//$pattern = '/te_pri_([a-zA-Z0-9]+)_/';


  // Use preg_match to find the dynamic value
  // if (preg_match($pattern, $tmpl_name[0], $matches)) {
  // echo "coming";
  // The dynamic value will be in $matches[1]
  //$match_code = $matches[1];
  //}


  // Split the string by '_' delimiter
  $parts = explode('_', $tmpl_name[0]);

  // Access the third element of the resulting array
  $template_code = $parts[2];

  // Check if the 3rd, 4th, or 5th character of $template_code matches 'i', 'v', or 'd'
  if ($template_code[2] === 'i') {
    $image_code = 'i';
  } else if ($template_code[3] === 'v') {
    $video_code = 'v';
  } else if ($template_code[4] === 'd') {
    $document_code = 'd';
  }

  // Check if decoding was successful and if the required keys exist
  if ($data !== null && isset($data['data'][0]['components'])) {
    // Access components
    $componentsJson = $data['data'][0]['components'];
    $componentsJson = str_replace('\n', '<br>', $componentsJson);
    // Decode the components JSON string
    $componentsArray = json_decode($componentsJson, true);
    // print_r($componentsArray);

    // Check if decoding of components was successful
    if ($componentsArray !== null) {
      $stateData = '';
      $stateData_box = '';
      $hdr_type = '';
      $flag_media = true;
      // Access the values of the decoded array
      foreach ($componentsArray as $component) {

        if ($component['type'] === 'BODY') {
          // $component['text'] = str_replace('\\n', "\n", $component['text']);
          $hdr_type .= "<input type='hidden' style='margin-left:10px;' name='hid_txt_body_variable' id='hid_txt_body_variable' value='" . $component['text'] . "'>";

          $stateData_1 = '';
          $stateData_1 = nl2br($component['text']);
          $stateData_2 = $stateData_1;

          $matches = null;
          $prmt = preg_match_all("/{{[0-9]+}}/", $component['text'], $matches);
          $matches_a1 = $matches[0];
          rsort($matches_a1);
          sort($matches_a1);
          for ($ij = 0; $ij < count($matches_a1); $ij++) {
            // Looping the ij is less than the count of matches_a1.if the condition is true to continue the process.if the condition are false to stop the process
            $expl2 = explode("{{", $matches_a1[$ij]);
            $expl3 = explode("}}", $expl2[1]);
            $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly name='txt_body_variable[$expl3[0]][]' id='txt_body_variable' placeholder='{{" . $expl3[0] . "}} Value' maxlength='20' tabindex='12' title='Enter {{" . $expl3[0] . "}} Value' value='-' style='width:100px;height: 30px;cursor: not-allowed;margin-top:10px;' class='form-control required'> </div><div style='float: left;'>";
            $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
            $stateData_2 = $stateData_1;
          }
          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Body : </div><div style='float:left;margin-left:10px;'>" . $stateData_2 . "</div></div>";
          }
        }

        if ($component['type'] == 'HEADER') {
          $hdr_type .= "<input type='hidden' name='hid_txt_header_variable' id='hid_txt_header_variable' value='" . $component['text'] . "'>";
          $stateData_1 = '';
          $stateData_1 = $component['text'];
          $stateData_2 = $stateData_1;

          $matches = null;
          $prmt = preg_match_all("/{{[0-9]+}}/", $component['text'], $matches);
          $matches_a0 = $matches[0];
          rsort($matches_a0);
          sort($matches_a0);
          for ($ij = 0; $ij < count($matches_a0); $ij++) {
            // Looping the ii is less than the count of matches_a0.if the condition is true to continue the process.if the condition are false to stop the process
            $expl2 = explode("{{", $matches_a0[$ij]);
            $expl3 = explode("}}", $expl2[1]);
            $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly tabindex='10' name='txt_header_variable[$expl3[0]][]' id='txt_header_variable' placeholder='{{" . $expl3[0] . "}} Value' title='Header Text' maxlength='20' value='-' style='width:100px;height: 30px;cursor: not-allowed;margin-top:10px;' class='form-control required'> </div><div style='float: left;'>";
            $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
            $stateData_2 = $stateData_1;
          }

          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
        }

        if ($image_code && $flag_media) {
          $stateData .= "<div id='header' style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:70px;'>IMAGE<i class='fa fa-image' style='font-size: 20px;margin-left:10px;'></i></div>";
          $flag_media = false;
        } else if ($video_code && $flag_media) {
          $stateData .= "<div id='header' style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:70px;'>VIDEO<i class='fa fa-play-circle' style='font-size: 20px;margin-left:10px;'></i></div>";
          $flag_media = false;

        } else if ($document_code && $flag_media) {
          $stateData .= "<div id='header' style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:20px;'>DOCUMENT<i class='fa fa-file-text' style='font-size: 20px;margin-left:10px;'></i></div>";
          $flag_media = false;

        }

        if ($component['type'] === 'BUTTONS') {
          // Loop through buttons
          foreach ($component['buttons'] as $button) {

            $stateData_2 = '';
            if ($button['type'] == 'URL') {
              $stateData_2 .= "<a href='" . $button['url'] . "' target='_blank'>" . $button['text'] . "</a>";
              $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons URL : </div><div style='float:left'>" . $button['url'] . " - " . $stateData_2 . "</div></div>";
            }

            if ($button['type'] == 'PHONE_NUMBER') { // Phone number
              $stateData_2 .= $button['text'] . " - " . $button['phone_number'];
              $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Phone No. : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
            }
            // Looping the kk is less than the count of buttons.if the condition is true to continue the process.if the condition are false to stop the process
            if ($button['type'] == 'QUICK_REPLY') {
              $stateData_2 .= $button['text'];
              $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Quick Reply : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
            }

          }

        }

        if ($component['type'] === 'FOOTER') {
          $hdr_type .= "<input type='hidden' name='hid_txt_footer_variable' id='hid_txt_footer_variable' value='" . $component['text'] . "'>";

          $stateData_2 = '';
          $stateData_2 = $component['text'];

          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Footer : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
        }
      }
      site_log_generate("Compose Whatsapp Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Meta Message Template available on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 1, "msg" => $stateData . $hdr_type);

    }

  } else {
    // Handle decoding error for main JSON
    site_log_generate("Compose Whatsapp Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Message Template not available on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => '-');
  }

}
// Compose SMS Page getSingleTemplate_meta - End
$template_label = htmlspecialchars(strip_tags(isset($_REQUEST['template_label']) ? $conn->real_escape_string($_REQUEST['template_label']) : ""));

// Compose SMS Page PreviewTemplate - Start
if (isset($_GET['previewTemplate_meta']) == "previewTemplate_meta") {
  $tmpl_name = explode('!', $tmpl_name);
  print_r($tmpl_name);
  $template_name = $tmpl_name[0];

  echo ('*************'.$template_name.'********************');
  $replace_txt = '';
  // Get data
  $replace_txt = '{"template_name" : "' . $tmpltemplate_name .'"} ';
  // It will call "message_templates" API to verify, can we access for the message_templates
  $curl_get = $api_url . "/template/get_single_template";
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $curl_get,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'
      ),
    )
  );

  // Send the data into API and execute 
  $yjresponse = curl_exec($curl);
  curl_close($curl);
  echo $yjresponse;

  site_log_generate("Single template Page: " . $yjresponse . "Page on " . date("Y-m-d H:i:s"), '../');

  // Decode the JSON string
  $data = json_decode($yjresponse, true);
  if ($data->response_status == 403 || $yjresponse == '') { ?>
    <script>window.location = "index"</script>
  <? }
  // Split the string by '_' delimiter
  $parts = explode('_', $tmpl_name[0]);

  // Access the third element of the resulting array
  $template_code = $parts[2];

  // Check if the 3rd, 4th, or 5th character of $template_code matches 'i', 'v', or 'd'
  if ($template_code[2] === 'i') {
    $image_code = 'i';
  } else if ($template_code[3] === 'v') {
    $video_code = 'v';
  } else if ($template_code[4] === 'd') {
    $document_code = 'd';
  }

  // Check if decoding was successful and if the required keys exist
  if ($data !== null && isset($data['data'][0]['components'])) {
    // Access components
    $componentsJson = $data['data'][0]['components'];
    $componentsJson = str_replace('\n', '<br>', $componentsJson);
    // Decode the components JSON string
    $componentsArray = json_decode($componentsJson, true);
    // print_r($componentsArray);

    // Check if decoding of components was successful
    if ($componentsArray !== null) {
      $stateData = '';
      $stateData_box = '';
      $hdr_type = '';
      $flag_media = true;
      // Access the values of the decoded array
      foreach ($componentsArray as $component) {

        if ($component['type'] === 'BODY') {
          // $component['text'] = str_replace('\\n', "\n", $component['text']);
          $hdr_type .= "<input type='hidden' style='margin-left:10px;' name='hid_txt_body_variable' id='hid_txt_body_variable' value='" . $component['text'] . "'>";

          $stateData_1 = '';
          $stateData_1 = nl2br($component['text']);
          $stateData_2 = $stateData_1;

          $matches = null;
          $prmt = preg_match_all("/{{[0-9]+}}/", $component['text'], $matches);
          $matches_a1 = $matches[0];
          rsort($matches_a1);
          sort($matches_a1);
          for ($ij = 0; $ij < count($matches_a1); $ij++) {
            // Looping the ij is less than the count of matches_a1.if the condition is true to continue the process.if the condition are false to stop the process
            $expl2 = explode("{{", $matches_a1[$ij]);
            $expl3 = explode("}}", $expl2[1]);
            $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly name='txt_body_variable[$expl3[0]][]' id='txt_body_variable' placeholder='{{" . $expl3[0] . "}} Value' maxlength='20' tabindex='12' title='Enter {{" . $expl3[0] . "}} Value' value='-' style='width:100px;height: 30px;cursor: not-allowed;margin-top:10px;' class='form-control required'> </div><div style='float: left;'>";
            $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
            $stateData_2 = $stateData_1;
          }
          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Body : </div><div style='float:left;margin-left:10px;'>" . $stateData_2 . "</div></div>";
          }
        }

        if ($component['type'] == 'HEADER') {
          $hdr_type .= "<input type='hidden' name='hid_txt_header_variable' id='hid_txt_header_variable' value='" . $component['text'] . "'>";

          $stateData_1 = '';
          $stateData_1 = $component['text'];
          $stateData_2 = $stateData_1;

          $matches = null;
          $prmt = preg_match_all("/{{[0-9]+}}/", $component['text'], $matches);
          $matches_a0 = $matches[0];
          rsort($matches_a0);
          sort($matches_a0);
          for ($ij = 0; $ij < count($matches_a0); $ij++) {
            // Looping the ii is less than the count of matches_a0.if the condition is true to continue the process.if the condition are false to stop the process
            $expl2 = explode("{{", $matches_a0[$ij]);
            $expl3 = explode("}}", $expl2[1]);
            $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly tabindex='10' name='txt_header_variable[$expl3[0]][]' id='txt_header_variable' placeholder='{{" . $expl3[0] . "}} Value' title='Header Text' maxlength='20' value='-' style='width:100px;height: 30px;cursor: not-allowed;margin-top:10px;' class='form-control required'> </div><div style='float: left;'>";
            $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
            $stateData_2 = $stateData_1;
          }

          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
        }

        if ($image_code && $flag_media) {
          $stateData .= "<div id='header' style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:70px;'>IMAGE<i class='fa fa-image' style='font-size: 20px;margin-left:10px;'></i></div>";
          $flag_media = false;
        } else if ($video_code && $flag_media) {
          $stateData .= "<div id='header' style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:70px;'>VIDEO<i class='fa fa-play-circle' style='font-size: 20px;margin-left:10px;'></i></div>";
          $flag_media = false;

        } else if ($document_code && $flag_media) {
          $stateData .= "<div id='header' style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:20px;'>DOCUMENT<i class='fa fa-file-text' style='font-size: 20px;margin-left:10px;'></i></div>";
          $flag_media = false;

        }

        if ($component['type'] === 'BUTTONS') {
          // Loop through buttons
          foreach ($component['buttons'] as $button) {

            $stateData_2 = '';
            if ($button['type'] == 'URL') {
              $stateData_2 .= "<a href='" . $button['url'] . "' target='_blank'>" . $button['text'] . "</a>";
              $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons URL : </div><div style='float:left'>" . $button['url'] . " - " . $stateData_2 . "</div></div>";
            }

            if ($button['type'] == 'PHONE_NUMBER') { // Phone number
              $stateData_2 .= $button['text'] . " - " . $button['phone_number'];
              $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Phone No. : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
            }
            // Looping the kk is less than the count of buttons.if the condition is true to continue the process.if the condition are false to stop the process
            if ($button['type'] == 'QUICK_REPLY') {
              $stateData_2 .= $button['text'];
              $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Quick Reply : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
            }

          }

        }

        if ($component['type'] === 'FOOTER') {
          $hdr_type .= "<input type='hidden' name='hid_txt_footer_variable' id='hid_txt_footer_variable' value='" . $component['text'] . "'>";

          $stateData_2 = '';
          $stateData_2 = $component['text'];

          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Footer : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
        }
      }

      site_log_generate("Compose Whatsapp Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Meta Message Template available on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 1, "msg" => $stateData . $hdr_type);

    }
  } else {
    // Handle decoding error for main JSON
    site_log_generate("Compose Whatsapp Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Message Template not available on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => '-');
  }
}
// Compose SMS Page PreviewTemplate - End

// Compose SMS Page validateMobno - Start
if (isset($_POST['validateMobno']) == "validateMobno") {
  // Get data
  $mobno = str_replace('"', '', htmlspecialchars(strip_tags(isset($_POST['mobno']) ? $conn->real_escape_string($_POST['mobno']) : "")));
  $dup = htmlspecialchars(strip_tags(isset($_POST['dup']) ? $conn->real_escape_string($_POST['dup']) : ""));
  $inv = htmlspecialchars(strip_tags(isset($_POST['inv']) ? $conn->real_escape_string($_POST['inv']) : ""));
  // To validate the mobile number
  $mobno = str_replace('\n', ',', $mobno);
  $newline = explode('\n', $mobno);
  $correct_mobno_data = [];
  $return_mobno_data = '';
  $issu_mob = '';
  $cnt_vld_no = 0;
  $max_vld_no = 2000000;
  for ($i = 0; $i < count($newline); $i++) {
    // Looping the i is less than the count of newline.if the condition is true to continue the process.if the condition are false to stop the process
    $expl = explode(",", $newline[$i]);
    // Looping  with in the looping the ij is less than the count of expl.if the condition is true to continue the process.if the condition are false to stop the process
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
  $json = array("status" => 1, "msg" => $return_mobno_data . "||" . $issu_mob);
}
// Compose SMS Page validateMobno - End

// Compose Whatsapp Page compose_whatsapp - Start
if ($_SERVER['REQUEST_METHOD'] == "GET" and $tmpl_call_function == "compose_whatsapp") {
  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [GET NOT ALLOWED] on " . date("Y-m-d H:i:s"), '../');
  $json = array("status" => 0, "msg" => "Get Method not allowed here!");
}
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "compose_whatsapp") {
  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');
  // Get data
  $txt_list_mobno = htmlspecialchars(strip_tags(isset($_REQUEST['txt_list_mobno']) ? $_REQUEST['txt_list_mobno'] : ""));
  $media_type = htmlspecialchars(strip_tags(isset($_REQUEST['media_type']) ? $_REQUEST['media_type'] : ""));
  $filename_upload = htmlspecialchars(strip_tags(isset($_REQUEST['filename_upload']) ? $_REQUEST['filename_upload'] : ""));
  $total_mobileno_count = htmlspecialchars(strip_tags(isset($_REQUEST['total_mobilenos_count']) ? $_REQUEST['total_mobilenos_count'] : ""));

  if (isset($txt_header_variable)) {
    for ($i1 = 1; $i1 <= count($txt_header_variable); $i1++) {
      // Looping the i1 is less than the count of txt_header_variable.if the condition is true to continue the process.if the condition are false to stop the process
      $stateData_1 = '';
      $stateData_1 = $hid_txt_header_variable;

      $matches = null;
      $prmt = preg_match_all("/{{[0-9]+}}/", $hid_txt_header_variable, $matches);
      $matches_a0 = $matches[0];
      rsort($matches_a0);
      sort($matches_a0);
      // Looping  with in the looping the ij is less than the count of matches_a0.if the condition is true to continue the process.if the condition are false to stop the process
      for ($ij = 0; $ij < count($matches_a0); $ij++) {
        $expl2 = explode("{{", $matches_a0[$ij]);
        $expl3 = explode("}}", $expl2[1]);
        $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $txt_header_variable[$i1][0], $stateData_1);
      }
      $header_details = $stateData_1;
    }
  }

  $sendto_api = '{';

  $matches_a1 = [];
  if (isset($txt_body_variable)) {

    $file_location = $full_pathurl . "uploads/compose_variables/" . $filename_upload;
    $file_basename = basename($file_location);
    if ($file_basename === false) {
      $json = array("status" => 2, "msg" => "Error occurred while extracting file name!");
    }

    $sendto_api .= '              
      "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
    "file_location": "' . $file_location . '",';

  } else if ($media_type) {

    $file_location = $full_pathurl . "uploads/compose_variables/" . $filename_upload;
    $file_basename = basename($file_location);
    if ($file_basename === false) {
      $json = array("status" => 2, "msg" => "Error occurred while extracting file name!");
    }

    $sendto_api .= '              
      "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
    "file_location": "' . $file_location . '",';
  } else if (!isset($txt_body_variable) && !$media_type && !isset($txt_header_variable) && !$filename_upload) {
    // Receiver Mobile Numbers
    $newline1 = explode("\n", $txt_list_mobno);
    $receive_mobile_nos = '';
    $cnt_mob_no = count($newline1);
    for ($i1 = 0; $i1 < count($newline1); $i1++) {
      // Looping the i1 is less than the count of newline1.if the condition is true to continue the process.if the condition are false to stop the process
      $expl1 = explode(",", $newline1[$i1]);
      for ($ij1 = 0; $ij1 < count($expl1); $ij1++) {
        // Looping  with in the looping the ij1 is less than the count of expl1.if the condition is true to continue the process.if the condition are false to stop the process
        if (validate_phone_number($expl1[$ij1])) {
          $mblno[] = $expl1[$ij1];
          $receive_mobile_nos .= $expl1[$ij1] . ',';
        }
      }
      $ttl_sms_cnt = count($mblno);
    }
    // Remove trailing comma from the string of mobile numbers
    $receive_mobile_nos = rtrim($receive_mobile_nos, ",");
    // Split the string into an array of mobile numbers
    $numbersArray = explode(',', $receive_mobile_nos);
    $numbersArray = array_filter($numbersArray, function ($value) {
      return isset($value) && $value !== '';
    });
    $data = [];
    // rap each mobile number in a separate array element
    foreach ($numbersArray as $number) {
      if (!empty($number)) {
        $data[] = [$number];
      }
    }
    // Name of the CSV file to be created
    $filename = $_SESSION['yjwatsp_user_id'] . "_csv_" . $milliseconds . ".csv";
    // Define file locations
    $file_location = $full_pathurl . "uploads/compose_variables/" . $filename;
    $file_basename = basename($file_location);
    if ($file_basename === false) {
      $json = array("status" => 2, "msg" => "Error occurred while extracting file name!");
    }
    $location = "../uploads/compose_upload_files/" . $filename;
    $location_1 = "../uploads/compose_variables/" . $filename;
    // Open file pointer in write mode
    $file = fopen($location, 'w');
    // Write data to CSV file
    foreach ($data as $row) {
      fputcsv($file, $row);
    }
    // Close file pointer
    fclose($file);
    // Copy the file to backup location
    if (copy($location, $location_1)) {
      // Set file permissions
      chmod($location_1, 0777);
      // Log the operation
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . $location_1 . " File uploaded successfully " . date("Y-m-d H:i:s"), '../');
    } else {
      site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Failed to copy the uploaded file to backup location " . date("Y-m-d H:i:s"), '../');
    }
    $sendto_api .= '          
  "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
  "file_location": "' . $file_location . '",';
    $ttl_sms_cnt = count($mblno);
  } else {
    if (!isset($txt_body_variable) && !$media_type && !isset($txt_header_variable)) {

      $file_location = $full_pathurl . "uploads/compose_variables/" . $filename_upload;
      $file_basename = basename($file_location);
      if ($file_basename === false) {
        $json = array("status" => 2, "msg" => "Error occurred while extracting file name!");
      }

      $sendto_api .= '              
        "user_id":"' . $_SESSION['yjwatsp_user_id'] . '",
      "file_location": "' . $file_location . '",';

    }
  }


  // Get data
  $chk_remove_duplicates = htmlspecialchars(strip_tags(isset($_REQUEST['chk_remove_duplicates']) ? $_REQUEST['chk_remove_duplicates'] : ""));
  $chk_remove_invalids = htmlspecialchars(strip_tags(isset($_REQUEST['chk_remove_invalids']) ? $_REQUEST['chk_remove_invalids'] : ""));
  $id_slt_contgrp = htmlspecialchars(strip_tags(isset($_REQUEST['id_slt_contgrp']) ? $_REQUEST['id_slt_contgrp'] : "0"));
  $txt_sms_type = htmlspecialchars(strip_tags(isset($_REQUEST['txt_sms_type']) ? $_REQUEST['txt_sms_type'] : "TEXT"));
  $txt_sms_type = strtoupper($txt_sms_type);
  $country_code = '';
  $mime_type = '';
  $id_slt_mobileno = htmlspecialchars(strip_tags(isset($_REQUEST['id_slt_mobileno']) ? $_REQUEST['id_slt_mobileno'] : "0"));
  $expl_id_slt_mobileno = explode('||', $id_slt_mobileno);
  $id_slt_mobileno = $expl_id_slt_mobileno[2];
  $wht_tmplsend_url = $expl_id_slt_mobileno[3];
  $wht_tmpl_url = $expl_id_slt_mobileno[1];
  $wht_bearer_token = $expl_id_slt_mobileno[0];
  $filename = '';

  // Get data
  $txt_sms_content = htmlspecialchars(strip_tags(isset($_REQUEST['txt_sms_content']) ? $_REQUEST['txt_sms_content'] : ""));
  $txt_caption = htmlspecialchars(strip_tags(isset($_REQUEST['txt_caption']) ? $_REQUEST['txt_caption'] : "Media"));
  $txt_char_count = htmlspecialchars(strip_tags(isset($_REQUEST['txt_char_count']) ? $_REQUEST['txt_char_count'] : "1"));
  $txt_sms_count = htmlspecialchars(strip_tags(isset($_REQUEST['txt_sms_count']) ? $_REQUEST['txt_sms_count'] : "1"));
  $txt_rcscard_title = htmlspecialchars(strip_tags(isset($_REQUEST['txt_rcscard_title']) ? $_REQUEST['txt_rcscard_title'] : ""));
  $chk_save_contact_group = htmlspecialchars(strip_tags(isset($_REQUEST['chk_save_contact_group']) ? $_REQUEST['chk_save_contact_group'] : ""));

  $expl_wht = explode("~~", $txt_whatsapp_mobno[0]);
  $storeid = $expl_wht[0];
  $confgid = $expl_wht[1];

  $txt_sms_content = substr($txt_sms_content, 0, 1000);

  $txt_char_count = strlen($txt_sms_content);
  $txt_sms_count = ceil($txt_char_count / 160);


  $usr_id = $_SESSION['yjwatsp_user_id'];
  // To Send the request  API
  $replace_txt = '{
    "user_id" : "' . $usr_id . '"
  }';
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "available_credits" API to verify, can we access for the available_credits
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/available_credits',
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
  site_log_generate("Compose Whatsapp Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Compose Whatsapp Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');


  if ($header->num_of_rows > 0) {
    for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
      // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
      $alotsms = $header->report[$indicator]->available_messages;
      $expdate = date("Y-m-d H:i:s", strtotime($header->report[$indicator]->expiry_date));
    }
  } else if ($header->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
  <? } else {
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


  // allocate the credits 
  if ($alotsms == 0 and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Whatsapp Credits are not available..] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Whatsapp Credits are not available. Kindly verify!!");
  } elseif ($alotsms < $ttl_sms_cnt and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Whatsapp Credits are not available.] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Whatsapp Credits are not available. Kindly verify!!");
  } elseif ($txt_char_count > 1000) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Morethan 1000 characters are not allowed for Whatsapp] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Morethan 1000 characters are not allowed for Whatsapp. Kindly verify!!");
  } elseif ($expdate == '' and $_SESSION['yjwatsp_user_master_id'] != 1) {
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Validity Period Expired.] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Validity Period Expired. Kindly verify!");
  } elseif (strtotime($expdate) < strtotime($current_date) and $_SESSION['yjwatsp_user_master_id'] != 1) {

    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Compose Whatsapp failed [Validity Period Expired..] on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => "Validity Period Expired. Kindly verify!!");
  } else { // otherwise

  }

  $expld1 = explode("!", $slt_whatsapp_template);
  $sendto_api .= '"template_id":"' . $expld1[3] . '",
                        "total_mobileno_count":"' . $total_mobileno_count . '",
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
                      }';

  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " api send text [$sendto_api] on " . date("Y-m-d H:i:s"), '../');
  // add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "compose_whatsapp_message" API to verify, can we access for thecompose_whatsapp_message
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/compose_rcs_message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $sendto_api,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        "cache-control: no-cache",
        'Content-Type: application/json; charset=utf-8'

      ),
    )
  );
  // Send the data into API and execute 
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $respobj = json_decode($response);
  site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " api send text - Response [$response] on " . date("Y-m-d H:i:s"), '../');

  $rsp_id = $respobj->response_status;
  if ($respobj->data[0] != '') {
    $rsp_msg_1 = strtoupper($respobj->data[0]);
  } else {
    $rsp_msg = strtoupper($respobj->response_msg);
  }

  if ($rsp_id == 203) {
    $json = array("status" => 2, "msg" => "Invalid User, Kindly try again with Valid User!!");
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [Invalid User, Kindly try again with Valid User!!] on " . date("Y-m-d H:i:s"), '../');
  } else if ($rsp_id == 201) {
    $json = array("status" => 0, "msg" => "Failure - $rsp_msg");
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [Failure - $rsp_msg] on " . date("Y-m-d H:i:s"), '../');
  } else {
    if ($respobj->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
    <? }
    $json = array("status" => 1, "msg" => "Campaign Created Successfully!!");
    site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " [Success] on " . date("Y-m-d H:i:s"), '../');
  }

}

// Compose Whatsapp Page compose_whatsapp - End

// Create Template create_template - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $temp_call_function == "create_template_28") {
  // Get data
  $categories = htmlspecialchars(strip_tags(isset($_REQUEST['categories']) ? $conn->real_escape_string($_REQUEST['categories']) : ""));
  /* $textarea = htmlspecialchars(strip_tags(isset($_REQUEST['textarea']) ? $conn->real_escape_string($_REQUEST['textarea']) : ""));
   $textarea = str_replace("'", "\'", $textarea);
   $textarea = str_replace('"', '\"', $textarea);
   $textarea = str_replace("\\r\\n", '\n', $textarea);
   $textarea = str_replace('&amp;', '&', $textarea);
   $textarea = str_replace(PHP_EOL, '\n', $textarea);*/

  // $txt_header_name = htmlspecialchars(strip_tags(isset($_REQUEST['txt_header_name']) ? $conn->real_escape_string($_REQUEST['txt_header_name']) : ""));
  // $txt_footer_name = htmlspecialchars(strip_tags(isset($_REQUEST['txt_footer_name']) ? $conn->real_escape_string($_REQUEST['txt_footer_name']) : ""));
  // $media_category = htmlspecialchars(strip_tags(isset($_REQUEST['media_category']) ? $conn->real_escape_string($_REQUEST['media_category']) : ""));
  // $txt_header_variable = htmlspecialchars(strip_tags(isset($_REQUEST['txt_header_variable']) ? $conn->real_escape_string($_REQUEST['txt_header_variable']) : ""));


  $template_label = htmlspecialchars(strip_tags(isset($_REQUEST['template_label']) ? $conn->real_escape_string($_REQUEST['template_label']) : ""));
  $campaign_type = htmlspecialchars(strip_tags(isset($_REQUEST['campaign_type']) ? $conn->real_escape_string($_REQUEST['campaign_type']) : ""));
  $communication_type = htmlspecialchars(strip_tags(isset($_REQUEST['header']) ? $conn->real_escape_string($_REQUEST['header']) : ""));

  $msg_content = htmlspecialchars(strip_tags(isset($_REQUEST['msg_content']) ? $conn->real_escape_string($_REQUEST['msg_content']) : ""));
  $msg_content = urldecode($conn->real_escape_string($_REQUEST['msg_content']));
  $textarea = str_replace("'", "\'", $msg_content);
  $msg_content = str_replace('"', '\"', $msg_content);
  $msg_content = str_replace("\\r\\n", '\n', $msg_content);
  $msg_content = str_replace('&amp;', '&', $msg_content);
  $msg_content = str_replace(PHP_EOL, '\n', $msg_content);
  $msg_content = str_replace('\\&quot;', '"', $msg_content);
  $msg_content = str_replace('"', '\"', $msg_content);

  $suggestion_box = htmlspecialchars(strip_tags(isset($_REQUEST['suggestion_box']) ? $conn->real_escape_string($_REQUEST['suggestion_box']) : ""));
  $suggestion_count = htmlspecialchars(strip_tags(isset($_REQUEST['suggestion_count']) ? $conn->real_escape_string($_REQUEST['suggestion_count']) : ""));
  $txt_msg = htmlspecialchars(strip_tags(isset($_REQUEST['txt_msg']) ? $conn->real_escape_string($_REQUEST['txt_msg']) : ""));


  if (isset($_POST['txt_msg'])) {
    // Get the JSON string from the 'txt_msg' key
    $jsonString = $_POST['txt_msg'];
    // Decode the JSON string into an associative array
    $jsonArray = json_decode($jsonString, true);
  }



  if ($_FILES["rich_card_media"]["name"] != '') {
    $image_size = $_FILES['rich_card_media']['size'];
    $image_type = $_FILES['rich_card_media']['type'];
    $file_type = explode("/", $image_type);

    $filename = $_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $file_type[1];
    $location = "../uploads/whatsapp_images/" . $filename;
    $location_1 = $site_url . "uploads/whatsapp_images/" . $filename;
    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);

    $rspns = '';
    if (move_uploaded_file($_FILES['rich_card_media']['tmp_name'], $location)) {
      $rspns = $location;
      // Add the 'media_file' key to the first element of the JSON array
      $jsonArray[0]['media_file'] = $location_1;  // Push the media_file path

      site_log_generate("Create Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
    }
  }

        // Re-encode the modified array back to a JSON string without escaping slashes
        $newJsonString = json_encode($jsonArray, JSON_UNESCAPED_SLASHES);


  // Add Bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "messenger_view_response" API to verify, can we access for the messenger view response
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $template_get_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
"templatelabel" : "' . $template_label . '",
"campaigntype" : "' . $campaign_type . '",
"communicationType" : "' . $communication_type . '",
"messageContent" : ' . $newJsonString . ',
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
}',
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'

      ),
    )
  );

  $log_4 = '{
"templatelabel" : "' . $template_label . '",
"campaigntype" : "' . $campaign_type . '",
"communicationType" : "' . $communication_type . '",
"messageContent" : ' .$newJsonString  . ',
"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
}'; // Send the data into API and execute 

  site_log_generate("Create Template Page : " . $_SESSION['yjwatsp_user_name'] . " executed the log ($log_4) on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  site_log_generate("Create Template Page : " . $_SESSION['yjwatsp_user_name'] . " executed the response ($response) on " . date("Y-m-d H:i:s"), '../');
  // After got response decode the JSON result
  $obj = json_decode($response);
  if ($obj->response_status == 200) { //success
    $json = array("status" => 1, "msg" => $obj->response_msg);
  } else {
    if ($obj->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    $json = array("status" => 0, "msg" => $obj->response_msg);
  }
}


// Create Template create_template - End



// Create Template create_template - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $temp_call_function == "create_template") {
  // Get data
  $categories = htmlspecialchars(strip_tags(isset($_REQUEST['categories']) ? $conn->real_escape_string($_REQUEST['categories']) : ""));
  $template_label = htmlspecialchars(strip_tags(isset($_REQUEST['template_label']) ? $conn->real_escape_string($_REQUEST['template_label']) : ""));
  $campaign_type = htmlspecialchars(strip_tags(isset($_REQUEST['campaign_type']) ? $conn->real_escape_string($_REQUEST['campaign_type']) : ""));
  $communication_type = htmlspecialchars(strip_tags(isset($_REQUEST['header']) ? $conn->real_escape_string($_REQUEST['header']) : ""));

  $suggestion_box = htmlspecialchars(strip_tags(isset($_REQUEST['suggestion_box']) ? $conn->real_escape_string($_REQUEST['suggestion_box']) : ""));
  $suggestion_count = htmlspecialchars(strip_tags(isset($_REQUEST['suggestion_count']) ? $conn->real_escape_string($_REQUEST['suggestion_count']) : ""));
  $txt_msg = htmlspecialchars(strip_tags(isset($_REQUEST['txt_msg']) ? $conn->real_escape_string($_REQUEST['txt_msg']) : ""));
$rich_card_media_type=htmlspecialchars(strip_tags(isset($_REQUEST['rich_card_media_type']) ? $conn->real_escape_string($_REQUEST['rich_card_media_type']) : ""));

  if (isset($_POST['txt_msg'])) {
    // Get the JSON string from the 'txt_msg' key
    $jsonString = $_POST['txt_msg'];
    // Decode the JSON string into an associative array
    $jsonArray = json_decode($jsonString, true);
  }
  if ($communication_type === "RICH CARD") {
  }

  if ($_FILES["rich_card_media"]["name"] != '') {
    $image_type = $_FILES['rich_card_media']['type'];
    $image_size = $_FILES['rich_card_media']['size'];
    $image_type = $_FILES['rich_card_media']['type'];
    $file_type = explode("/", $image_type);

    $filename = $_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $file_type[1];
    $location = "../uploads/whatsapp_images/" . $filename;
    $location_1 = $site_url . "uploads/whatsapp_images/" . $filename;
    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);

    $rspns = '';
    if (move_uploaded_file($_FILES['rich_card_media']['tmp_name'], $location)) {
      $rspns = $location;
      // Add the 'media_file' key to the first element of the JSON array
      $jsonArray[0]['media_file'] = $location_1;  // Push the media_file path

      site_log_generate("Create Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
    }
  }

 
// Check if files have been uploaded
if (isset($_FILES['carousel_media'])) {
  $totalFiles = count($_FILES['carousel_media']['name']);
  $uploadedFiles = [];

  for ($i = 0; $i < $totalFiles; $i++) {
      // Check if the file was uploaded without errors
      if ($_FILES['carousel_media']['error'][$i] == UPLOAD_ERR_OK) {
          $image_size = $_FILES['carousel_media']['size'][$i];
          $image_type = $_FILES['carousel_media']['type'][$i];
          $file_type = explode("/", $image_type);

          // Create a unique filename
          $filename = $_SESSION['yjwatsp_user_id'] . "_" . microtime(true) . "." . $file_type[1];
          $location = "../uploads/whatsapp_images/" . $filename;
          $location_1 = $site_url . "uploads/whatsapp_images/" . $filename;

          // Check file extension and validate as needed
          $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
          $imageFileType = strtolower($imageFileType);

          // Move the file to the designated location
          if (move_uploaded_file($_FILES['carousel_media']['tmp_name'][$i], $location)) {
              $uploadedFiles[] = $location_1; // Store file path for response or further processing
              
              // Find the correct position in the JSON array to update
              foreach ($jsonArray as &$carouselItem) {
                  foreach ($carouselItem as &$item) {
                      if (empty($item['media_file'])) {
                          $item['media_file'] = $location_1;
                          // break 2; // Break out of both foreach loops
                      }
                  }
              }

              site_log_generate("Create Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
          } else {
              $json = array("status" => 0, "msg" => "Failed to upload file: " . $_FILES['carousel_media']['name'][$i]);
          }
      } else {
          $json = array("status" => 0, "msg" => "Error uploading file: " . $_FILES['carousel_media']['name'][$i]);
         
      }
  }

} else {
  $json = array("status" => 0, "msg" => "No files were uploaded.");
}

        // Re-encode the modified array back to a JSON string without escaping slashes
        $newJsonString = json_encode($jsonArray, JSON_UNESCAPED_SLASHES);


  // Add Bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  $replace_txt .= '{
    "templatelabel" : "' . $template_label . '",
    "campaigntype" : "' . $campaign_type . '",
    "communicationType" : "' . $communication_type . '",
    "messageContent" : ' . $newJsonString . ',
    "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"';

if ($communication_type == "RICH CARD") {
    $replace_txt .= ', "media_type": "' . strtolower($rich_card_media_type) . '"';
}

$replace_txt .= '
}';
  // It will call "messenger_view_response" API to verify, can we access for the messenger view response
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $template_get_url,
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
    )
  );


  site_log_generate("Create Template Page : " . $_SESSION['yjwatsp_user_name'] . " executed the log ($replace_txt) on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  // echo $response;
  curl_close($curl);
  site_log_generate("Create Template Page : " . $_SESSION['yjwatsp_user_name'] . " executed the response ($response) on " . date("Y-m-d H:i:s"), '../');
  // After got response decode the JSON result
  $obj = json_decode($response);
  if ($obj->response_status == 200) { //success
    $json = array("status" => 1, "msg" => $obj->response_msg);
  } else {
    if ($obj->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    $json = array("status" => 0, "msg" => $obj->response_msg);
  }
}
// Create Template create_template - End










// copy_file Page copy_file - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $storecopy_file == "copy_file") {
  site_log_generate("copy_file Page : User : " . $_SESSION['yjwatsp_user_name'] . " " . date("Y-m-d H:i:s"), '../');
  // Check if the request contains the copied file

  if (isset($_FILES['copiedFile']) && $_FILES['copiedFile']['error'] === UPLOAD_ERR_OK) {

    // Get the file information
    $path_parts = pathinfo($_FILES["copiedFile"]["name"]);
    $extension = $path_parts['extension'];
    $filename = $_SESSION['yjwatsp_user_id'] . "_csv_" . $milliseconds . "." . $extension;
    /* Location */
    $location = "../uploads/compose_upload_files/" . $filename;
    $file_location = $full_pathurl . "uploads/compose_upload_files/" . $filename;

    $location_1 = "../uploads/compose_variables/" . $filename;
    $file_location_1 = $full_pathurl . "uploads/compose_variables/" . $filename;

    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);
    /* Valid extensions */
    $valid_extensions = array("csv");
    $response = 0;
    /* Check file extension */
    if (in_array(strtolower($imageFileType), $valid_extensions)) {
      /* Upload file */
      if (move_uploaded_file($_FILES['copiedFile']['tmp_name'], $location)) {
        // Copy the file to backup location
        if (copy($location, $location_1)) {
          $response = $location; // You can set this to any of the locations
          $response = $location_1;
          // Set file permissions
          chmod($filename, 0777);
          $csvFile = fopen($location, 'r') or die("can't open file");
          $json = array("status" => 1, "msg" => "File uploaded successfully", "file_location" => $file_location);
        } else {
          $json = array("status" => 0, "msg" => "Failed to copy the uploaded file to backup location");
        }
      } else {
        $json = array("status" => 0, "msg" => "Failed to move the uploaded file");
      }
    } else {
      $json = array("status" => 0, "msg" => "Invalid file extension. Only CSV files are allowed.");
    }
  } else {
    $json = array("status" => 0, "msg" => "No file uploaded or an error occurred during upload");
  }
  // Output JSON response
  header('Content-Type: application/json');
}
// copy_file copy_file - end

//chatbot flow - start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "chat_bot") {

  $start_name = htmlspecialchars(strip_tags(isset($_REQUEST['start_name']) ? $conn->real_escape_string($_REQUEST['start_name']) : ""));
  $restart_name = htmlspecialchars(strip_tags(isset($_REQUEST['restart_name']) ? $conn->real_escape_string($_REQUEST['restart_name']) : ""));
  $invalid_name = htmlspecialchars(strip_tags(isset($_REQUEST['invalid_name']) ? $conn->real_escape_string($_REQUEST['invalid_name']) : ""));

  $button_txt_st = htmlspecialchars(strip_tags(isset($_REQUEST['button_txt_st']) ? $conn->real_escape_string($_REQUEST['button_txt_st']) : ""));
  $txtarea_msg_st = htmlspecialchars(strip_tags(isset($_REQUEST['txtarea_msg']) ? $conn->real_escape_string($_REQUEST['txtarea_msg']) : ""));
  $txtarea_reply_st = htmlspecialchars(strip_tags(isset($_REQUEST['txtarea_reply']) ? $conn->real_escape_string($_REQUEST['txtarea_reply']) : ""));
  $txtarea_list_st = htmlspecialchars(strip_tags(isset($_REQUEST['txtarea_list_st']) ? $conn->real_escape_string($_REQUEST['txtarea_list_st']) : ""));

  $reply_array = array();
  $reply_array_add = array();
  // $list_array = array();

  foreach ($bot_array as $bot_array1) {
    $bot_array_list .= '"' . $bot_array1 . '"';
  }

  $chatbot_array = explode(',', $bot_array);

  // print_r($myArray[0]);

  foreach ($textarea_reply as $text_area_rpy) {
    $text_area_reply .= '"' . $text_area_rpy . '"';

  }
  foreach ($textarea_list as $text_area_lt) {
    $text_area_list .= '"' . $text_area_lt . '"';

  }

  foreach ($list_button as $list_btn) {
    $list_btn_txt .= '"' . $list_btn . '"';

  }

  foreach ($button_txt as $button_text) {
    $button_text_list .= '"' . $button_text . '"';

  }

  $reply_trim_adding = '';



  // Looping the i is less than the reply_button_st.if the condition is true to continue the process.if the condition are false to stop the process
  for ($i = 0; $i < count($reply_button_st); $i++) {
    $reply .= '{
      "type": "reply",
      "reply": {
          "id": "' . $reply_button_st[$i] . '",
          "title": "' . $reply_button_st[$i] . '"
      }
     },';
    // echo $reply;
  }

  $reply_trim = trim($reply, ",");

  if ($list_button_st) {
    for ($i = 0; $i < count($list_button_st); $i++) {
      // Looping the i is less than the list_button_st.if the condition is true to continue the process.if the condition are false to stop the process
      $list .= '{
      "id": "' . $list_button_st[$i] . '",
      "title": "' . $list_button_st[$i] . '"
  },';


    }
  }
  $list_trim = trim($list, ",");


  $textarea = str_replace("'", "\'", $textarea);
  $textarea = str_replace('"', '\"', $textarea);

  $chat_bot = array();


  $chat_bot_send = '';
  $chat_bot_send .= '[
		';

  if ($txtarea_msg_st || $txtarea_reply_st || $txtarea_list_st) {

    switch ($txtarea_msg_st || $txtarea_reply_st || $txtarea_list_st) {
      case $txtarea_msg_st:
        $chat_bot_send .= ' { 
        "id": "1",
        "parent": [
               "0"
       ],
       "pattern": "/' . $start_name . '/",
       "type": ["text"],
       "message": [
         {
                 "type": "text",
                 "text": {
                         "body": "' . $txtarea_msg_st . '"
                 }
         }
        ],
       "restart": [
        {
            "type": "text",
            "text": {
                "body": "' . $restart_name . '"
            }
        }
    ],
    "invalid": [
        {
            "type": "text",
            "text": {
                "body": "' . $invalid_name . '"
            }
        }
    ]
    },';
        break;
      case $txtarea_reply_st:
        $chat_bot_send .= ' { 
          "id": "1",
          "parent": [
                 "0"
         ],
         "pattern": "/' . $start_name . '/",
         "type":["text"],
         "message": [
          {
              "type": "interactive",
              "interactive": {
                  "type": "button",
                  "body": {
                      "text": "' . $txtarea_reply_st . '"
                  },
                  "action": {
                      "buttons":[' . $reply_trim . '] 
          }
        }
      }
       ],
      "restart": [
        {
            "type": "text",
            "text": {
                "body": "' . $restart_name . '"
            }
        }
    ],
    "invalid": [
        {
            "type": "text",
            "text": {
                "body": "' . $invalid_name . '"
            }
        }
    ]
},';
        break;
      case $txtarea_list_st:
        $chat_bot_send .= ' { 
            "id": "1",
            "parent": [
                   "0"
           ],
           "pattern": "/' . $start_name . '/",
           "type": ["text"],
           "message": [
            {
                "type": "interactive",
                "interactive": {
                    "type": "list",
                    "body": {
                        "text":  "' . $txtarea_list_st . '"
                    },
                    "action": {
                        "button":  "' . $button_txt_st . '",
                        "sections": [
                            {
                                "rows": [' . $list_trim . ']
                            }
                        ]
                    }
                }
            }
        ],
        "restart": [
          {
              "type": "text",
              "text": {
                  "body": "' . $restart_name . '"
              }
          }
      ],
      "invalid": [
          {
              "type": "text",
              "text": {
                  "body": "' . $invalid_name . '"
              }
          }
      ]
  }, ';
        break;

    }
  }

  if ($chatbot_array != '') {
    for ($i = 0; $i < count($chatbot_array); $i++) {
      // Looping the i is less than the chatbot_array.if the condition is true to continue the process.if the condition are false to stop the process
      switch ($chatbot_array != '') {
        case ($chatbot_array[$i] == 'Replybutton_1' || $chatbot_array[$i] == 'Replybutton_2' || $chatbot_array[$i] == 'Replybutton_3'):
          $myArray = explode('_', $chatbot_array[$i]);

          // print_r($myArray);
          $reply_trim_adding = '';
          $reply = '';

          $chat_bot_send .= ' { 
        "id": "' . ($i + 2) . '",
        "parent": [
          "' . ($i + 1) . '"
       ],   
       "pattern": "/' . $_POST['' . "reply_pattern_" . ($i + 1) . ''] . '/",
       "type":["text"],
       "message": [
        {
            "type": "interactive",
            "interactive": {
                "type": "button",
                "body": { 
                    "text": "' . $_POST['' . "textarea_reply" . ($i + 1) . ''] . '"
                },
                "action": {
                  ';

          for ($j = 0; $j < $myArray[1]; $j++) {
            // Looping the j is less than the myArray.if the condition is true to continue the process.if the condition are false to stop the process
            $reply .= '{
             "type": "reply",
             "reply": {
                 "id": "' . $_POST['' . ($i + 1) . "_reply_button_" . ($j + 1) . ''] . '",
                 "title":"' . $_POST['' . ($i + 1) . "_reply_button_" . ($j + 1) . ''] . '"
             }
            },';
          }
          ;
          $reply_trim_adding = trim($reply, ",");
          $chat_bot_send .= '"buttons":[' . $reply_trim_adding . '
            ] 
        }
      }
    }
     ]
  },';

          break;
        case ($chatbot_array[$i] == 'List_1' || $chatbot_array[$i] == 'List_2' || $chatbot_array[$i] == 'List_3' || $chatbot_array[$i] == 'List_4' || $chatbot_array[$i] == 'List_5' || $chatbot_array[$i] == 'List_6' || $chatbot_array[$i] == 'List_7' || $chatbot_array[$i] == 'List_8' || $chatbot_array[$i] == 'List_9' || $chatbot_array[$i] == 'List_10'):

          $myArray = explode('_', $chatbot_array[$i]);
          // print_r($myArray);
          $reply_trim_adding = '';
          $reply = '';
          $chat_bot_send .= ' { 
          "id": "' . ($i + 2) . '",
          "parent": [
            "' . ($i + 1) . '"
         ],
         "pattern": "/' . $_POST['' . "list_pattern_" . ($i + 1) . ''] . '/",
         "type": ["text"],
         "message": [
          {
              "type": "interactive",
              "interactive": {
                  "type": "list",
                  "body": {
                      "text":  "' . $_POST['' . "textarea_list_" . ($i + 1) . ''] . '"
                  },
                  "action": {
                    "button": "' . $_POST['' . "button_txt_" . ($i + 1) . ''] . '",
                    ';
          for ($j = 0; $j < $myArray[1]; $j++) {
            // Looping the j is less than the myArray.if the condition is true to continue the process.if the condition are false to stop the process
            $reply .= '{
               "type": "reply",
               "reply": {
                   "id": "' . $_POST['' . ($i + 1) . "_list_button_" . ($j + 1) . ''] . '",
                   "title":"' . $_POST['' . ($i + 1) . "_list_button_" . ($j + 1) . ''] . '"
               }
              },';
          }
          ;
          $reply_trim_adding = trim($reply, ",");
          $chat_bot_send .= '"sections":[{
                "rows": [
                ' . $reply_trim_adding . '
                ]
              }] 
          }
          }
              
          }
      ]
        },';
          break;
        case ($chatbot_array[$i] == 'Message'):

          $chat_bot_send .= ' { 
            "id": "' . ($i + 2) . '",
            "parent": [
                   "' . ($i + 1) . '"
           ],
           "pattern": "/' . $_POST['' . "message_name_" . ($i + 1) . ''] . '/",
           "type": ["text"],
           "message": [
             {
                     "type": "text",
                     "text": {
                             "body": "' . $_POST['' . "textarea_msg_" . ($i + 1) . ''] . '"
                     }
             }
            ]
            },';
          // }
          break;
      }
    }

  }
  $chat_bot_send_trim = trim($chat_bot_send, ",");

  $chat_bot_send_trim .= ']';

  // To Send the request  API
  $replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
  "whatsapp_config_id" : "' . $whatsapp_config_id . '",
  "flow_json" : "' . $flow_json . '",
  "flow_msg" : "' . $flow_msg . '"
}';
  //add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "chat_bot" API to verify, can we access for the chat_bot
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/chatbot/chat_bot',
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
  site_log_generate("Chat bot Page : " . $_SESSION['yjwatsp_user_name'] . " logged in send it to Service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $sql = json_decode($response, false);
  site_log_generate("Chat bot Page : Username => " . $_SESSION['yjwatsp_user_name'] . " executed the query reponse [$response] on " . date("Y-m-d H:i:s"), '../');
  if ($sql->response_code == 200) {
    site_log_generate("Chat bot Page : " . $_SESSION['yjwatsp_user_name'] . " new mobile no added successfully on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => "Chatbot Created Successfully!!");
  } else if ($sql->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
  <? }
  echo $chat_bot_send_trim;
  $json = array("status" => 1, "msg" => "success");
}
//chatbot flow - End

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with JSON Response
header('Content-type: application/json');
echo json_encode($json);

