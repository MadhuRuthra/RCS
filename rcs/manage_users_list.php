<?php
/*
Primary Admin user only allow to view this users list page.
This page is used to view the list of Users and its Status.
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

// If logged in users is not primary admin, then it will redirect to Dashboard page
if ($_SESSION['yjwatsp_user_master_id'] != 1) { ?>
  <script>window.location = "dashboard";</script>
  <? exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Manage Users List Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Manage Users List ::
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
            <h1>Manage Users List</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="manage_users">Add Users</a></div>
              <div class="breadcrumb-item">Manage Users List</div>
            </div>
          </div>

          <!-- Button Panel -->
          <div class="row">
            <div class="col-12">
              <h4 class="text-right"><a href="manage_users" class="btn btn-primary"><i class="fas fa-plus"></i> Add
                  Users</a></h4>
            </div>
          </div>

          <!-- List Panel -->
          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive" id="id_manage_users_list">
                      Loading..
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>
      </div>


	      <!-- Confirmation details content Reject-->
   <div class="modal" tabindex="-1" role="dialog" id="delete-Modal">
  <div class="modal-dialog" role="document" >
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Confirmation details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the user ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>



         <!-- Confirmation details content Reject-->
         <div class="modal" tabindex="-1" role="dialog" id="activate-Modal">
  <div class="modal-dialog" role="document" >
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Confirmation details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to activate the user ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Activate</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
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
  <script src="assets/js/page/modules-datatables.js"></script>

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

	 //delete the user
    function delete_user(user_id, indicatori)
    {
      console.log(user_id);
      $('#delete-Modal').modal({ show: true });

      // Call remove_senderid function with the provided parameters
      $('#delete-Modal').find('.btn-danger').on('click', function() {
          $('#delete-Modal').modal({ show: false });
          func_save_phbabt(user_id, indicatori);
      });
    }

    // To save the Phone no id, business account id, bearer token
   function func_save_phbabt(user_id, indicatori) 
   {
    console.log("!!!!!");
    console.log(user_id);

      var send_code = "&user_id=" + user_id;
      $.ajax({
          type: 'post',
          url: "ajax/message_call_functions.php?delete_user_functions=delete_users" + send_code,
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
    
	          if (response.msg == "Success")
            {

                setTimeout(function () {
                window.location = 'manage_users_list';
                }, 1000);

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


    //Activate the user
    function activate_user(user_id, indicatori)
    {
      console.log(user_id);
      $('#activate-Modal').modal({ show: true });

      // Call remove_senderid function with the provided parameters
      $('#activate-Modal').find('.btn-danger').on('click', function() {
          $('#activate-Modal').modal({ show: false });
          func_save_phbabt_activate(user_id, indicatori);
      });
    }

    // To save the Phone no id, business account id, bearer token
   function func_save_phbabt_activate(user_id, indicatori) 
   {
    console.log("!!!!!");
    console.log(user_id);

      var send_code = "&user_id=" + user_id;
      $.ajax({
          type: 'post',
          url: "ajax/message_call_functions.php?activate_user_functions=activate_users" + send_code,
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
    
	          if (response.msg == "Success")
            {

                setTimeout(function () {
                window.location = 'manage_users_list';
                }, 1000);

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


    //edit the user details
    function edit_user(user_id, indicatori)
    {
      console.log(user_id);
      // Call remove_senderid function with the provided parameters
      edit_user_details(user_id);
    }

    function edit_user_details(user_id) 
    {
      $.ajax({
        type: 'post',
        url: "ajax/display_functions.php?call_function=edit_users",
        dataType: 'html',
        success: function (response) {
          $("#id_manage_users_list").html(response);
        },
        error: function (response, status, error) { }
      });
    }


    // On loading the page, this function will call
    $(document).ready(function () {
      find_manage_users_list();
	//setInterval(find_manage_users_list, 10000); // Every 5 mins (300000), it will call
    });

    // To list the Users from API
    function find_manage_users_list() {
      $.ajax({
        type: 'post',
        url: "ajax/display_functions.php?call_function=manage_users_list",
        dataType: 'html',
        success: function (response) {
          $("#id_manage_users_list").html(response);
        },
        error: function (response, status, error) { }
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
