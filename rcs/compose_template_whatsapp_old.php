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

include_once('api/configuration.php'); // Include configuration.php
extract($_REQUEST); // Extract the request

// If the Session is not available redirect to index page
if ($_SESSION['yjwatsp_user_id'] == "") { ?>
  <script>window.location = "index";</script>
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
            <h1>Compose Whatsapp</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="template_rcs_list">Whatsapp List</a></div>
              <div class="breadcrumb-item">Compose Whatsapp</div>
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
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Select Whatsapp Template <label
                            style="color:#FF0000">*</label></label>
                        <div class="col-sm-7">
			
                          <select name="slt_whatsapp_template" id='slt_whatsapp_template' class="form-control"
                            data-toggle="tooltip" data-placement="top" title="" onchange="call_getsingletemplate()"
                            data-original-title="Select Whatsapp Template" tabindex="1" autofocus required="">
                            <!-- onchange="call_getsingletemplate()" onfocus="func_template_senderid()" -->
                            <option value="" selected>Choose Whatsapp Template</option>
			
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
                              for ($indicator = 0; $indicator < count($state1->templates); $indicator++) { // Set the response details into Option    ?>
                                <option
                                  value="<?= $state1->templates[$indicator]->template_name ?>!<?= $state1->templates[$indicator]->language_code ?>!<?= $state1->templates[$indicator]->body_variable_count ?>!<?= $state1->templates[$indicator]->template_id ?>">
                                  <?= $state1->templates[$indicator]->template_name ?>
                                  [
                                  <?= $state1->templates[$indicator]->language_code ?>]
                                </option>
                              <? }
                            } else if ($state1->response_status == 403 || $response == '') {
                              header("Location: index");
                            }
                            ?>
                            </table>
                          </select>

                        </div>
                        <div class="col-sm-2">
                        </div>
                      </div>
                      <!-- Whatsapp Sender ID -->
                      <? /*<div class="form-group mb-2 row">
                  <label class="col-sm-3 col-form-label">Whatsapp Sender ID <label style="color:#FF0000">*</label>
                    <span data-toggle="tooltip"
                      data-original-title="Avl. Credits - Available Credits">[?]</span></label>
                  <div class="col-sm-7">
                    <div <? if ($_SESSION['yjwatsp_user_master_id'] != 0) { ?>style="display: none;" <? } ?>><input
                        type="radio" name="rdo_senderid" id="rdo_senderid" tabindex="1" autofocus value="A"
                        checked="checked" onclick="func_open_senderid('A')"><i class="helper"></i>&nbsp;Admin
                      Sender ID&nbsp;&nbsp;&nbsp;<input type="radio" name="rdo_senderid" id="rdo_senderid"
                        tabindex="1" value="U" onclick="func_open_senderid('U')"><i class="helper"></i>&nbsp;User
                      Sender ID</div>

                  

                          <?  if ($cntmonth > 0) { // to select the whatsapp senderid 
                              if ($indicator % 2 == 0) { ?>
                                <tr>
                                <? } ?>
                                <td>  <!-- to select the mobile numbers -->
                                  <input type="checkbox" checked onclick="call_gettemplate()" class="cls_checkbox"
                                    id="txt_whatsapp_mobno" name="txt_whatsapp_mobno[]" tabindex="1" autofocus
                                    value="<?= $state1->result[$indicator]->store_id . "~~" . $state1->result[$indicator]->whatspp_config_id . "~~" . $state1->result[$indicator]->country_code.$state1->result[$indicator]->mobile_no . '~~' . $state1->result[$indicator]->bearer_token . '~~' . $whatsapp_tmplate_url . $state1->result[$indicator]->whatsapp_business_acc_id . '~~0~~' . $whatsapp_tmplate_url . $state1->result[$indicator]->phone_number_id ?>">
                                  <label class="form-label"> <?= $state1->result[$indicator]->country_code.$state1->result[$indicator]->mobile_no ?> [Avl. Credits :
                                    <b>
                                      <?= $cntmonth ?>
                                    </b>]</label>
                                </td>
                                <?
                                if ($indicator % 2 == 1) { ?>
                                </tr>
                              <? }
                            }
                          }
                        }
                        ?>
                      </table>
                    </div>

                  </div>
                  <div class="col-sm-2">
                  </div>
                </div> */ ?>

		

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
                      <!-- Enter Mobile Numbers -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Enter Mobile Numbers : <label
                            style="color:#FF0000">*</label> <span data-toggle="tooltip"
                            data-original-title="Mobile numbers allowed with Country Code and without + symbol. Upload Mobile numbers using CSV File">[?]</span>
                          <label style="color:#FF0000">(With Country Code and without + symbol. New-Line Separated.)</label></label>
                        <div class="col-sm-7">
                          <!-- To using the mobile numbers -->
                          <textarea id="txt_list_mobno" name="txt_list_mobno" tabindex="2" required=""
                         onblur="call_remove_duplicate_invalid()" onclick="call_remove_duplicate_invalid()"onkeypress="return isValidInput(event)"
                         placeholder="919234567890,919234567891,919234567892,919234567893"
                            class="form-control form-control-primary required" data-toggle="tooltip"
                            data-placement="top" data-html="true" title=""
                            data-original-title="Enter Mobile Numbers. Each row must contains only one mobile no with Country Code and without + symbol. For Ex : 919234567890,919234567891,919234567892,919234567893"
                            style="height: 150px !important; width: 100%;"></textarea>
                          <div id='txt_list_mobno_txt' class='text-danger'></div>

			 
			<!--	<label style="color:#FF0000">Note: Compose Whatsapp Should Have Atleast 100 Numbers</label></label> -->

			<?php if ($_SESSION['yjwatsp_user_master_id'] != 1 ) { ?>
    				<label style="color:#FF0000">Note: Campaign Should Have Atleast 100 Numbers</label>
			<?php } ?>

                        </div>
                        <!-- Remove dublicates -->
                        <div class="col-sm-2">
                          <div class="checkbox-fade fade-in-primary" style="display: none;">
                            <label data-toggle="tooltip" data-placement="top" data-html="true" title=""
                              data-original-title="Click here to Remove the Duplicates">
                              <input type="checkbox" name="chk_remove_duplicates" id="chk_remove_duplicates" checked
                                value="remove_duplicates" tabindex="10" onclick="call_remove_duplicate_invalid()">
                              <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                              <span class="text-inverse" style="color:#FF0000 !important">Remove Duplicates</span>
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
                              <span class="text-inverse" style="color:#FF0000 !important">Remove Stop Status Mobile
                                No's</span>
                            </label>
                          </div>
                          <!-- To upload check the files -->
                          <div class="checkbox-fade fade-in-primary" id='id_mobupload'>
                            <input type="file" class="form-control" name="upload_contact" id='upload_contact'
                              tabindex="3" <? if ($display_action == 'Add') { ?>required="" <? } ?>
                              accept="text/csv" onchange="validateFile(this)"
                              data-toggle="tooltip" data-placement="top" data-html="true" title=""
                              data-original-title="Upload the Mobile Numbers via Excel, CSV, Text Files"> <label
                              style="color:#FF0000">[Upload the Mobile Numbers via CSV File]</label>
                          </div>
                          <div class="checkbox-fade fade-in-primary" id='id_mobupload_sub' style="display: none;">
                            <label class="error_display"><b>Kindly upload the mobile numbers from Customized Template
                                Panel</b></label>
                          </div>
                        </div>
                      </div>
                      <!-- To upload check the files -->
                      <div class="form-group mb-3 row" style="display: none;">
                        <label class="col-sm-3 col-form-label" style="float: left">Upload Media Files</label>
                        <div class="col-sm-7" style="float: left">
                          <input type="file" class="form-control mb-1" name="txt_media" id="txt_media" tabindex="4"
                            accept="image/jpeg,image/jpg,image/gif,image/png,application/pdf,video/h263,video/m4v,video/mp4,video/mpeg,video/mpeg4,video/webm"
                            data-toggle="tooltip" onblur="validate_filesize(this)" data-placement="top" data-html="true"
                            title=""
                            data-original-title="Upload Any Media below or equal to 5 MB Size - Upload Only the Audio MP4, Video MP4, JPG, PNG, PDF file">
                          <input type="text" name="txt_caption" id='txt_caption' tabindex="4" class="form-control"
                            value="" maxlength="200"
                            placeholder="Enter Media Caption [Maximum - 150 Characters allowed]" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Enter Media Caption">
                        </div>
                        <div class="col-sm-2">
                        </div>
                      </div>
                      <!-- To upload the Customized Template -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">&nbsp;</label>
                        <div class="col-sm-7">
                          <div style="clear: both; word-wrap: break-word; word-break: break-word;"
                            id="slt_whatsapp_template_single"></div>
                          <input type="hidden" id="txt_sms_content" name="txt_sms_content">

                          <div id="id_show_variable_csv" style="clear: both; display: none">
                            <label class="error_display"><b>Customized Template</b></label>
                            <input type="file" class="form-control" name="fle_variable_csv" id='fle_variable_csv' onchange="validateFile(this)"
                              accept="text/csv" data-toggle="tooltip" data-placement="top" data-html="true" title=""
                              data-original-title="Upload the Mobile Numbers via CSV Files" tabindex="8">
                            <input type="hidden" id="txt_variable_count" name="txt_variable_count" value="0">
                            <label class="j-label mt-1"><a href="uploads/imports/sample_variables.csv" download
                                class="btn btn-info alert-ajax btn-outline-info"><i class="fas fa-download"></i>
                                Download Sample CSV File</a></label>
                          </div>
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
                        <label class="col-sm-3 col-form-label">Reply Buttons <br>(Maximum 3 Allowed)</label>
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
                        <label class="col-sm-3 col-form-label">Product List <br>(Maximum 4 Allowed)</label>
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
                      <input type="hidden" class="form-control" name='tmpl_call_function' id='tmpl_call_function'
                        value='compose_whatsapp' />
                      <a href="#!" onclick="preview_compose_template()" name="preview_submit" id="preview_submit"
                        tabindex="11" value="" class="btn btn-info">Preview</a>
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
      <? include("libraries/site_footer.php"); ?>

    </div>
  </div>

  <!-- Modal content-->
  <div class="modal fade" id="default-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style=" max-width: 75% !important;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Preview Template</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="id_modal_display" style=" word-wrap: break-word; word-break: break-word;">
          <h5>Welcome</h5>
          <p>Waiting for load Data..</p>
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
              <p id="file_response_msg"></p>
              <span>Are you sure you want to create a campaign?</span>
            </div>
            <div class="modal-footer" style="margin-right:30%;">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Yes</button>
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
          <div class="container" style="text-align: center;">
            <img alt="image" style="width: 50px; height: 50px; display: block; margin: 0 auto;" id="image_display">
           <br>
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

  <audio id="audio" style="display: none;"></audio>
  <input type="hidden" name="txt_media_duration" id="txt_media_duration" style="display: none;">
  <script>
    // start function document
    $(function () {
      $('.theme-loader').fadeOut("slow"); $('.theme-loader1').fadeOut("slow");
      init();
    });
    document.body.addEventListener("click", function (evt) {
      //note evt.target can be a nested element, not the body element, resulting in misfires
      $("#id_error_display").html("");
      $("#file_document_header").prop('disabled', false);
      $("#file_document_header_url").prop('disabled', false);
    });
    // preview_compose_template funct
    function preview_compose_template() {
      $("#slt_whatsapp_template_single").html("");
      var tmpl_name = $("#slt_whatsapp_template").val();
      $.ajax({
        type: 'post',
        url: "ajax/whatsapp_call_functions.php?previewTemplate_meta=previewTemplate_meta&tmpl_name=" + tmpl_name,
        beforeSend: function () {
          $('.theme-loader').show();
	  $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide();
	  $('.theme-loader1').hide();
        },
        success: function (response_msg) {
          $("#id_modal_display").html(response_msg.msg);
          $("#id_modal_display :input").attr("disabled", true);
          $('#default-Modal').modal({ show: true });
          $('.theme-loader').hide(); $('.theme-loader1').hide();
          call_getsmscount();
          call_getsingletemplate();
        },
        error: function (response_msg, status, error) {
          $("#txt_sms_content").val(response_msg.msg);
          $("#txt_char_count").val(response_msg.msg.length);
          $('.theme-loader').hide(); $('.theme-loader1').hide();
          call_getsmscount();
        }
      });

    }

    //var preview_close = document.getElementById('close');
    // preview_close.addEventListener('click', function preview_close(){
    //call_getsingletemplate();
    //});
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
      var file = e.currentTarget.files[0];
      $('#txt_media_duration').val('');
      //check file extension for audio/video type
      if (file.name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)) {
        obUrl = URL.createObjectURL(file);
        document.getElementById('audio').setAttribute('src', obUrl);
      }
    });

    function validate_filesize(file_name) {
      // console.log(file_name+"=="+file_name.duration);
      $("#id_error_display").html("");
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
    }

    function validate_filesizes(file_name) {
      // console.log(file_name);
      $("#id_error_display").html("");
      var file_size = file_name.files[0].size;
      // console.log(file_size);
      if (file_size > 5242880) { // 5 MB
        $("#id_error_display").html("File size must below 5 MB Size. Kindly try again!!");
        console.log("Failed");
      } else {
        console.log("Success");
      }
    }

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
            $('.theme-loader').show(); $('.theme-loader1').show();
          },
          complete: function () {
            $('.theme-loader').hide(); $('.theme-loader1').hide();
          },
          success: function (response) {
            // alert("==="+response.msg);
            $('#id_own_senderid').html(response.msg);

            if (admin_user == 'A') {
              $('#id_own_senderid').css('display', 'none');
            } else if (admin_user == 'U') {
              $('#id_own_senderid').css('display', 'block');
            }
            $('.theme-loader').hide(); $('.theme-loader1').hide();
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
          $('.theme-loader').show(); $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide(); $('.theme-loader1').hide();
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
          $('.theme-loader').hide(); $('.theme-loader1').hide();
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
            $('.theme-loader').show(); $('.theme-loader1').show();
          },
          complete: function () {
            $('.theme-loader').hide(); $('.theme-loader1').hide();
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
            $('.theme-loader').hide(); $('.theme-loader1').hide();
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


    function validate_phone_number(phone) {
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
    
    function call_remove_duplicate_invalid() {
      var inv = 1; // Define or assign the variable inv
      var dup = 1; // Define or assign the variable dup

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
            if (dup === 1) {
              if (correct_mobno_data.indexOf(expl[ij]) === -1) {
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
            }
          } else {
            issu_mob += expl[ij] + ',';
          }
        }
      }

      // Output the results as needed
      $('#txt_list_mobno').val(return_mobno_data);
      $('#txt_list_mobno_txt').html('Invalid Mobile Nos: ' + issu_mob);
    }

    // call_gettemplate funct
    function call_gettemplate() {
      $("#id_error_display").html("");
      $("#slt_whatsapp_template_single").html("");
      var id_slt_mobileno_split = id_slt_mobileno.split("~~");

      $.ajax({
        type: 'post',
        url: "ajax/whatsapp_call_functions.php?getTemplate_meta=getTemplate_meta&wht_tmpl_url=" + id_slt_mobileno_split[4] + "&wht_bearer_token=" + id_slt_mobileno_split[3],
        beforeSend: function () {
          $('.theme-loader').show(); $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide(); $('.theme-loader1').hide();
        },
        success: function (response_msg) {
          $('#slt_whatsapp_template').html(response_msg.msg);
          $('.theme-loader').hide(); $('.theme-loader1').hide();
        },
        error: function (response_msg, status, error) {
          $("#slt_whatsapp_template").html(response_msg.msg);
          $('.theme-loader').hide(); $('.theme-loader1').hide();
        }
      });
    }

    // call_getsingletemplate funtc
    function call_getsingletemplate() {
      $("#slt_whatsapp_template_single").html("");
      $('#id_show_variable_csv').css('display', 'none');
      var slt_whatsapp_template = $("#slt_whatsapp_template").val();
      var id_slt_mobileno = $("#txt_whatsapp_mobno").val();

      $.ajax({
        type: 'post',
        url: "ajax/whatsapp_call_functions.php?getSingleTemplate_meta=getSingleTemplate_meta&tmpl_name=" + slt_whatsapp_template,
        beforeSend: function () {
          $("#id_error_display").html("");
          $('.theme-loader').show(); $('.theme-loader1').show();
        },
        complete: function () {
          $("#id_error_display").html("");
          $('.theme-loader').hide(); $('.theme-loader1').hide();
        },
        success: function (response_msg) {
          $('#slt_whatsapp_template_single').html(response_msg.msg);
          $("#txt_sms_content").val(response_msg.msg);
          $("#txt_char_count").val(response_msg.msg.length);
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
          }else{
            console.log("else")
            $('#txt_variable_count').val(slt_whatsapp_template_split[2]);
            $('#txt_list_mobno').attr('readonly', false);
            $("#fle_variable_csv").attr("required", false);
            $('#id_show_variable_csv').css('display', 'none');
            // $("#upload_contact").val('');
            // $('#txt_list_mobno').val('');
            $("#id_mobupload").css('display', 'block');
            $("#id_mobupload_sub").css('display', 'none');
          }
          $('.theme-loader').hide(); $('.theme-loader1').hide();
          call_getsmscount();
          $("#id_error_display").html("");
        },
        error: function (response_msg, status, error) {
          $("#slt_whatsapp_template_single").html(response_msg.msg);
          $("#txt_sms_content").val(response_msg.msg);
          $("#txt_char_count").val(response_msg.msg.length);
          $('.theme-loader').hide(); $('.theme-loader1').hide();
          call_getsmscount();
          $("#id_error_display").html("");
        }
      });
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
          $('.theme-loader').show(); $('.theme-loader1').show();
        },
        complete: function () {
          $('.theme-loader').hide(); $('.theme-loader1').hide();
        },
        success: function (response_msg) {
          // alert(response_msg.msg);
          $("#txt_sms_content").val(response_msg.msg);
          $("#txt_char_count").val(response_msg.msg.length);
          $('.theme-loader').hide(); $('.theme-loader1').hide();
          call_getsmscount();
        },
        error: function (response_msg, status, error) {
          $('.theme-loader').hide(); $('.theme-loader1').hide();
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
      // var len = $('.cls_checkbox:checked').length;
      // // alert("cnt:"+len);
      // if (len <= 0) {
      //   $("#id_error_display").html("Please check at least one Whatsapp Sender ID");
      //   $('#txt_whatsapp_mobno').focus();
      //   flag = false;
      // }

  var slt_whatsapp_template = $("#slt_whatsapp_template").val();
let parts = slt_whatsapp_template.split('_');
var tempate_code = parts[2];

if (tempate_code[2] === 'i' || tempate_code[3] === 'v' || tempate_code[4] === 'd') {
    var fileInput = document.getElementById('file_image_header');
    var file_image_header_url = $('#file_image_header_url').val();
    
    // Get the selected file
    var file = fileInput.files[0];
    if (file == null && !file_image_header_url) {
      $('#file_image_header').attr('required', 'required');
        $("#id_error_display").html("Media file not uploaded");
        flag = false;
    }
}

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
            $('.theme-loader').show(); $('.theme-loader1').show();
          },
          complete: function () {
            $('#compose_submit').attr('disabled', false);
            $('.theme-loader').hide(); $('.theme-loader1').hide();
          },
          success: function (response) {
   $('#image_display').removeAttr('src');
            if (response.status == '0') {
              // $('#id_slt_header').val('');
              // $('#id_slt_template').val('');
              // $('#txt_list_mobno').val('');
              // $('#chk_remove_duplicates').val('');
              // $('#chk_remove_invalids').val('');
              // $('#txt_sms_content').val('');
              // $('#txt_char_count').val('');
              // $('#txt_sms_count').val('');
              $('#id_submit_composercs').attr('disabled', false);
              $('#compose_submit').attr('disabled', false);
              //$("#id_error_display").html(response.msg);
              $('#image_display').attr('src', 'assets/img/failed.png');
              $('#campaign_compose_message').modal({ show: true });
              $("#message").html(response.msg);
            } else if (response.status == 2) {
              $('#compose_submit').attr('disabled', false);
             // $("#id_error_display").html(response.msg);
              $('#image_display').attr('src', 'assets/img/failed.png');
              $('#campaign_compose_message').modal({ show: true });
              $("#message").html(response.msg);
            } else if (response.status == 1) {
              $('#compose_submit').attr('disabled', false);
              //$("#id_error_display").html(response.msg);
              $('#campaign_compose_message').modal({ show: true });
              $('#image_display').attr('src', 'assets/img/success.png');
              $("#message").html(response.msg);
              setInterval(function () {
                window.location = 'template_rcs_list';
              }, 3000);
            }
            $('.theme-loader').hide(); $('.theme-loader1').hide();
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
            $('.theme-loader').show(); $('.theme-loader1').show();
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

  function validateFile(input) {
      console.log("coming")
      $('#txt_list_mobno').attr('readonly', false);
      $('#txt_list_mobno').val('');
      var fileId = input.id;
      var idParts = fileId.split('_');
      if (idParts[2] == 'add') {
        $("#sure_question").html("Are you sure you want to add members to group?");
      } else if (idParts[2] == 'remove') {
        $("#sure_question").html("Are you sure you want to remove members to group?");
      }
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
        readFileContents(file);
      }
    }

    function validateNumber(number) {
      return /^91[6-9]\d{9}$/.test(number);
    }
    function readFileContents(file) {
      $('.preloader-wrapper').show();
      var reader = new FileReader();
      reader.onload = function (event) {
        var contents = event.target.result;
        var workbook = XLSX.read(contents, {
          type: 'binary'
        });
        var firstSheetName = workbook.SheetNames[0];
        var worksheet = workbook.Sheets[firstSheetName];
        var data = XLSX.utils.sheet_to_json(worksheet, {
          header: 1
        });
        //array values get in invalids,dublicates
        var invalidValues = [];
        var duplicateValuesInColumnA = [];
        var uniqueValuesInColumnA = new Set();
        var valid_count;
        for (var rowIndex = 0; rowIndex < data.length; rowIndex++) {
          var valueA = data[rowIndex][0]; // Assuming column A is at index 0
          if (!validateNumber(valueA)) {
            console.log(valueA + "invalid");
            invalidValues.push(valueA);
          } else if (uniqueValuesInColumnA.has(valueA)) {
            duplicateValuesInColumnA.push(valueA);
            console.log(valueA + "duplicate")
          } else {
            console.log(valueA + "valid")

            uniqueValuesInColumnA.add(valueA);
          }
        }
        $('#txt_list_mobno').attr('readonly', true);
        valid_count = uniqueValuesInColumnA.length;
        $('#txt_list_mobno').val(Array.from(uniqueValuesInColumnA).join(','));

        var totalCount = data.length - 1;

        if (invalidValues.length > 0 && duplicateValuesInColumnA.length > 0) {
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Invalid Numbers: \n' + JSON.stringify(invalidValues.length) + '\n Duplicate Numbers: \n' + JSON.stringify(duplicateValuesInColumnA.length) + '</b>');
          // Add a click event handler to the modal's backdrop (outside the modal)
          $('body').on('click', function (e) {
            // Check if the click target is outside of the modal
            if (!$('#upload_file_popup').is(e.target) && $('#upload_file_popup').has(e.target).length === 0) {
              // Close the modal if the click is outside
              $('#upload_file_popup').modal('hide');
            }
          });
        } else if (duplicateValuesInColumnA.length > 0) {
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Duplicate Numbers : \n' + JSON.stringify(duplicateValuesInColumnA.length) + '</b>');
          // Add a click event handler to the modal's backdrop (outside the modal)
          $('body').on('click', function (e) {
            // Check if the click target is outside of the modal
            if (!$('#upload_file_popup').is(e.target) && $('#upload_file_popup').has(e.target).length === 0) {
              // Close the modal if the click is outside
              $('#upload_file_popup').modal('hide');
            }
          });
        } else if (invalidValues.length > 0) {
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          // Show the modal
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Invalid Numbers : \n' + JSON.stringify(invalidValues.length) + '</b>');
          // Add a click event handler to the modal's backdrop (outside the modal)
          $('body').on('click', function (e) {
            // Check if the click target is outside of the modal
            if (!$('#upload_file_popup').is(e.target) && $('#upload_file_popup').has(e.target).length === 0) {
              // Close the modal if the click is outside
              $('#upload_file_popup').modal('hide');
            }
          });
        } else {
          // console.log(uniqueValuesInColumnA.length);
          $('.preloader-wrapper').hide();
          $('.loading_error_message').css("display", "none");
          $('#upload_file_popup').modal('show');
          $('#file_response_msg').html('<b>Validating Successfully.</b>');
          // Add a click event handler to the modal's backdrop (outside the modal)
          $('body').on('click', function (e) {
            // Check if the click target is outside of the modal
            if (!$('#upload_file_popup').is(e.target) && $('#upload_file_popup').has(e.target).length === 0) {
              // Close the modal if the click is outside
              $('#upload_file_popup').modal('hide');
            }
          });
        }
      };
      reader.readAsBinaryString(file);
    }

    $('#upload_file_popup').find('.btn-secondary').on('click', function () {
      window.location.href = 'compose_template_rcs';
      // var uploadType = $(this).data('upload-type');
      // console.log(uploadType);
      // if (uploadType === 'contact') {
      //     // Action for upload_contact
      //     $('#txt_list_mobno').attr('readonly', false);
      //     $('#upload_contact').val('');
      // } else if (uploadType === 'variable_csv') {
      //     // Action for fle_variable_csv
      //     $('#fle_variable_csv').val('');
      // }
      // $('#txt_list_mobno').val('');
    });


    function isValidInput(event) {
      var charCode = event.charCode || event.keyCode;
      return (charCode === 44 || // Allow comma (,)
        charCode === 8 ||  // Allow backspace
        charCode === 13 || // Allow enter
        (charCode >= 48 && charCode <= 57)); // Allow digits (0-9)
    }

  </script>
</body>

</html>
