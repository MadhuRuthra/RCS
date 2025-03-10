<?php
/*
Authendicated users only allow to view this Dashboard page.
This page is used to view the count of the Whatsapp summary list 
of the logged in user and their team members.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
*/

session_start(); // start session
error_reporting(0); // The error reporting function

include_once('api/configuration.php');// Include configuration.php
extract($_REQUEST); // Extract the Request

// If the Session is not available redirect to index page
if ($_SESSION['yjwatsp_user_id'] == "") { ?>
  <script>window.location = "index";</script>
  <?php exit();
}

$site_page_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME); // Collect the Current page name
site_log_generate("Dashboard Page : User : " . $_SESSION['yjwatsp_user_name'] . " access the page on " . date("Y-m-d H:i:s"));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Dashboard ::
    <?= $site_title ?>
  </title>
  <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">

<script src="assets/js/canvasjs.min.js"></script>
<!-- <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script> -->


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
          <div id="id_dashboard_count"></div>
		<div style="margin-top:50px; width: 100% !important; height: 400px !important;" >
    <canvas id="myChart1" style="width: 100% !important; height: 400px !important;"></canvas></div>
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
  <script src="assets/modules/jquery.sparkline.min.js"></script>
  <script src="assets/modules/chart.min.js"></script>
  <script src="assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
  <script src="assets/modules/summernote/summernote-bs4.js"></script>
  <script src="assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>

  <!-- Page Specific JS File -->
  <script src="assets/js/page/index.js"></script>

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js'></script>
  <script>
    // On loading the page, this function will call
    $(document).ready(function () {
      find_dashboard();
    });

    var user_name, available_messages, total_msg, total_success, total_waiting,tot_failed;
    var available_messagesArray, user_nameArray, total_successArray, total_waitingArray,tot_failedArray;

    function find_dashboard() {
  $.ajax({
    type: 'post',
    url: "ajax/display_functions.php?call_function=dashboard_counts",
    dataType: 'html',
    success: function (response) {
      $("#id_dashboard_count").html(response);

      var user_name = document.getElementById('user_name').value;
        var available_messages = document.getElementById('available_messages').value;
        var total_msg = document.getElementById('total_msg').value;
        var total_success = document.getElementById('total_success').value;
        var total_waiting = document.getElementById('total_waiting').value;
        var tot_failed = document.getElementById('tot_failed').value;

        var user_nameArray = JSON.parse(user_name);
        // console.log(user_nameArray);
        var available_messagesArray = JSON.parse(available_messages);
        var total_msgArray = JSON.parse(total_msg);
        var total_successArray = JSON.parse(total_success);
        var total_waitingArray = JSON.parse(total_waiting);
        var tot_failedArray = JSON.parse(tot_failed);

        // Convert each element to a number
        total_msgArray = total_msgArray.map(function (value) {
          return Number(value);
        });
        total_successArray = total_successArray.map(function (value) {
          return Number(value);
        });
        total_waitingArray = total_waitingArray.map(function (value) {
          return Number(value);
        });
        tot_failedArray = tot_failedArray.map(function (value) {
          return Number(value);
        });

       // bar Chart
var barChartData = {
  labels: user_nameArray,
  datasets: [
    {
      label: "Total MSG",
      backgroundColor: 'rgba(53, 55, 75, 0.6)',
      borderColor: 'rgba(53, 55, 75, 1)',
      borderWidth: 1,
      data: total_msgArray
    },
    {
      label: "Inprogress MSG",
      backgroundColor: 'rgba(99, 132, 0, 0.6)',
      borderColor: 'rgba(99, 132, 0, 1)',
      borderWidth: 1,
      data: total_waitingArray
    },
    {
      label: "Failed MSG",
      backgroundColor: 'rgba(80, 114, 123, 0.6)',
      borderColor: 'rgba(80, 114, 123, 1)',
      borderWidth: 1,
      data: tot_failedArray
    },
    {
      label: "Delivered MSG",
      backgroundColor: 'rgba(120, 160, 131, 0.6)',
      borderColor: 'rgba(120, 160, 131, 1)',
      borderWidth: 1,
      data: total_successArray
    }
  ]
};

var chartOptions = {
  responsive: true,
  legend: {
    position: "top"
  },
  title: {
    display: true,
    text: "Messages"
  },
  scales: {
    yAxes: [{
      ticks: {
        beginAtZero: true
      }
    }]
  }
}
// document.addEventListener("DOMContentLoaded", function() {
  var ctx = document.getElementById("myChart1");
  if (ctx) {
    var chart = new Chart(ctx, {
      type: "bar",
      data: barChartData,
      options: chartOptions
    });
  } else {
    console.error("Canvas element with ID 'myChart1' not found.");
  }
    },
    error: function (response, status, error) {
    }
  });
}
  </script>
</body>

</html>

