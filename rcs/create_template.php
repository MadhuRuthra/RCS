<?php
/*
Authendicated users only allow to view this Create Template page.
This page is used to Create new Templates.
It will send the form to API service and check with the Whatsapp Facebook
and get the response from them and store into our DB.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 03-Jul-2023
*/

session_start(); // start session
error_reporting(0); // The error reporting function

include_once "api/configuration.php"; // Include configuration.php
extract($_REQUEST); // Extract the request

// If the Session is not available redirect to index page
if ($_SESSION["yjwatsp_user_id"] == "") {
  ?>
  <script>window.location = "index";</script>
  <?php
  exit();
}

$site_page_name = pathinfo($_SERVER["PHP_SELF"], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Create Template Page : User : " . $_SESSION["yjwatsp_user_name"] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Create Template :: <?= $site_title ?></title>

  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- CSS Libraries -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <!-- Multi Option was selected -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
  <link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet" />

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/components.css">




  <!-- style include in css -->
  <style>
    /* textarea {
      resize: none;
    } */
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

    /* .custom-width { */
    /* width: auto; */
    /* Set the desired width */
    /* } */
    #suggestion_container .row {
      display: flex;
      flex-wrap: wrap;
    }

    #suggestion_container .col-md-6 {
      display: flex;
      flex-direction: column;
    }

    #suggestion_container .card {
      flex: 1;
      /* Makes the card take all available height */
      min-height: 250px;
      /* Set a minimum height if necessary */
      display: flex;
      flex-direction: column;
      /* justify-content: space-between;  */
      /* Ensure the remove button stays at the bottom */
    }

    #suggestion_container .card .suggestion_fields {
      flex: 1;
      /* Let the fields section grow as needed */
    }

    #suggestion_container .card button {
      align-self: flex-end;
      /* Ensure the button aligns at the bottom */
    }

   .carousel_suggestion .row {
      display: flex;
      flex-wrap: wrap;
    }

    .carousel_suggestion .col-md-6 {
      display: flex;
      flex-direction: column;
    }

    .carousel_suggestion .card {
      flex: 1;
      min-height: 250px;
      display: flex;
      flex-direction: column;
    }

    .carousel_suggestion .card .suggestion_fields {
      flex: 1;
    }

    .carousel_suggestion .card button {
      align-self: flex-end;
    }
  </style>
</head>

