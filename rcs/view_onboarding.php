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

if ($_SESSION['yjwatsp_user_master_id'] != 1) { ?>
  <script>window.location = "dashboard";</script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("View On Boarding Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));

// To Send the request  API
$replace_txt = '{
  "user_id" : "' . $_REQUEST["usr"] . '"
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
site_log_generate("View On Boarding Page : " . $uname . " Execute the service [$replace_txt, $bearer_token] on " . date("Y-m-d H:i:s"), "../");
$response = curl_exec($curl);
curl_close($curl);

// After got response decode the JSON result
$state1 = json_decode($response, false);

// Log file generate
site_log_generate("View On Boarding Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), "../");

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
    $_SESSION["profile_images"] = $profile_image;
    $business_category = $state1->report[$indicator]->business_category;
    $sender = $state1->report[$indicator]->sender;
    $sender2 = $state1->report[$indicator]->sender2;
    $sender_1 = $state1->report[$indicator]->sender_1;
    $sender_2 = $state1->report[$indicator]->sender_2;

    $message_type = $state1->report[$indicator]->message_type;
    $opt_process = $state1->report[$indicator]->opt_process;
    $enquiry_approval = $state1->report[$indicator]->enquiry_approval;
    $company_display_name = $state1->report[$indicator]->company_display_name;
    $privacy_terms = $state1->report[$indicator]->privacy_terms;

    $terms_conditions = $state1->report[$indicator]->terms_conditions;
    $document_proof = $state1->report[$indicator]->document_proof;
    $proof_doc_name = $state1->report[$indicator]->proof_doc_name;
    $_SESSION["proof_doc_names"] = $proof_doc_name;
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
  <title>View On Boarding ::
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
            <h1>View On Boarding</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item">View On Boarding</div>
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
                        <span class="col-6 label">
                          Client Name :
                        </span>
                        <span class="col-6">
                          <? $_SESSION['pdf_generate_uname'] = $user_name; ?>
                          <?= $_SESSION['pdf_generate_uname'] ?>
                        </span>
                      </div>
                      <div class="row mt-2  grid_clr_white">
                        <span class="col-6 label">
                          Contact Person :
                        </span>
                        <span class="col-6">
                          <?= $cont_person ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Designation :
                        </span>
                        <span class="col-6">
                          <?= $cont_designation ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Contact No :
                        </span>
                        <span class="col-6">
                          <?= $cont_mobile_no ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Contact Email ID :
                        </span>
                        <span class="col-6">
                          <?= $cont_email ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Billing Address & GST :
                        </span>
                        <span class="col-6">
                          <?= $billing_address ?>
                        </span>
                      </div>

                      </br></br>
                      <h5 class="text-center"><i class="icofont icofont-sign-in"></i>Company Information </h5></br>

                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Name Of The Business :
                        </span>
                        <span class="col-6">
                          <?= $company_name ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Company Website Details :
                        </span>
                        <span class="col-6">
                          <?= $company_website ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Parents Company Name :
                        </span>
                        <span class="col-6">
                          <?= $parent_company_name ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Company Display Name (To be configured) :
                        </span>
                        <span class="col-6">
                          <?= $company_display_name ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Description Of The Business :
                        </span>
                        <span class="col-6">
                          <?= $description_business ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Registered Address Of The Business :
                        </span>
                        <span class="col-6">
                          <?= $user_address ?>
                        </span>
                      </div>

                      </br></br>
                      <h5 class="text-center"><i class="icofont icofont-sign-in"></i>Communication Information </h5>
                      </br>

                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Email ID :
                        </span>
                        <span class="col-6">
                          <?= $user_email ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Contact No :
                        </span>
                        <span class="col-6">
                          <?= $user_mobile ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Profile/ Display Picture Of The Business
                        </span>
                        <span class="col-6">
                          <? if ($profile_image != '') {
                            $_SESSION['profile_image'] = $profile_image; ?><img
                              src="uploads/whatsapp_images/<?= $profile_image ?>" style="width: 100px; height: auto;">
                          <? } ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Select Business Category :
                        </span>
                        <span class="col-6">
                          <?   // To Show the Service Category list
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
                          site_log_generate("View On Boarding Page : " . $uname . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                          $response = curl_exec($curl);
                          curl_close($curl);
                          // After got response decode the JSON result
                          $state1 = json_decode($response, false);
                          site_log_generate("View On Boarding Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
                          $i1 = 0;
                          // Based on the JSON response, list in the option button
                          if ($state1->num_of_rows > 0) {
                            // Looping the indicator is less than the count of report.if the condition is true to continue the process and to get the report details.if the condition are false to stop the process and to send the no available data
                            for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
                              $message_category_id = $state1->report[$indicator]->message_category_id;
                              $message_category_title = $state1->report[$indicator]->message_category_title;
                              $i1++; ?>
                              <? if ($business_category == $message_category_id) { ?>
                                <?= $message_category_title ?>
                              <? } ?>
                              <?
                            }
                          } else if ($state1->response_status == 403 || $response == '') { ?>
                              <script>
                                window.location = "index"
                              </script>
                          <? }
                          ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Sender ID :
                        </span>
                        <span class="col-6">
                          <?= $sender ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Sender ID 1 (Communication) :
                        </span>
                        <span class="col-6">
                          <?= $sender_1 ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Sender ID 2:
                        </span>
                        <span class="col-6">
                          <?= $sender2 ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Sender ID 2 (Communication) :
                        </span>
                        <span class="col-6">
                          <?= $sender_2 ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Type Of Message :
                        </span>
                        <span class="col-6">
                          <? if ($message_type == 2) { ?> Promotional
                          <? } elseif ($message_type == 3) { ?> Both
                          <? } else { ?> Transactional
                          <? } ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Opt In Process :
                        </span>
                        <span class="col-6">
                          <? if ($opt_process == 2) { ?> SMS
                          <? } elseif ($opt_process == 3) { ?> Website & Others
                          <? } else { ?> SMS
                          <? } ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Enquiry/ Approval Page :
                        </span>
                        <span class="col-6">
                          <?= $enquiry_approval ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Privacy & Terms Of Service Page :
                        </span>
                        <span class="col-6">
                          <?= $privacy_terms ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Terms & Condition Page :
                        </span>
                        <span class="col-6">
                          <?= $terms_conditions ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Proof Of Document :
                        </span>
                        <span class="col-6">
                          <? if ($document_proof == 2) { ?> Utility bill (Mobile, Electricity, Bank statement)
                          <? } elseif ($document_proof == 3) { ?> Change of Business name
                          <? } elseif ($document_proof == 4) { ?> Certification of incorporation
                          <? } elseif ($document_proof == 5) { ?> Tax document
                          <? } else { ?> Business registration or License document
                          <? } ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_green">
                        <span class="col-6 label">
                          Upload PDF :
                        </span>
                        <span class="col-6">
                          <? if ($proof_doc_name != '') {
                            $_SESSION['proof_doc_name'] = $proof_doc_name; ?><a
                              href="uploads/whatsapp_docs/<?= $_SESSION["proof_doc_name"] ?>" download>Download Document</a>
                          <? } ?>
                        </span>
                      </div>
                      <div class="row mt-2 grid_clr_white">
                        <span class="col-6 label">
                          Expected Volumes For Day :
                        </span>
                        <span class="col-6">
                          <?= $volume_day_expected ?>
                        </span>
                      </div>
                    </div>

                    <? if ($usr_mgt_status != 'Y') { ?>
                      <div class="row mt-2 grid_clr_green " id="remove_content">
                        <div class="col-6 label " style=" text-align: center;vertical-align: middle;">
                          <span class="error_display text-center"> Add Rejected Comments </span>
                          <input type="text" class="form-control" name='txt_remarks' id='txt_remarks'
                            value='<?= $rejected_comments ?>' maxlength="250" />
                        </div>
                        <div class="col-6" style=" text-align: center;vertical-align: middle;">
                          <span class="error_display text-center" id='id_error_display_onboarding'></span>
                          <input type="hidden" class="form-control" name='txt_user' id='txt_user'
                            value='<?= $_REQUEST["usr"] ?>' />
                          <input type="hidden" class="form-control" name='call_function' id='call_function'
                            value='apprej_onboarding' />
                          <? if ($usr_mgt_status != 'R') { ?>
                            <button type="button" onclick="approve_usr_popup()"
                              style="width:150px; text-align: center;vertical-align: middle;" tabindex="30" title="Approve"
                              class="btn btn-success btn-md btn-block waves-effect waves-light text-center ">Approve</button>
                            <button onclick="reject_usr_popup()" type="button" title="Reject"
                              style="width:150px; text-align: center;vertical-align: middle;" tabindex="31"
                              class="btn btn-danger btn-md btn-block waves-effect waves-light text-center ">Reject</button>
                          <? } ?>
                        </div>
                      </div>
                    <? } ?>
                    <?/*<div class="row m-t-30">
                    <div class="col-md-12" style="text-align:center;">&nbsp;</div>
                  </div>
<? if($usr_mgt_status != 'Y')  { ?>
                  <div class="row m-t-30">
                    <div class="col-md-12" style="text-align:center;">
                      <span class="error_display text-center" id='id_error_display_onboarding'></span>&nbsp;
                    </div>
                  </div>
                  <div class="row  m-t-30 ">
                    <div class="col-md-8" style="text-align:center">
                      <input type="text" class="form-control" name='txt_remarks' id='txt_remarks' value='<?=$rejected_comments?>' maxlength="250" />
                    </div>
<? if($usr_mgt_status != 'R')  { ?>
                    <div class="col-md-4" style="text-align:center">
                      <input type="hidden" class="form-control" name='txt_user' id='txt_user' value='<?=$_REQUEST["usr"]?>' />
                      <input type="hidden" class="form-control" name='call_function' id='call_function' value='apprej_onboarding' />
                       <button type="button"  onclick="approve_usr_popup()" style="width:150px;float: left;margin-left:auto;margin-right:auto;" tabindex="30" title="Approve" class="btn btn-success btn-md btn-block waves-effect waves-light text-center ">Approve</button>
                      <button onclick="reject_usr_popup()" type="button" title="Reject" style="width:150px;float: left;margin-left:auto;margin-right:auto;margin-top: 0px;" tabindex="31"  class="btn btn-danger btn-md btn-block waves-effect waves-light text-center ">Reject</button>
                    </div>
<? } ?>
                  </div>
<? }*/ ?>
                    <div class="row m-t-30">
                      <div class="col-md-12" style="text-align:center;">&nbsp;</div>
                    </div>

                </div>
                </form>
                <div class="text-center">
                  <button class="btn btn-success text-center" type="button" id="generatePdf"
                    style="text-align:center;">Generate ZIP</button>
                </div>
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

  <!-- Confirmation details content Reject-->
  <div class="modal" tabindex="-1" role="dialog" id="reject-Modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Confirmation details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to reject ?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Reject</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirmation details content Approve-->
  <div class="modal" tabindex="-1" role="dialog" id="approve-Modal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Confirmation details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to approve ?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">Approve</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
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
  </script>
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <!--Remove dublicates numbers -->
  <script>
    // start function document
    $(function () {
      $('#id_qrcode').fadeOut("slow");
    });
    //document.getElementById('generatePdf').addEventListener('click', function() {
    //const content = document.getElementById('frm_edit_onboarding').innerHTML;

    // Send the captured content to a server-side script for PDF generation
    //fetch('generate_pdf.php', {
    //method: 'POST',
    //headers: {
    //'Content-Type': 'application/json',
    //},
    //body: JSON.stringify({ content }),
    //})
    //.then(response => response.blob())
    //.then(blob => {
    //const url = window.URL.createObjectURL(blob);
    //const a = document.createElement('a');
    //a.href = url;
    //a.download = 'generated_pdf.pdf';
    //a.click();
    //window.URL.revokeObjectURL(url);
    //});
    //});
    //document.getElementById('generatePdf').addEventListener('click', function() {
    // const content = document.getElementById('frm_edit_onboarding').innerHTML;
    // Find the div element to remove by its id
    //var divToRemove = content.querySelector('#remove_content');

    // Check if the div exists and remove it
    //if (divToRemove) {
    //  divToRemove.parentNode.removeChild(divToRemove);
    //}
    // Send the captured content to a server-side script for PDF generation
    //  fetch('generate_pdf.php', {
    //  method: 'POST',
    //headers: {
    //  'Content-Type': 'application/json',
    //},
    //body: JSON.stringify({ content }),
    //})
    //.then(response => response.blob())
    // .then(blob => {
    // const url = window.URL.createObjectURL(blob);

    // Trigger the download after generating the PDF
    // downloadZipWithPDF(url);
    // });
    //});


    document.getElementById('generatePdf').addEventListener('click', function () {
      const content = document.getElementById('frm_edit_onboarding').innerHTML;

      // Create a temporary container element to parse the HTML content
      var container = document.createElement('div');
      container.innerHTML = content;

      // Find the div element to remove by its id
      var divToRemove = container.querySelector('#remove_content');

      // Check if the div exists and remove it
      if (divToRemove) {
        divToRemove.parentNode.removeChild(divToRemove);
      }

      // Get the sanitized HTML content
      var sanitizedHTML = container.innerHTML;

      // Send the captured content to a server-side script for PDF generation
      fetch('generate_pdf.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ content: sanitizedHTML }),
      })
        .then(response => response.blob())
        .then(blob => {
          const url = window.URL.createObjectURL(blob);

          // Trigger the download after generating the PDF
          downloadZipWithPDF(url);
        });
    });

    function downloadZipWithPDF(pdfUrl) {
      // Create a hidden form and submit it to download the ZIP
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'download_zip.php'; // PHP script to generate and serve the ZIP file
      form.style.display = 'none';

      // Add an input field for the PDF URL
      const pdfInput = document.createElement('input');
      pdfInput.type = 'text';
      pdfInput.name = 'pdfUrl';
      pdfInput.value = pdfUrl;
      form.appendChild(pdfInput);

      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    }


    //popup function
    function reject_usr_popup() {
      $('#reject-Modal').modal({ show: true });
      // Call remove_senderid function with the provided parameters
      $('#reject-Modal').find('.btn-danger').on('click', function () {
        $('#reject-Modal').modal({ show: false });
        reject_function();
      });
    }

    function reject_function() {
      // $("#submit_user_reject").click(function(e) {
      //e.preventDefault();
      var txt_remarks = $('#txt_remarks').val();

      // alert("##"+txt_remarks+"##");
      var flag = true;
      if (txt_remarks == "") {
        $('#txt_remarks').css('border-color', 'red');
        console.log("##");
        flag = false;
      }

      /* If all are ok then we send ajax request to call_functions.php *******/
      if (flag) {
        // die();
        var fd = $("#frm_edit_onboarding").serialize();
        $.ajax({
          type: 'post',
          url: "ajax/call_functions.php?aprj_status=R",
          dataType: 'json',
          data: fd,
          beforeSend: function () { // Before Send to Ajax
            $('#submit_user_approve').attr('disabled', true);
            $('#submit_user_reject').attr('disabled', true);
            $('#load_page').show();
          },
          complete: function () { // After complete the Ajax
            $('#submit_user_approve').attr('disabled', false);
            $('#submit_user_reject').attr('disabled', false);
            $('#load_page').hide();
          },
          success: function (response) { // Success
            // exit();
            if (response.status == '2' || response.status == '0') { // Failure Response
              $('#submit_user_approve').attr('disabled', false);
              $('#submit_user_reject').attr('disabled', false);
              $("#id_error_display_onboarding").html(response.msg);
            } else if (response.status == 1) { // Success Response
              $('#submit_user_approve').attr('disabled', false);
              $('#submit_user_reject').attr('disabled', false);
              window.location = "manage_users_list";
            }
          },
          error: function (response, status, error) { // If any error occurs
            // die();
            $('#submit_user_approve').attr('disabled', false);
            $('#submit_user_reject').attr('disabled', false);
            $("#id_error_display_onboarding").html(response.msg);
          }
        });
      }
    }
    // });


    //approve_usr_popup function
    function approve_usr_popup() {
      $('#approve-Modal').modal({ show: true });
      // Call remove_senderid function with the provided parameters
      $('#approve-Modal').find('.btn-success').on('click', function () {
        $('#approve-Modal').modal({ show: false });
        approve_function();
      });
    }
    // $("#submit_user_approve").click(function(e) {
    function approve_function() {
      var fd = $("#frm_edit_onboarding").serialize();
      $.ajax({
        type: 'post',
        url: "ajax/call_functions.php?aprj_status=A",
        dataType: 'json',
        data: fd,
        beforeSend: function () { // Before Send to Ajax
          $('#submit_user_approve').attr('disabled', true);
          $('#submit_user_reject').attr('disabled', true);
          $('#load_page').show();
        },
        complete: function () { // After complete the Ajax
          $('#submit_user_approve').attr('disabled', false);
          $('#submit_user_reject').attr('disabled', false);
          $('#load_page').hide();
        },
        success: function (response) { // Success
          // exit();
          if (response.status == '2' || response.status == '0') { // Failure Response
            $('#submit_user_approve').attr('disabled', false);
            $('#submit_user_reject').attr('disabled', false);
            $("#id_error_display_onboarding").html(response.msg);
          } else if (response.status == 1) { // Success Response
            $('#submit_user_approve').attr('disabled', false);
            $('#submit_user_reject').attr('disabled', false);
            window.location = "manage_users_list";
          }
        },
        error: function (response, status, error) { // If any error occurs
          $('#submit_user_approve').attr('disabled', false);
          $('#submit_user_reject').attr('disabled', false);
          $("#id_error_display_onboarding").html(response.msg);
        }
      });
    }
    // });
  </script>
</body>

</html>