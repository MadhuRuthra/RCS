<?php
/*
Authendicated users only allow to view this Compose Whatsapp page.
This page is used to Compose Whatsapp messages.
It will send the form to API service and send it to the Whatsapp Facebook
and get the response from them and store into our DB.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/

session_start(); // start session
error_reporting(0); // The error reporting function

include_once ('api/configuration.php'); // Include configuration.php
extract($_REQUEST); // Extract the request

// If the Session is not available redirect to index page
if ($_SESSION['yjwatsp_user_id'] == "") { ?>
  <script>
    window.location = "index";
  </script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Compose Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Compose Whatsapp ::
    <?= $site_title ?>
  </title>
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/components.css">

  <!-- style include in css -->
  <style>
    textarea {
      resize: none;
    }

    .btn-warning,
    .btn-warning.disabled {
      width: 100% !important;
    }

    .theme-loader {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 100;
      width: 100%;
      height: 100%;
      background-color: rgba(192, 192, 192, 0.5);
      background-image: url("assets/img/loader.gif");
      background-repeat: no-repeat;
      background-position: center;
    }

    .theme-loader1 {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 100;
      width: 100%;
      height: 100%;
      background-color: rgba(192, 192, 192, 0.5);
      background-image: url("assets/img/inprogress.gif");
      background-repeat: no-repeat;
      background-position: center;
    }

    .preloader-wrapper {
      display: flex;
      justify-content: center;
      background: rgba(22, 22, 22, 0.3);
      width: 100%;
      height: 100%;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
      align-items: center;
    }

    .preloader-wrapper>.preloader {
      background: transparent url("assets/img/ajaxloader.webp") no-repeat center top;
      min-width: 128px;
      min-height: 128px;
      z-index: 10;
      /* background-color:#f27878; */
      position: fixed;
    }

    .updateprocessing {
      display: flex;
      justify-content: center;
      background: rgba(22, 22, 22, 0.3);
      width: 100%;
      height: 100%;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
      align-items: center;
    }

    .updateprocessing>.miniloader {
      background: transparent url("assets/img/ajaxloader.webp") no-repeat center top;
      min-width: 128px;
      min-height: 128px;
      z-index: 10;
      /* background-color:#f27878; */
      position: fixed;
    }

    html,
    body {
      height: 100%;
    }

    body {
      padding: 0;
      margin: 0;
      color: #2c3e51;
      background: #f5f5f5;
      font-family: 'Ubuntu', sans-serif;
    }

    .container {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .main {
      margin: 1rem;
      max-width: 350px;
      width: 50%;
      height: 250px;
    }

    @media(max-width:34em) {
      .main {
        min-width: 150px;
        width: auto;
      }
    }

    select {
      display: none !important;
    }

    .dropdown-select {
      background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);
      background-repeat: repeat-x;
      filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);
      background-color: #fff;
      border-radius: 6px;
      border: solid 1px #eee;
      box-shadow: 0px 2px 5px 0px rgba(155, 155, 155, 0.5);
      box-sizing: border-box;
      cursor: pointer;
      display: block;
      float: left;
      font-size: 14px;
      font-weight: normal;
      height: 42px;
      line-height: 40px;
      outline: none;
      padding-left: 18px;
      padding-right: 30px;
      position: relative;
      text-align: left !important;
      transition: all 0.2s ease-in-out;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      white-space: nowrap;
      width: auto;

    }

    .dropdown-select:focus {
      background-color: #fff;
    }

    .dropdown-select:hover {
      background-color: #fff;
    }

    .dropdown-select:active,
    .dropdown-select.open {
      background-color: #fff !important;
      border-color: #bbb;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;
    }

    .dropdown-select:after {
      height: 0;
      width: 0;
      border-left: 4px solid transparent;
      border-right: 4px solid transparent;
      border-top: 4px solid #777;
      -webkit-transform: origin(50% 20%);
      transform: origin(50% 20%);
      transition: all 0.125s ease-in-out;
      content: '';
      display: block;
      margin-top: -2px;
      pointer-events: none;
      position: absolute;
      right: 10px;
      top: 50%;
    }

    .dropdown-select.open:after {
      -webkit-transform: rotate(-180deg);
      transform: rotate(-180deg);
    }

    .dropdown-select.open .list {
      -webkit-transform: scale(1);
      transform: scale(1);
      opacity: 1;
      pointer-events: auto;
    }

    .dropdown-select.open .option {
      cursor: pointer;
    }

    .dropdown-select.wide {
      width: 100%;
    }

    .dropdown-select.wide .list {
      left: 0 !important;
      right: 0 !important;
    }

    .dropdown-select .list {
      box-sizing: border-box;
      transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear;
      -webkit-transform: scale(0.75);
      transform: scale(0.75);
      -webkit-transform-origin: 50% 0;
      transform-origin: 50% 0;
      box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09);
      background-color: #fff;
      border-radius: 6px;
      margin-top: 4px;
      padding: 3px 0;
      opacity: 0;
      overflow: hidden;
      pointer-events: none;
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 999;
      max-height: 250px;
      overflow: auto;
      border: 1px solid #ddd;
    }

    .dropdown-select .list:hover .option:not(:hover) {
      background-color: transparent !important;
    }

    .dropdown-select .dd-search {
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0.5rem;
    }

    .dropdown-select .dd-searchbox {
      width: 100%;
      height: 50px;
      padding: 0.5rem;
      border: 1px solid #999;
      border-color: #999;
      border-radius: 4px;
      outline: none;
    }

    .dropdown-select .dd-searchbox:focus {
      border-color: #12CBC4;
    }

    .dropdown-select .list ul {
      padding: 0;
    }

    .dropdown-select .option {
      cursor: default;
      font-weight: 400;
      line-height: 40px;
      outline: none;
      padding-left: 18px;
      padding-right: 29px;
      text-align: left;
      transition: all 0.2s;
      list-style: none;
    }

    .dropdown-select .option:hover,
    .dropdown-select .option:focus {
      background-color: #f6f6f6 !important;
    }

    .dropdown-select .option.selected {
      font-weight: 600;
      color: #12cbc4;
    }

    .dropdown-select .option.selected:focus {
      background: #f6f6f6;
    }

    .dropdown-select a {
      color: #aaa;
      text-decoration: none;
      transition: all 0.2s ease-in-out;
    }

    .dropdown-select a:hover {
      color: #666;
    }

    .suggestion-card-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Creates three equal-width columns */
    gap: 16px; /* Space between cards */
}

.suggestion-card {
    padding: 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
    background-color: #fff; /* Optional: background color for cards */
}
  </style>
</head>

