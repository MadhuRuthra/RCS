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
  <title>Approve Template ::
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
            <h1>Approve Template</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item">Approve Template</div>
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
          <form class="needs-validation" novalidate="" id="frm_sender_id" name="frm_sender_id" action="#" method="post"
            enctype="multipart/form-data">

            <div class="form-group mb-2 row">
              <label class="col-sm-3 col-form-label">Reason <label style="color:#FF0000">*</label></label>
              <div class="col-sm-9">
                <input class="form-control form-control-primary" type="text" name="reject_reason" id="reject_reason"
                  maxlength="50" title="Reason to Reject" tabindex="12" placeholder="Reason to Reject" onkeydown="return /[a-z, ]/i.test(event.key)">
              </div>
            </div>
          </form>
          <p>Are you sure you want to reject ?</p>
        </div>
        <div class="modal-footer">
          <span class="error_display" id='id_error_reject'></span>
          <button type="button" class="btn btn-danger">Reject</button>
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
      find_approve_template();
    });
    // start function document
    $(function () {
      $('.theme-loader').fadeOut("slow");
      // init();
    });

    // To list the Whatsapp No from API
    function find_approve_template() {
      $.ajax({
        type: 'post',
        url: "ajax/display_functions.php?call_function=approve_template",
        dataType: 'html',
        success: function (response) {
          $("#id_approve_template").html(response);
        },
        error: function (response, status, error) { }
      });
    }
    setInterval(find_approve_template, 60000); // Every 1 min (60000), it will call

  








// Popup function to handle approval
function getData(event, type, indicatori) {
    //console.log(tempId, mediaId);

    // Update the input fields with the provided values
    if (type=="temp_id") {
        document.getElementById('temp_id' + indicatori).value = event.target.value.trim();
    }
    if (type=="media_id") {
        document.getElementById('media_bx' + indicatori).value = event.target.value.trim();
    }
}



function showError(indicatori, message) {
    var errorDiv = document.getElementById('approve_error_' + indicatori);
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block'; // Show the error message
    }
}

function hideError(indicatori) {
    var errorDiv = document.getElementById('approve_error_' + indicatori);
    if (errorDiv) {
        errorDiv.style.display = 'none'; // Hide the error message
    }
}

function approve_popup(unique_template_id, template_status, indicatori, indicator) {
    unique_templateid = unique_template_id;
    templatestatus = template_status;
    table_id = indicatori;

    var tempIdInput = document.getElementById('temp_id' + indicator);
    var mediaBoxInput = document.getElementById('media_bx' + indicator);

    console.log("tstin.........")
    console.log(tempIdInput.value.trim())
    var error = "";
    if ((!tempIdInput.value.trim() || tempIdInput.value.trim() == "") && (mediaBoxInput.value.trim() == "" || !mediaBoxInput.value.trim())) {
        error = "Please enter template ID and media ID";
        console.log("Both Template ID and Media ID are empty");
    } else if (tempIdInput.value.trim() == "") {
        error = "Please enter template ID";
        console.log("Template ID is empty");
    } else if (mediaBoxInput.value.trim() == "") {
        error = "Please enter media ID";
        console.log("Media ID is empty");
    }

    var errorDiv = document.getElementById('approve_error_' + indicatori);
    if (error != "") {
        errorDiv.textContent = error; // Set text content
        errorDiv.style.display = 'block';
        return;
    } else {
        errorDiv.style.display = 'none';
    }

    $('#approve-Modal').modal('show');

    $('#approve-Modal').find('.btn-success').off('click').on('click', function () {
        $('#approve-Modal').modal('hide');
        func_save_phbabt(unique_templateid, templatestatus, table_id, tempIdInput.value.trim(), mediaBoxInput.value.trim());
    });
}



// To save the Phone no id, business account id, bearer token
function func_save_phbabt(unique_template_id, template_status, indicatori, tempId, mediaBox) {
    var send_code = {
        unique_template_id: unique_template_id,
        template_status: template_status,
        templateid: tempId,
        media_url: mediaBox
    };
    console.log("********")
    console.log(send_code);

    $.ajax({
        type: 'post',
        url: "ajax/message_call_functions.php?tmpl_call_function=approve_template" ,
        data: send_code,
        dataType: 'json',
        beforeSend: function () {
            $('.theme-loader').show();
        },
        complete: function () {
            $('.theme-loader').hide();
        },
        success: function (response) {
        // alert(response);
            if (response.status === 0) {
                $('#id_approved_lineno_' + indicatori).html('<a href="javascript:void(0)" class="btn disabled btn-outline-success">' + response.msg + '</a>');
            } else {
                $('#id_approved_lineno_' + indicatori).html('<a href="javascript:void(0)" class="btn disabled btn-outline-success">Success</a>');
                setTimeout(function () {
                    window.location = 'approve_template';
                }, 3000);
            }
        },
        error: function (response, status, error) {
            console.error('Error:', status, error);
        }
    });
}



    var unique_templateid, approvestatus, table_id;
    //popup function
    function change_status_popup(unique_template_id, approve_status, indicatori) {
      unique_templateid = unique_template_id, approvestatus = approve_status, table_id = indicatori
      $('#reject-Modal').modal({ show: true });
    }

    $('#reject-Modal').on('hidden.bs.modal', function (e) {
      $("#id_error_reject").html("");
      $('#reject_reason').val('');
    });


    // Call remove_senderid function with the provided parameters
    $('#reject-Modal').find('.btn-danger').on('click', function () {
      var reason = $('#reject_reason').val();
      console.log(reason);
      if (reason == "") {
        $('#reject-Modal').modal({ show: true });
        $("#id_error_reject").html("Please enter reason to reject");
      }else if (reason.length < 4 || reason.length > 50) {
        $('#reject-Modal').modal({ show: true });
        $("#id_error_reject").html("Reason to reject must be between 4 and 50 characters.");
    } 
      else {
        $('#reject-Modal').modal({ show: false });
        var send_code = "&unique_template_id=" + unique_templateid + "&template_status=" + approvestatus + "&reject_reason=" + reason;
        $.ajax({
          type: 'post',
          url: "ajax/message_call_functions.php?tmpl_call_function=approve_template" + send_code,
          dataType: 'json',
          success: function (response) { // Success
            if (response.status == 1) { // Success Response
              $('#reject-Modal').modal({ show: close });
              $('#id_approved_lineno_' + table_id).html('<a href="javascript:void(0)" class="btn disabled btn-outline-danger">Rejected</a>');
              window.location = 'approve_template';
              // setTimeout(function () {
              //   window.location = 'approve_template';
              //   alert('reloading');
              // }, 2000);
            }
          },
          error: function (response, status, error) { // Error 
          }
        });
      }
  

    });


    
function call_getsingletemplate(template_message, template_category) {
  $("#slt_whatsapp_template_single").html("");  // Clear previous content

  // Initialize response message
  let response_msg = "";
  template_category = template_category.toUpperCase();
  template_message = template_message.replace(/\n/g, '\\n');

  try {
    console.log(template_category)

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
              response_msg = "Category is not recognized.......";
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
