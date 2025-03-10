<?php
/*
Primary Admin user only allow to view this Approve Sender ID list page.
This page is used to view the list of Waiting for approve the Sender ID and we can change its Status.
Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table

Version : 1.0
Author : Madhubala (YJ0009)
Date : 03-Jul-2023
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

// If the logged in user is not the Primary Admin, then it will redirect to dashboard page
if ($_SESSION['yjwatsp_user_master_id'] != 1) { ?>
  <script>window.location = "dashboard";</script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Approve Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Approve Sender ID ::
    <?= $site_title ?>
  </title>
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/searchPanes.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/select.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/colReorder.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/buttons.dataTables.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <!-- style include in css -->
  <style>
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
  </style>
</head>

<body>
  <div class="theme-loader"></div>
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
            <h1>Approve Sender ID</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item">Approve Sender ID</div>
            </div>
          </div>

          <!-- List Panel -->
          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive" id="id_approve_whatsapp_no_api">
                      Loading..
                    </div>
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
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>

  <script>

    // On loading the page, this function will call
    $(document).ready(function () {
      find_approve_whatsapp_no_api();
    });
    // start function document
    $(function () {
      $('.theme-loader').fadeOut("slow");
      init();
    });

    // To list the Whatsapp No from API
    function find_approve_whatsapp_no_api() {
      $.ajax({
        type: 'post',
        url: "ajax/display_functions.php?call_function=approve_whatsapp_no_api",
        dataType: 'html',
        success: function (response) {
          $("#id_approve_whatsapp_no_api").html(response);
        },
        error: function (response, status, error) { }
      });
    }
    //setInterval(find_approve_whatsapp_no_api, 60000); // Every 1 min (60000), it will call
    //popup function
    var table_id, whatspp_configid, textvalue, phonenumberid, whatsapp_businessaccid, bearertokenvalue, mobilenumber;

    function func_save_phbabt_popup(indicatori, whatspp_config_id, text_value, phone_number_id, whatsapp_business_acc_id, bearer_token_value, mobile_number) {
      table_id = indicatori, whatspp_configid = whatspp_config_id, textvalue = text_value, phonenumberid = phone_number_id, whatsapp_businessaccid = whatsapp_business_acc_id, bearertokenvalue = bearer_token_value, mobilenumber = mobile_number;

      $('#' + text_value).remove();
      let phone_numberid = $('input[name' + "=" + 'phone_number_id' + "_" + text_value + ']').val();
      let whatsapp_business_accid = $('input[name' + "=" + 'whatsapp_business_acc_id' + "_" + text_value + ']').val();
      let bearer_token_value_id = $('input[name' + "=" + 'bearer_token_value' + "_" + text_value + ']').val();
      if ((phone_numberid == '') && (whatsapp_business_accid == '') && (bearer_token_value_id == '')) {
        $('#id_approved_lineno_' + text_value).append('<a href="javascript:void(0)" id="' + text_value + '" class="btn disabled btn-outline-danger">Kindly fill all the fields.!!</a>');
      } else if ((phone_numberid.length != 15) || (whatsapp_business_accid.length != 15)) {
        $('#id_approved_lineno_' + text_value).append('<a href="javascript:void(0)" id="' + text_value + '" class="btn disabled btn-outline-danger">Invalid format!</a>');
      } else if (bearer_token_value_id == '') {
        $('#id_approved_lineno_' + text_value).append('<a href="javascript:void(0)" id="' + text_value + '" class="btn disabled btn-outline-danger">Kindly fill all the fields.!!</a>');
      } else {
        $(".btn-outline-danger").remove();
        $('#approve-Modal').modal({ show: true });
        // Call remove_senderid function with the provided parameters
      }
    }

    $('#approve-Modal').find('.btn-success').on('click', function () {
      $('#approve-Modal').modal({ show: false });
      func_save_phbabt(table_id, whatspp_configid, textvalue, phonenumberid, whatsapp_businessaccid, bearertokenvalue, mobilenumber);
    });

    //popup function
    var whatspp_configid, approvestatus, table_id;
    function change_status_popup(whatspp_config_id, approve_status, indicatori) {
      whatspp_configid = whatspp_config_id, approvestatus = approve_status, table_id = indicatori;
      $('#reject-Modal').modal({ show: true });
      // Call remove_senderid function with the provided parameters
    }

    $('#reject-Modal').find('.btn-danger').on('click', function () {
      $('#reject-Modal').modal({ show: false });
      change_status(whatspp_configid, approvestatus, table_id);
    });

    // To save the Phone no id, business account id, bearer token
    function func_save_phbabt(indicatori, whatspp_config_id, text_value, phone_number_id, whatsapp_business_acc_id, bearer_token_value, mobile_number) {
      let phone_numberid = $('input[name' + "=" + 'phone_number_id' + "_" + text_value + ']').val();
      let whatsapp_business_accid = $('input[name' + "=" + 'whatsapp_business_acc_id' + "_" + text_value + ']').val();
      let bearer_token_value_id = $('input[name' + "=" + 'bearer_token_value' + "_" + text_value + ']').val();
      var send_code = "&whatspp_config_id=" + whatspp_config_id + "&phone_number_id=" + phone_numberid + "&whatsapp_business_acc_id=" + whatsapp_business_accid + "&bearer_token_value=" + bearer_token_value_id + "&mobile_number=" + mobile_number;
      $.ajax({
        type: 'post',
        url: "ajax/message_call_functions.php?tmpl_call_function=save_phbabt" + send_code,
        dataType: 'json',
        beforeSend: function () {
          $('.theme-loader').show();
        },
        complete: function () {
          $('.theme-loader').hide();
        },
        success: function (response) { // Success
          if (response.status = 0) {
            $('#id_approved_lineno_' + text_value).html('<a href="javascript:void(0)" class="btn disabled btn-outline-success">' + response.msg + '</a>');
          } else { // Success
            $('#id_approved_lineno_' + text_value).html('<a href="javascript:void(0)" class="btn disabled btn-outline-success">Success</a>');
            setTimeout(function () {
              window.location = 'approve_whatsapp_no_api';
            }, 3000); // Every 3 seconds it will check
            $('.theme-loader').hide();
          }
        },
        error: function (response, status, error) { // Error
        }
      });
    }


    // Rejected status update
    function change_status(whatspp_config_id, approve_status, indicatori) {
      var send_code = "&whatspp_config_id=" + whatspp_config_id + "&approve_status=" + approve_status;
      $.ajax({
        type: 'post',
        url: "ajax/message_call_functions.php?tmpl_call_function=approve_whatsappno" + send_code,
        dataType: 'json',
        success: function (response) { // Success
          if (response.status == 1) { // Success Response
            $('#id_approved_lineno_' + indicatori).html('<a href="javascript:void(0)" class="btn disabled btn-outline-danger">Rejected</a>');
            setTimeout(function () {
              window.location = 'approve_whatsapp_no_api';
            }, 2000);
          }
        },
        error: function (response, status, error) { // Error 
        }
      });
    }

    // To Show Datatable with Export, search panes and Column visible
    $('#table-1').DataTable({
      dom: 'Bfrtip',
      colReorder: true,
      buttons: [{
        extend: 'copyHtml5',
        exportOptions: {
          columns: [0, ':visible']
        }
      }, {
        extend: 'csvHtml5',
        exportOptions: {
          columns: ':visible'
        }
      }, {
        extend: 'pdfHtml5',
        exportOptions: {
          columns: ':visible'
        }
      }, {
        extend: 'searchPanes',
        config: {
          cascadePanes: true
        }
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: false
        },
        targets: [0]
      }]
    });
  </script>
</body>

</html>