<body>
  <div class="theme-loader"></div>
  <div class="theme-loader1"></div>
  <div class="preloader-wrapper" style="display:none;">
    <div class="preloader">
    </div>
    <div class="text" style="color: white; background-color:#f27878; padding: 10px; margin-left:400px;">
      <b>Mobile number validation processing ...<br /> Please wait.</b>
    </div>
  </div>
  <div class="updateprocessing" style="display:none;">
    <div class="miniloader">
    </div>
    <!-- <div class="text" style="color: white; background-color:#63ed7a; padding: 10px; margin-left:400px;"> -->
    <div class="text" style="color: white; background-color:#f27878; padding: 10px; margin-left:400px;">
      <b>File update processing...<br /> Please wait.</b>
    </div>
  </div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <!-- include header function adding -->
      <? include ("libraries/site_header.php"); ?>

      <!-- include sitemenu function adding -->
      <? include ("libraries/site_menu.php"); ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <!-- Title and Breadcrumbs -->
          <div class="section-header">
            <h1>Compose RCS</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="template_rcs_list">RCS List</a></div>
              <div class="breadcrumb-item">Compose RCS</div>
            </div>
          </div>

          <!-- Title and Breadcrumb -->
          <div class="section-body">
            <div class="row">

              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form class="needs-validation" novalidate="" id="frm_compose_whatsapp" name="frm_compose_whatsapp"
                    action="#" method="post" enctype="multipart/form-data">
                    <!-- Select Whatsapp Template -->
                    <div class="card-body">
                      <div class="form-group mb-4 row">
                        <label class="col-sm-3 col-form-label">Select RCS Template <label
                            style="color:#FF0000">*</label></label>
                        <div class="col-sm-7">

                          <select name="slt_whatsapp_template" id='slt_whatsapp_template' class="form-control"
                            data-toggle="tooltip" data-placement="top" title="" onchange="call_getsingletemplate()"
                            data-original-title="Select Whatsapp Template" tabindex="1" autofocus required="">
                            <!-- onchange="call_getsingletemplate()" onfocus="func_template_senderid()" -->
                            <option value="" selected style="text-align:center;">Choose
                              RCS Template</option>

                            <? // To using the select template 
                            $load_templates = '{
                                "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
                          }';// Add user id
                            site_log_generate("Compose Business Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " Execute the service ($load_templates) on " . date("Y-m-d H:i:s"));

                            $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';// Add Bearer Token  
                            $curl = curl_init();
                            curl_setopt_array(
                              $curl,
                              array(
                                CURLOPT_URL => $api_url . '/template/get_template',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => $load_templates,
                                CURLOPT_HTTPHEADER => array(
                                  $bearer_token,
                                  'Content-Type: application/json'

                                ),
                              )
                            );
                            // Send the data into API and execute
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $state1 = json_decode($response, false);
                            site_log_generate("Compose Business Whatsapp Page : User : " . $_SESSION['yjwatsp_user_name'] . " get the Service response ($response) on " . date("Y-m-d H:i:s"));
                            // After got response decode the JSON result
                            if ($state1->response_code == 1) {
                              // Looping the indicator is less than the count of templates.if the condition is true to continue the process and to get the details.if the condition are false to stop the process
                              for ($indicator = 0; $indicator < count($state1->templates); $indicator++) { // Set the response details into Option                                           ?>
                                <option 
                                  value="<?= $state1->templates[$indicator]->template_name ?>!<?= $state1->templates[$indicator]->template_category ?>!<?= $state1->templates[$indicator]->body_variable_count ?>!<?= $state1->templates[$indicator]->template_id ?>!<?= $indicator ?>">
                                  <?= $state1->templates[$indicator]->template_name ?>
                                  [<?= $state1->templates[$indicator]->template_category ?>]
                                </option>
                              
                              <? }
                               for ($indicator = 0; $indicator < count($state1->templates); $indicator++) { // Set the response details into Option                                           ?>
                                <textarea id ="template_message_<?= $indicator ?>" style="display:none"><?= htmlspecialchars($state1->templates[$indicator]->template_message) ?></textarea>
                              
                              <? }
                            } else if ($state1->response_status == 403 || $response == '') {
                              header("Location: index");
                            }
                            ?>
                            </table>
                          </select>

                        </div>
                      
                        <div class="col-sm-2">
                          <button type="button" class="btn btn-success mr-2" style="margin-top: 10px; display:none" id="view_button" onclick="templateView()">View Template</button>
                        </div>
                      </div>


                      <!-- duplicate_value Options-->
                      <div class="form-group mb-4 row display_after_template" style="display:none">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-7">
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="duplicate_value" id="duplicate_allowed"
                              value="duplicate_allowed">Duplicate allowed
                            <label class="form-check-label" for="Duplicate Allowed"></label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="duplicate_value"
                              id="duplicate_not_allowed" value="duplicate_not_allowed" checked>
                            <label class="form-check-label" for="Duplicate Not Allowed">Duplicate not allowed</label>
                          </div>
                        </div>
                        <div class="col-sm-2"></div>
                      </div>

                      <!-- Campaign Name  -->
                      <div class="form-group mb-2 row" style="display: none">
                        <label class="col-sm-3 col-form-label">Campaign Name <label style="color:#FF0000">*</label>
                          <span data-toggle="tooltip"
                            data-original-title="Campaign Name allowed maximum 30 Characters. Unique values only allowed">[?]</span></label>
                        <div class="col-sm-7">
                          <input type="text" name="txt_campaign_name" id='txt_campaign_name' class="form-control"
                            value="Campaign Name" required="" maxlength="30" onblur="func_validate_campaign_name()"
                            placeholder="Enter Campaign Name" data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="Enter Campaign Name">
                          <input type='hidden' name="id_slt_mobileno" id="id_slt_mobileno"
                            value="<?= $whatsapp_bearer_token ?>||<?= $whatsapp_tmpl_url ?>||0||<?= $whatsapp_tmplsend_url ?>" />
                          </select>
                        </div>
                        <div class="col-sm-2">
                        </div>
                      </div>

                      <!-- To upload the Customized Template -->
                      <div class="form-group mb-2 row template_message_container" style="display:none">
                        <label class="col-sm-3 col-form-label">Template Message &nbsp;</label>
                        <div class="col-sm-7">
                          <div style="clear: both; word-wrap: break-word; word-break: break-word;"
                            id="slt_whatsapp_template_single">-</div>
                          <input type="hidden" id="txt_sms_content" name="txt_sms_content">
                          <div id="id_show_variable_csv" style="clear: both; display: none">
                            <label class="error_display"><b>Upload Base</b></label>
                            <input type="file" class="form-control" name="fle_variable_csv" id='fle_variable_csv'
                              onclick="chooseFile(this)" accept="text/csv" data-toggle="tooltip" data-placement="top"
                              data-html="true" title="" data-original-title="Upload the Mobile Numbers via CSV Files"
                              tabindex="8">
                            <input type="hidden" id="txt_variable_count" name="txt_variable_count" value="0">
                            <input type="hidden" id="media_type" name="media_type" value="">
                            <label class="j-label mt-1">
                              <!-- Add logic to conditionally download different sample CSV files -->
                              <a id="sample_csv_link" class="btn btn-info alert-ajax btn-outline-info"
                                onclick="downloadSampleCSV()">
                                <i class="fas fa-download"></i> Download Sample CSV File
                              </a>
                            </label>
                          </div>
                          <span class="introduction_notes" style="color:#FF0000"></span>
                        </div>
                        <div class="col-sm-2">
                          <span class="introduction_notes_mb" style="color:#FF0000;display:none;"><b>Pls upload the file
                              in
                              the below format :</b> <br> 1) Read as: Column1.<br> 2) contacts
                            <br> 3) 9190000000</span>
                        </div>
                      </div>

                      <!-- Enter Mobile Numbers -->
                      <div class="form-group mb-2 row mobile_number_container display_after_template" style="display:none">
                        <label class="col-sm-3 col-form-label">Mobile Number Base : <label
                            style="color:#FF0000">*</label> <span data-toggle="tooltip"
                            data-original-title="Mobile numbers allowed with Country Code and without + symbol. Upload Mobile numbers using CSV File">[?]</span>
                          <!-- <label style="color:#FF0000">(With Country Code and without +
                            symbol. New-Line
                            Separated.)</label> -->
                          </label>
                        <!-- <div class="col-sm-7">
                          <textarea id="txt_list_mobno" name="txt_list_mobno" tabindex="2" required=""
                            onblur="call_remove_duplicate_invalid()" onclick="call_remove_duplicate_invalid()"
                            onkeypress="return isValidInput(event)"
                            placeholder="919234567890,919234567891,919234567892,919234567893"
                            class="form-control form-control-primary required" data-toggle="tooltip"
                            data-placement="top" data-html="true" title=""
                            data-original-title="Mobile Number Base . Each row must contains only one mobile no with Country Code and without + symbol. For Ex : 919234567890,919234567891,919234567892,919234567893"
                            style="height: 150px !important; width: 100%;"></textarea>
                          <div id='txt_list_mobno_txt' class='text-danger'></div>

                          <?php //if ($_SESSION['yjwatsp_user_master_id'] != 1) { ?>
                            <label style="color:#FF0000">Note: Campaign Should Have Atleast 100
                              Numbers</label>
                          <?php //} ?>

                        </div> -->
                        <!-- Remove duplicates -->
                        <div class="col-sm-7">
                          <div class="checkbox-fade fade-in-primary" style="display: none;">
                            <label data-toggle="tooltip" data-placement="top" data-html="true" title=""
                              data-original-title="Click here to Remove the Duplicates">
                              <input type="checkbox" name="chk_remove_duplicates" id="chk_remove_duplicates" checked
                                value="remove_duplicates" tabindex="10" onclick="call_remove_duplicate_invalid()">
                              <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                              <span class="text-inverse" style="color:#FF0000 !important">Remove
                                Duplicates</span>
                            </label>
                          </div>
                          <!-- To using The invalid mobile numbers  -->
                          <div class="checkbox-fade fade-in-primary" style="display: none;">
                            <label data-toggle="tooltip" data-placement="top" data-html="true" title=""
                              data-original-title="Click here to remove Invalids Mobile Nos">
                              <input type="checkbox" name="chk_remove_invalids" id="chk_remove_invalids" checked
                                value="remove_invalids" tabindex="10" onclick="call_remove_duplicate_invalid()">
                              <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                              <span class="text-inverse" style="color:#FF0000 !important">Remove Invalids</span>
                            </label>
                          </div>
                          <!-- Remove Stop Status Mobile No's -->
                          <div class="checkbox-fade fade-in-primary" style="display: none;">
                            <label data-toggle="tooltip" data-placement="top" data-html="true" title=""
                              data-original-title="Click here to remove Stop Status Mobile No's">
                              <input type="checkbox" name="chk_remove_stop_status" id="chk_remove_stop_status" checked
                                value="remove_stop_status" tabindex="10" onclick="call_remove_duplicate_invalid()">
                              <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                              <span class="text-inverse" style="color:#FF0000 !important">Remove Stop Status
                                Mobile
                                No's</span>
                            </label>
                          </div>
                          <!-- To upload check the files -->
                          <div class="checkbox-fade fade-in-primary" id='id_mobupload'>
                            <input type="file" class="form-control" name="upload_contact" id='upload_contact'
                              tabindex="3" <? if
                              ($display_action == 'Add') { ?>required="" <? } ?> accept="text/csv"
                              onclick="chooseFile(this)" data-toggle="tooltip" data-placement="top" data-html="true"
                              title="" data-original-title="Upload the Mobile Numbers via
                                                        Excel, CSV, Text Files" required>
                            <label style="color:#FF0000">[Upload the Mobile Numbers via CSV
                              File]</label></br>
                            <a class="btn btn-info alert-ajax btn-outline-info" onclick="downloadSampleCSV()">
                              <i class="fas fa-download"></i> Download Sample CSV File
                            </a>
                          </div>
                          <div class="checkbox-fade fade-in-primary" id='id_mobupload_sub' style="display: none;">
                            <label class="error_display"><b>Kindly upload the mobile numbers
                                from Customized Template
                                Panel</b></label>
                          </div>
                        </div>
                      </div>
                      <!-- To upload check the files -->
                      <div class="form-group mb-3 row" style="display: none;">
                        <label class="col-sm-3 col-form-label" style="float: left">Upload Media
                          Files</label>
                        <div class="col-sm-7" style="float: left">
                          <input type="file" class="form-control mb-1" name="txt_media" id="txt_media" tabindex="4"
                            accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,video/h263,video/m4v,video/mp4,video/mpeg,video/mpeg4,video/webm"
                            data-toggle="tooltip" data-placement="top" data-html="true" title=""
                            data-original-title="Upload Any Media below or equal to 5 MB Size - Upload Only the Audio MP4, Video MP4, JPG, PNG, PDF file">
                          <input type="text" name="txt_caption" id='txt_caption' tabindex="4" class="form-control"
                            value="" maxlength="200"
                            placeholder="Enter Media Caption [Maximum - 150 Characters allowed]" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Enter Media Caption">
                        </div>
                        <div class="col-sm-2">
                        </div>
                      </div>
                      <!-- if the url button is select to visible the url Button -->
                      <div class="form-group mb-2 row" id="id_open_url" style="display: none;">
                        <div class="form-group mb-2 row">
                          <label class="col-sm-3 col-form-label">URL Button</label>
                          <div class="col-sm-7">
                            <table class="table table-striped table-bordered m-0"
                              style="table-layout: fixed; white-space: inherit; width: 100%; overflow-x: scroll;">
                              <tbody>
                                <tr>
                                  <td class="col-md-6" style="width: 50%;">
                                    <input class="form-control" type="url" name="txt_open_url" tabindex="8"
                                      id="txt_open_url" maxlength="100" placeholder="URL [https://www.google.com]"
                                      onblur="return validate_url('txt_open_url')">
                                  </td>
                                  <td class="col-md-6" style="width: 50%;">
                                    <input class="form-control" type="text" name="txt_open_url_data" tabindex="8"
                                      id="txt_open_url_data" maxlength="25" placeholder="Button Name"
                                      title="Button Name">
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <div class="col-sm-2">
                          </div>
                        </div>
                        <!-- if the url button is select to visible the call Button -->
                        <div class="form-group mb-2 row">
                          <label class="col-sm-3 col-form-label">Call Button</label>
                          <div class="col-sm-7">
                            <table class="table table-striped table-bordered m-0"
                              style="table-layout: fixed; white-space: inherit; width: 100%; overflow-x: scroll;">
                              <tbody>
                                <tr>
                                  <td class="col-md-6" style="width: 40%;">
                                    <input class="form-control" type="text" name="txt_call_button" tabindex="9"
                                      id="txt_call_button" maxlength="10"
                                      onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))"
                                      placeholder="Mobile Number" title="Mobile Number">
                                  </td>
                                  <td class="col-md-6" style="width: 40%;">
                                    <input class="form-control" type="text" name="txt_call_button_data" tabindex="9"
                                      id="txt_call_button_data" maxlength="50" placeholder="Button Name"
                                      title="Button Name">
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <div class="col-sm-2">
                          </div>
                        </div>
                      </div>
                      <!-- if the url button is select to visible the Reply Buttons-->
                      <div class="form-group mb-2 row" id="id_reply_button" style="display: none;">
                        <label class="col-sm-3 col-form-label">Reply Buttons <br>(Maximum 3
                          Allowed)</label>
                        <div class="col-sm-7">
                          <table class="table table-striped table-bordered m-0"
                            style="table-layout: fixed; white-space: inherit; width: 100%; overflow-x: scroll;">
                            <tbody>
                              <tr>
                                <td class="col-md-5" style="width: 40%;">
                                  <input class="form-control" type="text" tabindex="10" name="txt_reply_buttons[]"
                                    id="txt_reply_buttons_1" maxlength="25" placeholder="Reply" title="Reply">
                                </td>
                                <td class="col-md-5" style="width: 40%;">
                                  <input class="form-control" type="text" tabindex="10" name="txt_reply_buttons_data[]"
                                    id="txt_reply_buttons_data_1" maxlength="25" placeholder="Reply Button"
                                    title="Reply Button">
                                </td>
                                <td class="col-md-2" style="width: 20%; padding: 5px !important;">
                                  <input type="button" class="btn btn-success" value="+ Add Reply"
                                    onclick="add_column('text_suggested_replies')">
                                  <input type="hidden" name="hidcnt_text_suggested_replies"
                                    id="hidcnt_text_suggested_replies" value="1">
                                </td>
                              </tr>

                              <tr>
                                <td colspan="3" style="padding: 0px;">
                                  <table id="id_text_suggested_replies" style="width: 100% !important;">
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-sm-2">
                        </div>
                      </div>
                      <!-- Product List  -->
                      <div class="form-group mb-2 row" id="id_option_list" style="display: none;">
                        <label class="col-sm-3 col-form-label">Product List <br>(Maximum 4
                          Allowed)</label>
                        <div class="col-sm-7">
                          <table class="table table-striped table-bordered m-0"
                            style="table-layout: fixed; white-space: inherit; width: 100%; overflow-x: scroll;">
                            <tbody>
                              <tr>
                                <td class="col-md-8" style="width: 80%;">
                                  <input class="form-control" type="text" tabindex="10" name="txt_option_list[]"
                                    id="txt_option_list_1" maxlength="25" placeholder="Product" title="Product">
                                </td>
                              </tr>
                              <tr>
                                <td class="col-md-8" style="width: 80%;">
                                  <input class="form-control" type="text" tabindex="10" name="txt_option_list[]"
                                    id="txt_option_list_2" maxlength="25" placeholder="Product" title="Product">
                                </td>
                                <td class="col-md-3" style="width: 20%;">
                                  <input type="button" class="btn btn-success" value="+ Add Products"
                                    onclick="add_column('option_list')">
                                  <input type="hidden" name="hidcnt_text_option_list" id="hidcnt_text_option_list"
                                    value="1">
                                </td>
                              </tr>

                              <tr>
                                <td colspan="3" style="padding: 0px;">
                                  <table id="id_text_option_list" style="width: 100% !important;">
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-sm-2">
                        </div>
                      </div>

                    </div>
                    <!-- submit button and error display -->
                    <div class="error_display" id='id_error_display'></div>
                    <div class="card-footer text-center">
                      <input type="hidden" name="txt_sms_count" id="txt_sms_count" value="<?= $sms_ttl_chars ?>">
                      <input type="hidden" name="txt_char_count" id="txt_char_count" value="<?= $cnt_ttl_chars ?>">
                      <input type="hidden" name="filename_upload" id="filename_upload" value="">
                      <input type="hidden" name="total_mobilenos_count" id="total_mobilenos_count" value="">
                      <input type="hidden" class="form-control" name='tmpl_call_function' id='tmpl_call_function'
                        value='compose_whatsapp' />
                      <!-- <a href="#!" onclick="preview_compose_template()" name="preview_submit" id="preview_submit"
                        tabindex="11" value="" class="btn btn-info">Preview</a> -->
                      <input type="submit" name="compose_submit" id="compose_submit" tabindex="12" value="Submit"
                        class="btn btn-success">
                      <a href="compose_template_rcs" name="cancel_submit" id="cancel_submit" tabindex="13" value=""
                        class="btn btn-danger">Cancel</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>

      </div>
      <!-- include site footer -->
      <? include ("libraries/site_footer.php"); ?>

    </div>
  </div>
