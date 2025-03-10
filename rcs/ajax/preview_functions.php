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
extract($_REQUEST);  // Extract the request
$current_date = date("Y-m-d H:i:s"); // To get currentdate function
$milliseconds = round(microtime(true) * 1000); // milliseconds in time

$bearer_token_header = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // add bearertoken

// Messenger Response Page view_response - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "view_response") {
  // To log file generate
  site_log_generate("Messenger Response Page : User : " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');
  // Get data
$message_id = $_POST['message_id']; // URL-decoded automatically
$message_from = $_POST['message_from']; // URL-decoded automatically
$message_to = $_POST['message_to']; // URL-decoded automatically

// If you need to manually decode any of these values
$decoded_message_id = urldecode($message_id);
$decoded_message_from = urldecode($message_from);
$decoded_message_to = urldecode($message_to);
  ?>


  <div class="inner_div" id="chathist">
    <?php
    // To Send the request  API
   $replace_txt = '{
    "message_from" : "' . htmlspecialchars($decoded_message_from) . '",
    "message_to" : "' . htmlspecialchars($decoded_message_to) . '"
  }';
    //add bearer token
    $bearer_token_header = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
    // It will call "messenger_view_response" API to verify, can we access for the messenger view response
    $curl = curl_init();
    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => $api_url . '/list/messenger_view_response',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => array(
          $bearer_token_header,
          'Content-Type: application/json'
        ),
      )
    );
    // Send the data into API and execute 
    site_log_generate("Messenger Response Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
    $response = curl_exec($curl);
    curl_close($curl);

    // After got response decode the JSON result
    $header = json_decode($response, false);
    if ($header->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
    <? }
    site_log_generate("Messenger Response Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
    $i = 0;
    $first_message_from = '-';
    $last_msg_time = '';
    $first_occurrence = 0;
    $message_id = '';
    // To get the one by one data
    if ($header->num_of_rows > 0) { // If the response is success to execute this condition
      for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
        // If the response is success to execute this condition. Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process


//echo $header->report[$indicator]->message_from."==";
//echo $header->report[$indicator]->message_to.'==';

     if (htmlspecialchars(urldecode($message_to)) == $header->report[$indicator]->message_from) {
          $sendfr = htmlspecialchars(urldecode($message_to));
          $sendto = htmlspecialchars(urldecode($message_from));
        } elseif (htmlspecialchars(urldecode($message_from)) == $header->report[$indicator]->message_from) {
          $sendfr = htmlspecialchars(urldecode($message_from));
          $sendto = htmlspecialchars(urldecode($message_to));
        }

        $message_id .= $header->report[$indicator]->message_id . ", ";
        $entry_date = date('d-m-Y h:i:s A', strtotime($header->report[$indicator]->message_rec_date));
        $display_data = '';
        switch (strtoupper($header->report[$indicator]->message_type)) {
          case 'TEXT':
            $display_data = "Text : " . base64_decode($header->report[$indicator]->msg_text);
            break;
          case 'LIST': ?>
            <style>
              li {
                text-align: left;
              }
            </style>
            <? $msg_data = $header->report[$indicator]->message_data;
            // Convert JSON string to PHP array
            $jsonArray = json_decode($msg_data, true);
            // Access the "body" content
            $bodyText = $jsonArray['interactive']['body']['text'];
            $expl2 = explode("'</li><li>'", $header->report[$indicator]->msg_list);
            // print_r($expl2[0]);
            $display_data = $bodyText . $expl2[0];
            break;
          case 'INTERACTIVE':
            $msg_data = $header->report[$indicator]->message_data;
            // Convert JSON string to PHP array
            $jsonArray = json_decode($msg_data, true);
            // Access the "body" content
            $bodyText = $jsonArray['list_reply']['title'];
            $display_data = $bodyText;
            break;
          case 'BUTTON':
            $display_data = "Reply Button : " . $header->report[$indicator]->msg_reply_button;
            break;
          case 'REACTION':
            $display_data = "Reaction : &#x" . $header->report[$indicator]->msg_reaction;
            break;
          case 'STICKER':
            $expl1 = explode("/", $header->report[$indicator]->msg_media_type);
            $display_data = "Sticker : <img src='uploads/response_media/" . $header->report[$indicator]->msg_media . "' style='max-width:200px !important; height: auto !important;'>";
            break;
          case 'AUDIO':
            $expl2 = explode(";", $header->report[$indicator]->msg_media_type);
            $display_data = ucwords($header->report[$indicator]->message_type) . ' : <audio controls>
            <source src="uploads/response_media/' . $header->report[$indicator]->msg_media . '" type="' . $expl2[0] . '">
          </audio>';
            break;
          case 'VIDEO':
            $display_data = ucwords($header->report[$indicator]->message_type) . ' : <video width="320" height="240" controls>
            <source src="uploads/response_media/' . $header->report[$indicator]->msg_media . '" type="' . $header->report[$indicator]->msg_media_type . '">
          </video>' . base64_decode($header->report[$indicator]->msg_media_caption);
            break;
          case 'IMAGE':
            $display_data = ucwords($header->report[$indicator]->message_type) . " : <img src='uploads/response_media/" . $header->report[$indicator]->msg_media . "' style='max-width:200px !important; height: auto !important;'>" . base64_decode($header->report[$indicator]->msg_media_caption);
            break;
          case 'DOCUMENT':
            $display_data = ucwords($header->report[$indicator]->message_type) . " : <a href='uploads/response_media/" . $header->report[$indicator]->msg_media . "'>Download</a>" . base64_decode($header->report[$indicator]->msg_media_caption);
            break;
          default:
            $display_data = "Text : " . base64_decode($header->report[$indicator]->msg_text);
            break;
        }
        if ($i == 0) {
          $i++;
          $first_message_from = $row;
        }
        $last_msg_time = $entry_date;
        $display_user = '';

  if ($header->report[$indicator]->message_from != "yeejai_technologies_lgzmaa9c_agent@rbm.goog") { // If the response is success to execute this condition
          $display_user = $header->report[$indicator]->message_from;
          if ($first_occurrence == 0) {
            $first_occurrence++;
            $last_msg_time = $entry_date;
          }
          ?>
          <div id="triangle" class="chat-item chat-right mb-2">
            <div id="message" class="chat-details">
              <span style="float:left;" class="chat-text">
                <?php echo $display_data; ?>
              </span> <br />
              <div>
                <span style="color:#606060;float:left;font-size:10px;clear:both;" class="chat-time">
                  <b>
                    <?php echo $display_user; ?>
                  </b>,
                  <?php echo $entry_date; ?>
                </span>
              </div>
            </div>
          </div>
          <?php
        } else { // otherwise
          $display_user = $header->report[$indicator]->message_from;
          ?>
          <div id="triangle1" class="chat-item chat-left mb-2">
            <div id="message1" class="chat-details">
              <span style="color:#000;float:right;" class="chat-text1">
                <?php echo $display_data; ?>
              </span> <br />
              <div>
                <span style="color:#606060;float:right;font-size:10px;clear:both;">
                  <b>
                    <?php echo $display_user; ?>
                  </b>,
                  <?php echo $entry_date; ?>
                </span>
              </div>
            </div>
          </div>
          <?php
        }
      }
    
    }
    ?>
  </div>
  <div id="app_footer" style='clear: both; border-top: 1px dashed #e4e6fc; padding: 10px;'>
    <div>
      <form id="frm_reply" name="frm_reply" action="#" method="POST">
        <div>
          <div class="row align-items-center justify-content-center">
            <?
            $time1 = strtotime($last_msg_time);
            $time2 = strtotime(date("Y-m-d h:i:s A"));
            $difference = round(abs($time2 - $time1) / 3600, 2);

            if ($difference <= 23) { ?>
              <div style="width:80%; float: left">
                <textarea id="txt_reply" name="txt_reply" autofocus required tabindex="1"
                  style="height: 75px !important; width: 100%;" placeholder="Enter your reply"
                  class="form-control form-control-primary required"></textarea>
              </div>
              <div class="text-center" style="width:20%; float: left;">
                <span class="error_display" id='id_error_display'></span>
                <input type="hidden" class="form-control" name='message_id' id='message_id' value='<?= $message_id ?>' />
                <input type="hidden" class="form-control" name='message_from' id='message_from' value='yeejai_technologies_lgzmaa9c_agent@rbm.goog' />
                <input type="hidden" class="form-control" name='message_to' id='message_to' value='<?= $display_user ?>' />
                <input type="hidden" class="form-control" name='sender_id' id='sender_id' value='<?= $display_user; ?>' />
                <input type="hidden" class="form-control" name='admin_number' id='admin_number'
                  value='<?= $admin_number ?>' />

                <input type="hidden" class="form-control" name='tmpl_call_function' id='tmpl_call_function'
                  value='messenger_reply' />
                <input type="submit" name="reply_submit" id="reply_submit" tabindex="2" value="Reply"
                  class="btn btn-success" style="margin-top: 18px;">
              </div>
            <? } else { ?>
              <span class="error_display">Last message received from client before 24 hours. So, We cannot send response
                here!!</span>
            <? } ?>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?
  site_log_generate("Messenger Response Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview User on " . date("Y-m-d H:i:s"), '../');
}
// Messenger Response Page view_response - End

