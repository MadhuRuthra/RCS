<?php
/*
Authendicated users to use this site header page for all main pages.
This page is used to give the header details for all main pages.
It is used to show to available credits api and login time api.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
*/

if ($_SESSION["yjwatsp_usr_mgt_status"] == 'N' or $_SESSION["yjwatsp_usr_mgt_status"] == 'R') {
  if ($site_page_name != 'on_boarding' and $site_page_name != 'dashboard') {
    ?>
    <script>window.location = "on_boarding";</script>
    <?
  }
}


// To Send the request  API
$replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
  "login_time" : "' . $_SESSION['yjwatsp_login_time'] . '"
}';
// add bearertoken
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
// It will call "login_time" API to verify, can we access for the login_time details 
$curl = curl_init();
curl_setopt_array(
  $curl,
  array(
    CURLOPT_URL => $api_url . '/list/login_time',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $replace_txt,
    CURLOPT_HTTPHEADER => array(
      $bearer_token,
      'Content-Type: application/json'
    ),
  )
);
// Send the data into API and execute 
site_log_generate("Header Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
$response = curl_exec($curl);
curl_close($curl);
// After got response decode the JSON result
$header = json_decode($response, false);
site_log_generate("Header Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

if ($header->num_of_rows > 0) {// If the response is success to execute this condition and this logout this page
  for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
    if ($header->report[$indicator]->user_log_status == 'O') { ?>
      <script>window.location = "logout";</script>
      <?php exit();
    }
  }
}
if ($header->response_status == 403 || $response == '') {
  // If the response is success to execute this condition and this logout this page
  ?>
  <script>window.location = "index";</script>
  <?php exit();
}
// To Send the request  API
$replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
// add bearer token
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
// It will call "available_credits" API to verify, can we access for the available_credits details 
$curl = curl_init();
curl_setopt_array(
  $curl,
  array(
    CURLOPT_URL => $api_url . '/list/available_credits',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => $replace_txt,
    CURLOPT_HTTPHEADER => array(
      $bearer_token,
      'Content-Type: application/json'
    ),
  )
);
// Send the data into API and execute 
site_log_generate("Header Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
$response = curl_exec($curl);
curl_close($curl);
// After got response decode the JSON result
$header = json_decode($response, false);
site_log_generate("Header Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

if ($header->num_of_rows > 0) {
  // If the response is success to execute this condition and this logout this page
  for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
    $total_messages = $header->report[$indicator]->total_messages;
    $available_messages = $header->report[$indicator]->available_messages;
    $_SESSION['yjwatsp_available_messages'] = $header->report[$indicator]->available_messages;
  }
} else if ($header->response_status == 403 || $response == '') { ?>
    <script>
      window.location = "index"
    </script>
<? }
?>
<style>
  /* css style for this page */
  span.autoShowHide {
    white-space: nowrap;
    width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  span.autoShowHide:hover {
    white-space: normal;
    overflow: visible;
    width: 300px;
  }
</style>
<? if ($site_page_name == 'business_summary_report') { ?>
  <nav class="navbar navbar-expand-lg main-navbar" style="z-index: 1;">
  <? } else { ?>
    <nav class="navbar navbar-expand-lg main-navbar">
    <? } ?>
    <form class="form-inline mr-auto">
      <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
      </ul>
    </form>

    <div class="search-element">
      <div>
        <? /* <span class="badge badge-secondary autoShowHide" style="color:#FFF; font-size:18px; font-weight: bold; text-align: right;">APK Key : <span title="Click here to Copy the API Key" onclick="copyContent()" id='id_apikey_copy'><?= ($_SESSION['yjwatsp_api_key']) ?></span></span>&nbsp;*/ ?><span
          class="badge badge-secondary" style="color:#FFF; font-size:18px; font-weight: bold; text-align: right;">Total
          Credits :
          <?= $available_messages ? indian_money_format($available_messages) : 0 ?>
        </span>
      </div>
    </div>
    <!-- using Navbar -->
    <ul class="navbar-nav navbar-right">
      <li class="dropdown"><a href="#" data-toggle="dropdown"
          class="nav-link dropdown-toggle nav-link-lg nav-link-user">
          <img alt="image" src="assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
          <div class="d-sm-none d-lg-inline-block">Hi,
            <?= strtoupper($_SESSION['yjwatsp_user_name']) ?>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <?/* if ($_SESSION['yjwatsp_user_master_id'] == 1 or $_SESSION['yjwatsp_user_master_id'] == 2) { ?>
            <a href="on_boarding" class="dropdown-item has-icon">
              <i class="fas fa-user"></i> On Boarding
            </a>
          <? }*/ ?>
          <a href="change_password" class="dropdown-item has-icon">
            <i class="fas fa-bolt"></i> Change Password
          </a>
          <div class="dropdown-divider"></div>
          <a href="logout" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- Script for this page -->
  <script>
    let text = document.getElementById('id_apikey_copy').innerHTML;
    const copyContent = async () => {
      try {
        await navigator.clipboard.writeText(text);
        console.log('Content copied to clipboard');
      } catch (err) {
        console.error('Failed to copy: ', err);
      }
    }
  </script>