<!-- Modal Popup window content-->
<div class="modal fade" id="default-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style=" max-width: 75% !important;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Template Details</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="id_modal_display" style=" word-wrap: break-word; word-break: break-word;">
          <h5>No Data Available</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success waves-effect " data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirmation details content-->
  <div class="modal" tabindex="-1" role="dialog" id="upload_file_popup">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="width: 400px;">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" style="width:30px" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <img alt="image" style="width: 50px; height: 50px; display: block; margin: 0 auto;" id="img_display">
          </br>
          <p class="text-center" id="file_response_msg"></p>
          <span class="display_msg">Are you sure you want to create a campaign?</span>
        </div>
        <div class="modal-footer" style="margin-right:30%;">
          <button type="button" class="btn btn-danger save_compose_file" data-dismiss="modal">Yes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Confirmation details content-->
  <div class="modal" tabindex="-1" role="dialog" id="campaign_compose_message">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="width: 400px;">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" style="width:30px" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <img alt="image" style="width: 50px; height: 50px; display: block; margin: 0 auto;" id="image_display">
          <br>
          <div class="container" style="text-align: center;">
            <span id="message"></span>
          </div>
        </div>
        <div class="modal-footer" style="margin-right:40%; text-align: center;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Okay</button>
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

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

  <audio id="audio" style="display: none;"></audio>
  <input type="hidden" name="txt_media_duration" id="txt_media_duration" style="display: none;">
  <script>
    function downloadSampleCSV() {
      var media_type = '';
      var variableCount = 0;
      //var personalized = $("#personalized").is(':checked');
      if(template_name != ""){
      var template_name = $("#slt_whatsapp_template").val();
        var templateDetails = template_name.split("!")
      console.log(templateDetails)  
      var variableCount = templateDetails[2]
      }
 
      console.log(variableCount)
      media_type = document.getElementById("media_type").value;
      console.log(media_type)

      if (media_type && variableCount > 0) {
        window.location.href = "uploads/imports/sample_variables_media.csv";
      } else if (variableCount > 0) {
        // Download a different sample CSV file for personalized messages with variable count > 0
        window.location.href = "uploads/imports/sample_variables.csv";
      } else if (media_type) {
        // Download the default sample CSV file
        window.location.href = "uploads/imports/sample_media.csv";
      } else {
        // Download the default sample CSV file
        window.location.href = "uploads/imports/sample_mobileno.csv";
      }
    }


    document.addEventListener("DOMContentLoaded", function () {
      $("input[type='radio'][name='personalization_option']").change(function () {
        if ($("#personalized").is(':checked')) {
          console.log("##########");
          $('#txt_list_mobno').attr('readonly', true);
          $("#fle_variable_csv").attr("required", true);
          $('#id_show_variable_csv').css('display', 'block');
          $('#header').css('display', 'none');
          $("#upload_contact").val('');
          $('#txt_list_mobno').val('');
          $("#id_mobupload").css('display', 'none');
          $("#id_mobupload_sub").css('display', 'block');
          $("#file_image_header").css('display', 'none');
          $("#file_image_header_url").css('display', 'none');
          $("#file_video_header").css('display', 'none');
          $("#file_video_header_url").css('display', 'none');
          $("#file_document_header").css('display', 'none');
          $("#file_document_header_url").css('display', 'none');

        } else {
          var variable_count = $('#txt_variable_count').val();
          console.log(variable_count);
          if ($('#txt_variable_count').val() > 0) {
            $('#txt_list_mobno').attr('readonly', true);
            $("#fle_variable_csv").attr("required", true);
            $('#id_show_variable_csv').css('display', 'block');
            $("#upload_contact").css('display', 'none');
            $('#header').css('display', 'block');
            $('#txt_list_mobno').val('');
            $("#upload_contact").val('');
            $("#id_mobupload").css('display', 'none');
            $("#id_mobupload_sub").css('display', 'block');
            $("#file_image_header").css('display', 'block');
            $("#file_image_header_url").css('display', 'block');
            $("#file_video_header").css('display', 'block');
            $("#file_video_header_url").css('display', 'block');
            $("#file_document_header").css('display', 'block');
            $("#file_document_header_url").css('display', 'block');

          } else {
            $('#txt_list_mobno').attr('readonly', false);
            $("#fle_variable_csv").attr("required", false);
            $('#id_show_variable_csv').css('display', 'none');
            $("#upload_contact").val('');
            $('#header').css('display', 'block');
            $('#txt_list_mobno').val('');
            $("#id_mobupload").css('display', 'block');
            $("#id_mobupload_sub").css('display', 'none');
            $("#file_image_header").css('display', 'block');
            $("#file_image_header_url").css('display', 'block');
            $("#file_video_header").css('display', 'block');
            $("#file_video_header_url").css('display', 'block');
            $("#file_document_header").css('display', 'block');
            $("#file_document_header_url").css('display', 'block');


          }

        }
      });
      // Your code here
    });


    // start function document
    $(function () {
      $('.theme-loader').fadeOut("slow");
      $('.theme-loader1').fadeOut("slow");
      init();
    });
    document.body.addEventListener("click", function (evt) {
      //note evt.target can be a nested element, not the body element, resulting in misfires
      $("#id_error_display").html("");
      //$("#file_document_header").prop('disabled', false);
      //$("#file_document_header_url").prop('disabled', false);
    });

    // preview_compose_template funct
    function preview_compose_template() {
      var form = $("#frm_compose_whatsapp")[0]; // Get the HTMLFormElement from the jQuery selector
      var data_serialize = $("#frm_compose_whatsapp").serialize();
      var fd = new FormData(form); // Use the form element in the FormData constructor
      // var txt_header_name = $('#txt_header_name').text();
      // fd.append('txt_header_name', txt_header_name);

      $.ajax({
        type: 'post',
        url: "ajax/preview_functions.php?previewTemplate_meta=previewTemplate_meta",
        data: fd,
        processData: false, // Important: Prevent jQuery from processing the data
        contentType: false, // Important: Let the browser set the content type
        beforeSend: function () {
          $('.theme-loader').show();
          $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        },
        success: function (response) { // Success
          $("#id_modal_display").html(response);
          $("#id_modal_display :input").attr("disabled", true);
          $('#default-Modal').modal({
            show: true
          });
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        },
        error: function (response, status, error) { // Error
          console.log("error");
          $("#id_modal_display").html(response.status);
          $('#default-Modal').modal({
            show: true
          });
        }
      });
    }


    $('#default-Modal').on('hidden.bs.modal', function () {
      $.ajax({
        url: 'delete_files.php', // Path to your PHP script
        type: 'POST',
        data: {
          // Optionally, you can pass data to your PHP script
          // For example, you can pass user ID or any other relevant data
          userId: <?php echo json_encode($_SESSION['yjwatsp_user_id']); ?>
        },
        success: function (response) {
          // Handle the response from the PHP script if needed
          console.log(response);
        },
        error: function (xhr, status, error) {
          // Handle errors if any
          console.error(xhr.responseText);
        }
      });
    });



    $("#close").click(function () {
      alert("###" + this.id);
      call_getsingletemplate();
    });
    var f_duration = 0;
    document.getElementById('audio').addEventListener('canplaythrough', function (e) {
      //add duration in the input field #f_du
      f_duration = Math.round(e.currentTarget.duration);
      // alert(f_duration);
      $('#txt_media_duration').val(f_duration);
      // URL.revokeObjectURL(obUrl);
    });
    var obUrl;
    document.getElementById('txt_media').addEventListener('change', function (e) {
      console.log("!!!");
      console.log(e.currentTarget);
      var file = e.currentTarget.files[0];
      $('#txt_media_duration').val('');
      //check file extension for audio/video type
      if (file.name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)) {
        obUrl = URL.createObjectURL(file);
        document.getElementById('audio').setAttribute('src', obUrl);
      }
    });

    /* function validate_filesize(file_name) {
       // console.log(file_name+"=="+file_name.duration);
       $("#id_error_display").html("");
 console.log("@@@@@");
 console.log(file_name);
       var file_size = file_name.files[0].size;
       console.log(file_name.files[0].name);
 
       if (file_name.files[0].name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)) {
         // alert("==="+parseInt($('#txt_media_duration').val()));
         if (parseInt($('#txt_media_duration').val()) > 31) {
           // alert("IN");
           $('#txt_media').val('');
         }
       }
 
       if (file_size > 5242880) { // 5 MB
         $("#id_error_display").html("Media File size must below 5 MB Size. Kindly try again!!");
         console.log("Failed");
       } else {
         console.log("Success");
       }
       $('#txt_media_duration').val('');
     } */

    /* function validate_filesizes(file_name) {
        console.log(file_name);
       $("#id_error_display").html("");
       var file_size = file_name.files[0].size;
       // console.log(file_size);
       if (file_size > 5242880) { // 5 MB
         $("#id_error_display").html("File size must below 5 MB Size. Kindly try again!!");
         console.log("Failed");
       } else {
         console.log("Success");
       }
     }*/

    function disable_texbox(my_filename, new_filename) {
      $("#" + my_filename).prop('disabled', false);
      $("#" + new_filename).val('');
      $("#" + new_filename).prop('disabled', true);
    }
    // func_open_senderid funct
    function func_open_senderid(admin_user) {
      var rdo_senderid = $("#rdo_senderid").val();

      if (admin_user != '') {
        var send_code = "&admin_user=" + admin_user;
        $.ajax({
          type: 'post',
          url: "ajax/call_functions.php?tmpl_call_function=senderid_admin_user" + send_code,
          dataType: 'json',
          beforeSend: function () {
            $('.theme-loader').show();
            $('.theme-loader1').show();
          },
          complete: function () {
            $('.theme-loader').hide();
            $('.theme-loader1').hide();
          },
          success: function (response) {
            // alert("==="+response.msg);
            $('#id_own_senderid').html(response.msg);

            if (admin_user == 'A') {
              $('#id_own_senderid').css('display', 'none');
            } else if (admin_user == 'U') {
              $('#id_own_senderid').css('display', 'block');
            }
            $('.theme-loader').hide();
            $('.theme-loader1').hide();
          },
          error: function (response, status, error) { }
        });
      }
    }
    // func_template_senderid func
    function func_template_senderid(admin_user) {
      var slt_whatsapp_template = $("#slt_whatsapp_template").val();
      var send_code = "&slt_whatsapp_template=" + slt_whatsapp_template;
      $('#txt_variable_count').val(0);
      $("#fle_variable_csv").attr("required", false);
      $('#id_show_variable_csv').css('display', 'none');
      $('#txt_list_mobno').attr('readonly', false);
      $("#id_mobupload").css('display', 'block');
      $("#id_mobupload_sub").css('display', 'none');
      console.log("!!!FALSE");

      $.ajax({
        type: 'post',
        url: "ajax/call_functions.php?tmpl_call_function=senderid_template" + send_code,
        dataType: 'json',
        beforeSend: function () {
          $('.theme-loader').show();
          $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        },
        success: function (response) {
          // alert("==="+response.msg);
          $('#id_own_senderid').html(response.msg);

          var slt_whatsapp_template_split = slt_whatsapp_template.split("!");
          if (slt_whatsapp_template_split[2] > 0) {
            $('#txt_variable_count').val(slt_whatsapp_template_split[2]);
            $('#txt_list_mobno').attr('readonly', true);
            $("#fle_variable_csv").attr("required", true);
            $('#id_show_variable_csv').css('display', 'block');
            $("#upload_contact").val('');
            $('#txt_list_mobno').val('');
            $("#id_mobupload").css('display', 'none');
            $("#id_mobupload_sub").css('display', 'block');
          }
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
          call_getsingletemplate();
        },
        error: function (response, status, error) { }
      });
    }
    // func_validate_campaign_name funct
    function func_validate_campaign_name() {
      var txt_campaign_name = $("#txt_campaign_name").val();
      $("#id_error_display").html('');
      $('#txt_campaign_name').css('border-color', '#e4e6fc');

      if (txt_campaign_name != '') {
        var send_code = "&txt_campaign_name=" + txt_campaign_name;
        $.ajax({
          type: 'post',
          url: "ajax/call_functions.php?tmpl_call_function=validate_campaign_name" + send_code,
          dataType: 'json',
          beforeSend: function () {
            $('.theme-loader').show();
            $('.theme-loader1').show();
          },
          complete: function () {
            $('.theme-loader').hide();
            $('.theme-loader1').hide();
          },
          success: function (response) {
            // alert("==="+response.msg);
            if (response.status == 0) {
              $('#txt_campaign_name').val('');
              $('#txt_campaign_name').focus();
              $("#txt_campaign_name").attr('data-original-title', response.msg);
              $("#txt_campaign_name").attr('title', response.msg);
              $('#txt_campaign_name').css('border-color', 'red');
              $('#id_error_display').html(response.msg);
            } else {

            }
            $('.theme-loader').hide();
            $('.theme-loader1').hide();
          },
          error: function (response, status, error) { }
        });
      }
    }


    function preview_open_url() {
      $("#txt_open_url").prop('required', true);
      $("#txt_open_url_data").prop('required', true);
      $("#txt_call_button").prop('required', true);
      $("#txt_call_button_data").prop('required', true);
      $('#id_open_url').toggle("display", "block");
    }

    function preview_call_button() {
      $("#txt_call_button").prop('required', true);
      $("#txt_call_button_data").prop('required', true);
      $('#id_call_button').toggle("display", "block");
    }

    function preview_reply_button() {
      $("#txt_reply_buttons_1").prop('required', true);
      $("#txt_reply_buttons_data_1").prop('required', true);
      $('#id_reply_button').toggle("display", "block");
    }

    function preview_option_list() {
      $("#txt_option_list_1").prop('required', true);
      $("#txt_option_list_2").prop('required', true);
      $('#id_option_list').toggle("display", "block");
    }

    function validate_url(url_site) {
      $('#compose_submit').prop('disabled', false);
      $("#id_error_display").html("");
      var url = $("#" + url_site).val();
      var pattern = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
      if (!pattern.test(url)) {
        $("#id_error_display").html("Invalid URL");
        $('#compose_submit').prop('disabled', true);
        return false;
      } else {
        $('#compose_submit').prop('disabled', false);
        return true;
      }
    }
    var slt_whatsapp_template_split;

    function chooseFile() {
      $("#txt_list_mobno").val('');
      $('#txt_list_mobno').attr('readonly', false);
      var slt_whatsapp_template = $("#slt_whatsapp_template").val();
      slt_whatsapp_template_split = slt_whatsapp_template.split("!");
      document.getElementById('upload_contact').value = '';
      document.getElementById('fle_variable_csv').value = '';
      console.log("choose file")
    }

    document.getElementById('upload_contact').addEventListener('change', function () {
      // if (slt_whatsapp_template_split[4] != '') {
      //   validateFile(upload_contact, "Media");
      // } else {
        validateFile(upload_contact);
      // }

    });

    document.getElementById('fle_variable_csv').addEventListener('change', function () {
      // if (slt_whatsapp_template_split[4] != '') {
      //   validateFile(fle_variable_csv, "Media");
      // } else {
        validateFile(fle_variable_csv);
      // }
    });

    function validate_phone_number(phone) {
      cosole.log("validate")
      // Allow +, - and . in phone number
      var filtered_phone_number = phone.replace(/[^\d]/g, '');

      // Check the length of the number
      if (filtered_phone_number.length !== 12) {
        return false;
      } else {
        // Check if the number matches the pattern
        if (/^91[6-9][0-9]{9}$/.test(filtered_phone_number)) {
          return true;
        } else {
          return false;
        }
      }
    }

    var dup, inv; DuplicateAllowed = false;
    // Example of using the function based on the value of 'dup' variable
    $('input[name="duplicate_value"]').change(function () {
      var selectedValue = $('input[name="duplicate_value"]:checked').val();
      // console.log(selectedValue);
      if (selectedValue === 'duplicate_allowed') {
        DuplicateAllowed = true;
        dup = 1;
        call_remove_duplicate_invalid(); // Allow duplicates
      } else {
        DuplicateAllowed = false;
        dup = 0; // Disallow duplicates
        call_remove_duplicate_invalid();
      }
    });

    function call_remove_duplicate_invalid() {
      inv = 1; // Define or assign the variable inv
      dup = dup ? dup : 0; // Set dup to its current value if defined, otherwise set it to 0
      console.log(dup + " dup"); // Log dup to console
      var txt_list_mobno = $('#txt_list_mobno').val();
      var mobno = txt_list_mobno.replace(/\n/g, ',');
      var newline = mobno.split('\n');
      var correct_mobno_data = [];
      var return_mobno_data = '';
      var issu_mob = '';
      var cnt_vld_no = 0;
      var max_vld_no = 2000000;

      for (var i = 0; i < newline.length; i++) {
        var expl = newline[i].split(',');
        for (var ij = 0; ij < expl.length; ij++) {
          var vlno;
          if (inv === 1) {
            vlno = validate_phone_number(expl[ij]);
          } else {
            vlno = newline[i];
          }

          if (vlno === true) {
            if (dup === 1 || correct_mobno_data.indexOf(expl[ij]) === -1) {
              if (expl[ij] !== '') {
                cnt_vld_no++;
                if (cnt_vld_no <= max_vld_no) {
                  correct_mobno_data.push(expl[ij]);
                  return_mobno_data += expl[ij] + ',\n';
                } else {
                  issu_mob += expl[ij] + ',';
                }
              } else {
                issu_mob += expl[ij] + ',';
              }
            } else {
              issu_mob += expl[ij] + ',';
            }
          } else {
            issu_mob += expl[ij] + ',';
          }
        }
      }
      // Output the results as needed
      $('#txt_list_mobno').val(return_mobno_data);
        var txt_list_mobno = $('#txt_list_mobno').val();
      var mobileNumbersArray = txt_list_mobno.split(',').filter(function (number) {
        return number.trim() !== ''; // Remove empty values after trimming whitespace
      });
      console.log(mobileNumbersArray.length);
      document.getElementById('total_mobilenos_count').value = mobileNumbersArray.length;
      $('#txt_list_mobno_txt').html('Invalid Mobile Nos: ' + issu_mob);
    }


    // call_gettemplate funct
    function call_gettemplate() {
      $("#id_error_display").html("");
      $("#slt_whatsapp_template_single").html("");
      var id_slt_mobileno_split = id_slt_mobileno.split("~~");

      $.ajax({
        type: 'post',
        url: "ajax/whatsapp_call_functions.php?getTemplate_meta=getTemplate_meta&wht_tmpl_url=" +
          id_slt_mobileno_split[4] + "&wht_bearer_token=" + id_slt_mobileno_split[3],
        beforeSend: function () {
          $('.theme-loader').show();
          $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        },
        success: function (response_msg) {
          $('#slt_whatsapp_template').html(response_msg.msg);
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        },
        error: function (response_msg, status, error) {
          $("#slt_whatsapp_template").html(response_msg.msg);
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        }
      });
    }

    function optionValue(){
      console.log("function called")
    }
    function templateView(){
      var template_name = $("#slt_whatsapp_template").val();
      if(template_name == ""){
        console.log("template not selected")
      $('#view_button').css('display', 'none');
        return;
      }
      var templateDetails = template_name.split("!")
      console.log(templateDetails)    
      var template_message = $("#template_message_"+templateDetails[4]).val();
      console.log(template_message)
     getsingletemplate(template_message,templateDetails[1]) 
    }

    // call_getsingletemplate funtc
    function call_getsingletemplate() {
      var template_name = $("#slt_whatsapp_template").val();
      if(template_name == ""){
        console.log("template not selected")
      $('#view_button').css('display', 'none');
      $('.display_after_template').css('display', 'none');
        return;
      }
      $('#view_button').css('display', '');
      $('.display_after_template').css('display', '');
    }

    function getsingletemplate(template_message, template_category) {
    $("#slt_whatsapp_template_single").html("");  // Clear previous content

    // Initialize response message
    let response_msg = "";
    template_category = template_category.toUpperCase();
    template_message = template_message.replace(/\n/g, '\\n');

    try {
        switch (template_category) {
            case "TEXT":
                response_msg = handleTextCategory(template_message);
                break;
            case "RICH TEXT":
            case "RICH CARD":
                response_msg = handleCardCategory(template_message, template_category);
                break;
            case "CAROUSEL":
                response_msg = handleCarouselCategory(template_message);
                break;
            default:
                response_msg = "Category is not recognized";
        }
    } catch (e) {
        console.error(e);
        response_msg = "Invalid format for template_message";
    }

    // Update and show the modal
    $("#id_modal_display").html(`
        <h5>Category: ${template_category}</h5>
        <div id="newDiv"></div>
        ${response_msg}
    `);
    $('#default-Modal').modal('show');
}

