<?php
/*
Primary Admin user only allow to this Manage Users page.
This page is used to manage the users.
It will send the form to API service 
and get the response from them and store into our DB.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 03-Jul-2023
*/

session_start(); // start session
error_reporting(0); // The error reporting function

include_once 'api/configuration.php'; // Include configuration.php
extract($_REQUEST);// Extract the request

// If the Session is not available redirect to index page
if ($_SESSION['yjwatsp_user_id'] == "") { ?>
  <script>window.location = "index";</script>
  <?php exit();
}

// If the logged in user is not Primary admin, it will redirect to Dashboard page
if ($_SESSION['yjwatsp_user_master_id'] != 1) { ?>
  <script>window.location = "dashboard";</script>
  <? exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Manage Users Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Add Users ::
    <?= $site_title ?>
  </title>
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <!-- style include in css -->
  <style>
    .progress {
      height: 0.2rem;
    }
  </style>
</head>

<body>
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
            <h1>Add Users</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="manage_users_list">Manage Users List</a></div>
              <div class="breadcrumb-item">Add Users</div>
            </div>
          </div>

          <!-- User Creation Form panel -->
          <div class="section-body">
            <div class="row">

              <div class="col-12 col-md-8 col-lg-8 offset-2">
                <div class="card">
                  <form class="needs-validation" novalidate="" id="frm_users" name="frm_users" action="#" method="post"
                    enctype="multipart/form-data">
                    <div class="card-body">
                      <!-- select user type -->
                      <div class="clear_both form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">User Type</label>
                        <div class="col-sm-8" style="float: right;">
                          <select name="slt_user_type" id='slt_user_type' class="form-control" data-toggle="tooltip"
                            data-placement="top" title="" autofocus="" tabindex="1" onblur="func_move_super_admin()"
                            onchange="func_move_super_admin()" data-original-title="Select User Type">
                            <? // To get the User type from API
                            $replace_txt = '{
                                "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
                              }'; // User ID
                            $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // Add Bearer Token
                            $curl = curl_init();
                            curl_setopt_array(
                              $curl,
                              array(
                                CURLOPT_URL => $api_url . '/list/user_type',
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
                            site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                            $response = curl_exec($curl);
                            curl_close($curl);

                            // After got response decode the JSON result
                            $header = json_decode($response, false);
                            site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

                            // Display the response data into option button
                            if ($header->num_of_rows > 0) {
                              // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process and to get the details.if the condition are false to stop the process
                              for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) { ?>
                                <option value="<?= $header->report[$indicator]->user_master_id ?>" <? if ($user_master_id == 4) { ?> selected <? } ?>>
                                  <?= $header->report[$indicator]->user_type ?>
                                </option>
                                <?
                              }
                            } else if ($header->response_status == 403 || $response == '') { ?>
                                <script>
                                  window.location = "index"
                                </script>
                            <? }
                            ?>
                          </select>
                        </div>
                      </div>
                      <!-- Select Super Admin names -->
                      <?/* <div class="clear_both form-group mb-2 row" id='id_superadmin'>
                  <label class="col-sm-4 col-form-label">Select Super Admin</label>
                  <div class="col-sm-8" style="float: right;">
                    <select name="slt_super_admin" id='slt_super_admin' class="form-control mb-2"
                      data-toggle="tooltip" data-placement="top" title="" tabindex="2"
                      data-original-title="Select Super Admin" onblur="func_display_dept_admin()"
                      onchange="func_display_dept_admin()">
                      <? // To get the Super Admin user from API
                      $replace_txt = '{
                          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
                        }'; // Add User ID
                      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // Add Bearer Token
                      $curl = curl_init();
                      curl_setopt_array($curl, array(
                        CURLOPT_URL => $api_url . '/list/display_super_admin',
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
                      site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                      $response = curl_exec($curl);
                      curl_close($curl);

                      // After got response decode the JSON result
                      $header = json_decode($response, false);
                      if ($header->response_status == 403 || $response == '') { ?>
                        <script>
                          window.location = "index"
                        </script>
                      <? }
                      site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

                      // To Dispay the response data into option button
                      for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
                        // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process and to get the details.if the condition are false to stop the process
                        if ($indicator == 0) {
                          $firstusr = $header->report[$indicator]->user_id;
                        }
                        echo '<option value="' . $header->report[$indicator]->user_id . '~~' . $header->report[$indicator]->user_short_name . '">' . $header->report[$indicator]->user_name . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div> */ ?>
                      <!-- Select Department Admin -->
                      <div class="clear_both form-group mb-2 row">
                        <label class="col-sm-4 col-form-label"> User Name</label>
                        <div class="col-sm-8" style="float: right;">
                          <input type="text" name="clientname_txt" id="clientname_txt" class="form-control" value=""
                            maxlength="50" tabindex="1" autofocus="" required="" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Enter User Name"
                            placeholder="Enter User Name" pattern="[a-zA-Z0-9 -_]+"
                            onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                        </div>
                      </div>
                      <!-- To create the loginid -->
                      <!-- <div class="clear_both form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">Login ID</label>
                        <div class="col-sm-8" style="float: right;">
                          <span id="id_loginid"> -- </span>
                          <input type="hidden" name="txt_loginid" id="txt_loginid" class="form-control" value="LOGINID"
                            maxlength="100" tabindex="2" required="" data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="Login ID" placeholder="Login ID" onblur="func_display_loginid()"
                            pattern="[a-zA-Z0-9 -_]+" onkeypress="return clsAlphaNoOnly(event)" onpaste="return false;">
                        </div>
                      </div> -->
                      <!-- login short names -->
                      <!-- <div class="clear_both form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">Login Short Name</label>
                        <div class="col-sm-8">
                          <span id="id_login_shortname"> -- </span>
                          <input type="hidden" name="txt_login_shortname" id="txt_login_shortname" class="form-control"
                            value="" maxlength="4" tabindex="2" required="" data-toggle="tooltip" data-placement="top"
                            title="" data-original-title="Login Short Name" placeholder="Login Short Name">
                        </div>
                      </div> -->
                      <!-- login password -->
                      <!-- <div class="form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">Login Password</label>
                        <div class="col-sm-8">
                          <input type="password" name="txt_user_password" id='txt_user_password' class="form-control"
                            value="" maxlength="100" tabindex="2" required="" data-toggle="tooltip" data-placement="top"
                            title="" data-original-title="Login Password" placeholder="Login Password"
                            onblur="return checkPasswordStrength()">
                          <div class="input-group-prepend">
                            <div class="input-group-text" onclick="password_visible1()" id="display_visiblitity"><i
                                class="fas fa-eye-slash"></i>
                            </div>
                          </div>

                        </div>
                      </div> -->


                      <div class="form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">New Password</label>
                        <div class="col-sm-8">
                          <div class="input-group" title="" data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="New Password : [Atleast 8 characters and Must Contains Numeric, Capital Letters and Special characters]">
                            <span class="input-group-addon"><i class="icofont icofont-ui-lock"></i></span>
                            <input type="password" name="txt_new_password" id='txt_new_password' class="form-control"
                              maxlength="100" value="" tabindex="2" required=""
                              placeholder="New Password : [Atleast 8 characters and Must Contains Numeric, Capital Letters and Special characters]"
                              onblur="return checkPasswordStrength()">
                            <div class="input-group-prepend">
                              <div class="input-group-text" onclick="password_visible1()" id="display_visiblitity"><i
                                  class="fas fa-eye-slash"></i>
                              </div>
                            </div>
                          </div>


                          <div id='idtxt_new_password' class='text-danger'></div>

                          <div class="progress" style="margin-top: 3px; height: 3px;">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                              aria-valuemax="100" style="width:0%" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Password Strength Meter" placeholder="Password Strength Meter">
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">Confirm Password</label>
                        <div class="col-sm-8">
                          <div class="input-group" title="" data-toggle="tooltip" data-placement="top" title=""
                            data-original-title="Confirm Password : [Atleast 8 characters and Must Contains Numeric, Capital Letters and Special characters]">
                            <span class="input-group-addon"><i class="icofont icofont-sand-clock"></i></span>
                            <input type="password" name="txt_confirm_password" id='txt_confirm_password'
                              class="form-control" maxlength="100" value="" tabindex="3" required=""
                              placeholder="Confirm Password">
                            <div class="input-group-prepend">
                              <div class="input-group-text" onclick="password_visible2()" id="display_visiblitity_1"><i
                                  class="fas fa-eye-slash"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="form-group mb-2 row" style="display: none">
                        <div class="col-sm-12">
                          <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                              aria-valuemax="100" style="width:0%" data-toggle="tooltip" data-placement="top" title=""
                              data-original-title="Password Strength Meter" placeholder="Password Strength Meter">
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Email text field -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">Email</label>
                        <div class="col-sm-8">
                          <input type="email" name="txt_user_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
                            id='txt_user_email' oninput="validateEmail()" class="form-control" required=""
                            maxlength="50" minlength="1" value="" tabindex="4" required="" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Email" placeholder="Email">
                        </div>
                      </div>

                      <!-- Mobile number Test -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-4 col-form-label">Mobile No</label>
                        <div class="col-sm-8">
                          <input type="text" name="txt_user_mobile" id='txt_user_mobile' class="form-control"
                          onkeypress="return (event.charCode !=8 && event.charCode ==0 ||  (event.charCode >= 48 && event.charCode <= 57))"
                            oninput="validateInput_phone()" maxlength="10" value="" tabindex="5" required=""
                            data-toggle="tooltip" data-placement="top" title="" data-original-title="Mobile No"
                            placeholder="Mobile No" >
                        </div>
                      </div>
                    </div>
                    <!-- Error Display & Submit button -->
                    <div class="error_display" id='id_error_display_signup'></div>
                    <div class="card-footer text-center">
                      <input type="hidden" class="form-control" name='call_function' id='call_function'
                        value='signup' />
                      <input type="submit" name="submit_signup" id="submit_signup" tabindex="6" value="Submit"
                        class="btn btn-success">
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

  <script>

    function validateEmail() {
      $("#id_error_display_signup").html("");
    }
    function validateInput_phone() {
      $("#id_error_display_signup").html("");
    }

    function checkPasswordStrength() {
      var number = /([0-9])/;
      var alphabets = /([a-zA-Z])/;
      var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
      var new_pass = $('#txt_new_password').val();
      if (new_pass.length < 8 && new_pass != '') {
        $('#idtxt_new_password').html("Weak (should be atleast 8 characters.)");
        $('#txt_new_password').css('border-color', 'red');
        return false;
      } else {
        if ($('#txt_new_password').val().match(number) && $('#txt_new_password').val().match(alphabets) && $('#txt_new_password').val().match(special_characters)) {
          $('#idtxt_new_password').html("");
          $('#txt_new_password').css('border-color', '#a0a0a0');
          return true;
        } else {
          if (new_pass != '') {
            $('#idtxt_new_password').html("Medium (should include alphabets, numbers and special characters.)");
            $('#txt_new_password').css('border-color', 'red');
            return false;
          }
        }
      }
    }

    function password_visible1() {
      var x = document.getElementById("txt_new_password");
      if (x.type === "password") {
        x.type = "text";
        $('#display_visiblitity').html('<i class="fas fa-eye"></i>');
      } else {
        x.type = "password";
        $('#display_visiblitity').html('<i class="fas fa-eye-slash"></i>');
      }
    }

    
    function password_visible2() {
      var x = document.getElementById("txt_confirm_password");
      if (x.type === "password") {
        x.type = "text";
        $('#display_visiblitity_1').html('<i class="fas fa-eye"></i>');
      } else {
        x.type = "password";
        $('#display_visiblitity_1').html('<i class="fas fa-eye-slash"></i>');
      }
    }

    function password_visible() {
      var x = document.getElementById("txt_ex_password");
      if (x.type === "password") {
        x.type = "text";
        $('#id_signup_display_visiblitity').html('<i class="fas fa-eye"></i>');
      } else {
        x.type = "password";
        $('#id_signup_display_visiblitity').html('<i class="fas fa-eye-slash"></i>');
      }
    }


    function clsAlphaNoOnly(e) { // Accept only alpha numerics, no special characters 
      var key = e.keyCode;
      if ((key >= 65 && key <= 90) || (key >= 97 && key <= 122) || (key >= 48 && key <= 57) || (key == 32) || (key == 95)) {
        return true;
      }
      return false;
    }

    // If Sign up submit button clicks
    $("#submit_signup").click(function (e) {
      $("#id_error_display_signup").html("");

      $('#clientname_txt').css('border-color', '');
      $('#txt_new_password').css('border-color', '');
      $('#txt_confirm_password').css('border-color', '');
      $('#txt_user_mobile').css('border-color', '');
      $('#txt_user_email').css('border-color', '');

      //get input field values
      var user_type = $('#slt_user_type').val();
      var uname = $('#clientname_txt').val();
      var login_shortname = $('#txt_login_shortname').val();
      var password = $('#txt_new_password').val();
      var confirm_password = $('#txt_confirm_password').val();
      var email = $('#txt_user_email').val();
      var user_mobile = $('#txt_user_mobile').val();

      var flag = true;
      /********validate all our form fields***********/
      /* Login ID field validation  */
      if (uname == "") {
        $('#clientname_txt').css('border-color', 'red');
        flag = false;
        e.preventDefault();
      }
      /* Login Short Name field validation  */
      if (login_shortname == "") {
        $('#txt_login_shortname').css('border-color', 'red');
        flag = false;
        e.preventDefault();
      }

      /* password field validation  */
      if (password == "") {
        $('#txt_new_password').css('border-color', 'red');
        flag = false;
        e.preventDefault();
      } else {
        if (checkPasswordStrength() == false) {
          flag = false;
          e.preventDefault();
        }
      }

      var txt_user_email = $('#txt_user_email').val();
      /* Email field validation  */
      var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
      if (filter.test(txt_user_email)) {
        // flag = true;
      } else {
        $('#txt_user_email').css('border-color', 'red');
        document.getElementById('txt_user_email').focus();
        $("#id_error_display_signup").html("Email is invalid");
        flag = false;
        e.preventDefault();
      }
      
      /* confirm_password field validation  */
      if (confirm_password == "") {
        $('#txt_confirm_password').css('border-color', 'red');
        flag = false;
        e.preventDefault();
      }

      /* password, confirm_password field validation  */
      if (confirm_password != "" && password != "" && confirm_password != password) {
        $('#txt_confirm_password').css('border-color', 'red');
        $("#id_error_display_signup").html("Confirm Password mismatch with Password");
        flag = false;
        e.preventDefault();
      }

      /* Email field validation  */
      if (email == "") {
        $('#txt_user_email').css('border-color', 'red');
        flag = false;
        e.preventDefault();
      }

      /* Mobile field validation  */
      if (user_mobile == "") {
        $('#txt_user_mobile').css('border-color', 'red');
        flag = false;
        e.preventDefault();
      }

      if (user_mobile.length != 10) {
        $('#txt_user_mobile').css('border-color', 'red');
        $("#id_error_display_signup").html("Please enter a valid mobile number");
        console.log("##");
        flag = false;
        e.preventDefault();
      }

      if (!(user_mobile.charAt(0) == "9" || user_mobile.charAt(0) == "8" || user_mobile.charAt(0) == "6" || user_mobile.charAt(0) == "7")) {
        $('#txt_user_mobile').css('border-color', 'red');
        $("#id_error_display_signup").html("Please enter a valid mobile number");
        document.getElementById('txt_user_mobile').focus();
        flag = false;
        e.preventDefault();
      }

      /* If all are ok then we send ajax request to ajax/call_functions.php *******/
      if (flag) {
        var data_serialize = $("#frm_users").serialize();
        $.ajax({
          type: 'post',
          url: "ajax/call_functions.php",
          dataType: 'json',
          data: data_serialize,
          beforeSend: function () { // Before send to Ajax
            $('#submit_signup').attr('disabled', true);
            $('#load_page').show();
          },
          complete: function () { // After complete Ajax
            //$('#submit_signup').attr('disabled', false);
            $('#load_page').hide();
          },
          success: function (response) { // Success
		console.log(response);
            if (response.status == '0') { // Failure response
              $('#txt_user_name').val('');
              $('#txt_user_email').val('');
              $('#txt_user_mobile').val('');
              $('#txt_loginid').val('');
              $('#txt_login_shortname').val('');
              $('#txt_user_password').val('');
              $('#txt_confirm_password').val('');
              $("#chk_terms").prop('checked', false);
              $('#submit_signup').attr('disabled', false);
              $("#id_error_display_signup").html(response.msg);
            }
	    else if (response.status == 2)
	    {
		$("#id_error_display_signup").html(response.msg);
     	    } else if (response.status == 1) { // Success Reponse
              $('#submit_signup').attr('disabled', true);
              $("#id_error_display_signup").html("Login ID Created...");
              setInterval(function () {
                window.location = 'manage_users_list';
              }, 2000);
            }
          },
          error: function (response, status, error) { // Error
		console.log(response);
            $('#txt_user_name').val('');
            $('#txt_user_email').val('');
            $('#txt_user_mobile').val('');
            $('#txt_loginid').val('');
            $('#txt_login_shortname').val('');
            $('#txt_user_password').val('');
            $('#txt_confirm_password').val('');
            $("#chk_terms").prop('checked', false);
            $('#submit_signup').attr('disabled', false);
            $("#id_error_display_signup").html(response.msg);
          }
        });
      }
    });

    var percentage = 0;
    function check(n, m) {
      var strn_disp = "Very Weak Password";
      if (n < 6) {
        percentage = 0;
        $(".progress-bar").css("background", "#FF0000");
        strn_disp = "Very Weak Password";
      } else if (n < 7) {
        percentage = 20;
        $(".progress-bar").css("background", "#758fce");
        strn_disp = "Weak Password";
      } else if (n < 8) {
        percentage = 40;
        $(".progress-bar").css("background", "#ff9800");
        strn_disp = "Medium Password";
      } else if (n < 10) {
        percentage = 60;
        $(".progress-bar").css("background", "#A5FF33");
        strn_disp = "Strong Password";
      } else {
        percentage = 80;
        $(".progress-bar").css("background", "#129632");
        strn_disp = "Very Strong Password";
      }

      // Check for the character-set constraints
      // and update percentage variable as needed.

      //Lowercase Words only
      if ((m.match(/[a-z]/) != null)) {
        percentage += 5;
      }

      //Uppercase Words only
      if ((m.match(/[A-Z]/) != null)) {
        percentage += 5;
      }

      //Digits only
      if ((m.match(/0|1|2|3|4|5|6|7|8|9/) != null)) {
        percentage += 5;
      }

      //Special characters
      if ((m.match(/\W/) != null) && (m.match(/\D/) != null)) {
        percentage += 5;
      }

      // Update the width of the progress bar
      $(".progress-bar").css("width", percentage + "%");
      if (percentage > 80) {
        $("#strength_display").html("");
      } else {
        $("#strength_display").html(strn_disp);
      }
    }
    // Update progress bar as per the input
    $(document).ready(function () {
      // Whenever the key is pressed, apply condition checks.
      $("#txt_new_password").keyup(function () {
        var m = $(this).val();
        var n = m.length;

        // Function for checking
        check(n, m);
      });
    });

    // To display the Login ID from API
    // function func_display_loginid() {
    //   var slt_user_type = $("#slt_user_type").val();
    //   var slt_super_admin = $("#slt_super_admin").val();
    //   var slt_dept_admin = $("#slt_dept_admin").val();
    //   $("#id_error_display_signup").html('');
    //   var send_code = "&slt_user_type=" + slt_user_type + "&slt_super_admin=" + slt_super_admin + "&slt_dept_admin=" + slt_dept_admin;
    //   $.ajax({
    //     type: 'post',
    //     url: "ajax/call_functions.php?tmpl_call_function=display_login_id" + send_code,
    //     dataType: 'json',
    //     success: function (response) { // Success
    //       if (response.status == 0) { // Failure response
    //         $('#txt_loginid').val('');
    //         $('#txt_loginid').focus();
    //         $('#submit_signup').attr('disabled', true);
    //         $('#id_error_display_signup').html(response.msg);
    //       } else { // Success response
    //         $('#txt_loginid').val(response.msg);
    //         $('#id_loginid').html(response.msg);
    //         $('#submit_signup').attr('disabled', false);
    //         const str = response.msg
    //         const pieces = str.split(/[\s_]+/)
    //         const last = pieces[pieces.length - 1]

    //         $('#txt_login_shortname').val(last);
    //         $('#id_login_shortname').html(last);
    //       }
    //     },
    //     error: function (response, status, error) { // Error
    //     }
    //   });
    // }

    // To move to Super Admin
    function func_move_super_admin() {
      var slt_user_type = $("#slt_user_type").val();

      if (slt_user_type == 2) {
        $('#id_superadmin').css("display", "none");
        $('#id_deptadmin').css("display", "none");
      }
      if (slt_user_type == 3) {
        $('#id_superadmin').css("display", "block");
        $('#id_deptadmin').css("display", "none");
      }
      if (slt_user_type == 4) {
        $('#id_superadmin').css("display", "block");
        $('#id_deptadmin').css("display", "block");
      }
      var slt_super_admin = $("#slt_super_admin").val();
      var slt_dept_admin = $("#slt_dept_admin").val();
      $('#slt_super_admin').focus();
      // func_display_loginid();
    }

    // To Display the Department Admin
    function func_display_dept_admin() {
      var slt_user_type = $("#slt_user_type").val();
      var slt_super_admin = $("#slt_super_admin").val();
      if (slt_user_type == 3) {
        $('#id_deptadmin').css("display", "none");
        // func_display_loginid();
      } else if (slt_user_type == 4) {
        $("#id_error_display_signup").html('');
        var send_code = "&slt_user_type=" + slt_user_type + "&slt_super_admin=" + slt_super_admin;
        $.ajax({
          type: 'post',
          url: "ajax/call_functions.php?tmpl_call_function=display_dept_admin" + send_code,
          dataType: 'json',
          success: function (response) { // Success
            if (response.status == 0) { // Failure response
              $('#slt_dept_admin').html('');
              $('#id_deptadmin').css("display", "block");
              $('#txt_loginid').val('');
              $('#id_loginid').html('');
              $('#submit_signup').attr('disabled', true);
              $('#id_error_display_signup').html(response.msg);
            } else { // Success Response
              $('#submit_signup').attr('disabled', false);
              $('#slt_dept_admin').html(response.msg);
              $('#id_deptadmin').css("display", "block");
              $('#id_error_display_signup').html('');
              // func_display_loginid();
            }
          },
          error: function (response, status, error) { // Error
          }
        });
      }
    }
  </script>
</body>

</html>
