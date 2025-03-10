<?php
/*
Authendicated users only allow to view this FAQ page.
This page is used to view the list of FAQ Questions and its answers.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 03-Jul-2023
*/

session_start(); // start session
error_reporting(0); // The error reporting function

include_once('api/configuration.php'); // Include configuration.php
extract($_REQUEST);// Extract the request

// If the Session is not available redirect to index page
if ($_SESSION['yjwatsp_user_id'] == "") { ?>
  <script>window.location = "index";</script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("FAQ Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>FAQ ::
    <?= $site_title ?>
  </title>
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.css">
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
            <h1>FAQ</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="dashboard">Dashboard</a></div>
              <div class="breadcrumb-item">FAQ</div>
            </div>
          </div>

          <!-- List Panel -->
          <div class="section-body">
            <div class="row">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div id="accordion">

                      <?
                      $replace_txt = '{
                          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
                        }'; // Add User id
                      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // Add Bearer Token
                      $curl = curl_init();
                      curl_setopt_array($curl, array(
                        CURLOPT_URL => $api_url . '/list/faq',
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
                      site_log_generate("FAQ Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
                      $response = curl_exec($curl);
                      curl_close($curl);

                      // After got response decode the JSON result
                      $header = json_decode($response, false);
                      site_log_generate("FAQ Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

                      // To list the response data
                      $indicatori = 0;
                      $hd = '';
                      if ($header->num_of_rows > 0) {
                        for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
                          // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process and to get the details.if the condition are false to stop the process
                          $indicatori++;
                          $whatsapp_faq_heading = $header->report[$indicator]->whatsapp_faq_heading;
                          $whatsapp_faq_ques = $header->report[$indicator]->whatsapp_faq_ques;
                          $whatsapp_faq_ans = $header->report[$indicator]->whatsapp_faq_ans;

                          $area = '';
                          $show = '';
                          if ($indicatori == 1) {
                            $area = 'aria-expanded="true"';
                            $show = 'show';
                          }

                          if ($whatsapp_faq_heading != $hd) { ?>
                            <div class="card-header" style="text-align: center !important;">
                              <h3>
                                <?= $whatsapp_faq_heading ?>
                              </h3>
                            </div>
                          <? } ?>
                          <div class="accordion">
                            <div class="accordion-header" role="button" data-toggle="collapse"
                              data-target="#panel-body-<?= $indicatori ?>" <?= $area ?>>
                              <h4>
                                <?= $whatsapp_faq_ques ?>
                              </h4>
                            </div>
                            <div class="accordion-body collapse <?= $show ?>" id="panel-body-<?= $indicatori ?>"
                              data-parent="#accordion">
                              <?= $whatsapp_faq_ans ?>
                            </div>
                          </div>
                          <?
                          $hd = $whatsapp_faq_heading;
                        }
                      } else if ($header->response_status == 403 || $response == '') { ?>
                          <script>
                            window.location = "index"
                          </script>
                      <? }

                      ?>

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
  </script>
</body>

</html>