function handleTextCategory(template_message) {
    try {
        const parsed_message = JSON.parse(template_message);
        const text_content = parsed_message[0].text || "No text available";
        return `<p style="white-space: pre-wrap;"><strong>Text:</strong> ${text_content}</p>`;
    } catch {
        return "Invalid format for template_message";
    }
}

function handleCardCategory(template_message, template_category) {
    try {
        const parsed_message = JSON.parse(template_message);
        const message = parsed_message[0];
        console.log(template_category)

        const text_content = message.text || "No text available";
        if(template_category == 'RICH CARD'){
          var card_title = message.card_title || "Not specified";
          var orientation = message.orientation || "Not specified";
          var card_alignment = message.card_allignment || "Not specified";
          var media_type = message.rich_card_media_type || "No media file available";
          var media_file = message.media_file || "No media file available";
        }
        const suggestions = message.suggestions || [];
        let card_html;
        if(template_category == 'RICH CARD'){
         card_html = `
            <p style="white-space: pre-wrap;"><strong>Text:</strong> ${text_content}</p>
            <p><strong>Card Title:</strong> ${card_title}</p>
            <p><strong>Orientation:</strong> ${orientation}</p>
            <p><strong>Card Alignment:</strong> ${card_alignment}</p>
            <p><strong>Media Type:</strong> ${media_type}</p>
            <p><strong>Media File:</strong> <a href="${media_file}" target="_blank">View Media</a></p>
        `;
        }
        else{
          card_html = `
            <p style="white-space: pre-wrap;"><strong>Text:</strong> ${text_content}</p>
        `;
        }
        if (suggestions.length > 0) {
            card_html += generateSuggestionsHtml(suggestions);
        } else {
            card_html += '<p>No suggestions available.</p>';
        }

        return card_html;
    } catch(e) {
      console.log(e)
        return "Invalid format for template_message";
    }
}

