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
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // To get bearertoken
$current_date = date("Y-m-d H:i:s"); // To get currentdate function


// Create Template Preview Page - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $preview_functions == "preview_template") {
  // To get the one by one data
  foreach ($button_url_text as $btn_txt_url) {
    $btn_txt_url_name .= $btn_txt_url;
  }
  foreach ($button_text as $btn_txt) {
    $btn_txt_name .= $btn_txt;
  }
  foreach ($button_quickreply_text as $txt_button_qr_txt) {
    $txt_button_qr_text1 .= '"' . $txt_button_qr_txt . '"' . ',';
  }
  $button_quickreply_text = $_POST['button_quickreply_text'];

  foreach ($reply_arr as $reply_arr1) {
    $reply_array_content = $reply_arr1;
  }

  foreach ($button_txt_phone_no as $btn_txt_phn) {
    $btn_txt_phn_no .= $btn_txt_phn;
  }
  foreach ($website_url as $web_url) {
    $web_url_link .= $web_url;

  }


  $stateData = '';
  $stateData_box = '';
  $hdr_type = '';
  if ($_SERVER['REQUEST_METHOD'] == "POST") { // // If the response is success to execute this condition
    if ($header) { // header variable

      switch ($header) {
        case('TEXT'):  // text
          $hdr_type .= "<input type='hidden' name='hid_txt_header_variable' id='hid_txt_header_variable' value='" . $txt_header_name . "'>";

          $stateData_1 = '';
          $stateData_1 = $txt_header_name;
          $stateData_2 = $stateData_1;

          $matches = null;
          $prmt = preg_match_all("/{{[0-9]+}}/", $txt_header_name, $matches);
          $matches_a0 = $matches[0];
          rsort($matches_a0);
          sort($matches_a0);
          for ($ij = 0; $ij < count($matches_a0); $ij++) {
            // Looping the ij is less than the count of matches_a0. if the condition is true to continue the process.if the condition is false to stop the process
            $expl2 = explode("{{", $matches_a0[$ij]);
            $expl3 = explode("}}", $expl2[1]);
            $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly tabindex='10' name='txt_header_variable[$expl3[0]][]' id='txt_header_variable' placeholder='{{" . $expl3[0] . "}} Value' title='Header Text' maxlength='20' value='-' style='width:100px;height: 30px;cursor: not-allowed;' class='form-control required'> </div><div style='float: left;'>";
            $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
            $stateData_2 = $stateData_1;
          }

          if ($stateData_2 != '') {
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
          break;


        case($media_category == 'document'): // document
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div> <div style='margin-left:60px;'><i class='fa fa-file-text' style='font-size: 18px'></i> <span> DOCUMENT</span>
            </div>
                </div>
              </div>
              </div></div>";
          break;


        case($media_category == 'image'): // image
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='margin-left:60px;'><i class='fa fa-image' style='font-size: 18px'></i> <span> IMAGE</span></div>
                  <span class='input-group-addon'><i class='icofont icofont-ui-messaging'></i></span>
                </div>
              </div>
              </div></div>";
          break;


        case $media_category == 'video': // video

          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header :</div> <div style='margin-left:60px;'><i cl// header variableass='fa fa-play-circle' style='font-size: 18px'></i> <span> VIDEO</span>
            </div>
                  <span class='input-group-addon'><i class='icofont icofont-ui-messaging'></i></span>
                </div>
              </div>
              </div></div>";
          break;
      }
    }

    if ($textarea) { // body variable
      $hdr_type .= "<input type='hidden' name='hid_txt_body_variable' id='hid_txt_body_variable' value='" . $textarea . "'>";

      $stateData_1 = '';
      $stateData_1 = $textarea;
      $stateData_2 = $stateData_1;

      $matches = null;
      $prmt = preg_match_all("/{{[0-9]+}}/", $textarea, $matches);
      $matches_a1 = $matches[0];
      rsort($matches_a1);
      sort($matches_a1);
      for ($ij = 0; $ij < count($matches_a1); $ij++) {
        // Looping the ij is less than the count of matches_a1. if the condition is true to continue the process.if the condition is false to stop the process
        $expl2 = explode("{{", $matches_a1[$ij]);
        $expl3 = explode("}}", $expl2[1]);
        $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly name='txt_body_variable[$expl3[0]][]' id='txt_body_variable' placeholder='{{" . $expl3[0] . "}} Value' maxlength='20' title='Enter {{" . $expl3[0] . "}} Value' value='-' style='width:100px;height: 30px;cursor: not-allowed;' class='form-control required'> </div><div style='float: left;'>";
        $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
        $stateData_2 = $stateData_1;
      }

      if ($stateData_2 != '') {
        $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Body : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
      }
    }

    if ($select_action5 = 'VISIT_URL' || ($select_action1 = 'PHONE_NUMBER' || $select_action3 = 'PHONE_NUMBER') && $select_action == 'QUICK_REPLY') {
      $stateData_2 = '';
      if ($select_action5 = 'VISIT_URL' && $web_url_link) {
        $stateData_2 .= "<a href='" . $web_url_link . "' target='_blank'>" . $btn_txt_url_name . "</a>";
        $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons URL : </div><div style='float:left'>" . $web_url_link . " - " . $stateData_2 . "</div></div>";
      }

      if (($select_action1 = 'PHONE_NUMBER' || $select_action3 = 'PHONE_NUMBER') && $btn_txt_phn_no) {
        $stateData_2 .= $country_code . " - " . $btn_txt_phn_no;
        $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Phone No. : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
      }

      for ($kk = 0; $kk < count($button_quickreply_text); $kk++) {
        // Looping the kk is less than the count of button_quickreply_text. if the condition is true to continue the process.if the condition is false to stop the process
        if ($select_action == 'QUICK_REPLY') {
          $stateData_2 = $button_quickreply_text[$kk];
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Quick Reply : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
        }
      }
    }
    if ($txt_footer_name) { // Footer 
      $hdr_type .= "<input type='hidden' name='hid_txt_footer_variable' id='hid_txt_footer_variable' value='" . $txt_footer_name . "'>";

      $stateData_2 = '';
      $stateData_2 = $txt_footer_name;

      if ($stateData_2 != '') {
        $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Footer : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
      }
    }

    site_log_generate("Template Create Preview Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Template available on " . date("Y-m-d H:i:s"), '../');
    site_log_generate("Template Create Preview Page : User : " . $_SESSION['yjwatsp_user_name'] . $stateData . $hdr_type . date("Y-m-d H:i:s"), '../');

    $json = array("status" => 1, "msg" => $stateData . $hdr_type);
  } else {    // otherwise
    site_log_generate("Template Create Preview Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Template not available on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => '-');
  }

}
//Create Template Preview Page - END



// previewTemplate_dreport Page - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $previewTemplate_dreport == "previewTemplate_dreport") {
  $template_name = htmlspecialchars(strip_tags(isset($_REQUEST['tmpl_name']) ? $conn->real_escape_string($_REQUEST['tmpl_name']) : ""));
  $sender = htmlspecialchars(strip_tags(isset($_REQUEST['sender_number']) ? $conn->real_escape_string($_REQUEST['sender_number']) : ""));
  // To Send the request API
  // To get the one by one data
  foreach ($button_url_text as $btn_txt_url) {
    $btn_txt_url_name .= $btn_txt_url;
  }
  foreach ($button_text as $btn_txt) {
    $btn_txt_name .= $btn_txt;
  }
  foreach ($button_quickreply_text as $txt_button_qr_txt) {
    $txt_button_qr_text1 .= '"' . $txt_button_qr_txt . '"' . ',';
  }
  $button_quickreply_text = $_POST['button_quickreply_text'];

  foreach ($reply_arr as $reply_arr1) {
    $reply_array_content = $reply_arr1;
  }

  foreach ($button_txt_phone_no as $btn_txt_phn) {
    $btn_txt_phn_no .= $btn_txt_phn;
  }
  foreach ($website_url as $web_url) {
    $web_url_link .= $web_url;
  }
  $replace_txt = '{
    "mobile_number":"' . $sender . '",
    "template_name" : "' . $template_name . '"
  }';

  // Add bearer token
  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
  // It will call "template_list" API to verify, can we can we allow to view the template list
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url . '/template/single_template',
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
  site_log_generate("Template List Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
  $yjresponse = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $yjresponseobj = json_decode($yjresponse, false);

  if (count($yjresponseobj->data) > 0) {
    $stateData = '';
    $stateData_box = '';
    $hdr_type = '';
    // Looping the ii is less than the count of data.if the condition is true to continue the process.if the condition are false to stop the process
    for ($ii = 0; $ii < count($yjresponseobj->data); $ii++) {
      if ($yjresponseobj->data[$ii]->components[0]->type == 'HEADER') {
        switch ($yjresponseobj->data[$ii]->components[0]->format) {
          case 'TEXT': // text
            $hdr_type .= "<input type='hidden' name='hid_txt_header_variable' id='hid_txt_header_variable' value='" . $yjresponseobj->data[$ii]->components[0]->text . "'>";

            $stateData_1 = '';
            $stateData_1 = $yjresponseobj->data[$ii]->components[0]->text;
            $stateData_2 = $stateData_1;

            $matches = null;
            $prmt = preg_match_all("/{{[0-9]+}}/", $yjresponseobj->data[$ii]->components[0]->text, $matches);
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
            break;

          case 'DOCUMENT': //document
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='float:left;margin-left:10px;'><a href=" . $yjresponseobj->data[$ii]->components[0]->example->header_handle[0] . " target='_blank'>Document Link</a></div>";
            break;
          case 'IMAGE': // Image
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='float:left;margin-left:10px;'><a href=" . $yjresponseobj->data[$ii]->components[0]->example->header_handle[0] . " target='_blank'><img src=" . $yjresponseobj->data[$ii]->components[0]->example->header_handle[0] . " alt='image' style='width:600px;height:700px' ></a></div>";
            break;
          case 'VIDEO':  // Video
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Header : </div><div style='float:left;margin-left:10px; '><a href=" . $yjresponseobj->data[$ii]->components[0]->example->header_handle[0] . " target='_blank'>Video Link</a></div>";
            break;
        }

      }

      if ($yjresponseobj->data[$ii]->components[1]->type == 'BODY') { // Body text
        $hdr_type .= "<input type='hidden' name='hid_txt_body_variable'  style='margin-left:10px;'  id='hid_txt_body_variable' value='" . $yjresponseobj->data[$ii]->components[1]->text . "'>";

        $stateData_1 = '';
        $stateData_1 = $yjresponseobj->data[$ii]->components[1]->text;
        $stateData_2 = $stateData_1;

        $matches = null;
        $prmt = preg_match_all("/{{[0-9]+}}/", $yjresponseobj->data[$ii]->components[1]->text, $matches);
        $matches_a1 = $matches[0];
        rsort($matches_a1);
        sort($matches_a1);
        for ($ij = 0; $ij < count($matches_a1); $ij++) {
          // Looping the ij is less than the count of matches_a1.if the condition is true to continue the process.if the condition are false to stop the process
          $expl2 = explode("{{", $matches_a1[$ij]);
          $expl3 = explode("}}", $expl2[1]);
          $stateData_box = "</div><div style='float:left; padding: 0 5px;'> <input type='text' readonly name='txt_body_variable[$expl3[0]][]' id='txt_body_variable' placeholder='{{" . $expl3[0] . "}} Value' maxlength='20' title='Enter {{" . $expl3[0] . "}} Value' value='-' style='width:100px;height: 30px;cursor: not-allowed;margin-top:10px;' class='form-control required'> </div><div style='float: left;'>";
          $stateData_1 = str_replace("{{" . $expl3[0] . "}}", $stateData_box, $stateData_1);
          $stateData_2 = $stateData_1;
        }

        if ($stateData_2 != '') {
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Body : </div><div style='float:left;margin-left:10px;'>" . $stateData_2 . "</div></div>";
        }

      }

      if ($yjresponseobj->data[$ii]->components[0]->type == 'BODY') { // Body text
        $hdr_type .= "<input type='hidden' style='margin-left:10px;' name='hid_txt_body_variable' id='hid_txt_body_variable' value='" . $yjresponseobj->data[$ii]->components[0]->text . "'>";

        $stateData_1 = '';
        $stateData_1 = $yjresponseobj->data[$ii]->components[0]->text;
        $stateData_2 = $stateData_1;

        $matches = null;
        $prmt = preg_match_all("/{{[0-9]+}}/", $yjresponseobj->data[$ii]->components[0]->text, $matches);
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

      if ($yjresponseobj->data[$ii]->components[1]->type == 'BUTTONS') {  // B Buttons
        $stateData_2 = '';
        if ($yjresponseobj->data[$ii]->components[1]->buttons[0]->type == 'URL') {
          $stateData_2 .= "<a href='" . $yjresponseobj->data[$ii]->components[1]->buttons[0]->url . "' target='_blank'>" . $yjresponseobj->data[$ii]->components[1]->buttons[0]->text . "</a>";
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons URL : </div><div style='float:left'>" . $yjresponseobj->data[$ii]->components[1]->buttons[0]->url . " - " . $stateData_2 . "</div></div>";
        }

        if ($yjresponseobj->data[$ii]->components[1]->buttons[0]->type == 'PHONE_NUMBER') { // Phone number
          $stateData_2 .= $yjresponseobj->data[$ii]->components[1]->buttons[0]->text . " - " . $yjresponseobj->data[$ii]->components[1]->buttons[0]->phone_number;
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Phone No. : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
        }
        // Looping the kk is less than the count of buttons.if the condition is true to continue the process.if the condition are false to stop the process
        for ($kk = 0; $kk < count($yjresponseobj->data[$ii]->components[1]->buttons); $kk++) { // Quickreply
          if ($yjresponseobj->data[$ii]->components[1]->buttons[$kk]->type == 'QUICK_REPLY') {
            $stateData_2 .= $yjresponseobj->data[$ii]->components[1]->buttons[$kk]->text;
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Quick Reply : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
        }
      }

      if ($yjresponseobj->data[$ii]->components[1]->type == 'FOOTER') { // Footer
        $hdr_type .= "<input type='hidden' name='hid_txt_footer_variable' id='hid_txt_footer_variable' value='" . $yjresponseobj->data[$ii]->components[1]->text . "'>";

        $stateData_2 = '';
        $stateData_2 = $yjresponseobj->data[$ii]->components[1]->text;

        if ($stateData_2 != '') {
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Footer : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
        }
      }
      if ($yjresponseobj->data[$ii]->components[2]->type == 'BUTTONS') { // Buttons
        $stateData_2 = '';

        if ($yjresponseobj->data[$ii]->components[2]->buttons[0]->type == 'URL') {
          $stateData_2 .= "<a href='" . $yjresponseobj->data[$ii]->components[2]->buttons[0]->url . "' target='_blank'>" . $yjresponseobj->data[$ii]->components[2]->buttons[0]->text . "</a>";
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons URL : </div><div style='float:left'>" . $yjresponseobj->data[$ii]->components[2]->buttons[0]->url . " - " . $stateData_2 . "</div></div>";
        }

        if ($yjresponseobj->data[$ii]->components[2]->buttons[0]->type == 'PHONE_NUMBER') { // Phone Number
          $stateData_2 .= $yjresponseobj->data[$ii]->components[2]->buttons[0]->text . " - " . $yjresponseobj->data[$ii]->components[2]->buttons[0]->phone_number;
          $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Phone No. : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
        }
        // Looping the kk is less than the count of buttons.if the condition is true to continue the process.if the condition are false to stop the process
        for ($kk = 0; $kk < count($yjresponseobj->data[$ii]->components[2]->buttons); $kk++) { //QUICK_REPLY
          if ($yjresponseobj->data[$ii]->components[2]->buttons[$kk]->type == 'QUICK_REPLY') {
            $stateData_2 .= $yjresponseobj->data[$ii]->components[2]->buttons[$kk]->text;
            $stateData .= "<div style='float:left; clear:both; line-height: 36px;'><div style='float:left; line-height: 36px;'>Buttons Quick Reply : </div><div style='float:left'>" . $stateData_2 . "</div></div>";
          }
        }
      }
    }
    site_log_generate("Compose Whatsapp Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Meta Message Template available on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 1, "msg" => $stateData . $hdr_type);
  } else {
    if ($yjresponseobj->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    site_log_generate("Compose Whatsapp Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " Get Message Template not available on " . date("Y-m-d H:i:s"), '../');
    $json = array("status" => 0, "msg" => '-');
  }
}
// previewTemplate_dreport Page - end

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with HTML Response
header('Content-type: application/json');
echo json_encode($json);