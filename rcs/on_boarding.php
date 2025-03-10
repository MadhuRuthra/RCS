<?php
/*
Authendicated users only allow to view this Add Sender ID page.
This page is used to view the Add a New Sender ID.
It will send the form to API service and Save to Whatsapp Facebook
and get the response from them and store into our DB.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 27-Jul-2023
*/

session_start(); // start session
error_reporting(0); // The error reporting function

include_once('api/configuration.php'); // Include configuration.php
extract($_REQUEST); // Extract the request

// If the Session is not available redirect to index page
if ($_SESSION['yjwatsp_user_id'] == "") { ?>
  <script>window.location = "index";</script>
  <?php exit();
}

if ($_SESSION['yjwatsp_user_master_id'] != 1 and $_SESSION['yjwatsp_user_master_id'] != 2) { ?>
  <script>window.location = "dashboard";</script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("On Boarding Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));

// To Send the request  API
$replace_txt = '{
  "user_id" : "' . $_SESSION["yjwatsp_user_id"] . '"
}';

// Add bearer token
$bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";

// It will call "p_login" API to verify, can we allow to login the already existing user for access the details
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $api_url . '/list/view_onboarding',
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
// Log file generate
site_log_generate("On Boarding Page : " . $uname . " Execute the service [$replace_txt, $bearer_token] on " . date("Y-m-d H:i:s"), "../");
$response = curl_exec($curl);
curl_close($curl);

// After got response decode the JSON result
$state1 = json_decode($response, false);

// Log file generate
site_log_generate("On Boarding Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), "../");

// To get the API response one by one data and assign to Session
if ($state1->response_status == 200) {
  // Looping the indicator is less than the count of response_result.if the condition is true to continue the process.if the condition are false to stop the process
  for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
    $cont_person = $state1->report[$indicator]->cont_person;
    $cont_designation = $state1->report[$indicator]->cont_designation;
    $cont_mobile_no = $state1->report[$indicator]->cont_mobile_no;
    $cont_email = $state1->report[$indicator]->cont_email;
    $billing_address = $state1->report[$indicator]->billing_address;

    $company_name = $state1->report[$indicator]->company_name;
    $company_website = $state1->report[$indicator]->company_website;
    $parent_company_name = $state1->report[$indicator]->parent_company_name;
    $company_display_name = $state1->report[$indicator]->company_display_name;
    $description_business = $state1->report[$indicator]->description_business;

    $profile_image = $state1->report[$indicator]->profile_image;
    $business_category = $state1->report[$indicator]->business_category;
    $sender_1 = $state1->report[$indicator]->sender_1;
    $sender_2 = $state1->report[$indicator]->sender_2;

    $sender = $state1->report[$indicator]->sender;
    $sender2 = $state1->report[$indicator]->sender2;

    $message_type = $state1->report[$indicator]->message_type;
    $opt_process = $state1->report[$indicator]->opt_process;
    $enquiry_approval = $state1->report[$indicator]->enquiry_approval;
    $company_display_name = $state1->report[$indicator]->company_display_name;
    $privacy_terms = $state1->report[$indicator]->privacy_terms;

    $terms_conditions = $state1->report[$indicator]->terms_conditions;
    $document_proof = $state1->report[$indicator]->document_proof;
    $proof_doc_name = $state1->report[$indicator]->proof_doc_name;
    $volume_day_expected = $state1->report[$indicator]->volume_day_expected;
    $rejected_comments = $state1->report[$indicator]->rejected_comments;

    $user_name = $state1->report[$indicator]->user_name;
    $user_email = $state1->report[$indicator]->user_email;
    $user_mobile = $state1->report[$indicator]->user_mobile;
    $user_address = $state1->report[$indicator]->user_address;
    $usr_mgt_status = $state1->report[$indicator]->usr_mgt_status;
  }
} else if ($state1->response_status == 403 || $response == '') { ?>
    <script>
      window.location = "index"
    </script>
<? }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>On Boarding ::
    <?= $site_title ?>
  </title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <!-- style include in css -->
  <style>
    .loader {
      width: 50;
      background-color: #ffffffcf;
    }

    .loader img {}

    .grid_clr_green {
      background-color: #c1e5cf1f;
      margin-right: -10px;
      margin-left: -10px;
    }

    .grid_clr_white {
      margin-right: -10px;
      margin-left: -10px;
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <!-- include header function adding -->
      <? include("libraries/site_header.php"); ?>

      <!-- include sitemenu function adding -->
      <? include("libraries/site_menu.php"); ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <!-- Title and Breadcrumbs -->
          <div class="section-header">
            <h1>On Boarding</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item">On Boarding</div>
            </div>
          </div>

          <!-- Form Entry Panel -->
          <div class="section-body">
            <div class="row">

              <div class="col-8 col-md-8 col-lg-8 offset-md-2">
                <div class="card" style="padding: 10px;">
                  <form class="md-float-material form-material" action="#" name="frm_edit_onboarding"
                    id='frm_edit_onboarding' class="needs-validation" novalidate="" enctype="multipart/form-data"
                    method="post">
                    <div>
                      <div class="row m-b-20">
                        <div class="col-md-12">
                          <h5 class="text-center"><i class="icofont icofont-sign-in"></i>Basic Information </h5>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Client Name<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $user_name;
                          } else { ?>
                            <input type="text" name="clientname_txt" id="clientname_txt" class="form-control"
                              value="<?= $user_name ?>" maxlength="50" tabindex="1" autofocus required=""
                              data-toggle="tooltip" data-placement="top" title="" data-original-title="Client Name"
                              placeholder="Client Name" pattern="[a-zA-Z0-9 -_]+"
                              onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Contact Person<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $cont_person;
                          } else { ?>
                            <input type="text" name="contact_person_txt" id="contact_person_txt" class="form-control"
                              value="<?= $cont_person ?>" maxlength="50" tabindex="2" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title=" Contact person"
                              placeholder="Contact person" pattern="[a-zA-Z0-9 -_]+"
                              onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Designation<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $cont_designation;
                          } else { ?>
                            <input type="text" name="designation_txt" id="designation_txt" class="form-control"
                              value="<?= $cont_designation ?>" maxlength="50" tabindex="3" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Contact person designation"
                              placeholder="Contact person designation" pattern="[a-zA-Z0-9 -_]+"
                              onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Contact No<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $cont_mobile_no;
                          } else { ?>
                            <input type="text" name="mobile_no_txt" id="mobile_no_txt" class="form-control"
                              value="<?= $cont_mobile_no ?>" maxlength="10" tabindex="4" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Contact person Contact no"
                              placeholder="Contact person Contact no" pattern="[0-9 -_]+"
                              onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Contact Email ID<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $cont_email;
                          } else { ?>
                            <input type="text" name="email_id_contact" id="email_id_contact" class="form-control"
                              value="<?= $cont_email ?>" maxlength="50" tabindex="5" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Contact person Email ID"
                              placeholder="Contact person Email ID">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Billing Address & GST<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $billing_address;
                          } else { ?>
                            <input type="text" name="bill_address" id="bill_address" class="form-control"
                              value="<?= $billing_address ?>" maxlength="300" tabindex="6" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Billing Address & GST"
                              placeholder="Billing Address & GST" pattern="^[a-zA-Z0-9/\s,.-]*$"
                              onkeypress="return address_validation(event)">
                          <? } ?>
                        </div>
                      </div>

                      </br></br>
                      <h5 class="text-center"><i class="icofont icofont-sign-in"></i>Company information </h5></br>

                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Name Of The Business<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $company_name;
                          } else { ?>
                            <input type="text" name="business_name" id="business_name" class="form-control"
                              value="<?= $company_name ?>" maxlength="50" tabindex="7" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="company name" placeholder="Company name"
                              pattern="[a-zA-Z0-9 -_]+" onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Company Website Details<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $company_website;
                          } else { ?>
                            <input type="text" name="cpy_website_details" id="cpy_website_details" class="form-control"
                              maxlength="50" value="<?= $company_website ?>" tabindex="8" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Company website details"
                              placeholder="Company website details" onkeypress="return single_quote_validation(event)"
                              onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Parents Company Name<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $parent_company_name;
                          } else { ?>
                            <input type="text" name="parent_company_name" id="parent_company_name" class="form-control"
                              value="<?= $parent_company_name ?>" maxlength="50" tabindex="9" required=""
                              data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Parents company name" placeholder="Parents company name"
                              pattern="[a-zA-Z0-9 -_]+" onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Company Display Name (To be configured)<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $company_display_name;
                          } else { ?>
                            <input type="text" name="cpy_display_name" id="cpy_display_name" class="form-control"
                              value="<?= $company_display_name ?>" maxlength="50" tabindex="10" required=""
                              data-toggle="tooltip" data-placement="top" title="" data-original-title="Group company name"
                              placeholder="Company display name" pattern="[a-zA-Z0-9 -_]+"
                              onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Description Of The Business<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $description_business;
                          } else { ?>
                            <input type="text" name="desp_business_txt" id="desp_business_txt" class="form-control"
                              value="<?= $description_business ?>" maxlength="100" tabindex="11" required=""
                              data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Description of the business" placeholder="Description of the business"
                              onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Registered Address Of The Business<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $user_address;
                          } else { ?>
                            <input type="text" name="reg_add_business" id="reg_add_business" class="form-control"
                              value="<?= $user_address ?>" maxlength="200" tabindex="12" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Official addres"
                              placeholder=" Registered Address of the business" pattern="^[a-zA-Z0-9/\s,.-]*$"
                              onkeypress="return address_validation(event)">
                          <? } ?>
                        </div>
                      </div>

                      </br></br>
                      <h5 class="text-center"><i class="icofont icofont-sign-in"></i>Communication Information </h5>
                      </br>

                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Email ID<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $user_email;
                          } else { ?>
                            <input type="text" name="email_id_txt" id="email_id_txt" class="form-control"
                              value="<?= $user_email ?>" maxlength="100" tabindex="13" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title=" Official Email ID"
                              placeholder=" Email ID">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Contact No<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $user_mobile;
                          } else { ?>
                            <input type="text" name="contact_no_cmpy" id="contact_no_cmpy" class="form-control"
                              value="<?= $user_mobile ?>" maxlength="10" tabindex="14" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Business contact no"
                              placeholder="Contact no" pattern="[0-9]+" onkeypress="return clsAlphaNoOnly(event)"
                              onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Profile/ Display Picture Of The Business
                        </div>
                        <div class="col-6">
                          <? if ($profile_image != '') { ?><img src="uploads/whatsapp_images/<?= $profile_image ?>"
                              style="width: 100px; height: auto;">
                          <? } ?>
                          <? if ($usr_mgt_status != 'Y') { ?>
                            <input type="file" name="profile_display_pic" id="profile_display_pic" class="form-control"
                              maxlength="100" tabindex="15" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title=" Profile/ Display picture of the business - Allowed only jpeg, png images. Maximum 5 MB Size allowed"
                              placeholder="Profile/ Display picture of the business" accept="image/png, image/jpeg">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Select Business Category<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <?
                          // To Show the Service Category list
                          $replace_txt = '{
                                  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
                                }';
                          $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
                          $curl = curl_init();
                          curl_setopt_array($curl, array(
                            CURLOPT_URL => $api_url . '/list/service_category_list',
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
                          site_log_generate("On Boarding Page : " . $uname . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                          $response = curl_exec($curl);
                          curl_close($curl);
                          // After got response decode the JSON result
                          $state1 = json_decode($response, false);
                          site_log_generate("On Boarding Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
                          $i1 = 0;
                          if ($usr_mgt_status != 'Y') {
                            // Based on the JSON response, list in the option button  ?>
                            <select name="slt_service_category" required="" id='slt_service_category' class="form-control"
                              data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Select Service Category" tabindex="16">
                              <?
                              if ($state1->num_of_rows > 0) {
                                // Looping the indicator is less than the count of report.if the condition is true to continue the process and to get the report details.if the condition are false to stop the process and to send the no available data 
                                for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
                                  $message_category_id = $state1->report[$indicator]->message_category_id;
                                  $message_category_title = $state1->report[$indicator]->message_category_title;
                                  $i1++; ?>
                                  <option value="<?= $message_category_id ?>" <? if ($i1 == 1 or $business_category == $message_category_id) { ?> selected <? } ?>>
                                    <?= $message_category_title ?>
                                  </option>
                                  <?
                                }
                              } else if ($state1->response_status == 403 || $response == '') { ?>
                                  <script>
                                    window.location = "index"
                                  </script>
                              <? }
                              ?>
                            </select>
                          <? } else {
                            for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
                              $message_category_id = $state1->report[$indicator]->message_category_id;
                              $message_category_title = $state1->report[$indicator]->message_category_title;
                              if ($business_category == $message_category_id) {
                                echo $message_category_title;
                              }
                            }
                          } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Sender ID<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $sender;
                          } else { ?>
                            <input type="text" name="sender_id_txt" id="sender_id_txt" class="form-control"
                              value="<?= $sender ?>" maxlength="10"
                              onkeypress="return (event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8)"
                              tabindex="17" required="" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Recommend fresh no or never used whats app" placeholder="Sender ID">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Sender ID 1 (Communication)<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $sender_1;
                          } else { ?>
                            <textarea type="text" name="sender_id_txt_1_txt" id="sender_id_txt_1_txt" class="form-control"
                              maxlength="300" tabindex="18" required="" data-toggle="tooltip" data-placement="top"
                              title=""
                              data-original-title="Message script / template. This message will send from this sender id. Maximum 300 characters."
                              onkeypress="return single_quote_validation(event)"
                              placeholder="Sender ID- 1 "><?= $sender_1 ?></textarea>
                          <? } ?>
                        </div>
                      </div>

                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Sender ID 2<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $sender2;
                          } else { ?>
                            <input type="text" name="sender_id_2_txt" id="sender_id_2_txt" class="form-control"
                              value="<?= $sender2 ?>" maxlength="10"
                              onkeypress="return (event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 8)"
                              tabindex="17" required="" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Recommend fresh no or never used whats app" placeholder="Sender ID">
                          <? } ?>
                        </div>
                      </div>

                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Sender ID 2 (Communication)<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $sender_2;
                          } else { ?>
                            <textarea type="text" name="sender_id_txt_2_txt" id="sender_id_txt_2_txt" class="form-control"
                              maxlength="300" tabindex="19" required="" data-toggle="tooltip" data-placement="top"
                              title=""
                              data-original-title="Message script / template. This message will send from this sender id. Maximum 300 characters."
                              placeholder="Sender ID 2"
                              onkeypress="return single_quote_validation(event)"><?= $sender_2 ?></textarea>
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Type Of Message<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            if ($message_type == 1) {
                              echo "Transactional";
                            }
                            if ($message_type == 2) {
                              echo "Promotional";
                            }
                            if ($message_type == 3) {
                              echo "Both";
                            }
                          } else { ?>
                            <select class="form-select form-control" required="" aria-label="Default select example"
                              tabindex="20" id="type_of_message" name="type_of_message">
                              <option value="1" selected>Transactional</option>
                              <option value="2" <? if ($message_type == 2) { ?> selected <? } ?>>Promotional </option>
                              <option value="3" <? if ($message_type == 3) { ?> selected <? } ?>>Both</option>
                            </select>
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Opt In Process<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            if ($opt_process == 1) {
                              echo "SMS";
                            }
                            if ($opt_process == 2) {
                              echo "OBD";
                            }
                            if ($opt_process == 3) {
                              echo "Website & Others";
                            }
                          } else { ?>
                            <select class="form-select form-control" required="" aria-label="Default select example"
                              tabindex="21" id="otp_in_process" name="otp_in_process">
                              <option value="1" selected>SMS</option>
                              <option value="2" <? if ($opt_process == 2) { ?> selected <? } ?>>OBD</option>
                              <option value="3" <? if ($opt_process == 3) { ?> selected <? } ?>>Website & Others</option>
                            </select>
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Enquiry/ Approval Page<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $enquiry_approval;
                          } else { ?>
                            <input type="text" name="enquiry_approve_txt" id="enquiry_approve_txt" class="form-control"
                              value="<?= $enquiry_approval ?>" maxlength="100" tabindex="22" required=""
                              data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Enquiry/ Approval page" placeholder="Enquiry/ Approval page"
                              onkeypress="return single_quote(event)" placeholder="Terms & Condition page"
                              onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Privacy & Terms Of Service Page<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $privacy_terms;
                          } else { ?>
                            <input type="text" name="privacy_terms_txt" id="privacy_terms_txt" class="form-control"
                              value="<?= $privacy_terms ?>" maxlength="100" tabindex="23" required="" data-toggle="tooltip"
                              data-placement="top" title="" data-original-title="Privacy & terms of service page"
                              placeholder="Privacy & terms of service page" onkeypress="return single_quote(event)"
                              placeholder="Terms & Condition page" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Terms & Condition Page<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $terms_conditions;
                          } else { ?>
                            <input type="text" name="terms_condition_txt" id="terms_condition_txt" class="form-control"
                              value="<?= $terms_conditions ?>" maxlength="100" tabindex="24" required=""
                              data-toggle="tooltip" data-placement="top" title="" data-original-title="Official T&C page"
                              placeholder="Terms & Condition page" onkeypress="return single_quote(event)"
                              placeholder="Terms & Condition page" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Proof Of Document<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            if ($document_proof == 1) {
                              echo "Business registration or License document";
                            }
                            if ($document_proof == 2) {
                              echo "Utility bill (Mobile, Electricity, Bank statement)";
                            }
                            if ($document_proof == 3) {
                              echo "Change of Business name";
                            }
                            if ($document_proof == 4) {
                              echo "Certification of incorporation";
                            }
                            if ($document_proof == 5) {
                              echo "Tax document";
                            }
                          } else { ?>
                            <select class="form-select form-control" required="" aria-label="Default select example"
                              tabindex="25" id="proof_document_slt" name="proof_document_slt">
                              <option value="1" selected>Business registration or License document</option>
                              <option value="2" <? if ($document_proof == 2) { ?> selected <? } ?>>Utility bill (Mobile,
                                Electricity, Bank statement)</option>
                              <option value="3" <? if ($document_proof == 3) { ?> selected <? } ?>>Change of Business name
                              </option>
                              <option value="4" <? if ($document_proof == 4) { ?> selected <? } ?>>Certification of
                                incorporation</option>
                              <option value="5" <? if ($document_proof == 5) { ?> selected <? } ?>>Tax document</option>
                            </select>
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <div class="col-6 label">
                          Upload PDF<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($proof_doc_name != '') { ?><a href="uploads/whatsapp_docs/<?= $proof_doc_name ?>"
                              download>Download Document</a>
                          <? } ?>
                          <? if ($usr_mgt_status != 'Y') { ?>
                            <input type="file" name="proof_document" id="proof_document" class="form-control"
                              maxlength="100" tabindex="26" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title=" Proof of document - Allowed only PDF files" accept="application/pdf"
                              placeholder=" Proof of document (Upload PDF)">
                          <? } ?>
                        </div>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <div class="col-6 label">
                          Expected Volumes For Day<label style="color:#FF0000">*</label>
                        </div>
                        <div class="col-6">
                          <? if ($usr_mgt_status == 'Y') {
                            echo $volume_day_expected;
                          } else { ?>
                            <input type="text" name="expected_volumes_day" id="expected_volumes_day" class="form-control"
                              value="<?= $volume_day_expected ?>" maxlength="8" tabindex="27" required=""
                              data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Expected volumes for day" placeholder="50000 per day"
                              pattern="[a-zA-Z0-9 -_]+" onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                          <? } ?>
                        </div>
                      </div>

                      <? if ($rejected_comments != '') { ?>
                        <div class="row mt-2 grid_clr_green">
                          <div class="col-6 label">
                            Remarks
                          </div>
                          <div class="col-6 error_display text-left">
                            <b>
                              <?= $rejected_comments ?>
                            </b>
                          </div>
                        </div>
                      <? } ?>
                    </div>
                    <? if ($usr_mgt_status != 'Y') { ?>
                      <div class="row m-t-30">
                        <div class="col-md-12" style="text-align:center;">
                          <span class="error_display text-center" id='id_error_display_onboarding'></span>&nbsp;
                        </div>
                      </div>

                      <div class="row  m-t-30">
                        <div class="col-md-12" style="text-align:center">
                          <input type="hidden" class="form-control" name='call_function' id='call_function'
                            value='edit_onboarding' />
                          <input type="submit" name="submit_onboarding" id="submit_onboarding"
                            style="width:150px;margin-left:auto;margin-right:auto" tabindex="30" value="Submit"
                            class="btn btn-success btn-md btn-block waves-effect waves-light text-center ">
                        </div>
                      </div>
                    <? } ?>
                    <div class="row m-t-30">
                      <div class="col-md-12" style="text-align:center;">&nbsp;</div>
                    </div>

                </div>
                </form>
              </div>
            </div>
          </div>
      </div>
      </section>
    </div>
    <!-- include site footer -->
    <? include("libraries/site_footer.php"); ?>
  </div>
  </div>

  <!-- General JS Scripts -->
  <script src="assets/modules/jquery.min.js"></script>
  <script src="assets/modules/popper.js"></script>
  <script src="assets/modules/tooltip.js"></script>
  <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="assets/modules/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>

  <!-- JS Libraies -->

  <!-- Page Specific JS File -->

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <!--Remove dublicates numbers -->
  <script>
    // start function document
    $(function () {
      $('#id_qrcode').fadeOut("slow");
    });

    document.body.addEventListener("click", function (evt) {
      $("#id_error_display_onboarding").html("");
    })
    // Sign up submit Button function Start
    $(document).on("submit", "form#frm_edit_onboarding", function (e) {
      e.preventDefault();
      $("#id_error_display_onboarding").html("");
      //get input field values 
      var clientname_txt = $('#clientname_txt').val();
      var contact_person_txt = $('#contact_person_txt').val();
      var designation_txt = $('#designation_txt').val();
      var mobile_no_txt = $('#mobile_no_txt').val();
      var email_id_contact = $('#email_id_contact').val();

      var bill_address = $('#bill_address').val();
      var business_name = $('#business_name').val();
      var cpy_website_details = $('#cpy_website_details').val();
      var parent_company_name = $('#parent_company_name').val();
      var cpy_display_name = $('#cpy_display_name').val();

      var desp_business_txt = $('#desp_business_txt').val();
      var reg_add_business = $('#reg_add_business').val();
      var email_id_txt = $('#email_id_txt').val();
      var contact_no_cmpy = $('#contact_no_cmpy').val();
      var profile_display_pic = $('#profile_display_pic').val();

      var slt_service_category = $('#slt_service_category').val();
      var sender_id_txt = $('#sender_id_txt').val();
      var sender_id_txt_1_txt = $('#sender_id_txt_1_txt').val();
      var sender_id_txt_2_txt = $('#sender_id_txt_2_txt').val();
      var type_of_message = $('#type_of_message').val();

      var otp_in_process = $('#otp_in_process').val();
      var enquiry_approve_txt = $('#enquiry_approve_txt').val();
      var privacy_terms_txt = $('#privacy_terms_txt').val();
      var terms_condition_txt = $('#terms_condition_txt').val();
      var proof_document_slt = $('#proof_document_slt').val();

      var proof_document = $('#proof_document').val();
      var expected_volumes_day = $('#expected_volumes_day').val();

      var flag = true;
      /********validate all our form fields***********/
      if (clientname_txt == "") {
        $('#clientname_txt').css('border-color', 'red');

        flag = false;
      }
      if (contact_person_txt == "") {
        $('#contact_person_txt').css('border-color', 'red');

        flag = false;
      }
      if (designation_txt == "") {
        $('#designation_txt').css('border-color', 'red');

        flag = false;
      }
      if (bill_address == "") {
        $('#bill_address').css('border-color', 'red');

        flag = false;
      }
      if (email_id_contact == "") {
        $('#email_id_contact').css('border-color', 'red');

        flag = false;
      }

      if (business_name == "") {
        $('#business_name').css('border-color', 'red');

        flag = false;
      }
      if (cpy_website_details == "") {
        $('#cpy_website_details').css('border-color', 'red');

        flag = false;
      }
      if (parent_company_name == "") {
        $('#parent_company_name').css('border-color', 'red');

        flag = false;
      }
      if (cpy_display_name == "") {
        $('#cpy_display_name').css('border-color', 'red');

        flag = false;
      }
      if (desp_business_txt == "") {
        $('#desp_business_txt').css('border-color', 'red');

        flag = false;
      }

      if (reg_add_business == "") {
        $('#reg_add_business').css('border-color', 'red');

        flag = false;
      }
      if (email_id_txt == "") {
        $('#email_id_txt').css('border-color', 'red');

        flag = false;
      }
      if (contact_no_cmpy == "") {
        $('#contact_no_cmpy').css('border-color', 'red');

        flag = false;
      }

      if (slt_service_category == "") {
        $('#slt_service_category').css('border-color', 'red');

        flag = false;
      }

      if (sender_id_txt == "") {
        $('#sender_id_txt').css('border-color', 'red');

        flag = false;
      }
      if (sender_id_txt_1_txt == "") {
        $('#sender_id_txt_1_txt').css('border-color', 'red');

        flag = false;
      }
      if (sender_id_txt_2_txt == "") {
        $('#sender_id_txt_2_txt').css('border-color', 'red');

        flag = false;
      }
      if (type_of_message == "") {
        $('#type_of_message').css('border-color', 'red');

        flag = false;
      }
      if (otp_in_process == "") {
        $('#otp_in_process').css('border-color', 'red');

        flag = false;
      }

      if (enquiry_approve_txt == "") {
        $('#enquiry_approve_txt').css('border-color', 'red');

        flag = false;
      }
      if (privacy_terms_txt == "") {
        $('#privacy_terms_txt').css('border-color', 'red');

        flag = false;
      }
      if (terms_condition_txt == "") {
        $('#terms_condition_txt').css('border-color', 'red');

        flag = false;
      }
      if (proof_document_slt == "") {
        $('#proof_document_slt').css('border-color', 'red');

        flag = false;
      }

      if (expected_volumes_day == "") {
        $('#expected_volumes_day').css('border-color', 'red');

        flag = false;
      }

      if (mobile_no_txt == "") {
        $('#mobile_no_txt').css('border-color', 'red');
        flag = false;
      }

      var mobile_no_txt = document.getElementById('mobile_no_txt').value;
      if (mobile_no_txt.length != 10) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        document.getElementById('mobile_no_txt').focus();
        flag = false;
      }
      if (!(mobile_no_txt.charAt(0) == "9" || mobile_no_txt.charAt(0) == "8" || mobile_no_txt.charAt(0) == "6" || mobile_no_txt.charAt(0) == "7")) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        document.getElementById('mobile_no_txt').focus();
        flag = false;
      }
      /************************************/
      if (contact_no_cmpy == "") {
        $('#contact_no_cmpy').css('border-color', 'red');

        flag = false;
      }

      var contact_no_cmpy = document.getElementById('contact_no_cmpy').value;
      if (contact_no_cmpy.length != 10) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        document.getElementById('contact_no_cmpy').focus();
        flag = false;
      }
      if (!(contact_no_cmpy.charAt(0) == "9" || contact_no_cmpy.charAt(0) == "8" || contact_no_cmpy.charAt(0) == "6" || contact_no_cmpy.charAt(0) == "7")) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        document.getElementById('contact_no_cmpy').focus();
        flag = false;
      }

      var sender_id_txt = document.getElementById('sender_id_txt').value;
      if (sender_id_txt.length != 10) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        flag = false;
        document.getElementById('sender_id_txt').focus();
      }
      if (!(sender_id_txt.charAt(0) == "9" || sender_id_txt.charAt(0) == "8" || sender_id_txt.charAt(0) == "6" || sender_id_txt.charAt(0) == "7")) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        document.getElementById('sender_id_txt').focus();
        flag = false;
      }


      var sender_id_txt = document.getElementById('sender_id_2_txt').value;
      if (sender_id_txt.length != 10) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        flag = false;
        document.getElementById('sender_id_2_txt').focus();
      }
      if (!(sender_id_txt.charAt(0) == "9" || sender_id_txt.charAt(0) == "8" || sender_id_txt.charAt(0) == "6" || sender_id_txt.charAt(0) == "7")) {
        $("#id_error_display_onboarding").html("Please enter a valid mobile number");
        document.getElementById('sender_id_2_txt').focus();
        flag = false;
      }

      /*************File Validation******************/
      const upload_limit = 5; //Maximum 2 MB
      // File type validation
      $("#profile_display_pic").change(function () {
        // xls, xlsx, csv, txt
        var file = this.files[0];
        var fileType = file.type;
        var match = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) || (fileType == match[4]) || (fileType == match[5]))) {
          $("#id_error_display_onboarding").html('Sorry, only PNG, JPG files are allowed to upload.');
          $("#profile_display_pic").val('');
          return false;
        }
        const size = this.files[0].size / 1024 / 1024;
        if (size < upload_limit) {
          return true;
        } else {
          $("#id_error_display_onboarding").html('Maximum File size allowed - ' + upload_limit + ' MB. Kindly reduce and choose below ' + upload_limit + ' MB');
          $("#profile_display_pic").val('');
          return false;
        }
      });
      $("#proof_document").change(function () {
        // xls, xlsx, csv, txt
        var file = this.files[0];
        var fileType = file.type;
        var match = ['application/pdf'];
        if (!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) || (fileType == match[4]) || (fileType == match[5]))) {
          $("#id_error_display_onboarding").html('Sorry,Only PDF files are allowed to be uploaded..');
          $("#proof_document").val('');
          return false;
        }
      });

      var email_id_contact = $('#email_id_contact').val();
      /* Email field validation  */
      var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
      if (filter.test(email_id_contact)) {
        // flag = true;
      } else {
        e.preventDefault();
        document.getElementById('email_id_contact').focus();
        $("#id_error_display_onboarding").html("Email is invalid");
        flag = false;
      }
      var email_id_txt = $('#email_id_txt').val();
      /* Email field validation  */
      var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
      if (filter.test(email_id_txt)) {
        //flag = true;
      } else {
        e.preventDefault();
        document.getElementById('email_id_txt').focus();
        $("#id_error_display_onboarding").html("Email is invalid");
        flag = false;
      }
      /********Validation end here ****/

      // alert("FLAG=="+flag+"==");
      $('#submit_onboarding').attr('disabled', false);
      /* If all are ok then we send ajax request to call_functions.php *******/
      if (flag) {
        var fd = new FormData(this);
        var files = $('#profile_display_pic')[0].files;
        if (files.length > 0) {
          fd.append('profile_display_pic_upld', files[0]);
        }
        var files1 = $('#proof_document')[0].files;
        if (files1.length > 0) {
          fd.append('proof_document_upld', files1[0]);
        }

        $.ajax({
          type: 'post',
          url: "ajax/call_functions.php",
          dataType: 'json',
          data: fd,
          contentType: false,
          processData: false,
          beforeSend: function () { // Before Send to Ajax
            $('#submit_onboarding').attr('disabled', true);
            $('#load_page').show();
          },
          complete: function () { // After complete the Ajax
            $('#submit_onboarding').attr('disabled', false);
            $('#load_page').hide();
          },
          success: function (response) { // Success
            // exit();
            if (response.status == 2 || response.status == 0) { // Failure Response
              $('#submit_onboarding').attr('disabled', false);
              $("#id_error_display_onboarding").html(response.msg);
            } else if (response.status == 1) { // Success Response
              $('#submit_onboarding').attr('disabled', false);
              $("#id_error_display_onboarding").html(response.msg);
              setInterval(function () {
                window.location = 'dashboard';
              }, 2000);
            }
          },
          error: function (response, status, error) { // If any error occurs
            // die();
            $('#submit_onboarding').attr('disabled', false);
            $("#id_error_display_onboarding").html(response.msg);
          }
        });
      }
    });
    // Sign up submit Button function End
    function address_validation(e) {
      // Accept only alphanumeric characters, spaces, '/', ',', '.', and '-'
      var key = e.key;
      var allowedCharacters = /^[a-zA-Z0-9/\s,.-]$/;
      if (allowedCharacters.test(key) || key === " ") {
        return true;
      }
      return false;
    }

    function clsAlphaNoOnly(e) { // Accept only alpha numerics, no special characters 
      var key = e.keyCode;
      if ((key >= 65 && key <= 90) || (key >= 97 && key <= 122) || (key >= 48 && key <= 57) || (key == 32) || (key == 95)) {
        return true;
      }
      return false;
    }

    // TEMplate Name - Space
    $(function () {
      $('#clientname_txt').on('keypress', function (e) {
        if (e.which == 32) {
          console.log('Space Detected');
          return false;
        }
      });
    });
    $(function () {
      $('#contact_person_txt').on('keypress', function (e) {
        if (e.which == 32) {
          console.log('Space Detected');
          return false;
        }
      });
    });

    function single_quote_validation(e) {
      // Accept only alphanumeric characters, spaces, '/', ',', '.', and '-'
      var key = e.key;
      var allowedCharacters = /^[a-zA-Z0-9\[\]\$\!\#\%\^\&\\(\)\-\+\=\-\;\:\,\.\?\@\_ ]$/;
      if (allowedCharacters.test(key) || key === " ") {
        return true;
      }
      return false;
    }

    function single_quote(e) {
      // Accept only alphanumeric characters, spaces, '/', ',', '.', '-', and some special characters
      var key = e.key;
      var allowedCharacters = /^[a-zA-Z0-9\[\]\$\!\#\%\^\&\/\(\)\-\+\=\-\;\:\,\.\?\@\_\ ]$/;
      if (allowedCharacters.test(key) || key === " ") {
        return true;
      }
      return false;
    }

  </script>
</body>

</html>