function handleCarouselCategory(template_message) {
    try {
        const message_all = JSON.parse(template_message);
        let card_html = "";

        message_all.forEach((message, s) => {
            card_html += `
                <button type="button" class="btn btn-success mr-2" style="margin-top: 10px;" 
                    onclick="addCauroselCards('card${s + 1}', ${message_all.length})" 
                    id="addCauroselCard_${s + 1}"> 
                    Card ${s + 1} 
                </button>
            `;
        });

        card_html += '<br><br>';

        message_all.forEach((message, s) => {
            const card = message[0];
            card_html += generateCardHtml(card, s + 1);
        });

        return card_html;
    } catch {
        return "Invalid format for template_message";
    }
}

function generateSuggestionsHtml(suggestions) {
    let suggestions_html = '<h5>Suggestions:</h5><div class="suggestion-card-container">';
    
    suggestions.forEach((suggestion, index) => {
        const actionType = suggestion.actionType.replaceAll("_", " ");
        const fields = suggestion.fields;

        suggestions_html += `
            <div class="suggestion-card">
                <h5>Suggestion ${index + 1}</h5>
                <label>Type of Action</label>
                <select disabled class="form-control action_type">
                    <option value="" selected>${actionType}</option>
                </select>
                ${generateFieldsHtml(suggestion.actionType, fields)}
            </div>
        `;
    });

    suggestions_html += '</div>'; // Closing suggestion-card-container
    return suggestions_html;
}

