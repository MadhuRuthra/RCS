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
  <title>Approve Template :: <?= $site_title ?></title>
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
            <h1>Approve Campaign</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item">Approve Campaign</div>
            </div>
          </div>

	  <!-- List Panel -->
          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive" id="id_approve_template">
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
  <div class="modal-dialog" role="document" >
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Confirmation details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="needs-validation" novalidate="" id="frm_sender_id" name="frm_sender_id" action="#" method="post"
            enctype="multipart/form-data">

            <div class="form-group mb-2 row">
              <label class="col-sm-3 col-form-label">Reason <label style="color:#FF0000">*</label></label>
              <div class="col-sm-9">
                <input class="form-control form-control-primary" type="text" name="reject_reason" id="reject_reason"
                  maxlength="50" title="Reason to Reject" tabindex="12" placeholder="Reason to Reject" onkeypress="return clsAlphaNoOnly(event)">
              </div>
            </div>
          </form>
        <p>Are you sure you want to reject ?</p>
      </div>
      <div class="modal-footer">
<span class="error_display" id='id_error_reject'></span>
        <button type="button" class="btn btn-danger reject_btn" >Reject</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

  <!-- Confirmation details content Approve-->
  <div class="modal" tabindex="-1" role="dialog" id="approve-Modal">
  <div class="modal-dialog" role="document" >
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


 <!-- Confirmation details content-->
 <div class="modal" tabindex="-1" role="dialog" id="approve_error">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="width: 400px;">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" style="width:30px" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="container" style="text-align: center;">
          <img alt="image" style="width: 50px; height: 50px; display: block; margin: 0 auto;" id="image_display">
           <br>
            <span id="split_error"></span>
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
      find_approve_template();
      //setInterval(find_approve_template, 60000); // Every 1 min (60000), it will call
    });

    // start function document
        $(function () {
      $('.theme-loader').fadeOut("slow");
      init();
    });

    // To list the rcs No from API
    function find_approve_template() {
      $.ajax({
        type: 'post',
        url: "ajax/display_functions.php?call_function=approve_message",
        dataType: 'html',
        success: function (response) {
          $("#id_approve_template").html(response);
        },
        error: function (response, status, error) { }
      });
 } 

  
function approve_popup(user_id, compose_rcs_id, total_mobileno_count, indicatori) 
{

    console.log(compose_rcs_id)
    console.log(total_mobileno_count)
    // Show the confirmation modal
    $('#approve-Modal').modal({ show: true });
    // Call remove_senderid function with the provided parameters
 //   $('#approve-Modal').find('.btn-success').on('click', function() {
	$('#approve-Modal').find('.btn-success').off().one('click', function() {
        $('#approve-Modal').modal({ show: false });
        func_save_phbabt(user_id, compose_rcs_id, indicatori);
    });
}



var compose_message_ids,user_ids, approve_statuss, indicatoris;
//popup function
  function reject_status_popup(compose_message_id,user_id, approve_status, indicatori){
console.log(compose_message_id,user_id, approve_status, indicatori);
compose_message_ids = compose_message_id ,user_ids = user_id,approve_statuss = approve_status, indicatoris = indicatori;
  $('#reject-Modal').modal({ show: true });
}

var reason;
    $('#reject-Modal').find('.btn-danger').on('click', function() {
   reason = $('#reject_reason').val();
        if (reason == "") {
          $('#reject-Modal').modal({ show: true });
          $("#id_error_reject").html("Please enter reason to reject");
        }
        else {
          $('.reject_btn').attr("data-dismiss", "modal");
          $('#reject-Modal').modal({ show: false });
      $('#reject-Modal').modal({ show: false });
      change_status(compose_message_ids,user_ids, approve_statuss, indicatoris);
}
  });

    // To save the Phone no id, business account id, bearer token
   function func_save_phbabt(user_id, compose_rcs_id, indicatori) 
   {

      var send_code = "&user_id=" + user_id + "&compose_id=" + compose_rcs_id;
      $.ajax({
          type: 'post',
          url: "ajax/message_call_functions.php?tmpl_call_function=compose_message" + send_code,
          dataType: 'json',
          beforeSend: function () 
          {
            $('.theme-loader').show();
          },
          complete: function () 
          {
            $('.theme-loader').hide();
          },
          success: function(response) {
          console.log('Success Response:', response);
    
	          if (response.status == 1)
          {
            console.log("!!!!");
              console.log(response.msg);
              $('#image_display').attr('src', 'assets/img/success.png');
               // Update the value of the 'message' element
                //$('#split_error').text(response.msg);
                 $('#split_error').text("Campaign started successfully");
              // Show the modal with the id 'approve_error'
              $('#approve_error').modal('show');
                setTimeout(function () {
                window.location = 'compose_message_approval';
                      }, 2000);

        }

	else if(response.status == 0) 
            {
              console.log("!!!!");
              console.log(response.msg);
              $('#image_display').attr('src', 'assets/img/failed.png');
               // Update the value of the 'message' element
                $('#split_error').text(response.msg);
              // Show the modal with the id 'approve_error'
              $('#approve_error').modal('show');
            }

	  else {
              console.error('Error:', response.response_msg);
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText); // Log the entire response
          }
      });
    }

    // Rejected status update
    function change_status(compose_message_id,user_id, approve_status, indicatori) {
      var send_code = "&compose_message_id=" + compose_message_id + "&approve_status=" + approve_statuss + "&selected_userid=" + user_id + "&reason=" + reason;
      $.ajax({
        type: 'post',
        url: "ajax/message_call_functions.php?tmpl_call_function=approve_rcsno" + send_code,
        dataType: 'json',
        success: function (response) { // Success
          if (response.status == 1) { // Success Response
            $('#id_approved_lineno_' + indicatori).html('<a href="javascript:void(0)" class="btn disabled btn-outline-danger">Rejected</a>'); 
            setTimeout(function () {
                window.location = 'compose_message_approval';
                      }, 2000); 
          }
        },
        error: function (response, status, error) { // Error 
	}
      });
    }


   function clsAlphaNoOnly(e) { // Accept only alpha numerics, no special characters
      var key = e.keyCode;
      if ((key >= 65 && key <= 90) || (key >= 97 && key <= 122) || (key >= 48 && key <= 57) || (key == 32) || (key == 95)) {
        return true;
      }
      return false;
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
