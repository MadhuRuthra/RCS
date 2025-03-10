<?php
/*
Authendicated users to use this site menu page for all main pages.
This page is used to give the menu details for all main pages.
It is used to some main activities are used.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
*/

session_start(); // Start session
error_reporting(E_ALL); // The error reporting function

include_once('../api/configuration.php'); // Include configuration.php
extract($_REQUEST); // Extract the request
$current_date = date("Y-m-d H:i:s"); // To get currentdate function
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . ''; // To get bearertoken

// Step 1: Get the current date
$todayDate = new DateTime();

// Step 2: Convert the date to Julian date
$baseDate = new DateTime($todayDate->format('Y-01-01'));
$julianDate = $todayDate->diff($baseDate)->format('%a') + 1; // Adding 1 since the day of the year starts from 0
$year = date("Y");
$julian_dates = str_pad($julianDate, 3, '0', STR_PAD_LEFT);
$hour_minutes_seconds = date("His");
$random_generate_three = rand(100, 999);
if ($_SESSION["yjwatsp_user_master_id"] == '1') {
// To Send the request API 
$replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
// Add bearer token
$bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
// It will call "approve_payment" API to verify, can we can we allow to view the message credit list
$curl = curl_init();
curl_setopt_array(
  $curl,
  array(
    CURLOPT_URL => $api_url . '/list/waiting_approval_list',
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
$response = curl_exec($curl);

site_log_generate("Approve Payment Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
curl_close($curl);
// echo $response ;
// After got response decode the JSON result
$sms = json_decode($response, false);

if ($sms->response_status == 403 || $response == '') { ?>
  <script>
    window.location = "index"
  </script>
<? }

}
$waiting_approval = $sms->waiting_payment ? $sms->waiting_payment : 0;
$waiting_senderid = $sms->waiting_senderid ? $sms->waiting_senderid : 0;
$waiting_template = $sms->waiting_template ? $sms->waiting_template : 0;
$waiting_compose = $sms->waiting_compose ? $sms->waiting_compose : 0;
$waiting_users = $sms->waiting_users ? $sms->waiting_users : 0;
?>
<!-- sidebar menu -->
 
<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="dashboard"><img src="assets/img/YeeJai_Logo.png" style="height:100%" /></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="dashboard"><img src="assets/img/YJ.png" style="height:100%" /></a>
    </div>
    <ul class="sidebar-menu">
      <li <? if ($site_page_name == 'dashboard') { ?>class="dropdown active" <? } else { ?>class="dropdown" <? } ?>>
        <a href="dashboard" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a>
      </li>

   <? if ($_SESSION['yjwatsp_user_master_id'] == 1 or $_SESSION['yjwatsp_user_master_id'] == 2 or $_SESSION['yjwatsp_user_master_id'] == 3) { ?>
        <? if ($_SESSION['yjwatsp_user_master_id'] == 1 ) { ?>
          <li <? if ($site_page_name == 'message_credit_list' or $site_page_name == 'request_demo_list' or $site_page_name == 'activation_payment_list' or $site_page_name == 'message_credit' or $site_page_name == 'approve_payment' or $site_page_name == 'manage_users_list' or $site_page_name == 'manage_users' or $site_page_name == 'user_repository' or $site_page_name == 'view_onboarding') { ?>class="dropdown active" <? } else { ?>class="dropdown" <? } ?>>
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-crown"></i>
              <span>Admin </span>
              <?/* if ($waiting_approval > 0 || $waiting_users > 0) { ?><span class="badge badge-success"
                  style="width: auto; min-width:30px; font-weight: bold; margin-left: 10px; margin-right: 20px; line-height: 20px;">
                  <?= $waiting_users ?>
                </span>
              <? }*/ ?>
            </a>
            <ul class="dropdown-menu">
              <?/*   <li <? if ($site_page_name == 'approve_payment' or $site_page_name == 'message_credit') { ?>class="active" <? } ?>><a class="nav-link" href="approve_payment">Approve Payment
                    <? if ($waiting_approval > 0) { ?><span class="badge badge-success"
                        style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                        <?= $waiting_approval ?>
                      </span>
                    <? } ?>
                  </a></li> */ ?>
              <li <? if ($site_page_name == 'message_credit_list') { ?>class="active" <? } ?>><a class="nav-link"
                  href="message_credit_list">Message Credit List</a></li>
     <li <? if ($site_page_name == 'manage_users_list') { ?>class="active" <? } ?>><a class="nav-link"
                  href="manage_users_list">Manage Users</a></li>

              <? if ($_SESSION['yjwatsp_user_master_id'] == 1) { ?>
                <?/*<li <? if ($site_page_name == 'manage_users_list' or $site_page_name == 'manage_users' or $site_page_name == 'user_repository' or $site_page_name == 'view_onboarding') { ?>class="active" <? } ?>><a
                    class="nav-link" href="manage_users_list">Manage Users
                    <? if ($waiting_users > 0) { ?><span class="badge badge-success"
                        style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                        <?= $waiting_users ?>
                      </span>
                    <? }*/ ?>
                  </a></li>
                <!-- <li <? if ($site_page_name == 'activation_payment_list') { ?>class="active" <? } ?>><a class="nav-link"
                    href="activation_payment_list">Activation Payment List</a></li>
                <li <? if ($site_page_name == 'request_demo_list') { ?>class="active" <? } ?>><a class="nav-link"
                    href="request_demo_list">Demo Request List</a></li> -->
              <? } ?>
            </ul>
          </li>
        <? } ?>

      <li <? if ($site_page_name == 'manage_store' or $site_page_name == 'approve_whatsapp_no' or $site_page_name == 'purchase_message_credit' or $site_page_name == 'purchase_message_list' or $site_page_name == 'manage_store_list' or $site_page_name == 'manage_whatsappno_list' or $site_page_name == 'manage_whatsapp_no' or $site_page_name == 'whatsapp_no_api_list' or $site_page_name == 'whatsapp_no_api' or $site_page_name == 'approve_whatsapp_no_api' or $site_page_name == 'template_list' or $site_page_name == 'create_template' or $site_page_name == 'approve_template') { ?>class="dropdown active" <? } else { ?>class="dropdown" <? } ?>>
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i>
          <span>Template</span>
          <? if ($waiting_template > 0) { ?><span class="badge badge-success"
              style="width: auto; min-width:30px; font-weight: bold; margin-left: 10px; margin-right: 20px; line-height: 20px;">
              <?= $waiting_template ?>
            </span>
          <? } ?>
        </a>
        <ul class="dropdown-menu">
          <? if ($_SESSION['yjwatsp_user_permission'] == 1 or $_SESSION['yjwatsp_user_permission'] == 2) { ?>
            <?/* <li <? if ($site_page_name == 'whatsapp_no_api' or $site_page_name == 'whatsapp_no_api_list' or $site_page_name == 'approve_template') { ?>class="active" <? } ?>><a class="nav-link"
                href="whatsapp_no_api_list">Sender ID</a></li>
            <? if ($_SESSION['yjwatsp_user_master_id'] == 1) { ?>
              <li <? if ($site_page_name == 'approve_whatsapp_no_api') { ?>class="active" <? } ?>><a class="nav-link"
                  href="approve_whatsapp_no_api">Approve Sender ID
                  <? if ($waiting_senderid > 0) { ?><span class="badge badge-success"
                      style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                      <?= $waiting_senderid ?>
                    </span>
                  <? } ?>
                </a></li>
            <? } */ ?>
            <li <? if ($site_page_name == 'create_template') { ?>class="active" <? } ?>>
              <a class="nav-link" href="create_template">Create </a>
            </li>

            <li <? if ($site_page_name == 'template_list') { ?>class="active" <? } ?>>
              <a class="nav-link" href="template_list">List</a>
            </li>
            <? if ($_SESSION['yjwatsp_user_master_id'] == 1 && $_SESSION['yjwatsp_user_id'] == 1) { ?>
              <li <? if ($site_page_name == 'approve_template') { ?>class="active" <? } ?>><a class="nav-link"
                  href="approve_template">Approve
                  <? if ($waiting_template > 0) { ?><span class="badge badge-success"
                      style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                      <?= $waiting_template ?>
                    </span>
                  <? } ?>
                </a></li>
            <? } ?>
          <? } ?>

          <? if ($_SESSION['yjwatsp_user_master_id'] != 1) { ?>
            <!-- <li <? if ($site_page_name == 'purchase_message_credit') { ?>class="active" <? } ?>><a class="nav-link"
                href="purchase_message_credit">Purchase Message Credit</a></li>
            <li <? if ($site_page_name == 'purchase_message_list') { ?>class="active" <? } ?>><a class="nav-link"
                href="purchase_message_list">Payment History</a></li> -->
          <? } ?>
        </ul>
      </li>

      <li <? if ($site_page_name == 'manual_report_upload' or $site_page_name == 'whatsapp_list' or $site_page_name == 'compose_template_rcs' or $site_page_name == 'template_rcs_list' or $site_page_name == 'messenger_responses' or $site_page_name == 'compose_message_approval') { ?>class="dropdown active" <? } else { ?>class="dropdown" <? } ?>>
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-comment"></i> <span>Campaign</span>
	<? if ($_SESSION['yjwatsp_user_master_id'] == 1 && $waiting_compose > 0) { ?><span class="badge badge-success"
              style="width: auto; min-width:30px; font-weight: bold; margin-left: 10px; margin-right: 20px; line-height: 20px;">
              <?= $waiting_compose ?>
            </span>
          <? } ?>
	</a>
        <ul class="dropdown-menu">

          <? if ($_SESSION['yjwatsp_user_permission'] == 1 or $_SESSION['yjwatsp_user_permission'] == 2) { ?>
            <li <? if ($site_page_name == 'compose_template_rcs') { ?>class="active" <? } ?>><a class="nav-link"
                href="compose_template_rcs">Compose</a></li>
            <li <? if ($site_page_name == 'template_rcs_list') { ?>class="active" <? } ?>><a class="nav-link"
                href="template_rcs_list">Campaign List</a></li>
                <? if ($_SESSION['yjwatsp_user_master_id'] == 1) { ?>
              <!-- <li class="menu-header">OTP</li> -->
              <li <? if ($site_page_name == 'compose_message_approval') { ?>class="active" <? } ?>><a class="nav-link"
                href="compose_message_approval">Approve
		  <? if ($_SESSION['yjwatsp_user_master_id'] == 1 && $waiting_compose > 0) { ?><span class="badge badge-success"
                      style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                      <?= $waiting_compose ?>
                    </span>
                  <? } ?>
		</a></li>
            <? } ?>
            <li <? if ($site_page_name == 'messenger_responses') { ?>class="active" <? } ?>><a class="nav-link"
                href="messenger_responses">Messenger Response</a></li>
          <? } ?>
        </ul>
      </li>

      <? /*if ($_SESSION['yjwatsp_user_master_id'] == 1 or $_SESSION['yjwatsp_user_master_id'] == 2 or $_SESSION['yjwatsp_user_master_id'] == 3) { ?>
        <? if ($_SESSION['yjwatsp_user_master_id'] == 1 ) { ?>
          <li <? if ($site_page_name == 'message_credit_list' or $site_page_name == 'request_demo_list' or $site_page_name == 'activation_payment_list' or $site_page_name == 'message_credit' or $site_page_name == 'approve_payment' or $site_page_name == 'manage_users_list' or $site_page_name == 'manage_users' or $site_page_name == 'user_repository' or $site_page_name == 'view_onboarding') { ?>class="dropdown active" <? } else { ?>class="dropdown" <? } ?>>
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-crown"></i>
              <span style="margin-left: 10px;">Admin </span>
              <?/* if ($waiting_approval > 0 || $waiting_users > 0) { ?><span class="badge badge-success"
                  style="width: auto; min-width:30px; font-weight: bold; margin-left: 10px; margin-right: 20px; line-height: 20px;">
                  <?= $waiting_users ?>
                </span>
              <? } ?>
            </a>
            <ul class="dropdown-menu">
              <?/*   <li <? if ($site_page_name == 'approve_payment' or $site_page_name == 'message_credit') { ?>class="active" <? } ?>><a class="nav-link" href="approve_payment">Approve Payment
                    <? if ($waiting_approval > 0) { ?><span class="badge badge-success"
                        style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                        <?= $waiting_approval ?>
                      </span>
                    <? } ?>
                  </a></li>  ?>
              <li <? if ($site_page_name == 'message_credit_list') { ?>class="active" <? } ?>><a class="nav-link"
                  href="message_credit_list">Message Credit List</a></li>

              <? if ($_SESSION['yjwatsp_user_master_id'] == 1) { ?>
                <?/*<li <? if ($site_page_name == 'manage_users_list' or $site_page_name == 'manage_users' or $site_page_name == 'user_repository' or $site_page_name == 'view_onboarding') { ?>class="active" <? } ?>><a
                    class="nav-link" href="manage_users_list">Manage Users
                    <? if ($waiting_users > 0) { ?><span class="badge badge-success"
                        style="width: auto; min-width:30px; margin-left: 10px; line-height: 20px; font-weight: bold;">
                        <?= $waiting_users ?>
                      </span>
                    <? } ?>
                  </a></li>
                <!-- <li <? if ($site_page_name == 'activation_payment_list') { ?>class="active" <? } ?>><a class="nav-link"
                    href="activation_payment_list">Activation Payment List</a></li>
                <li <? if ($site_page_name == 'request_demo_list') { ?>class="active" <? } ?>><a class="nav-link"
                    href="request_demo_list">Demo Request List</a></li> -->
              <? } ?>
            </ul>
          </li>
        <? }*/ ?>

        <li <? if ($site_page_name == 'generate_report' or $site_page_name == 'manual_report_upload' or $site_page_name == 'generate_report_list' or $site_page_name == 'summary_report' or $site_page_name == 'details_report' or $site_page_name == 'business_summary_report' or $site_page_name == 'business_details_report') { ?>class="dropdown active" <? } else { ?>class="dropdown" <? } ?>>
          <a href="#" class="nav-link has-dropdown"><i class="fas fa-chart-bar"></i> <span>Reports</span></a>
          <ul class="dropdown-menu">
                <? if (($_SESSION['yjwatsp_user_id'] == 1) && ($_SESSION['yjwatsp_user_master_id'] == 1)) { ?>
            <li <? if ($site_page_name == 'manual_report_upload') { ?>class="active" <? } ?>><a class="nav-link"
                href="manual_report_upload">Upload Report</a></li>
          <? } ?>
          <? if ($_SESSION['yjwatsp_user_master_id'] == 1) { ?>
            <li <? if ($site_page_name == 'generate_report_list') { ?>class="active" <? } ?>><a class="nav-link"
                href="generate_report_list">Generate Report</a></li>
          <? } ?>	
            <? if ($_SESSION['yjwatsp_user_permission'] == 1 or $_SESSION['yjwatsp_user_permission'] == 2) { ?>
              <li <? if ($site_page_name == 'summary_report') { ?>class="active" <? } ?>><a class="nav-link"
                  href="summary_report">Summary Report</a></li>
              <li <? if ($site_page_name == 'business_details_report') { ?>class="active" <? } ?>><a class="nav-link"
                  href="business_details_report">Detailed Report</a></li>
            <? } ?>
          </ul>
        </li>
      <? } ?>
    </ul>

  </aside>
</div>