function generateFieldsHtml(actionType, fields) {
    let fieldHTML = '';
    switch (actionType) {
        case 'REPLY':
            fieldHTML = `<label>Suggestion Text</label><input type="text" class="form-control" value="${fields.text_message}" readonly>`;
            break;
        case 'DIALER_ACTION':
            fieldHTML = `
                <label>Suggestion Text</label><input type="text" class="form-control" value="${fields.dial_sugg_text}" readonly>
                <label class="mt-2">Mobile Number</label><input type="text" class="form-control" value="${fields.mobile_number}" readonly>
            `;
            break;
        case 'URL_ACTION':
            fieldHTML = `
                <label>Suggestion Text</label><input type="text" class="form-control" value="${fields.url_sugg_text}" readonly>
                <label class="mt-2">URL</label><input type="text" class="form-control" value="${fields.url_link}" readonly>
            `;
            break;
        case 'VIEW_LOCATION(Lat/Lang)':
            fieldHTML = `
                <label>Suggestion Text</label><input type="text" class="form-control" value="${fields.location_sugg_txt}" readonly>
                <label class="mt-2">Label</label><input type="text" class="form-control" value="${fields.location_url}" readonly>
                <div class="row mt-2">
                    <div class="col-sm-6">
                        <label>Latitude</label><input type="text" class="form-control" value="${fields.latitude}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label>Longitude</label><input type="text" class="form-control" value="${fields.longitude}" readonly>
                    </div>
                </div>
            `;
            break;
        case 'VIEW_LOCATION(query)':
            fieldHTML = `
                <label>Suggestion Text</label><input type="text" class="form-control" value="${fields.locate_sugg_text}" readonly>
                <label class="mt-2">Query</label><input type="text" class="form-control" value="${fields.locate_url}" readonly>
            `;
            break;
        case 'SHARE_LOCATION':
            fieldHTML = `<label>Suggestion Text</label><input type="text" class="form-control" value="${fields.share_txt_location_sugg_txt}" readonly>`;
            break;
        case 'CREATE_CALENDAR':
            fieldHTML = `
                <label>Suggestion Text</label><input type="text" class="form-control" value="${fields.calender_sugg_txt}" readonly>
                <div class="row mt-2">
                    <div class="col-sm-6"><label>From Date</label><input type="text" class="form-control" value="${fields.from_date}" readonly></div>
                    <div class="col-sm-6"><label>To Date</label><input type="text" class="form-control" value="${fields.to_date}" readonly></div>
                </div>
                <label class="mt-2">Event Title</label><input type="text" class="form-control" value="${fields.event}" readonly>
                <label class="mt-2">Description</label><textarea class="form-control" readonly>${fields.event_label}</textarea>
            `;
            break;
        default:
            break;
    }
    return fieldHTML;
}

function generateCardHtml(card, index) {
    const text_content = card.text || "No text available";
    const card_title = card.card_title || "Not specified";
    const orientation = card.orientation || "Not specified";
    const card_alignment = card.card_allignment || "Not specified";
    const media_type = card.rich_card_media_type || "No media file available";
       const media_height = card.media_height || "Not specified";
    const media_width = card.media_width || "Not specified";
    const media_file = card.media_file || "No media file available";
    const suggestions = card.suggestions || [];

    let card_html = `
        <div id="card${index}" style="display:none">
            <p style="white-space: pre-wrap;"><strong>Text:</strong> ${text_content}</p>
            <p><strong>Card Title:</strong> ${card_title}</p>
            <p><strong>Orientation:</strong> ${orientation}</p>
            <p><strong>Card Alignment:</strong> ${card_alignment}</p>
                   <p><strong>Media Height:</strong> ${media_height}</p>
            <p><strong>Media Width:</strong> ${media_width}</p>
            <p><strong>Media Type:</strong> ${media_type}</p>
            <p><strong>Media File:</strong> <a href="${media_file}" target="_blank">View Media</a></p>
    `;

    if (suggestions.length > 0) {
        card_html += generateSuggestionsHtml(suggestions);
    } else {
        card_html += '<p>No suggestions available.</p>';
    }
    card_html += '</div>';
    return card_html;
}

