<?php
/*
Authendicated users only allow to view this Add Sender ID page.
This page is used to view the Add a New Sender ID.
It will send the form to API service and Save to Whatsapp Facebook
and get the response from them and store into our DB.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
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

$allowd = 1;
if ($_SESSION['yjwatsp_user_master_id'] == 4) { // If logged in user has user_master_id - 4 (Agent Only), this panel enables
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
  }';

  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';  // Add bearer token
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url . '/list/senderid_allowed',
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

  // Send the	data into API and execute
  site_log_generate("Manage Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);

  // After got response decode the JSON result
  $header = json_decode($response, false);
  if ($header->response_status == 403 || $response == '') { ?>
    <script>
      window.location = "index"
    </script>
  <? }
  site_log_generate("Manage Sender ID Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

  if ($header->num_of_rows > 0) { // success message
    for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
      // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
      if ($header->report[$indicator]->cntusr > 0) {
        $allowd = 0;
      }
    }
  }
}

// If allowed status = 0 means it will redirect to Sender ID list page and it is not allowed to access this page
if ($allowd == 0) { ?>
  <script>window.location = "whatsapp_no_api_list";</script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Manage Sender ID ::
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
            <h1>Manage Sender ID</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="whatsapp_no_api_list">Manage Sender ID List</a></div>
              <div class="breadcrumb-item">Manage Sender ID</div>
            </div>
          </div>

          <!-- Form Entry Panel -->
          <div class="section-body">
            <div class="row">

              <div class="col-12 col-md-8 col-lg-8 offset-2">
                <div class="card">
                  <form class="needs-validation" novalidate="" id="frm_store" name="frm_store" action="#" method="post"
                    enctype="multipart/form-data">
                    <div class="card-body">
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Country Code</label>
                        <div class="col-sm-9">
                          <select id="txt_country_code" name="txt_country_code" class="form-control" tabindex="1"
                            autofocus>
                            <? // To Show the country list
                            $replace_txt = '{
                              "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '" 
                            }';
                            $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // Add bearer Token
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                              CURLOPT_URL => $api_url . '/list/country_list',
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
                            site_log_generate("Manage Sender ID Page : " . $uname . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                            $response = curl_exec($curl);
                            curl_close($curl);

                            // After got response decode the JSON result
                            $state1 = json_decode($response, false);
                            site_log_generate("Manage Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

                            // Based on the JSON response, list in the option button
                            if ($state1->num_of_rows > 0) {
                              for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
                                // Looping the indicator is less than the count of report.if the condition is true to continue the process and to get the report details.if the condition are false to stop the process and to send the no available data
                                $shortname = $state1->report[$indicator]->shortname;
                                $phonecode = $state1->report[$indicator]->phonecode;
                                $countryid = $state1->report[$indicator]->id;
                                ?>
                                <option value="<?= $countryid . "~~" . $phonecode ?>" <? if ($shortname == 'IN') {
                                        echo "selected";
                                      } ?>><?=
                                         $shortname . " +" . $phonecode ?>
                                </option>
                              <?php }
                            } else if ($state1->response_status == 403 || $response == '') { ?>
                                <script>
                                  window.location = "index"
                                </script>
                            <? }
                            site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the Query ($sql_dashboard1) on " . date("Y-m-d H:i:s"));
                            ?>
                          </select>
                        </div>
                      </div>
                      <!-- Mobile Number text field using -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Mobile Number <label
                            style="color:#FF0000">*</label></label>
                        <div class="col-sm-9">
                          <input type="text" name="mobile_number" id='mobile_number' class="form-control"
                            value="<?= $_REQUEST['mob'] ?>" tabindex="1" required="" maxlength="10"
                            placeholder="Mobile Number" data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="Mobile Number" oninput="validateInput_phone()"
                            onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"
                            <? if ($_REQUEST['mob'] != '') { ?> readonly <? } ?>>
                        </div>
                      </div>
                      <!-- Profile name text field using -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Profile Name <label
                            style="color:#FF0000">*</label></label>
                        <div class="col-sm-9">
                          <input type="text" name="txt_display_name" id='txt_display_name' class="form-control"
                            value="<?= $_REQUEST['mob'] ?>" tabindex="2" required="" maxlength="30"
                            oninput="validateInput_profile()" placeholder="Profile Name" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Profile Name">
                        </div>
                      </div>
                      <!-- profile image text field using -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Profile Image <label
                            style="color:#FF0000">*</label></label>
                        <div class="col-sm-9">
                          <input type="file" class="form-control" name="fle_display_logo" id='fle_display_logo'
                            tabindex="3" accept="image/png, image/jpg, image/jpeg" required="" data-toggle="tooltip"
                            data-placement="top" title=""
                            data-original-title="Profile Image - Allowed only jpg, png images. Maximum 2 MB Size allowed">
                        </div>
                      </div>
                      <!-- To select the Service Category using in dropdown-->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Service Category <label
                            style="color:#FF0000">*</label></label>
                        <div class="col-sm-9">
                          <select name="slt_service_category" id='slt_service_category' class="form-control"
                            data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="Select Service Category" tabindex="4">
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
                            site_log_generate("Manage Sender ID Page : " . $uname . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                            $response = curl_exec($curl);
                            curl_close($curl);
                            // After got response decode the JSON result
                            $state1 = json_decode($response, false);
                            site_log_generate("Manage Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

                            $i1 = 0;
                            // Based on the JSON response, list in the option button
                            if ($state1->num_of_rows > 0) {
                              // Looping the indicator is less than the count of report.if the condition is true to continue the process and to get the report details.if the condition are false to stop the process and to send the no available data
                              for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
                                $message_category_id = $state1->report[$indicator]->message_category_id;
                                $message_category_title = $state1->report[$indicator]->message_category_title;
                                $i1++; ?>
                                <option value="<?= $message_category_id ?>" <? if ($i1 == 1) { ?> selected <? } ?>>
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
                        </div>
                      </div>
                      <!-- submit button using -->
                      <div class="card-footer text-center">
                        <span class="error_display" id='id_error_display'></span><br>
                        <input type="hidden" class="form-control" name='tmpl_call_function' id='tmpl_call_function'
                          value='save_mobile_api' />
                        <input type="submit" name="compose_submit" id="compose_submit" tabindex="5" value="Submit"
                          class="btn btn-success">

                        <div class="container">
                          <span class="error_display" id='qrcode_display'></span>
                          <img src='./assets/img/loader.gif' id="id_qrcode" alt='QR Code'>
                        </div>
                      </div>

                  </form>
                </div>
              </div>
            </div>

            <div class="text-left">
              <span class="error_display1"><b>Note :</b> <br> 1) Enter 10 digit mobile number.<br> 2) The sender ID or
                the mobile should not have or used whats app. We recommend a fresh mobile nos for all your
                communications. <br> 3) Super admin will have all the right to control the Sender ID, template ID
                creation in terms of coordination & approval <br> 4) Profile Image - Allowed only jpg, png images.
                Maximum 2 MB Size allowed</span>
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

    function validateInput_phone() {
      $("#id_error_display").html("");
    }
    // profile name validation

    //function validateInput(e) {
    //var textField = document.getElementById('txt_display_name');
    //textField.value = textField.value.replace(/\s{2,}/g, ' ').replace(/[^a-zA-Z\_]/g, ' ');
    //if(textField.value){
    //onkeydown="return false;"
    //}
    //}
    function validateInput_profile() {
      var textField = document.getElementById('txt_display_name');
      var text = textField.value;
      var isValid = !/\s{2,}|[^a-zA-Z_ ]/g.test(text);

      console.log(isValid);  // false (invalid due to multiple spaces)

      if (isValid === false) {
        console.log("&&");
        textField.value = text.replace(/\s{2,}|[^a-zA-Z_ ]/g, '');
        return false;
      }
    }


    const upload_limit = 1; //Maximum 2 MB
    // File type validation
    $("#fle_display_logo").change(function () {
      // xls, xlsx, csv, txt
      var file = this.files[0];
      var fileType = file.type;
      var match = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) || (fileType == match[4]) || (fileType == match[5]))) {
        $("#id_error_display").html('Sorry, only PNG, JPG files are allowed to upload.');
        $("#fle_display_logo").val('');
        return false;
      }

      const size = this.files[0].size / 1024 / 1024;
      if (size < upload_limit) { return true; }
      else {
        $("#id_error_display").html('Maximum File size allowed - ' + upload_limit + ' MB. Kindly reduce and choose below ' + upload_limit + ' MB');
        $("#fle_display_logo").val('');
        return false;
      }
    });

    // function call_composesms() {
    $(document).on("submit", "form#frm_store", function (e) {
      e.preventDefault();
      console.log("View Submit Pages");
      console.log("came Inside");
      $("#id_error_display").html("");
      $('#mobile_number').css('border-color', '#a0a0a0');
      $('#txt_display_name').css('border-color', '#a0a0a0');
      $('#fle_display_logo').css('border-color', '#a0a0a0');

      //get input field values 
      var mobile_number = $('#mobile_number').val();
      var txt_display_name = $('#txt_display_name').val();
      var fle_display_logo = $('#fle_display_logo').val();

      var flag = true;
      /********validate all our form fields***********/
      /* mobile_number field validation  */
      if (mobile_number == "") {
        $('#mobile_number').css('border-color', 'red');
        console.log("##");
        flag = false;
      }
      var mobile_number = document.getElementById('mobile_number').value;
      if (mobile_number.length != 10) {
        $("#id_error_display").html("Please enter a valid mobile number");
        console.log("##");
        flag = false;
      }
      if (!(mobile_number.charAt(0) == "9" || mobile_number.charAt(0) == "8" || mobile_number.charAt(0) == "6" || mobile_number.charAt(0) == "7")) {
        $("#id_error_display").html("Please enter a valid mobile number");
        document.getElementById('mobile_number').focus();
        flag = false;
      }

      /* txt_display_name field validation  */
      if (txt_display_name == "") {
        $('#txt_display_name').css('border-color', 'red');
        console.log("##");
        flag = false;
      }

      /* fle_display_logo field validation  */
      if (fle_display_logo == "") {
        $('#fle_display_logo').css('border-color', 'red');
        console.log("##");
        flag = false;
      }

      /* If all are ok then we send ajax request to ajax/master_call_functions.php *******/
      if (flag) {
        var fd = new FormData(this);
        var files = $('#fle_display_logo')[0].files;
        if (files.length > 0) {
          fd.append('file', files[0]);
        }
        $.ajax({
          type: 'post',
          url: "ajax/store_call_functions.php?tmpl_call_function=save_mobile_api",
          dataType: 'json',
          data: fd,
          contentType: false,
          processData: false,
          beforeSend: function () {
            $('#compose_submit').attr('disabled', true);
            $('.theme-loader').show();
          },
          complete: function () {
            $('.theme-loader').hide();
          },
          success: function (response) {
            console.log("SUCC");
            if (response.status == '0') {
              //alert(response.status);
              $('#mobile_number').val('');
              $('#txt_display_name').val('');
              $('#fle_display_logo').val('');
              $('#compose_submit').attr('disabled', false);
              $("#id_error_display").html(response.msg);
            } else if (response.status == 1) {
              //alert(response.status);
              $('#compose_submit').attr('disabled', true);
              $("#id_error_display").html("Sender ID added successfully!..");
              setInterval(function () {
                window.location = 'whatsapp_no_api_list';
                $('#mobile_number').val('');
                $('#txt_display_name').val('');
                $('#fle_display_logo').val('');
              }, 2000);
            }
            $('.theme-loader').hide();
          },
          error: function (response, status, error) {

            console.log("FAL");
            $('#mobile_number').val('');
            $('#txt_display_name').val('');
            $('#fle_display_logo').val('');

            $('#compose_submit').attr('disabled', false);
            $('.theme-loader').show();
            window.location = 'whatsapp_no_api_list';
            $("#id_error_display").html(response.msg);
          }
        });
      }
    });

  </script>
</body>

</html>