?>
<!-- script include -->
<script>
  $(document).ready(function () {
    $('#txt_reply').keydown(function () {
      var message = $("txt_reply").val();
      if (event.keyCode == 13) {
        if (message == "") { } else {
          $('#frm_reply').submit();
        }
        $("#txt_reply").val('');
        return false;
      }
    });
  });
</script>
<?

if (isset($_GET['previewTemplate_meta']) == "previewTemplate_meta") {
  $slt_whatsapp_template    = htmlspecialchars(strip_tags(isset($_REQUEST['slt_whatsapp_template']) ? $_REQUEST['slt_whatsapp_template'] : ""));
  $template = explode ("!", $slt_whatsapp_template); 

  $personalization_option     = htmlspecialchars(strip_tags(isset($_REQUEST['personalization_option']) ? $_REQUEST['personalization_option'] : ""));
  $txt_sms_content     = isset($_REQUEST['txt_sms_content']) ? $_REQUEST['txt_sms_content'] : "";

  $txt_list_mobno = str_replace("\\r\\n", '', $txt_list_mobno);

  if($txt_list_mobno != '') {
    // Explode
    $str_arr = explode (",", $txt_list_mobno); 
    $entry_contact = '';
    for($indicatori = 0; $indicatori < count($str_arr); $indicatori++) {
      $entry_contact .= ''.$str_arr[$indicatori].',';
    }
    $entry_contact = rtrim($entry_contact, ", ");
  }

 $file_image_header_url		  = htmlspecialchars(strip_tags(isset($_REQUEST['file_image_header_url']) ? $_REQUEST['file_image_header_url'] : ""));

 $file_video_header_url		  = htmlspecialchars(strip_tags(isset($_REQUEST['file_video_header_url']) ? $_REQUEST['file_video_header_url'] : ""));
 
 $file_document_header_url		  = htmlspecialchars(strip_tags(isset($_REQUEST['file_document_header_url']) ? $_REQUEST['file_document_header_url'] : ""));
 
if($_FILES["fle_variable_csv"]["name"] != '') {
  $path_parts = pathinfo($_FILES["fle_variable_csv"]["name"]);
  $extension = $path_parts['extension'];
  $filename = $_SESSION['yjwatsp_user_id'] . "_csvpreview_" . $milliseconds . "." . $extension;
  /* Location */
  $location = "../uploads/compose_variables/" . $filename;
  $group_contact = $site_url . "uploads/compose_variables/" . $filename;
  $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
  $imageFileType = strtolower($imageFileType);

  /* Valid extensions */
  $valid_extensions = array("csv");
  $response = 0;
  /* Check file extension */
  if (in_array(strtolower($imageFileType), $valid_extensions)) {
    /* Upload file */
    if (move_uploaded_file($_FILES['fle_variable_csv']['tmp_name'], $location)) {
      $response = $location;
    }
  }
}

if($_FILES["upload_contact"]["name"] != '') {
  $path_parts = pathinfo($_FILES["upload_contact"]["name"]);
  $extension = $path_parts['extension'];
  $filename = $_SESSION['yjwatsp_user_id'] . "_csvpreview_" . $milliseconds . "." . $extension;
  /* Location */
  $location = "../uploads/compose_variables/" . $filename;
  $group_contact = $site_url . "uploads/compose_variables/" . $filename;
  $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
  $imageFileType = strtolower($imageFileType);

  /* Valid extensions */
  $valid_extensions = array("csv");
  $response = 0;
  /* Check file extension */
  if (in_array(strtolower($imageFileType), $valid_extensions)) {
    /* Upload file */
    if (move_uploaded_file($_FILES['upload_contact']['tmp_name'], $location)) {
      $response = $location;
    }
  }
}


if ($_FILES["file_image_header"]["name"] != '') {
  /* Location */
  $msg_type = 'IMAGE';
  $image_size = $_FILES['file_image_header']['size'];
  $image_type = $_FILES['file_image_header']['type'];
  $file_type = explode("/", $image_type);

  $filename = $_SESSION['yjwatsp_user_id'] . "_preview_" . $milliseconds . "." . $file_type[1];
  $location = "../uploads/whatsapp_images/" . $filename;
  $location_1 = $site_url . "uploads/whatsapp_images/" . $filename;
  $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
  $imageFileType = strtolower($imageFileType);

  $rspns = '';
  /* Upload file */
  if (move_uploaded_file($_FILES['file_image_header']['tmp_name'], $location)) {
    $rspns = $location;
    site_log_generate("Create Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
  }
}

if($file_image_header_url){
  $location_1 = $file_image_header_url;
}	


if ($_FILES["file_video_header"]["name"] != '') {
  /* Location */
  $msg_type = 'Video';
  $image_size = $_FILES['file_video_header']['size'];
  $image_type = $_FILES['file_video_header']['type'];
  $file_type = explode("/", $image_type);

  $filename = $_SESSION['yjwatsp_user_id'] . "_preview_" . $milliseconds . "." . $file_type[1];
  $location = "../uploads/whatsapp_videos/" . $filename;
  $location_1 = $site_url . "uploads/whatsapp_videos/" . $filename;
  $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
  $imageFileType = strtolower($imageFileType);

  $rspns = '';
  /* Upload file */
  if (move_uploaded_file($_FILES['file_video_header']['tmp_name'], $location)) {
    $rspns = $location;
    site_log_generate("Compose Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
  }
}

if($file_video_header_url){
  $location_1 = $file_video_header_url;
}	

if ($_FILES["file_document_header"]["name"] != '') {
  /* Location */
  $msg_type = 'Document';
  $image_size = $_FILES['file_document_header']['size'];
  $image_type = $_FILES['file_document_header']['type'];
  $file_type = explode("/", $image_type);

  $filename = $_SESSION['yjwatsp_user_id'] . "_preview_" . $milliseconds . "." . $file_type[1];
  $location = "../uploads/whatsapp_docs/" . $filename;
  $location_1 = $site_url . "uploads/whatsapp_docs/" . $filename;
  $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
  $imageFileType = strtolower($imageFileType);

  $rspns = '';
  /* Upload file */
  if (move_uploaded_file($_FILES['file_document_header']['tmp_name'], $location)) {
    $rspns = $location;
    site_log_generate("Create Template Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
  }
}

if($file_document_header_url){
  $location_1 = $file_document_header_url;
}	
  ?>
	<table class="table table-striped table-bordered m-0" style="table-layout: fixed; white-space: inherit; width: 100%; overflow-x: scroll;">
				<tbody>
          <? if(count($template) > 0 ) { ?>
						<tr>
								<th scope="row">Select Whatsapp Template</th>
                                                                <td style="white-space: inherit !important;">
    <?php 
        // Check if $template[0] and $template[1] have values
        $displayValue = (!empty($template[0]) && !empty($template[1])) ? $template[0] . "[" . $template[1] . "]" : '-';
        echo $displayValue;
    ?>
</td>
						</tr>
					<? } ?>
          <? if($personalization_option != '' && $template[4] ) { ?>
						<tr>
								<th scope="row"> Media Type</th>
								<td style="white-space: inherit !important;"><?= $personalization_option?></td>
						</tr>
					<? } ?>

					<? if($entry_contact != '' && $group_contact == '') { ?>
						<tr>
								<th scope="row">Enter Mobile Numbers</th>
								<td style="white-space: inherit !important;"><?=$entry_contact?></td>
						</tr>
					<? } ?>
          <? if($location_1 != '' ) { ?>
						<tr>
								<th scope="row">Header Media</th>
								<td style="white-space: inherit !important;"><a href= "<?=$location_1?>"  target='_blank'>Media Link</a></td>
						</tr>
					<? } ?>
          <? if($group_contact != '') { ?>
						<tr>
								<th scope="row">Upload Mobile Numbers</th>
								<td style="white-space: inherit !important;"><a href= "<?=$group_contact?>"  target='_blank'>Download Mobile Numbers</a></td>
						</tr>
					<? } ?>
  <? if($txt_sms_content != '') { ?>
						<tr>
								<th scope="row">Template Content</th>
								<td style="white-space: inherit !important;"><?= $txt_sms_content?></td>
						</tr>
					<? } ?>
				</tbody>
		</table>
	<? 
  }

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with HTML Response
header('Content-type: text/html');
echo $result_value;