function addCauroselCards(cardId, numCards) {
    // Hide all carousel cards
    for (let i = 1; i <= numCards; i++) {
        $(`#card${i}`).hide();
    }
    // Show selected carousel card
    $(`#${cardId}`).show();
}
    // call_gettemplate_data funct
    function call_gettemplate_data() {
      var id_slt_template = $("#id_slt_template").val();
      $.ajax({
        type: 'post',
        url: "ajax/whatsapp_call_functions.php",
        data: {
          getTemplate_data: 'getTemplate_data',
          id_slt_template: id_slt_template
        },
        beforeSend: function () {
          $('.theme-loader').show();
          $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        },
        success: function (response_msg) {
          // alert(response_msg.msg);
          $("#txt_sms_content").val(response_msg.msg);
          $("#txt_char_count").val(response_msg.msg.length);
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
          call_getsmscount();
        },
        error: function (response_msg, status, error) {
          $('.theme-loader').hide();
          $('.theme-loader1').hide();
        }
      });
    }

    // function call_composesms() {
    $(document).on("submit", "form#frm_compose_whatsapp", function (e) {
      e.preventDefault();
      console.log("View Submit Pages");
      console.log("came Inside");
      $("#id_error_display").html("");


      $('#txt_list_mobno').css('border-color', '#a0a0a0');
      $('#chk_remove_duplicates').css('border-color', '#a0a0a0');
      $('#chk_remove_invalids').css('border-color', '#a0a0a0');
      $('#txt_sms_content').css('border-color', '#a0a0a0');
      $('#txt_char_count').css('border-color', '#a0a0a0');
      $('#txt_sms_count').css('border-color', '#a0a0a0');

      //get input field values 
      var txt_whatsapp_mobno = $('#txt_whatsapp_mobno').val();

      var txt_campaign_name = $('#txt_campaign_name').val();
      var txt_list_mobno = $('#txt_list_mobno').val();
      var chk_remove_duplicates = $('#chk_remove_duplicates').val();
      var chk_remove_invalids = $('#chk_remove_invalids').val();

      var flag = true;

      var slt_whatsapp_template = $("#slt_whatsapp_template").val();
      let parts = slt_whatsapp_template.split('_');
      var tempate_code = parts[2];

      /********validate all our form fields***********/
      /* txt_whatsapp_mobno field validation  */
      if (txt_whatsapp_mobno == "") {
        $('#txt_whatsapp_mobno').css('border-color', 'red');
        console.log("##");
        flag = false;
      }

      /* txt_campaign_name field validation  */
      if (txt_campaign_name == "") {
        $('#txt_campaign_name').css('border-color', 'red');
        console.log("##");
        flag = false;
      }
      /* txt_list_mobno field validation  */
      if (txt_list_mobno == "") {
        $('#txt_list_mobno').css('border-color', 'red');
        console.log("##");
        flag = false;
      }
      /* chk_remove_duplicates field validation  */
      if (chk_remove_duplicates == "") {
        $('#chk_remove_duplicates').css('border-color', 'red');
        console.log("$$");
        flag = false;
      }
      /* chk_remove_invalids field validation  */
      if (chk_remove_invalids == "") {
        $('#chk_remove_invalids').css('border-color', 'red');
        console.log("%%");
        flag = false;
      }

      /* If all are ok then we send ajax request to ajax/master_call_functions.php *******/
      if (flag) {
        var fd = new FormData(this);

        $.ajax({
          type: 'post',
          url: "ajax/whatsapp_call_functions.php",
          dataType: 'json',
          data: fd,
          contentType: false,
          processData: false,
          beforeSend: function () {
            $('#compose_submit').attr('disabled', true);
            $('.theme-loader').show();
            $('.theme-loader1').show();
          },
          complete: function () {
            //$('#compose_submit').attr('disabled', false);
            $('.theme-loader').hide();
            $('.theme-loader1').hide();
          },
          success: function (response) {
            $('#image_display').removeAttr('src');
            if (response.status == '0') {
              $('#id_submit_composercs').attr('disabled', false);
              $('#compose_submit').attr('disabled', false);
              $('#image_display').attr('src', 'assets/img/failed.png');
              $('#campaign_compose_message').modal({
                show: true
              });
              $("#message").html(response.msg);
            } else if (response.status == 2) {
              $('#compose_submit').attr('disabled', false);
              $('#image_display').attr('src', 'assets/img/failed.png');
              $('#campaign_compose_message').modal({
                show: true
              });
              $("#message").html(response.msg);
            } else if (response.status == 1) {
              $('#compose_submit').attr('disabled', false);
              $('#campaign_compose_message').modal({
                show: true
              });
              $('#image_display').attr('src', 'assets/img/success.png');
              $("#message").html(response.msg);
              setInterval(function () {
                window.location = 'template_rcs_list';
              }, 3000);
            }
            $('.theme-loader').hide();
            $('.theme-loader1').hide();
          },
          error: function (response, status, error) {
            // wrong;
            console.log("FAL");
            e.preventDefault();
            $('#id_slt_header').val('');
            $('#id_slt_template').val('');
            $('#txt_list_mobno').val('');
            $('#chk_remove_duplicates').val('');
            $('#chk_remove_invalids').val('');
            $('#txt_sms_content').val('');
            $('#txt_char_count').val('');
            $('#txt_sms_count').val('');
            $('#id_submit_composercs').attr('disabled', false);
            $('#compose_submit').attr('disabled', false);
            $('.theme-loader').show();
            $('.theme-loader1').show();
            window.location = 'compose_template_rcs';
            $("#id_error_display").html(response.msg);
          }
        });
      }
    });

    // call_getsmscountn function 
    function call_getsmscount() {
      var lngth = $('#txt_sms_content').val().length;
      $('#txt_char_count').val(lngth);
      var sms_cnt = parseInt(lngth / 160);
      $('#txt_sms_count').val(+sms_cnt + 1);
    }

    function validateFile(input, Media) {
      console.log(input, Media);
      console.log("coming")
      $('#txt_list_mobno').attr('readonly', false);
      $('#txt_list_mobno').val('');
      var file = input.files[0];
      console.log(file);
      var allowedExtensions = /\.csv$/i;
      var maxSizeInBytes = 100 * 1024 * 1024; // 100MB
      if (!allowedExtensions.test(file.name)) {
        document.getElementById('id_error_display').innerHTML = 'Invalid file type. Please select an .csv file.';
        input.value = ''; // Clear the file input
      } else if (file.size > maxSizeInBytes) {
        document.getElementById('id_error_display').innerHTML = 'File size exceeds the maximum limit (100MB).';
        input.value = ''; // Clear the file input
      } else {
        document.getElementById('id_error_display').innerHTML = ''; // Clear any previous error message
        readFileContents(file, Media, DuplicateAllowed);
      }
    }

    var copiedFile, file_location_path;
    var cleanedData = [];
    // validate mobile numbers
    function validateNumber(number) {
      return /^91[6-9]\d{9}$/.test(number);
    }
    //copy file
    function copyFile(file) {
      // Extract filename and extension
      var fileNameParts = file.name.split('.');
      var fileName = fileNameParts[0];
      var fileExtension = fileNameParts[1];
      // Append "_copy" to the filename
      var copiedFileName = fileName + "_copy." + fileExtension;
      // Create a new file with the copied filename
      var copiedFile = new File([file], copiedFileName, { type: file.type });
      // Return the copied file
      return copiedFile;
    }

    // read Files 
    function readFileContents(file, Media, DuplicateAllowed) {
      cleanedData = [];
      console.log(DuplicateAllowed);
      $(".display_msg").css("display", "");
      $(".modal-footer").css("display", "");
      $('#img_display').removeAttr('src');
      $('.preloader-wrapper').show();
      var reader = new FileReader();
      reader.onload = function (event) {
        var contents = event.target.result;
        var workbook = XLSX.read(contents, {
          type: 'binary'
        });
        // Copy the file  
        copiedFile = copyFile(file);
        // Use the copied file as needed
        console.log("Copied file:", copiedFile);

        var firstSheetName = workbook.SheetNames[0];
        var worksheet = workbook.Sheets[firstSheetName];
        var data = XLSX.utils.sheet_to_json(worksheet, {
          header: 1
        });

        //array values get in invalids,dublicates
        var invalidValues = [];
        var duplicateValuesInColumnA = [];
        var uniqueValuesInColumnA = new Set();
        var validCount = 0;

        for (var columnIndex = 0; columnIndex < data[0].length; columnIndex++) {
          var value = data[0][columnIndex]; // Value in the first row at the current column index
          // console.log(value + "value");
        }

        var firstRowLength = data[0].length;
        var slt_whatsapp_template = $("#slt_whatsapp_template").val();
        var slt_whatsapp_template_split = slt_whatsapp_template.split("!");
        console.log("shan testinggg")
        console.log(slt_whatsapp_template_split[2])
        var template_variable_count = parseInt(slt_whatsapp_template_split[2], 10);

        console.log("Length of the first row: " + firstRowLength);
        for (var rowIndex = 0; rowIndex < data.length; rowIndex++) {
          console.log(data[rowIndex].length)
          var valueA = data[rowIndex][0]; // Assuming column A is at index 0
          if (!validateNumber(valueA) && valueA != undefined) {
            invalidValues.push(valueA);
          }
          else if (data[rowIndex].length == template_variable_count) {
            console.log(valueA)
            invalidValues.push(valueA);
          }
           else if (uniqueValuesInColumnA.has(valueA) && DuplicateAllowed === false && valueA != undefined) {
            validCount++;
            duplicateValuesInColumnA.push(valueA);
          } else if (valueA != undefined) {
            uniqueValuesInColumnA.add(valueA);
            validCount++;
            // Construct a JSON object for the current row
            var jsonObject = {};
            for (var columnIndex = 0; columnIndex < data[rowIndex].length; columnIndex++) {
              var key = columnIndex; // You can customize the key names as needed
              jsonObject[key] = data[rowIndex][columnIndex];
            }
            cleanedData.push(jsonObject); // Add the JSON object to cleanedData
          }
        }
        // console.log(invalidValues)
        $('#txt_list_mobno').attr('readonly', true);
        // valid_count = uniqueValuesInColumnA.length;
        // console.log(validCount);
        //document.getElementById('total_mobilenos_count').value = validCount;
        $('#txt_list_mobno').val(Array.from(uniqueValuesInColumnA).join(','));
        var totalCount = data.length;
    

        if (Media !== undefined) {
          template_variable_count += 2; // Increment by 2
        } else {
          template_variable_count += 1; // Increment by 1
        }
        console.log(template_variable_count + "template_variable_count")
        console.log(invalidValues.length + " invalidValues.length")
        console.log(duplicateValuesInColumnA.length + " duplicateValuesInColumnA.length")
        console.log(totalCount + " totalCount.length")

        var selectedValue = $('input[name="duplicate_value"]:checked').val();
      if (selectedValue === 'duplicate_allowed') {
        document.getElementById('total_mobilenos_count').value = validCount;
      }else{
        var valid_no = validCount - duplicateValuesInColumnA.length;
        document.getElementById('total_mobilenos_count').value = valid_no;
      }

        if (slt_whatsapp_template == '') {
          console.log("if 1")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $('#txt_list_mobno').val("");
          $(".display_msg").css("display", "none");
          $(".modal-footer").css("display", "none");
          $('#img_display').attr('src', 'assets/img/failed.png');
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Please select Template!.</b>');
          document.getElementById('upload_contact').value = '';
          document.getElementById('fle_variable_csv').value = '';
          $('#txt_list_mobno').attr('readonly', false);
        } else if (((template_variable_count != '0') && template_variable_count != firstRowLength)) {
          console.log(template_variable_count)
          console.log(firstRowLength)

          console.log("else if 3")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $(".display_msg").css("display", "none");
          $(".modal-footer").css("display", "none");
          $('#upload_file_popup').modal('show');
          $('#img_display').attr('src', 'assets/img/failed.png');
          $('#file_response_msg').html('<b>Variable count mismatch. </b>');
          document.getElementById('upload_contact').value = '';
          document.getElementById('fle_variable_csv').value = '';
          $('#txt_list_mobno').val("");

        }
         else if ((invalidValues.length + duplicateValuesInColumnA.length === totalCount)) {
          console.log("else if 4")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $(".display_msg").css("display", "none");
          $(".modal-footer").css("display", "none");
          $('#img_display').attr('src', 'assets/img/failed.png');
          // $('#id_error_display').html('<b>The count of valid numbers is 0. Therefore, it is not possible to create a campaign, and the file cannot be uploaded. </b>');
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html(
            '<b>The count of valid numbers is 0. Therefore, it is not possible to create a campaign, and the file cannot be uploaded. </b>'
          );
          document.getElementById('upload_contact').value = '';
          document.getElementById('fle_variable_csv').value = '';
          $('#txt_list_mobno').val("");
        } else if ((Media == undefined && firstRowLength > 1 && !template_variable_count)) {
          console.log("else if 1")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $(".display_msg").css("display", "none");
          $(".modal-footer").css("display", "none");
          $('#upload_file_popup').modal('show');
          $('#img_display').attr('src', 'assets/img/failed.png');
          $('#file_response_msg').html('<b>Invalid File Format.Check Template. </b>');
          // $('#id_error_display').html('<b>Invalid File Format.Check Template. </b>');
          document.getElementById('upload_contact').value = '';
          document.getElementById('fle_variable_csv').value = '';
          $('#txt_list_mobno').val("");
          $('#txt_list_mobno').attr('readonly', false);

        } else if (((Media && firstRowLength > 2) || (Media && firstRowLength < 2)) && (
          template_variable_count == 0)) {
          console.log("else if 2")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $('#upload_file_popup').modal('show');
          $(".display_msg").css("display", "none");
          $(".modal-footer").css("display", "none");
          $('#img_display').attr('src', 'assets/img/failed.png');
          $('#file_response_msg').html('<b>Invalid File Format.Check Template. </b>');
          document.getElementById('upload_contact').value = '';
          document.getElementById('fle_variable_csv').value = '';
          $('#txt_list_mobno').val("");

        } else if (invalidValues.length > 0 && duplicateValuesInColumnA.length > 0) {
          console.log("else if 5")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $('#img_display').css("display", "none");
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Invalid Numbers: \n' + JSON.stringify(invalidValues.length) +
            '\n Duplicate Numbers: \n' + JSON.stringify(duplicateValuesInColumnA.length) + '</b>');
        } else if (duplicateValuesInColumnA.length > 0) {
          $('#img_display').css("display", "none");
          console.log("else if 6")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Duplicate Numbers : \n' + JSON.stringify(duplicateValuesInColumnA
            .length) + '</b>');
        } else if (invalidValues.length > 0) {
          $('#img_display').css("display", "none");
          console.log("else if 7")
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Invalid Numbers : \n' + JSON.stringify(invalidValues.length) +
            '</b>');
        } else {
          csvfile();
          console.log("else  8");
          $('#txt_list_mobno').attr('readonly', true);
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          // $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Validating Successfully.</b>');
        }
      }
      reader.readAsBinaryString(file);
    }

    $('#upload_file_popup').find('.save_compose_file').on('click', function () {
      csvfile();
    });

    function csvfile() {
      var fd = new FormData();
      // Append the copied file to the FormData object
      fd.append('copiedFile', copiedFile);
      $.ajax({
        type: 'post',
        url: "ajax/whatsapp_call_functions.php?storecopy_file=copy_file",
        dataType: 'json',
        data: fd,
        contentType: false,
        processData: false,
        beforeSend: function () {
          $('.updateprocessing').show();
        },
        complete: function () {
          $('.updateprocessing').hide();
          $('.loading_error_message').css("display", "none");
        },
        success: function (response) {
          if (response.status == '0') {
            console.log("File Not copied ...failed");
            console.log(response.msg);
          } else {
            file_location_path = response.file_location;
            console.log("File copied Successfully");
            // Convert cleanedData to CSV format
            const productValuesArrays = cleanedData.map(obj => Object.values(obj));
            // const headers = Object.keys(cleanedData[0]);
            // productValuesArrays.unshift(headers);
            const csvContent = productValuesArrays.map(row => row.join(",")).join("\n");
            // Get the file name
            var fileName = file_location_path.substring(file_location_path.lastIndexOf('/') + 1);
            console.log("File name:", fileName);
            // Set the hidden value
            document.getElementById('filename_upload').value = fileName;
            // Convert the CSV content into a Blob
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            // Create a FormData object and append the Blob
            const formData = new FormData();
            formData.append('valid_numbers', blob);
            formData.append('filename', fileName);
            // Send the FormData to the server using AJAX
            $.ajax({
              type: 'POST',
              url: 'csvfile_write.php',
              data: formData,
              contentType: false,
              processData: false,
              success: function (response) {
                console.log('File written successfully');
              },
              error: function (xhr, status, error) {
                console.error('Error occurred while writing the file:', error);
              }
            });
          }
        }
      });
    }

    $('#upload_file_popup').find('.btn-secondary').on('click', function () {
      window.location.href = 'compose_template_rcs';
    });

    function isValidInput(event) {
      var slt_whatsapp_template = $("#slt_whatsapp_template").val();
      if (slt_whatsapp_template == '') {
        $('#id_error_display').html('<b>Please select Template! .</b>');
        return false;
      }
      var charCode = event.charCode || event.keyCode;
      return (charCode === 44 || // Allow comma (,)
        charCode === 8 || // Allow backspace
        charCode === 13 || // Allow enter
        (charCode >= 48 && charCode <= 57)); // Allow digits (0-9)
    }

    function create_custom_dropdowns() {
      $('select').each(function (i, select) {
        if (!$(this).next().hasClass('dropdown-select')) {
          $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') +
            '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
          var dropdown = $(this).next();
          var options = $(select).find('option');
          var selected = $(this).find('option:selected');
          dropdown.find('.current').html(selected.data('display-text') || selected.text());
          options.each(function (j, o) {
            var display = $(o).data('display-text') || '';
            dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ?
              'selected' : '') + '" data-value="' + $(o).val() +
              '" data-display-text="' + display + '">' + $(o).text() + '</li>');
          });
        }
      });
      $('.dropdown-select ul').before(
        '<div class="dd-search"><input id="txtSearchValue" autocomplete="off" onkeyup="filter()" class="dd-searchbox" type="text"></div>'
      );
    }
    // Event listeners

    // Open/close
    $(document).on('click', '.dropdown-select', function (event) {
      if ($(event.target).hasClass('dd-searchbox')) {
        return;
      }
      $('.dropdown-select').not($(this)).removeClass('open');
      $(this).toggleClass('open');
      if ($(this).hasClass('open')) {
        $(this).find('.option').attr('tabindex', 0);
        $(this).find('.selected').focus();
      } else {
        $(this).find('.option').removeAttr('tabindex');
        $(this).focus();
      }
    });

    // Close when clicking outside
    $(document).on('click', function (event) {
      if ($(event.target).closest('.dropdown-select').length === 0) {
        $('.dropdown-select').removeClass('open');
        $('.dropdown-select .option').removeAttr('tabindex');
      }
      event.stopPropagation();
    });

    function filter() {
      var valThis = $('#txtSearchValue').val();
      $('.dropdown-select ul > li').each(function () {
        var text = $(this).text();
        (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();
      });
    };
    // Search

    // Option click
    $(document).on('click', '.dropdown-select .option', function (event) {
      $(this).closest('.list').find('.selected').removeClass('selected');
      $(this).addClass('selected');
      var text = $(this).data('display-text') || $(this).text();
      $(this).closest('.dropdown-select').find('.current').text(text);
      $(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
    });

    // Keyboard events
    $(document).on('keydown', '.dropdown-select', function (event) {
      var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[
        0]);
      // Space or Enter
      //if (event.keyCode == 32 || event.keyCode == 13) {
      if (event.keyCode == 13) {
        if ($(this).hasClass('open')) {
          focused_option.trigger('click');
        } else {
          $(this).trigger('click');
        }
        return false;
        // Down
      } else if (event.keyCode == 40) {
        if (!$(this).hasClass('open')) {
          $(this).trigger('click');
        } else {
          focused_option.next().focus();
        }
        return false;
        // Up
      } else if (event.keyCode == 38) {
        if (!$(this).hasClass('open')) {
          $(this).trigger('click');
        } else {
          var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find(
            '.list .option.selected')[0]);
          focused_option.prev().focus();
        }
        return false;
        // Esc
      } else if (event.keyCode == 27) {
        if ($(this).hasClass('open')) {
          $(this).trigger('click');
        }
        return false;
      }
    });

    $(document).ready(function () {
      create_custom_dropdowns();
    });
  </script>
</body>

</html>