<body>
  <div class="theme-loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <!-- include header function adding -->
      <?
      include("libraries/site_header.php");
      ?>

      <!-- include sitemenu function adding -->
      <?
      include("libraries/site_menu.php");
      ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <!-- Title and Breadcrumbs -->
          <div class="section-header">
            <h1>Create Template</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="template_list">Template List</a></div>
              <div class="breadcrumb-item">Create Template</div>
            </div>
          </div>

          <!-- Create Template Form -->
          <div class="section-body">
            <div class="row">

              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <form class="needs-validation" novalidate="" id="frm_compose_whatsapp" name="frm_compose_whatsapp"
                    action="#" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                      <div class="form-group mb-2 row">
                        <!-- Template label -->
                        <label class="col-sm-3 col-form-label">Template label <label style="color:#FF0000">*</label>
                          <span data-toggle="tooltip" data-original-title="Enter template label.">[?]</span></label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" name="template_label" required maxlength="160"
                            tabindex="11" placeholder="Enter template label" id="template_label"
                            onkeydown="return /[a-z, ]/i.test(event.key)">
                        </div>
                      </div>

                      <!-- Campaign type  -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Campaign type <span data-toggle="tooltip"
                            data-original-title="Select campaign type">
                            <label style="color:#FF0000"> * </label> [?]</span><span
                            style="margin-left:10px;"></span></label>
                        <div class="col-sm-7">
                          <select id="select_id_1" name="campaign_type" class="form-control" tabindex="3">
                            <option value="None" type="radio"> Select campaign type </option>
                            <option value="transaction"> Transaction </option>
                            <option value="promotion"> Promotion </option>
                          </select>
                        </div>
                      </div>
                      <!-- Communication type  -->
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3 col-form-label">Communication type <span data-toggle="tooltip"
                            data-original-title="Select communication type">
                            <label style="color:#FF0000"> * </label> [?]</span><span
                            style="margin-left:10px;"></span></label>
                        <div class="col-sm-7">
                          <select id="select_id" name="header" class="form-control" tabindex="3"
                            onchange="getSelectedValue()">
                            <option value="None" type="radio"> Select communication type </option>
                            <option value="TEXT"> Text </option>
                            <option value="RICH TEXT"> Rich Text </option>
                            <option value="RICH CARD"> Rich Card </option>
                            <option value="CAROUSEL"> Carousel </option>
                          </select>
                          <div id="carousel_card_add" class="container carousel_card_add" style="display:none;">
                            <button type="button" class="btn btn-success" style="margin-top: 10px;"
                              onclick="addcarouselCards(this)" id="addcarouselCard_1">
                              <i class="fa fa-plus-circle"></i> cards 1
                            </button>
                            <button type="button" class="btn btn-success" style="margin-top: 10px;"
                              onclick="addcarouselCards(this)" id="addcarouselCard_2">
                              <i class="fa fa-plus-circle"></i> cards 2
                            </button>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <button type="button" class="btn btn-success add_cards carousel_card_add"
                            style="margin-top: 10px;display:none;" onclick="addcarouselCards(this)">
                            <i class="fa fa-plus-circle"></i> Add cards
                          </button>
                        </div>
                      </div>
                      <!-- Carousel cards will be dynamically added here  container-->
                      <div id="textFieldsCarousel" class="carousel slide carousel_card_container"
                        data-bs-ride="carousel" style="display:none;">
                        <div class="carousel-inner">
                          <!-- Carousel cards will be dynamically added here -->
                        </div>
                      </div>


                      <div class="container_rich" id="containerRich" style="display:none;">
                        <!-- Card title -->
                        <div class="form-group mb-2 row">
                          <label class="col-sm-3 col-form-label">Card title<label style="color:#FF0000">*</label> <span
                              data-toggle="tooltip" data-original-title="Enter the Card title.">[?]</span></label>
                          <div class="col-sm-7">
                            <input type="text" class="form-control card_title" name="card_title" id="card_title"
                              maxlength="200" tabindex="11" placeholder="Enter Card title"
                              onkeydown="return /[a-z, ]/i.test(event.key)">
                            <div class="row" style="right: 0px;">
                              <div class="col-sm" style="margin-top: 5px;"> <span id="current_card_value">0</span><span
                                  id="maximum_card">/ 200</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Select media  -->
                        <div class="form-group mb-2 row">
                          <label class="col-sm-3 col-form-label">Select media <span data-toggle="tooltip"
                              data-original-title="Select media">
                              <label style="color:#FF0000"> * </label> [?]</span><span
                              style="margin-left:10px;"></span></label>
                          <div class="col-sm-5">
                            <select id="rich_card_media_type" name="rich_card_media_type" onclick="slt_media();"
                              class="form-control rich_card_media_type" tabindex="3">
                              <option value="None" type="radio">Select media type </option>
                              <option value="Image"> Image </option>
                              <option value="Video"> Video </option>
                            </select>
                          </div>
                          <div class="col-sm-3">
                            <input type="file" class="rich_card_media" name="rich_card_media" id="rich_card_media"
                              tabindex="10" style="display:none;" onchange="validateMediaFile()" />
                          </div>
                        </div>

                        <!-- Image height -->
                        <div class="form-group mb-2 row">
                          <label class="col-sm-3 col-form-label">Media height <span data-toggle="tooltip"
                              data-original-title="Image height">
                              <label style="color:#FF0000"> * </label> [?]</span><span
                              style="margin-left:10px;"></span></label>
                          <div class="col-sm-7">
                            <select id="media_height" name="media_height" class="form-control media_height"
                              tabindex="3">
                              <option value="None" type="radio"> Select height </option>
                              <option value="short"> Short </option>
                              <option value="medium"> Medium </option>
                            </select>
                          </div>
                        </div>

                        <div class="rich_card" style="display:none;">
                          <!-- Card Orientation -->
                          <div class="form-group mb-2 row">
                            <label class="col-sm-3 col-form-label">Card orientation <span data-toggle="tooltip"
                                data-original-title="Card Orientation">
                                <label style="color:#FF0000"> * </label> [?]</span><span
                                style="margin-left:10px;"></span></label>
                            <div class="col-sm-7">
                              <select id="orientation" name="orientation" class="form-control" tabindex="3">
                                <option value="None" type="radio"> Select Card Orientation </option>
                                <option value="horizontal"> Horizontal </option>
                                <option value="vertical"> Vertical </option>
                              </select>
                            </div>
                          </div>
                          <!-- card allignment  -->
                          <div class="form-group mb-2 row">
                            <label class="col-sm-3 col-form-label"> Card allignment <span data-toggle="tooltip"
                                data-original-title="Card allignment">
                                <label style="color:#FF0000"> * </label> [?]</span><span
                                style="margin-left:10px;"></span></label>
                            <div class="col-sm-7">
                              <select id="card_allignment" name="card_allignment" class="form-control" tabindex="3">
                                <option value="None" type="radio"> Select Card Allignment</option>
                                <option value="top"> Top </option>
                                <option value="bottom">Bottom </option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <!-- </div> -->
                      </div>
                      <!-- Body Content -->
                      <div class="form-group mb-2 row content" style="display:none;">
                        <label class="col-sm-3 col-form-label">Body <label style="color:#FF0000">*</label> <span
                            data-toggle="tooltip"
                            data-original-title="Enter the text for your message.">[?]</span></label>
                        <div class="col-sm-7">
                          <div class="row">
                            <div class="col-8">
                              <!-- TEXT area alert -->
                              <textarea id="textarea" class="delete form-control" name="msg_content" required
                                maxlength="160" tabindex="11" placeholder="Enter Body Content" rows="6"
                                style="height: 150px !important;"></textarea>
                              <div class="row" style="right: 0px;">
                                <div class="col-sm" style="margin-top: 5px;"> <span
                                    id="current_text_value">0</span><span id="maximum">/ 160</span>
                                </div>
                                <div class="col-sm" style=" margin-top: 5px;">​<a href='#!' name="btn" type="button"
                                    id="btn" tabindex="12" class="btn btn-success"> + Add variable</a></div>
                              </div>

                              <!-- TEXT area alert End -->
                            </div>
                            <div class="col container1" id="add_suggestion" style="display:none;">
                              <button type="button" class="btn btn-success" id="add_suggestion_button"
                                style="margin-top: 50px;" onclick="addSuggestion()">
                                <i class="fa fa-plus-circle"></i> Add suggestion
                              </button>

                            </div>

                          </div>
                        </div>


                      </div>
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3"></label>
                        <div class=" col-sm-7 content" style="border-color:red;display:none;">
                          <div class="row"><span>
                              <ul style="list-style-type: disc;">
                                <div>
                                  <ul style="list-style-type: disc;">
                                    <li style="width: 800px;">Dynamic variables should not exceed 10 in number.</li>
                                    <li style="width: 800px;">Dynamic variables should not exceed 10 characters in
                                      length.</li>
                                    <li style="width: 800px;">You can add variables using the "Add Variable" option or
                                      by typing square brackets (e.g., []).</li>
                                  </ul>
                                </div>
                              </ul>
                            </span></div>
                        </div>
                      </div>


                    </div>
                    <div id="suggestion_container" name="suggestion_box" class="container mt-4">
                      <div class="row">
                        <!-- Suggestion cards will be added here dynamically -->
                      </div>
                    </div>



                    <div id="file-warning" style="color: red; display: none;">
                      Please upload a file before submitting.
                    </div>

                    <div class="error_display" id='id_error_display_submit'></div>
                    <div class="card-footer text-center">
                      <input type="hidden" class="form-control" name='suggestion_count' id='suggestion_count'
                        value='' />
                      <input type="hidden" class="form-control" name='tmp_qty_count' id='tmp_qty_count' value='1' />
                      <input type="hidden" class="form-control" name='temp_call_function' id='temp_call_function'
                        value='create_template' />
                      <input type="hidden" class="form-control" name='hid_sendurl' id='hid_sendurl'
                        value='<?= $server_http_referer ?>' />
                      <input type="hidden" class="form-control" name='carousel_count' id='carousel_count' value='' />
                      <input type="button" onclick="myFunction_clear()" value="Clear" class="btn btn-success"
                        id="clr_button">
                      <input type="submit" onclick="checkEmpty()" name="submit" id="submit" tabindex="26"
                        value=" Save & Submit" class="btn btn-success">
                      <!-- <input type="button" value="Preview Content" onclick="preview_content()" data-toggle="modal"
                        data-target="#previewModal" class="btn btn-success" id="pre_button" name="pre_button"> -->
                    </div>

                  </form>

        </section>
      </div>

      <!-- Modal content-->
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
      <!-- Preview Data Modal content End-->

      <!-- include site footer -->
      <?
      include("libraries/site_footer.php");
      ?>

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

  <script src="assets/js/xlsx.core.min.js"></script>
  <script src="assets/js/xls.core.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <script>
  let i = 1;
    let suggestionCount = 0; // Track the number of suggestions
    let maxSuggestions = 11; // Maximum number of suggestions allowed
    let carouselCount = 3; // Track the number of suggestions
    let maxcarousel = 10; // Maximum number of suggestions allowed
    let selectedValue;

    function slt_media() {
      // Get the select element
      var selectElement = document.getElementById('rich_card_media_type');
      // Get the selected value
      var media_value = selectElement.options[selectElement.selectedIndex].value;
      $('#rich_card_media').val('');

      if (media_value == 'Image') {
        $('#rich_card_media').prop('accept', 'image/png, image/gif, image/jpeg, image/jpg');
        $('#rich_card_media').css("display", "block");
        $('#rich_card_media').attr('required', 'required');
      } else if (media_value == 'Video') {
        $('#rich_card_media').prop('accept', 'video/h263,video/m4v,video/mp4,video/mpeg,video/mpeg4,video/webm');
        $('#rich_card_media').css("display", "block");
        $('#rich_card_media').attr('required', 'required');
      } else {
        $('#rich_card_media').css("display", "none");
        $('#rich_card_media').prop('accept', '');
        $('#rich_card_media').removeAttr('required');
        $('#rich_card_media').val('');
      }
    }



    function validateMediaFile() {
      const fileInput = document.getElementById('rich_card_media');
      const file = fileInput.files[0];
      const mediaType = $('#rich_card_media_type').val();

      if (!file) return true; // No file selected, no need to validate

      const { type: fileType, size: fileSize } = file;
      const MAX_SIZES = { Image: 1 * 1024 * 1024, Video: 5 * 1024 * 1024 }; // 1MB for images, 5MB for videos

      const VALID_TYPES = {
        Image: /image\/(png|gif|jpeg|jpg)/,
        Video: /video\/(mp4|mpeg|mpeg4|webm|h263|m4v)/
      };

      if (!VALID_TYPES[mediaType]?.test(fileType) || fileSize > MAX_SIZES[mediaType]) {
        alert(mediaType === 'Image'
          ? "Invalid image file. Must be PNG, GIF, JPEG, or JPG and less than  1 MB."
          : "Invalid video file. Must be MP4, MPEG, etc. and less than 5 MB.");
          fileInput.value = ''; // Clear the file input
        return false;
      }

      return true;
    }

    function carousel_media_type_func(selectElement) {
      // Get the corresponding file input element
      const index = Array.from(document.querySelectorAll('.carousel_media_type')).indexOf(selectElement);
      const fileInput = document.querySelectorAll('.carousel_media')[index];
      // Show or hide file input based on the selected media type
      if (selectElement.value === 'None') {
        fileInput.style.display = 'none';
        fileInput.val(''); // Clear the file input
      } else {
        fileInput.style.display = '';
        fileInput.value = ''; 
      }
      if (selectElement.value === 'Image') {
            fileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/jpg');
        } else if (selectElement.value === 'Video') {
            fileInput.setAttribute('accept', 'video/mp4, video/mpeg, video/webm');
        }
        fileInput.value = ''; // Clear the file input when changing media type
    }
    
    function validateMediaFiles() {
      // Get all media type selects and file inputs
      const mediaTypes = document.querySelectorAll('.carousel_media_type');
      const fileInputs = document.querySelectorAll('.carousel_media');

      const MAX_SIZES = { Image: 1 * 1024 * 1024, Video: 5 * 1024 * 1024 }; // 1MB for images, 5MB for videos
      const VALID_TYPES = {
        Image: /image\/(png|gif|jpeg|jpg)/,
        Video: /video\/(mp4|mpeg|mpeg4|webm|h263|m4v)/
      };

      for (let i = 0; i < fileInputs.length; i++) {
        const fileInput = fileInputs[i];
        const mediaType = mediaTypes[i].value;

        // Get the file from the file input
        const file = fileInput.files[0];

        if (!file) continue; // No file selected for this input

        const { type: fileType, size: fileSize } = file || {};

        // Validate file type and size
        if (!VALID_TYPES[mediaType]?.test(fileType) || fileSize > MAX_SIZES[mediaType]) {
          alert(mediaType === 'Image'
            ? "Invalid image file. Must be PNG, GIF, JPEG, or JPG and less than 1 MB."
            : "Invalid video file. Must be MP4, MPEG, etc. and less than 5 MB.");
            fileInput.value = ''; // Clear the file input
          return false;
        }
      }

      return true;
    }


    //TEXT AREA COUNT
    $("#card_title").keyup(function () {
      $("#current_card_value").text($(this).val().length);
    });

    function getSelectedValue() {
    // Get the select element
    var selectElement = document.getElementById('select_id');
    // Get the selected value
    var selectedValue = selectElement.options[selectElement.selectedIndex].value;
    var mediaInput = document.getElementById('rich_card_media');
    var mediaTypeSelect = document.getElementById('rich_card_media_type');

    // Common operations for clearing values and hiding elements
    function clearAndHide() {
        mediaInput.value = ''; // Clear the file input
        mediaInput.style.display = 'none'; // Hide the file input
        mediaTypeSelect.value = 'None'; // Reset media type select
    }

    if (selectedValue === 'TEXT') {
      i = 1;
        $('#carousel_card_add').css("display", "none");
        $("#textarea").val('');
        $("#card_title").val('');
        clearAndHide();
        const select_media_height = document.getElementById('media_height');
        select_media_height.value = 'None';
        const select_rich_orientation = document.getElementById('orientation');
        select_rich_orientation.value = 'None';
        const select_rich_card_allignment = document.getElementById('card_allignment');
        select_rich_card_allignment.value = 'None';
        const suggestionContainer = document.querySelector('#suggestion_container .row');
        suggestionContainer.innerHTML = '';
        document.getElementById('add_suggestion_button').disabled = false;
        $('.carousel').css("display", "none");
        $('.content').css("display", "");
        $('.rich_card').css("display", "none");
        $('#add_suggestion').css("display", "none");
        $('#containerRich').css("display", "none");
        $('#suggestion_container').css("display", "none");
        $("#textarea").attr("required", true);
        $(".carousel_card_title").attr("required", false);
        $(".carousel_msg_content").attr("required", false);
        $('.carousel_card_add').css("display", "none");
    } 
    else if (selectedValue === "RICH TEXT") {
      i = 1;
        $('#carousel_card_add').css("display", "none");
         $('.carousel_card_add').css("display", "none");
        $("#card_title").val('');
        clearAndHide();
          document.getElementById('add_suggestion').hidden = false;
        const select_media_height = document.getElementById('media_height');
        select_media_height.value = 'None';
        const select_rich_orientation = document.getElementById('orientation');
        select_rich_orientation.value = 'None';
        const select_rich_card_allignment = document.getElementById('card_allignment');
        select_rich_card_allignment.value = 'None';
        $("#textarea").val('');
        suggestionCount = 0; // Track the number of suggestions
        maxSuggestions = 11; // Maximum number of suggestions allowed
        const suggestionContainer = document.querySelector('#suggestion_container .row');
        suggestionContainer.innerHTML = '';
        document.getElementById('add_suggestion_button').disabled = false;
        $('#add_suggestion').css("display", "");
        $('.carousel').css("display", "none");
        $('.content').css("display", "");
        $('#suggestion_container').css("display", "");
        $('#containerRich').css("display", "none");
        $('.rich_card').css("display", "none");
        $("#textarea").attr("required", true);
        $(".carousel_card_title").attr("required", false);
        $(".carousel_msg_content").attr("required", false);
    }
    else if (selectedValue === "RICH CARD") {
      i = 1;
           document.getElementById('add_suggestion').hidden = false;
        $('#carousel_card_add').css("display", "none");
        $("#textarea").val('');
        suggestionCount = 0; // Track the number of suggestions
        maxSuggestions = 11; // Maximum number of suggestions allowed
        $("#card_title").val('');
        clearAndHide();
        const select_media_height = document.getElementById('media_height');
        select_media_height.value = 'None';
        const select_rich_orientation = document.getElementById('orientation');
        select_rich_orientation.value = 'None';
        const select_rich_card_allignment = document.getElementById('card_allignment');
        select_rich_card_allignment.value = 'None';
        const suggestionContainer = document.querySelector('#suggestion_container .row');
        suggestionContainer.innerHTML = '';
        document.getElementById('add_suggestion_button').disabled = false;
        $('#add_suggestion').css("display", "");
        $('.carousel').css("display", "none");
        $('.content').css("display", "");
        $('.rich_card').css("display", "");
        $('#containerRich').css("display", "");
        $('#suggestion_container').css("display", "");
        maxSuggestions = 4; // Set maxSuggestions for CARD
        $('#maximum').html("/ 2000");
        $('#msg_content').attr('maxlength', '2000');
        $("#textarea").attr("required", true);
        $(".carousel_card_title").attr("required", false);
        $(".carousel_msg_content").attr("required", false);
        $('.carousel_card_add').css("display", "none");
    }
    else if (selectedValue === "CAROUSEL") { 
           document.getElementById('add_suggestion').hidden = false;
      i = 1;
        $("#textarea").val('');
        $("#card_title").val('');
        $('#addcarouselCard_3, #addcarouselCard_4,#addcarouselCard_5,#addcarouselCard_6,#addcarouselCard_7,#addcarouselCard_8,#addcarouselCard_9,#addcarouselCard_10').remove();
        clearAndHide();
        carouselCount = 3; // Track the number of suggestions
        maxcarousel = 10; // Maximum number of suggestions allowed
        const carouselInner = document.querySelector('#textFieldsCarousel .carousel-inner');
        carouselInner.innerHTML = '';
        $('.carousel_card_add').css("display", "");
         $('#carousel_card_add').css("display", "");
        $('.carousel').css("display", "");
        $('.rich_card').css("display", "none");
        $('.content').css("display", "none");
        $('#containerRich').css("display", "none");
        $('#suggestion_container').css("display", "none");
        maxSuggestions = 4; // Set maxSuggestions for CARD
        $("#textarea").attr("required", false);
    }
    else {
        $('#carousel_card_add').css("display", "none");
        $('.carousel_card_add').css("display", "none");
        $("#textarea").val('');
        $("#card_title").val('');
        clearAndHide();
        suggestionCount = 0; // Track the number of suggestions
        maxSuggestions = 11; // Maximum number of suggestions allowed
        carouselCount = 3; // Track the number of suggestions
        maxcarousel = 10; // Maximum number of suggestions allowed
        const suggestionContainer = document.querySelector('#suggestion_container .row');
        suggestionContainer.innerHTML = '';
        const carouselInner = document.querySelector('#textFieldsCarousel .carousel-inner');
        carouselInner.innerHTML = '';
        document.getElementById('add_suggestion_button').disabled = false;
        $(".carousel_card_title").attr("required", false);
        $(".carousel_msg_content").attr("required", false);
        $('.rich_card').css("display", "none");
        $('.carousel').css("display", "none");
        $('#maximum').html("/ 160");
        $('#msg_content').attr('maxlength', '160');
        $('.content').css("display", "none");
        $('#containerRich').css("display", "none");
        $('#add_suggestion').css("display", "none");
        $('#suggestion_container').css("display", "");
    }
}

  

  var flag_valid = true;
    function addcarouselCards(button) {
      var buttonId = button.id;

      const remainingCards = document.querySelectorAll('.carousel-item');
      if (buttonId == '' && flag_valid) {
        $('#textFieldsCarousel').css("display", "block");
        flag_valid = false;
        carouselCount = 3;
        // Create a new button element with jQuery
        const newButton = $(
          `<button type="button" class="btn btn-success" style="margin-top: 10px;" 
        onclick="addcarouselCards(this)" id="addcarouselCard_${carouselCount}">
        <i class="fa fa-plus-circle"></i> ${carouselCount} Cards
      </button>`
        );
        // Append the new button to the container
        $('#carousel_card_add').append(newButton);
      } else if (buttonId != '') {
        const splitParts = buttonId.split('_');
        $('#textFieldsCarousel').css("display", "block");
        carouselCount = splitParts[1] - 1;
      } else if (carouselCount == 11) {
        $('.add_cards').css("display", "none");
      } else if (carouselCount >= 2 && carouselCount <= 10) {
        var existingCards = $('#carousel_card_add button').length;
        // Log the count to the console
        console.log(existingCards + "existingCards");
        // Check if the number of cards is less than the maximum allowed
        if (existingCards >= 10) {
          $('.add_cards').css("display", "none");
          return; // Stop the function if the limit is reached
        } else {
          $('.add_cards').css("display", "");
        }
        const element = document.querySelector(`#addcarouselCard_${carouselCount}`);
        if (!element) {
          // Create a new button element with jQuery
          const newButton = $(
            `<button type="button" class="btn btn-success" style="margin-top: 10px;" 
        onclick="addcarouselCards(this)" id="addcarouselCard_${carouselCount}">
        <i class="fa fa-plus-circle"></i> ${carouselCount} Cards
      </button>`
          );
          // Append the new button to the container
          $('#carousel_card_add').append(newButton);
        } else {
          carouselCount = checkMissingIds();
          // Create a new button element with jQuery using the updated carouselCount
          const newButton = $(
            `<button type="button" class="btn btn-success" style="margin-top: 10px;" 
        onclick="addcarouselCards(this)" id="addcarouselCard_${carouselCount}">
        <i class="fa fa-plus-circle"></i> ${carouselCount} Cards
      </button>`
          );
          // Append the new button to the container
          $('#carousel_card_add').append(newButton);
        }

        function checkMissingIds() {
          const allIds = [1, 2, 3, 4, 5, 6, 7, 8, 10];
          const existingIds = allIds.filter(id => $(`#addcarouselCard_${id}`).length > 0);
          const missingIds = allIds.filter(id => !existingIds.includes(id));
          console.log('Missing IDs:', missingIds);
          // Return the first missing ID or a default value
          return missingIds.length > 0 ? missingIds[0] : Math.max(...allIds) + 1;
        }

      }

      $('.carousel-item').removeClass('active');
      // Select all buttons with the specific ID pattern or class
      //  const buttonCount = $('[id^="addcarouselCard_"]').length;
      if (carouselCount < maxcarousel) {
        carouselCount++; // Increment the carousel count
        const cardId = `carousel_card_${carouselCount}`;
        const carouselInner = document.querySelector('.carousel-inner');
        $('#carousel_count').val(carouselCount); // Update hidden field with the current count
        // Check if there is an existing active card
        const activeCard = document.querySelector('.carousel-item.active');
        const cardDiv = document.createElement('div');
        cardDiv.className = `carousel-item ${carouselCount != 0 ? 'active' : ''}`; // Set active class only if no card is active and it's the first card
        cardDiv.id = cardId;
        $(`#carousel_card_${carouselCount - 1}`).removeClass("active");
        // Check if an element with the given cardId exists inside 'carousel-inner'
        const cardExists = carouselInner.querySelector(`#${cardId}`) !== null;
        if (cardExists) {
          // Add 'active' class to the current card
          $(`#carousel_card_${carouselCount}`).addClass('active');
          cardDiv.className = `carousel-item ${carouselCount != 0 ? 'active' : ''}`; // Set active class only if no card is active and it's the first card
        } else {
          cardDiv.innerHTML = `<div class="form-group mb-2 row">
                <label class="col-sm-3 col-form-label">Card title <label style="color:#FF0000">*</label></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control carousel_card_title" name="carousel_card_title[]" id="carousel_card_title_${carouselCount}" required
                    maxlength="200" placeholder="Enter Card title">
                  <div class="row" style="right: 0px;">
                    <div class="col-sm" style="margin-top: 5px;"> 
                      <span id="current_card_value_${carouselCount}">0</span><span>/ 200</span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Select media  -->
              <div class="form-group mb-2 row">
                <label class="col-sm-3 col-form-label">Select media <label style="color:#FF0000"> *</label></label>
                <div class="col-sm-7">
                  <select class="form-control carousel_media_type" name="carousel_media_type[]" id="carousel_media_type_${carouselCount}" onclick="carousel_media_type_func(this)" >
                  <option value="None" type="radio">Select media type </option>
                    <option value="Image">Image</option>
                    <option value="Video">Video</option>
                  </select>
                </div>
                <div class="col-sm-2">
                  <input type="file" class="carousel_media" name="carousel_media[]" id="carousel_media_${carouselCount}" onchange="validateMediaFiles(this)" style="display:none;"/>
                </div>
              </div>
               <!-- Image height -->
                        <div class="form-group mb-2 row">
                          <label class="col-sm-3 col-form-label">Image height <span data-toggle="tooltip"
                              data-original-title="Add a title or choose which type of media you'll use for this header">
                              <label style="color:#FF0000"> * </label> [?]</span><span
                              style="margin-left:10px;"></span></label>
                          <div class="col-sm-7">
                            <select id="carousel_media_height_${carouselCount}" name="carousel_media_height[]" class="form-control carousel_media_height"
                              tabindex="3">
                              <option value="None" type="radio"> Select image height </option>
                              <option value="short"> Short </option>
                              <option value="medium"> Medium </option>
                            </select>
                          </div>
                        </div>
                        <!-- Image width -->
                        <div class="form-group mb-2 row carousel" >
                          <label class="col-sm-3 col-form-label">Image width <span data-toggle="tooltip"
                              data-original-title="Add a title or choose which type of media you'll use for this header">
                              <label style="color:#FF0000"> * </label> [?]</span><span
                              style="margin-left:10px;"></span></label>
                          <div class="col-sm-7">
                            <select id="media_width_carousel_${carouselCount}" name="media_width_carousel[]"
                              class="form-control media_width_carousel" tabindex="3">
                              <option value="None" type="radio"> Select image width </option>
                              <option value="small"> Small </option>
                              <option value="medium"> Medium </option>
                            </select>
                          </div>
                        </div>
                                 <!-- Body Content -->
                      <div class="form-group mb-2 row content">
                        <label class="col-sm-3 col-form-label">Body <label style="color:#FF0000">*</label> <span
                            data-toggle="tooltip"
                            data-original-title="Enter the text for your message in the language that you've selected.">[?]</span></label>
                        <div class="col-sm-7">
                          <div class="row">
                            <div class="col-8">
                              <!-- TEXT area alert -->
                              <textarea id="carousel_textarea_${carouselCount}" class="carousel_msg_content form-control " name="carousel_textarea[]" required
                                maxlength="2000" tabindex="11" placeholder="Enter Body Content" rows="6"
                                style="height: 150px !important;"></textarea>
                              <div class="row" style="right: 0px;">
                                <div class="col-sm" style="margin-top: 5px;"> <span
                                    id="current_text_value_${carouselCount}">0</span><span id="maximum">/ 2000</span>
                                </div>
                              
                              </div>
                              <!-- TEXT area alert End -->
                            </div>
                            <div class="col container1" id="carousel_add_suggestion_${carouselCount}">
                              <button type="button" class="btn btn-success" style="margin-top: 50px;"
                                onclick="carouseladdSuggestion(${carouselCount})">
                                <i class="fa fa-plus-circle"></i> Add suggestion
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="form-group mb-2 row">
                        <label class="col-sm-3"></label>
                        <div class=" col-sm-7 content" style="border-color:red;display:none;">
                          <div class="row"><span>
                              <ul style="list-style-type: disc;">
                                <div>
                                  <li style="width:800px;">The body text contains variable parameters at the beginning
                                    or end. You need
                                    to either change this format or add a sample.</li>
                                  <li style="width:800px;">Variables must not empty.</li>
                                  <li style="width:800px;">This template contains too many variable parameters relative
                                    to the message
                                    length. You need to decrease the number of variable parameters or increase the
                                    message length.</li>
                                  <li style="width:800px;">Dynamic variables length should not exceed 10 characters</li>
                                  <li style="width:800px;">The body text contains variable parameters that are next to
                                    each other. You
                                    need to either change this format or add a sample.</li>
                                  <li style="width:800px;"> <a target="_blank"
                                      href="https://developers.facebook.com/docs/whatsapp/message-templates/guidelines/">Learn
                                      more about formatting in Message Template Guidelines</a></li>
                                </div>
                              </ul>
                            </span></div>
                        </div>
                      </div>
                    </div>
            </div>  <div id="carousel_suggestion_container_${carouselCount}" name="suggestion_box" class="container mt-4">
                      <div class="row carousel_suggestion">
                        <!-- Suggestion cards will be added here dynamically -->
                      </div> ${carouselCount >= 3 ? `
   <div class="text-center"> <button type="button" class="btn btn-danger mt-3" onclick="removecarouselcard(this)">Remove carousel Card</button>
  ` : ''}</div>
                    </div>`;

          carouselInner.appendChild(cardDiv);
          // Deactivate the previously active card
          const previousActiveCard = document.querySelector('.carousel-item.active');
          if (previousActiveCard) {
            previousActiveCard.classList.remove('active');
          }

          // Activate the new card
          cardDiv.classList.add('active');

          // Disable the "Add Suggestion" button if the maximum is reached
          if (carouselCount >= maxcarousel) {
            document.getElementsByClassName('carousel_card_add').hidden = true;
          }
        }
      }
      const activeId = getActiveCarouselId();
    }


    function getActiveCarouselId() {
      const activeItem = document.querySelector('.carousel-item.active'); // Find the active item
      if (activeItem) {
        return activeItem.id; // Return the ID of the active item
      }
      return null; // Return null if no active item is found
    }

    /*function removecarouselcard(button) {
      //alert('remove');
      const card = button.closest('.carousel-item');
      card.remove();
      $('#carousel_count').val(carouselCount);
      $('#addcarouselCard_' + carouselCount).css("display", "none");
      carouselCount--; // Decrement the suggestion count
      if (carouselCount < maxCarouselCount) {
        //alert('helloall');
        $('#addcarouselCard').css("display", "");
    }
    }*/
    function removecarouselcard(button) {
      const card = button.closest('.carousel-item'); // Find the closest carousel item
      const cardId = card.id; // Get the ID of the card to be removed
      $('#carousel_count').val(carouselCount);
      $('#addcarouselCard_' + (carouselCount)).remove();
      card.remove();
      carouselCount--;
      const remainingCards = document.querySelectorAll('.carousel-item');
      if (remainingCards.length > 0) {
        const lastCard = remainingCards[remainingCards.length - 1];
        lastCard.classList.add('active');
      } else {
        $('#textFieldsCarousel').css("display", "none");
      }
      if (carouselCount < 2) {
        $('.btn-danger').css("display", "none");
      }
      if (carouselCount < maxcarousel) {
        $('.carousel_card_add').css("display", "");
      }
      $('.carousel_card_container').css("display", "");
    }
    
    function carouseladdSuggestion(id_carousel) {
      var containerId = '#carousel_suggestion_container_' + id_carousel;
      // Find the container
      var $container = $(containerId);
      // Count suggestion cards within the container
      var suggestionCount = $container.find('[id^="suggestion_card_"]').length;
      maxSuggestions = '4'
      if (suggestionCount < maxSuggestions) {
        suggestionCount++; // Increment the suggestion count
        const cardId = `suggestion_card_${suggestionCount}`;
        const suggestionContainer = document.querySelector('#carousel_suggestion_container_' + id_carousel + ' .row');
        $('#suggestion_count').val(suggestionCount);
        const cardDiv = document.createElement('div');
        cardDiv.className = 'col-md-6'; // Each suggestion will take up half the row (2 per row)
        cardDiv.innerHTML = `
              <div class="card p-3 mb-3" id="${cardId}"  >
                  <div class="row">
                      <div class="col-sm-6">
                      <h5>Suggestion ${suggestionCount}</h5>
                          <label>Type of Action</label>
                          <select class="form-control action_type" name ="suggestion_option_values[]" id="suggestion_option_values" onchange="updateSuggestioncarousel(this)" required>
                              <option value="">Select suggestion</option>
                              <option value="REPLY">Reply</option>
                              <option value="DIALER_ACTION">Dialer Action</option>
                              <option value="URL_ACTION">URL Action</option>
                              <option value="VIEW_LOCATION(Lat/Lang)">View Location(Lat/Lang)</option>
                              <option value="VIEW_LOCATION(query)">View Location(query)</option>
                              <option value="SHARE_LOCATION">Share Location</option>
                              <option value="CREATE_CALENDAR">Create Calendar</option>
                          </select>
                      </div>
                  </div>
                  <div class="row mt-3">
                      <div class="col-sm-12 suggestion_fields">
                          <!-- Dynamic fields will be inserted here -->
                      </div>
                  </div>
                  <button type="button" class="btn btn-danger mt-3" onclick="removeSuggestioncarousel(this)">Remove Suggestion</button>
              </div>
          `;

        suggestionContainer.appendChild(cardDiv);

        // Disable the "Add Suggestion" button if the maximum is reached
        if (suggestionCount >= maxSuggestions) {
          document.getElementById('add_suggestion').hidden = true;
        }
      }
    }
    function updateSuggestioncarousel(selectElement) {
      const selectedAction = selectElement.value;
      const suggestionFieldsDiv = selectElement.closest('.card').querySelector('.suggestion_fields');

      let fieldHTML = '';
      switch (selectedAction) {
        case 'REPLY':
          fieldHTML = `
              <label for="text_input">Suggestion Text</label>
<input type="text" id="text_input" name="text_message[]" class="form-control" placeholder="Enter your reply" maxlength="25" 
       onkeydown="return /[a-z, ]/i.test(event.key)" required>
<div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>`; break;
        case 'DIALER_ACTION':
          fieldHTML = `
                <label>Suggestion Text</label>
                <input type="text" name="dial_sugg_text[]"class="form-control" placeholder="Call me" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
                <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
                <label class="mt-2">Mobile Number</label>
                <input type="text" class="form-control mobile-number" name="mobile_number[]" placeholder="Enter mobile number" id="mobile_number" maxlength="13"
                    oninput="validateMobileNumber(this)" onkeypress="return (event.charCode !=8 && event.charCode ==0 ||  (event.charCode >= 48 && event.charCode <= 57))" value="+91" required />
                <div id="mobile_number_error" style="color: #6c757d;">Mobile number must start with '+91' followed by 10 digits.</div>`;
          break;
        case 'URL_ACTION':
          fieldHTML = `
    <label>Suggestion Text</label>
    <input type="text" class="form-control" name="url_sugg_text[]" placeholder="Enter text" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
    <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
    <label class="mt-2">URL</label>
    <input type="text" class="form-control" name="url_link[]" placeholder="Enter URL" oninput="validateURL(this)" maxlength="2048" required>
    <div class="url_error" style="color: #6c757d;">URL must start with 'http://' or 'https:// & 2048 characters are allowed'</div>`;

          break;
        case 'VIEW_LOCATION(Lat/Lang)':
          fieldHTML = `
        <label>Suggestion Text</label>
        <input type="text" class="form-control" name="location_sugg_text[]" placeholder="Enter text" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
        <label class="mt-2">Label</label>
        <input type="text" class="form-control" name="location_url[]" placeholder="Enter text" required>

        <div class="row mt-2">
           <div class="col-sm-6">
        <label>Latitude</label>
        <input type="text" class="form-control" name="latitude[]" placeholder="Enter latitude" id="latitude" 
               oninput="validateFloatingPoint(this)" 
               onkeydown="return /[0-9.,-]/.test(event.key) || event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight'" 
               required>
        <div id="latitude_error" style="color: #6c757d; display: none;">Please enter a valid floating-point number.</div>
    </div>
    <div class="col-sm-6">
        <label>Longitude</label>
        <input type="text" class="form-control" name="longitude[]" placeholder="Enter longitude" id="longitude" 
               oninput="validateFloatingPoint(this)" 
               onkeydown="return /[0-9.,-]/.test(event.key) || event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight'" 
               required>
        <div id="longitude_error" style="color: #6c757d; display: none;">Please enter a valid floating-point number.</div>
    </div>
        </div>`;
          break;

        case 'VIEW_LOCATION(query)':
          fieldHTML = `
        <label>Suggestion Text</label>
        <input type="text" class="form-control" name="locate_sugg_text[]" placeholder="Enter text"  maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
        <label class="mt-2">Query</label>
        <input type="text" class="form-control" name="location_query[]" placeholder="Enter Query" maxlength="120"  
             onkeypress="restrictInput(event)" title="Single quotes are allowed, but double quotes are not allowed." required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 120 characters allowed]</div>`;
          break;

        case 'SHARE_LOCATION':
          fieldHTML = `
                <label>Suggestion Text</label>
                <input type="text" class="form-control" name="share_sugg_txt[]" placeholder="Enter text" onkeydown="return /[a-z, ]/i.test(event.key)" maxlength="25" required>
                <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>`;
          break;
        case 'CREATE_CALENDAR':
          fieldHTML = `
        <label>Suggestion Text</label>
        <input type="text" class="form-control" name="calender_sugg_txt[]" placeholder="Enter text"  maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
        
        <div class="row mt-2">
            <div class="col-sm-6">
                <label>From Date</label>
                <input type="date" class="form-control" name= "from_date[]" placeholder="Select from date" min="<?php echo date("Y-m-d"); ?>" required>
            </div>
            <div class="col-sm-6">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date[]" placeholder="Select to date" min="<?php echo date("Y-m-d"); ?>" required>
            </div>
        </div>
        <label class="mt-2">Event Title</label>
        <input type="text" class="form-control"  name ="event_name[]" placeholder="Enter event details" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
         <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>

        <label class="mt-2">Description</label>
        <textarea class="form-control" name="event_label[]" id="event_label" placeholder="Enter Description" maxlength="2048" style="height: 100px !important; width: 100%;" required></textarea>
        <div id="validation_message" style="color: #6c757d;">[Maximum 2048 characters allowed]</div>`;
          break;

        default:
          fieldHTML = '';
          break;
      }

      suggestionFieldsDiv.innerHTML = fieldHTML;

    }

    function removeSuggestioncarousel(button) {
      const card = button.closest('.col-md-6');
      card.remove();
      suggestionCount--; // Decrement the suggestion count
      $('#suggestion_count').val(suggestionCount);
      updateSuggestionNumberscarousel(button);
      // Re-enable the "Add Suggestion" button if suggestions are removed
      if (suggestionCount < maxSuggestions) {
        document.getElementById('add_suggestion').disabled = false;
      }
    }
    function updateSuggestionNumberscarousel(id_carousel) {
      const cards = document.querySelectorAll('#carousel_suggestion_container_' + id_carousel + ' .card');
      cards.forEach((card, index) => {
        const cardHeader = card.querySelector('.card-header h5');
        cardHeader.textContent = `Suggestion ${index + 1}`;

        const suggestionCountElement = card.querySelector('.suggestion-count');
        suggestionCountElement.textContent = `Number of Suggestions: ${index + 1}`;
      });
    }

    function addSuggestion() {
      if (suggestionCount < maxSuggestions) {
        suggestionCount++; // Increment the suggestion count
        const cardId = `suggestion_card_${suggestionCount}`;
        const suggestionContainer = document.querySelector('#suggestion_container .row');
        $('#suggestion_count').val(suggestionCount);
        const cardDiv = document.createElement('div');
        cardDiv.className = 'col-md-6'; // Each suggestion will take up half the row (2 per row)
        cardDiv.innerHTML = `
              <div class="card p-3 mb-3" id="${cardId}"  >

                  <div class="row">
                      <div class="col-sm-6">
                      <h5>Suggestion ${suggestionCount}</h5>
                   
                          <label>Type of Action</label>
                          <select class="form-control action_type" name ="suggestion_option_values[]" id="suggestion_option_values" onchange="updateSuggestionFields(this)" required>
                              <option value="">Select suggestion</option>
                              <option value="REPLY">Reply</option>
                              <option value="DIALER_ACTION">Dialer Action</option>
                              <option value="URL_ACTION">URL Action</option>
                              <option value="VIEW_LOCATION(Lat/Lang)">View Location(Lat/Lang)</option>
                              <option value="VIEW_LOCATION(query)">View Location(query)</option>
                              <option value="SHARE_LOCATION">Share Location</option>
                              <option value="CREATE_CALENDAR">Create Calendar</option>
                          </select>
                      </div>
                  </div>
                  <div class="row mt-3">
                      <div class="col-sm-12 suggestion_fields">
                          <!-- Dynamic fields will be inserted here -->
                      </div>
                  </div>
                  <button type="button" class="btn btn-danger mt-3" onclick="removeSuggestion(this)">Remove Suggestion</button>
              </div>
          `;

        suggestionContainer.appendChild(cardDiv);

        // Disable the "Add Suggestion" button if the maximum is reached
        if (suggestionCount >= maxSuggestions) {
          document.getElementById('add_suggestion_button').disabled = true;
        }
      }
    }

    function updateSuggestionFields(selectElement) {
      const selectedAction = selectElement.value;
      const suggestionFieldsDiv = selectElement.closest('.card').querySelector('.suggestion_fields');

      let fieldHTML = '';
      switch (selectedAction) {
        case 'REPLY':
          fieldHTML = `
              <label for="text_input">Suggestion Text</label>
<input type="text" id="text_input" name="text_message[]" class="form-control" placeholder="Enter your reply" maxlength="25" 
       onkeydown="return /[a-z, ]/i.test(event.key)" required>
<div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>`; break;
        case 'DIALER_ACTION':
          fieldHTML = `
                <label>Suggestion Text</label>
                <input type="text" name="dial_sugg_text[]"class="form-control" placeholder="Call me" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
                <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
                <label class="mt-2">Mobile Number</label>
                <input type="text" class="form-control mobile-number" name="mobile_number[]" placeholder="Enter mobile number" id="mobile_number" maxlength="13"
                    oninput="validateMobileNumber(this)" onkeypress="return (event.charCode !=8 && event.charCode ==0 ||  (event.charCode >= 48 && event.charCode <= 57))" value="+91" required />
                <div id="mobile_number_error" style="color: #6c757d;">Mobile number must start with '+91' followed by 10 digits.</div>`;
          break;
        case 'URL_ACTION':
          fieldHTML = `
    <label>Suggestion Text</label>
    <input type="text" class="form-control" name="url_sugg_text[]" placeholder="Enter text" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
    <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
    <label class="mt-2">URL</label>
    <input type="text" class="form-control" name="url_link[]" placeholder="Enter URL" oninput="validateURL(this)" maxlength="2048" required>
    <div class="url_error" style="color: #6c757d;">URL must start with 'http://' or 'https:// & 2048 characters are allowed'</div>`;

          break;
        case 'VIEW_LOCATION(Lat/Lang)':
          fieldHTML = `
        <label>Suggestion Text</label>
        <input type="text" class="form-control" name="location_sugg_text[]" placeholder="Enter text" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
        <label class="mt-2">Label</label>
        <input type="text" class="form-control" name="location_url[]" placeholder="Enter text" required>

        <div class="row mt-2">
           <div class="col-sm-6">
        <label>Latitude</label>
        <input type="text" class="form-control" name="latitude[]" placeholder="Enter latitude" id="latitude" 
               oninput="validateFloatingPoint(this)" 
               onkeydown="return /[0-9.,-]/.test(event.key) || event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight'" 
               required>
        <div id="latitude_error" style="color: #6c757d; display: none;">Please enter a valid floating-point number.</div>
    </div>
    <div class="col-sm-6">
        <label>Longitude</label>
        <input type="text" class="form-control" name="longitude[]" placeholder="Enter longitude" id="longitude" 
               oninput="validateFloatingPoint(this)" 
               onkeydown="return /[0-9.,-]/.test(event.key) || event.key === 'Backspace' || event.key === 'ArrowLeft' || event.key === 'ArrowRight'" 
               required>
        <div id="longitude_error" style="color: #6c757d; display: none;">Please enter a valid floating-point number.</div>
    </div>
        </div>`;
          break;

        case 'VIEW_LOCATION(query)':
          fieldHTML = `
        <label>Suggestion Text</label>
        <input type="text" class="form-control" name="locate_sugg_text[]" placeholder="Enter text"  maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
        <label class="mt-2">Query</label>
        <input type="text" class="form-control" name="location_query[]" placeholder="Enter Query" maxlength="120"  
             onkeypress="restrictInput(event)" title="Single quotes are allowed, but double quotes are not allowed." required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 120 characters allowed]</div>`;
          break;

        case 'SHARE_LOCATION':
          fieldHTML = `
                <label>Suggestion Text</label>
                <input type="text" class="form-control" name="share_sugg_txt[]" placeholder="Enter text" onkeydown="return /[a-z, ]/i.test(event.key)" maxlength="25" required>
                <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>`;
          break;
        case 'CREATE_CALENDAR':
          fieldHTML = `
        <label>Suggestion Text</label>
        <input type="text" class="form-control" name="calender_sugg_txt[]" placeholder="Enter text"  maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
        <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>
        
        <div class="row mt-2">
            <div class="col-sm-6">
                <label>From Date</label>
                <input type="date" class="form-control" name= "from_date[]" placeholder="Select from date" min="<?php echo date("Y-m-d"); ?>" required>
            </div>
            <div class="col-sm-6">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date[]" placeholder="Select to date" min="<?php echo date("Y-m-d"); ?>" required>
            </div>
        </div>
        <label class="mt-2">Event Title</label>
        <input type="text" class="form-control"  name ="event_name[]" placeholder="Enter event details" maxlength="25" onkeydown="return /[a-z, ]/i.test(event.key)" required>
         <div id="validation_message" style="color: #6c757d;">[Maximum 25 characters allowed]</div>

        <label class="mt-2">Description</label>
        <textarea class="form-control" name="event_label[]" id="event_label" placeholder="Enter Description" maxlength="2048" style="height: 100px !important; width: 100%;" required></textarea>
        <div id="validation_message" style="color: #6c757d;">[Maximum 2048 characters allowed]</div>`;
          break;

        default:
          fieldHTML = '';
          break;
      }

      suggestionFieldsDiv.innerHTML = fieldHTML;

    }

    function validateURL(input) {
      const value = input.value;
      const errorDiv = input.nextElementSibling; // Assuming the error message is next to the input

      if (value.startsWith('http://') || value.startsWith('https://')) {
        errorDiv.style.display = 'none';
        input.style.borderColor = '';
      } else {
        errorDiv.style.display = 'block';
        input.style.borderColor = 'red';
        return;
      }
    }


    function removeSuggestion(button) {
      const card = button.closest('.col-md-6');
      card.remove();
      suggestionCount--; // Decrement the suggestion count
      $('#suggestion_count').val(suggestionCount);
      if (suggestionCount < maxSuggestions) {
        document.getElementById('add_suggestion_button').disabled = false;
      }
      updateSuggestionNumbers();
      // Re-enable the "Add Suggestion" button if suggestions are removed

    }
    function updateSuggestionNumbers() {
      const cards = document.querySelectorAll('#suggestion_container .card');
      cards.forEach((card, index) => {
        const cardHeader = card.querySelector('.card-header h5');
        
        cardHeader.textContent = `Suggestion ${index + 1}`;

        const suggestionCountElement = card.querySelector('.suggestion-count');
        suggestionCountElement.textContent = `Number of Suggestions: ${index + 1}`;
      });
    }

    function validateMobileNumber(input) {
      const prefix = '+91';
      if (!input.value.startsWith(prefix)) {
        input.value = prefix;
      }
      if (input.value.length > 13) {
        input.value = input.value.slice(0, 13); // Limit input length to 13 characters
      }
    }
    function validateFloatingPoint(input) {
      const value = input.value;
      const errorDivId = input.id + '_error';
      const errorDiv = document.getElementById(errorDivId);

      let isValid = false;

      // Regular expressions for latitude and longitude
      const latitudePattern = /^-?([1-8]?\d(\.\d{1,6})?|90(\.0{1,6})?)$/;
      const longitudePattern = /^-?((1[0-7]\d(\.\d{1,6})?)|([1-9]?\d(\.\d{1,6})?)|180(\.0{1,6})?)$/;

      if (input.id === 'latitude') {
        isValid = latitudePattern.test(value) || value === '';
      } else if (input.id === 'longitude') {
        isValid = longitudePattern.test(value) || value === '';
      }

      if (isValid) {
        errorDiv.style.display = 'none';
        input.style.borderColor = '';
      } else {
        errorDiv.style.display = 'block';
        input.style.borderColor = 'red';
      }
    }
    function getAllCarouselSuggestions() {
      const carouselData = []; // Array to store data for all carousel cards

      $('.carousel-item').each(function (index) {
        const cardIndex = index + 1;

        /*const cardData = {
          cardTitle: $(`#carousel_card_title_${cardIndex}`).val() || '',
          mediaType: $(`#carousel_media_type_${cardIndex}`).val() || '',
          mediaHeight: $(`#carousel_media_height_${cardIndex}`).val() || '',
          mediaWidth: $(`#media_width_carousel_${cardIndex}`).val() || '',
          text: $(`#carousel_textarea_${cardIndex}`).val() || '',
          suggestion: [] // Placeholder for additional fields or suggestions
        };*/
              const cardData = {
          card_title: $(`#carousel_card_title_${cardIndex}`).val() || '',
          rich_card_media_type: $(`#carousel_media_type_${cardIndex}`).val() || '',
          media_height: $(`#carousel_media_height_${cardIndex}`).val() || '',
          media_width: $(`#media_width_carousel_${cardIndex}`).val() || '',
          text: $(`#carousel_textarea_${cardIndex}`).val() || '',
          suggestions: [] // Placeholder for additional fields or suggestions
        };

        // Gather suggestions related to the current card
        const suggestionContainers = $(`#carousel_suggestion_container_${cardIndex} .card`);

        suggestionContainers.each(function () {
          const card = $(this); // Convert DOM element to jQuery object
          const actionType = card.find('select.action_type').val();
          const fields = {};

          switch (actionType) {
            case 'REPLY':
              fields.text_message = card.find('input[name="text_message[]"]').val();
              break;
            case 'DIALER_ACTION':
              fields.dial_sugg_text = card.find('input[name="dial_sugg_text[]"]').val();
              fields.mobile_number = card.find('input[name="mobile_number[]"]').val();
              break;
            case 'URL_ACTION':
              fields.url_sugg_text = card.find('input[name="url_sugg_text[]"]').val();
              fields.url_link = card.find('input[name="url_link[]"]').val();
              break;
            case 'VIEW_LOCATION(Lat/Lang)':
              fields.location_sugg_txt = card.find('input[name="location_sugg_text[]"]').val();
              fields.location_url = card.find('input[name="location_url[]"]').val();
              fields.latitude = card.find('input[name="latitude[]"]').val();
              fields.longitude = card.find('input[name="longitude[]"]').val();
              break;
            case 'VIEW_LOCATION(query)':
              fields.locate_sugg_text = card.find('input[name="locate_sugg_text[]"]').val();
              fields.locate_url = card.find('input[name="location_query[]"]').val();
              break;
            case 'SHARE_LOCATION':
              fields.share_txt_location_sugg_txt = card.find('input[name="share_sugg_txt[]"]').val();
              break;
            case 'CREATE_CALENDAR':
              fields.calender_sugg_txt = card.find('input[name="calender_sugg_txt[]"]').val();
              fields.from_date = card.find('input[name="from_date[]"]').val();
              fields.to_date = card.find('input[name="to_date[]"]').val();
              fields.event = card.find('input[name="event_name[]"]').val();
              fields.event_label = card.find('textarea[name="event_label[]"]').val();
              break;
            default:
              break;
          }

          if (Object.keys(fields).length > 0) {
            cardData.suggestions.push({ actionType, fields });
          }
        });

        carouselData.push([cardData]); // Add the card data to the array
      });

      return carouselData; // Return the complete carouselData array
    }

    function getAllSuggestions() {
      const suggestions = [];
      const suggestionCards = document.querySelectorAll('#suggestion_container .card');

      suggestionCards.forEach(card => {
        const actionType = card.querySelector('select.action_type').value;
        const fields = {};

        switch (actionType) {
          case 'REPLY':
            fields.text_message = card.querySelector('input[name="text_message[]"]').value;
            break;
          case 'DIALER_ACTION':
            fields.dial_sugg_text = card.querySelector('input[name="dial_sugg_text[]"]').value;
            fields.mobile_number = card.querySelector('input[name="mobile_number[]"]').value;
            break;
          case 'URL_ACTION':
            fields.url_sugg_text = card.querySelector('input[name="url_sugg_text[]"]').value;
            fields.url_link = card.querySelector('input[name="url_link[]"]').value;
            break;
          case 'VIEW_LOCATION(Lat/Lang)':
            fields.location_sugg_txt = card.querySelector('input[name="location_sugg_text[]"]').value;
            fields.location_url = card.querySelector('input[name="location_url[]"]').value;
            fields.latitude = card.querySelector('input[name="latitude[]"]').value;
            fields.longitude = card.querySelector('input[name="longitude[]"]').value;
            break;

          case 'VIEW_LOCATION(query)':
            fields.locate_sugg_text = card.querySelector('input[name="locate_sugg_text[]"]').value;
            fields.locate_url = card.querySelector('input[name="location_query[]"]').value;

            break;
          case 'SHARE_LOCATION':
            // fields.share_txt_location = $(this).find('input[name="share_txt_location[]"]').value;
            fields.share_txt_location_sugg_txt = card.querySelector('input[name="share_sugg_txt[]"]').value;
            break;
          case 'CREATE_CALENDAR':
            fields.calender_sugg_txt = card.querySelector('input[name="calender_sugg_txt[]"]').value;
            fields.from_date = card.querySelector('input[name="from_date[]"]').value;
            fields.to_date = card.querySelector('input[name="to_date[]"]').value;
            fields.event = card.querySelector('input[name="event_name[]"]').value;
            fields.event_label = card.querySelector('textarea[name="event_label[]"]').value;
            break;
         
         
            default:
            break;
        }

        if (Object.keys(fields).length > 0) {
          suggestions.push({ actionType, fields });
        }
      });
      return suggestions;
    }


    // start function document
    $(function () {
      $('.theme-loader').fadeOut("slow");
      // init();
    });
    document.body.addEventListener("click", function (evt) {
      //note evt.target can be a nested element, not the body element, resulting in misfires
      $("#id_error_display_submit").html("");
    });
    // id_error_display_submit -clear
    function validateInput_phone() {
      $("#id_error_display_submit").html("");
    }

    // Use the appropriate selector to get all the file inputs
    var fileInputs = $('.carousel_media'); // This gets all file inputs with the class 'carousel_media'

    // Check if at least one file input has files selected
    var filesSelected = false;

    fileInputs.each(function () {
      var files = $(this)[0].files; // Get the file list from the input
      if (files.length > 0) {
        filesSelected = true; // At least one file input has files
      }
    });


    function checkEmpty() {
      
      var selectElement = document.getElementById('select_id');
      var selectedValue = selectElement.value;

      var selectCam = document.getElementById('select_id_1');
      if (selectElement.value === "None") {
        selectElement.style.borderColor = "red"; // Set border color to red
        selectElement.focus(); // Optionally, focus the field to bring attention to it
      } else {
        selectElement.style.borderColor = ""; // Reset the border color if a valid option is selected
      }

      if (selectCam.value === "None") {
        selectCam.style.borderColor = "red"; // Set border color to red
        selectCam.focus(); // Optionally, focus the field to bring attention to it
      } else {
        selectCam.style.borderColor = ""; // Reset the border color if a valid option is selected
      }

      var card_title_element = document.getElementById('card_title');
      var selectMedia = document.getElementById('rich_card_media_type');
      var selectedMedia = selectMedia.value;
      var mediaHeight = document.getElementById('media_height');
      var cardOrientation = document.getElementById('orientation');
      var cardAllignment = document.getElementById('card_allignment');
      var fileInput = $('#rich_card_media')[0].files[0];
      if (selectMedia.value === "None") {
        selectMedia.style.borderColor = "red"; // Set border color to red
        selectMedia.focus(); // Optionally, focus the field to bring attention to it
      } else {
        selectMedia.style.borderColor = ""; // Reset the border color if a valid option is selected
      }
      if (mediaHeight.value === "None") {
        mediaHeight.style.borderColor = "red"; // Set border color to red
        mediaHeight.focus(); // Optionally, focus the field to bring attention to it
      } else {
        mediaHeight.style.borderColor = ""; // Reset the border color if a valid option is selected
      }
      if (cardOrientation.value === "None") {
        cardOrientation.style.borderColor = "red"; // Set border color to red
        cardOrientation.focus(); // Optionally, focus the field to bring attention to it
      } else {
        cardOrientation.style.borderColor = ""; // Reset the border color if a valid option is selected
      }

      if (cardAllignment.value === "None") {
        cardAllignment.style.borderColor = "red"; // Set border color to red
        cardAllignment.focus(); // Optionally, focus the field to bring attention to it
      } else {
        cardAllignment.style.borderColor = ""; // Reset the border color if a valid option is selected
      }

      if (card_title.value === "") {
        card_title_element.style.borderColor = "red";
        card_title_element.focus();
      } else {
        card_title_element.style.borderColor = "";
      }
      if ((selectedMedia === 'Image' || selectedMedia === 'Video') && !fileInput) {
        // Prevent the form from submitting
        alert('Please upload your media file')
        return false; // Stop further execution

      }

      if (document.getElementById("select_id").value === "") {
        event.preventDefault();
      }

      $('.media_height,.carousel_media_height, .media_width_carousel, .carousel_media_type,.carousel_media, .rich_card_media_type').each(function () {
        var selectedValue = $(this).val();
        var elementId = $(this).attr('id');
        var $element = $('#' + elementId); // Select the actual element using jQuery

        // console.log("Element ID:", elementId, "Selected Value:", selectedValue);

        if (selectedValue === "None") {
          $element.css('border-color', 'red'); // Set border color to red
          $element.focus(); // Optionally, focus the field to bring attention to it
        } else {
          $element.css('border-color', ''); // Reset the border color if a valid option is selected
        }
      });
    }

    function validateMobileNumbers() {
      let valid = true;
      $('.mobile-number').each(function () {
        const value = $(this).val();
        if (value === '+91') {
          $(this).addClass('error');
          valid = false;
        } else {
          $(this).removeClass('error');
        }
      });
      return valid;
    }


    // While clicking the Submit button
    $(document).ready(function () {
      $(document).on("submit", "form#frm_compose_whatsapp", function (e) {
   
        flag = true;
        e.preventDefault();
        // var rich_card_media_type = $('#rich_card_media_type').val();
        // var media_height = $('#media_height').val();
        // var orientation = $('#orientation').val();
        // var card_allignment = $('#card_allignment').val();
        var textarea = $('#textarea').val();
        var mediafile = $('#mediafile').val();
        var fileInput = $('#rich_card_media')[0].files[0];
        var mobile_number = $('#mobile_number').val();

        var send_data = [];

        var selectElement = document.getElementById('select_id');
        var selectElements = document.getElementById('select_id_1');
        var selectedValue = selectElement.value;
        var suggestionContainers = document.getElementById('suggestion_container');
        var selectedValues = document.getElementById('rich_card_media_type'); // Get the selected media type
        var selectedMedia = selectedValues.value;
        if (selectElements.value === 'None' || selectElements.value === '') {
            $(selectElements).css('border-color', 'red'); // Set border color to red if invalid
            flag = false; // Set flag to false if invalid
        } 
        // If the selected value is 'TEXT', proceed with retrieving form values
        if (selectedValue === 'TEXT') {
          // Retrieve form field values
          var templateLabel = $("input[name='template_label']").val();
          var header1 = $("select[name='campaign_type']").val();
          var header = $("input[name='header']").val();
          var msgContent = $("textarea[name='msg_content']").val();

          send_data.push({
            text: msgContent
          });
          // console.log(JSON.stringify(send_data) + " send_data with suggestions");
        } else if (selectedValue === 'RICH TEXT') {
          if (!validateMobileNumbers()) {
            $('#mobile_number').css('border-color', 'red');
            flag = false;
            e.preventDefault();
          }
          // Retrieve form field values
          var msgContent = $("textarea[name='msg_content']").val();
          // Collect suggestions from getAllSuggestions function
          const suggestions = getAllSuggestions();
          if (suggestions.length === 0) {
            $("#id_error_display_submit").html("At least one suggestion must be provided.");
            return null;
          }
          // Collect form data into send_data array
          send_data.push({
            text: msgContent,
            suggestions: suggestions
          });
          // console.log(JSON.stringify(send_data) + " send_data with suggestions");
        } else if (selectedValue === 'RICH CARD') {
          var fields = ['#rich_card_media_type', '#media_height', '#orientation', '#card_allignment'];
          fields.forEach(function (selector) {
            var value = $(selector).val();
            if (value === 'None' || value === '') { // Check for 'None' value or empty value
              $(selector).css('border-color', 'red');
              flag = false;
            } else {
              $(selector).css('border-color', ''); // Clear previous error
            }
          });

          if (!flag) {
            e.preventDefault(); // Prevent form submission if validation fails
          }
          if (!validateMobileNumbers()) {
            $('#mobile_number').css('border-color', 'red');
            flag = false;
            e.preventDefault();
          }
          // let isValid = true; // Initialize a flag to track validation

          // Retrieve form field values
          var card_title = $("input[name='card_title']").val();
          var rich_card_media_type = $("select[name='rich_card_media_type']").val();
          var media_height = $("select[name='media_height']").val();
          var orientation = $("select[name='orientation']").val();
          var card_allignment = $("select[name='card_allignment']").val();
          var msgContent = $("textarea[name='msg_content']").val().trim();
          // Collect suggestions from getAllSuggestions function
          const suggestions = getAllSuggestions();
          // Collect form data into send_data array
          send_data.push({
            text: msgContent,
            card_title: card_title,
            rich_card_media_type: rich_card_media_type,
            media_height: media_height,
            orientation: orientation,
            card_allignment: card_allignment,
            suggestions: suggestions
          });
          // console.log(JSON.stringify(send_data) + " send_data with suggestions");
        } else if (selectedValue === 'CAROUSEL') {
          // Define an array of selectors for validation
          var fields = ['.carousel_card_title', '.carousel_media_type', '.carousel_media_height', '.media_width_carousel', '.carousel_msg_content'];
          var flag = true; // Initialize flag for validation status
          // Iterate over each selector
          fields.forEach(function (selector) {
            // Find elements with the current selector
            $(selector).each(function () {
              var value = $(this).val();
              if (value === 'None' || value === '') { // Check for 'None' value or empty value
                $(this).css('border-color', 'red'); // Set border color to red if invalid
                flag = false; // Set flag to false if any field is invalid
              } else {
                $(this).css('border-color', ''); // Clear previous error if valid
              }
            });
          });

          // Prevent form submission if validation fails
          if (!flag) {
            e.preventDefault(); // Ensure e is available (e.g., inside an event handler)
          }

          if (!validateMobileNumbers()) {
            $('#mobile_number').css('border-color', 'red');
            flag = false;
            e.preventDefault();
          }
          var itemCount = $('.carousel-inner .carousel-item').length;
          if (itemCount == 0 || itemCount == 1) {
            $("#id_error_display_submit").html("Minimum 2 carousel card is selected!.");
            flag = false;
            return;
          }

          var totalFiles = 0; // Initialize total file count
          var fileInputs = document.querySelectorAll('.carousel_media');

          // Iterate over each file input element
          fileInputs.forEach(function (fileInput) {
            var fileCount = fileInput.files.length;
            totalFiles += fileCount; // Sum the total number of files
            // console.log('Number of files in input with id ' + fileInput.id + ': ' + fileCount);
          });

          // console.log('Total number of files across all inputs: ' + totalFiles);
          // Check if the total number of files matches itemCount
          if (totalFiles !== itemCount) {
            alert('Please upload your media file');
            return false; // Stop further execution
          }
          // Retrieve form field values
          const suggestions_1 = getAllCarouselSuggestions();
          // Collect form data into send_data array
          send_data = suggestions_1.slice();
          // send_data.push(suggestions_1);
          // console.log(JSON.stringify(send_data) + " send_data with suggestions");

        }

        e.preventDefault();
        if (flag) { // If no flag is red
          var fd = new FormData(this);
          var rich_card_media = $('#rich_card_media')[0].files;
          var text_area_value = $('.delete').text();
          var txt_header_name = $('#txt_header_name').text();
          if (rich_card_media.length > 0) {
            fd.append('rich_card_media', rich_card_media[0]);
          }
          // Assuming `send_data` is an array
          fd.append('txt_msg', JSON.stringify(send_data));

          // Submit the form into Ajax - ajax/whatsapp_call_functions.php
          // console.log("chdmhcechedb,chw")
          $.ajax({
            type: 'post',
            url: "ajax/whatsapp_call_functions.php",
            dataType: 'json',
            data: fd,
            contentType: false,
            processData: false,
            beforeSend: function () { // Before send to Ajax
              $('#submit').attr('disabled', true);
              $('.theme-loader').show();
            },
            complete: function () { // After complete the Ajax
              //$('#submit').attr('disabled', false);
              $('.theme-loader').hide();
            },
            success: function (response) { // Succes
              if (response.status == '0' || response.status == 0) { // Failed Status
                $('#submit').attr('disabled', false);
                $('.theme-loader').hide();
                $("#id_error_display_submit").html(response.msg);
              } else if (response.status == 1 || response.status == '1') { // Success Status
                $('#submit').attr('disabled', true);
                suggestionContainers.style.display = 'none';
                $('.theme-loader').hide();
                $("#id_error_display_submit").html("Template created successfully !!");
                setInterval(function () {
                  // window.location = 'template_list';
                  window.location.reload();
                  document.getElementById("frm_compose_whatsapp").reset();
                }, 2000);
              }
              $('.theme-loader').hide();
            },
            error: function (response, status, error) { // Error

              $('#submit').attr('disabled', false);
              $("#id_error_display_submit").html(response.msg);
              // window.location = 'template_list';
            }
          })
        }
      });
    });
    // FORM Clear value    
    function myFunction_clear() {
      //document.getElementById("frm_compose_whatsapp").reset();
      //document.getElementById('suggestion_container').style.display = 'none';
window.location='create_template';


    }


    // FORM preview value
    function preview_content() {
      var form = $("#frm_compose_whatsapp")[0]; // Get the HTMLFormElement from the jQuery selector
      var data_serialize = $("#frm_compose_whatsapp").serialize();
      var fd = new FormData(form); // Use the form element in the FormData constructor
      var txt_header_name = $('#txt_header_name').text();
      fd.append('txt_header_name', txt_header_name);

      $.ajax({
        type: 'post',
        url: "ajax/preview_call_functions.php?preview_functions=preview_template",
        data: fd,
        processData: false, // Important: Prevent jQuery from processing the data
        contentType: false, // Important: Let the browser set the content type
        success: function (response) { // Success
          if (response.status == 0) { // Failure Response
            $("#id_modal_display").html('No Data Available!!');
          } else if (response.status == 1) { // Success Response
            $("#id_modal_display").html(response.msg);
          }
          $('#default-Modal').modal({ show: true }); // Open in a Modal Popup window
        },
        error: function (response, status, error) { // Error
          console.log("error");
          $("#id_modal_display").html(response.status);
          $('#default-Modal').modal({ show: true });
        }
      });
    }


  
    const txt_field_array = [];

    // Event listener for keyup events in the textarea
    const textarea = document.getElementById('textarea');
    textarea.addEventListener('keyup', updateResult);

    const btn = document.getElementById('btn');
    btn.addEventListener('click', function handleClick() {
      if (i <= 10) {
        // Get the current cursor position
        const cursorPos = textarea.selectionStart;
        const textAfterCursor = textarea.value.substring(cursorPos);
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        // Get the text before and after the cursor

        const textBeforeCursor = textarea.value.substring(0, cursorPos);
        const selectedText = text.substring(start, end);
        // textarea.value = ${before}[${selectedText}]${after};
        // Insert the variable at the cursor position

        // textarea.value = textBeforeCursor + '[' + i + ']' + textAfterCursor;
        var braketText = `[${i}]`
        textarea.value = `${textBeforeCursor}${braketText}${textAfterCursor}`;

        const newCursorPosition = end + braketText.length; // 2 accounts for the brackets added
        textarea.selectionStart = textarea.selectionEnd = newCursorPosition;
        textarea.focus();
        // Update cursor position to be after the newly inserted variable
        // textarea.selectionStart = cursorPos + 2; // Adjust this if the length of your variable changes
        // textarea.selectionEnd = cursorPos + 2;

        // Add the variable to the array
        txt_field_array.push(i.toString());

       
       

        // Increment the variable index
        i++;
      }
    });

    function updateResult() {
      var variable_count = [];
      var t = textarea.value;
      var length = textarea.value.length;
      if (length == 0) {
       
        i = 1;
      }
      var regex = /[(\d+)]/;
      var match = t.match(regex);
      if (match) {
        var digits = match[1];
        if (!txt_field_array.includes(digits)) {

          txt_field_array.push(digits.toString());
          i = digits;
        
          i++;
        }
      } else {

      }
      var s = t.split("[");
      // Looping the j is less than the s.length.if the condition is true to continue the process and split the variable and push the variable count.if the condition are false to stop the process
      for (var j = 1; j < s.length; j++) {
        var s1 = s[j].split("]");
        variable_count.push(s1[0]);
      }
      
      if (txt_field_array.length > variable_count.length) {
        var res = txt_field_array.filter(function (obj) {
          return variable_count.indexOf(obj) == -1;
        })
        var item = res[0];

        var index = txt_field_array.indexOf(item);
        txt_field_array.splice(index, 1);
      
        var child = document.getElementById('Variable' + res[0] + '');
       
      }
    }

    function carousel_button(button) {
      var buttonId = button.id;
      var splitParts = buttonId.split('_');
      const textarea = document.getElementById('carousel_textarea_' + splitParts[1]);
      var txt_field_array = [];
      textarea.addEventListener('keyup', updateResult);
      textarea.value += '';

      const btn = document.getElementById('btn_' + splitParts[1]);
      if (i <= 10) {
        var cursorPos = textarea.selectionStart;
        var textBeforeCursor = textarea.value.substring(0, cursorPos);
        var textAfterCursor = textarea.value.substring(cursorPos, textarea.value.length);
        textarea.value = textBeforeCursor + '[' + i + ']' + textAfterCursor;
        textarea.selectionStart = cursorPos + 4; // Adjust cursor position
        textarea.selectionEnd = cursorPos + 4;
        txt_field_array.push(i.toString());

        
        i++;
      }

      function updateResult() {
        var variable_count = [];
        var t = textarea.value;
        var length = textarea.value.length;
        if (length == 0) {
          
          i = 1;
        }
        var regex = /[(\d+)]/;
        var match = t.match(regex);
        if (match) {
          var digits = match[1];
          if (!txt_field_array.includes(digits)) {

            txt_field_array.push(digits.toString());
            i = digits;
           
            i++;
          }
        } else {

        }
        var s = t.split("[");
        // Looping the j is less than the s.length.if the condition is true to continue the process and split the variable and push the variable count.if the condition are false to stop the process
        for (var j = 1; j < s.length; j++) {
          var s1 = s[j].split("]");
          variable_count.push(s1[0]);
        }
        
        if (txt_field_array.length > variable_count.length) {
          var res = txt_field_array.filter(function (obj) {
            return variable_count.indexOf(obj) == -1;
          })
          var item = res[0];

          var index = txt_field_array.indexOf(item);
          txt_field_array.splice(index, 1);
          
          var child = document.getElementById('Variable' + res[0] + '');
          
        }
      }
    }
    $(document).on('keyup', '.carousel_msg_content', function () {
      // Get the ID of the textarea
      const textareaId = this.id;
      // Split the ID to extract the relevant part
      const splitParts = textareaId.split('_');
      // Get the length of the text in the textarea
      const textLength = $(this).val().length;
      // Update the HTML content of the corresponding span element
      $("#current_text_value_" + splitParts[2]).html(textLength);
    });

    $(document).on('keyup', '.carousel_card_title', function () {
      // Get the ID of the textarea
      const textareaId = this.id;
      // Split the ID to extract the relevant part
      const splitParts = textareaId.split('_');
      // Get the length of the text in the textarea
      const textLength = $(this).val().length;
      // Update the HTML content of the corresponding span element
      $("#current_card_value_" + splitParts[3]).html(textLength);
    });


    //TEXT AREA COUNT
    // $("#textarea").keyup(function () {
    //   var textValue = $(this).val();
    //   var regex = /[(\d+)]/;
    //  // Extract digits from the text
    //             var match = textValue.match(regex);
    //             if (match) {
    //                 // If digits are found, clear the text area
    //             }
    //   $("#current_text_value").text($(this).val().length);
    // });
    $("#textarea").keyup(function () {
      var $textarea = $(this);
      var textValue = $textarea.val();

      // Define the regex pattern to match variables enclosed in square brackets
      var regex = /\[(\d+)\]/g;
      var seenVariables = {}; // To keep track of variables we've seen
      var uniqueTextValue = '';
      var lastIndex = 0;
      // Use a function to process each match and build the new text
      textValue.replace(regex, function (match, p1, index) {
        if (seenVariables[p1]) {
          // If we've seen this variable before, remove it from the text
          uniqueTextValue += textValue.substring(lastIndex, index);
          lastIndex = index + match.length;
        } else {
          // If it's a new variable, add it to the text and mark as seen
          seenVariables[p1] = true;
        }
      });
      // Add the remaining part of the text after the last match
      uniqueTextValue += textValue.substring(lastIndex);

      // Update the text area with the unique text
      $textarea.val(uniqueTextValue);

      // Update the length of the current text
      $("#current_text_value").text(uniqueTextValue.length);
    });

    // TEMplate Name - Space
    $(function () {
      $('#txt_template_name').on('keypress', function (e) {
        if (e.which == 32) {
          return false;
        }
      });
    });

    $('#textarea').on('input', function (event) {
      // Get the input value
      var inputValue = $(this).val();

      // Check if backticks (`), single quotes ('), or double quotes (") are present in the input
      if (inputValue.includes('`') || inputValue.includes("'") || inputValue.includes('"')) {
        // Remove all occurrences of backticks, single quotes, and double quotes from the input
        inputValue = inputValue.replace(/[`'"]/g, '');

        // Update the input value
        $(this).val(inputValue);
      }
    });
    function restrictInput(event) {
      const char = event.key;
      if (char === '"') {
        event.preventDefault(); // Prevent entering double quotes and single quotes
        // alert('Double quotes and single quotes are not allowed.');
      }
    }
  </script>
</body>

</html>
