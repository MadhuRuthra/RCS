<?php
/*
This page has some functions which is access from Frontend.
This page is act as a Backend page which is connect with Node JS API and PHP Frontend.
It will collect the form details and send it to API.
After get the response from API, send it back to Frontend.

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

// Step 3: Output the result in 3-digit format
// echo "Today's Julian date in 3-digit format: " . str_pad($julianDate, 3, '0', STR_PAD_LEFT);
$year = date("Y");
$julian_dates = str_pad($julianDate, 3, '0', STR_PAD_LEFT);
$hour_minutes_seconds = date("His");
$random_generate_three = rand(100, 999);

// Dashboard Page dashboard_count - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "dashboard_countss") {
  site_log_generate("Dashboard Page : User : " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');
  // To Send the request  API
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
  }';

  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';  // add the bearer
  // It will call "dashboard" API to verify, can we access for the dashboard details
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/dashboard/dashboard',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'

      ),
    )
  );

  // Send the data into API and execute 
  site_log_generate("Dashboard Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');

  //echo $response;
  $response = curl_exec($curl);
  curl_close($curl);

  // After got response decode the JSON result
  $state1 = json_decode($response, false);
  site_log_generate("Dashboard Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
  $total_msg = 0;
  $total_success = 0;
  $total_failed = 0;
  $total_invalid = 0;
  $total_waiting = 0;
  $total_delivered = 0;

  $total_msg = array();
  $total_success = array();
  $total_waiting = array();
  $tot_failed = array();
  $available_messages = array();
  $user_name = array();
  $total_delivered = array();
  // To get the one by one data
 if ($state1->response_status == 403 || $response == '') { ?>
          <script>
            window.location = "index"
          </script>
        <? }
 else if ($state1->response_code == 1) { // If the response is success to execute this condition
    for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
      //Looping the indicator is less than the count of report.if the condition is true to continue the process.if the condition is false to stop the process 
      $header_title = $state1->report[$indicator]->header_title;
      $user_id = $state1->report[$indicator]->user_id;
      $user_master_id = $state1->report[$indicator]->user_master_id;
      $api_key = $state1->report[$indicator]->api_key;
      $total_failed = $state1->report[$indicator]->total_failed;
      $total_invalid = $state1->report[$indicator]->total_invalid;

      $user_name[] = $state1->report[$indicator]->user_name;
      $available_messages[] = $state1->report[$indicator]->available_messages;
      $total_msg[] = $state1->report[$indicator]->total_msg;
      $total_success[] = $state1->report[$indicator]->total_success;
      $total_delivered[] = $state1->report[$indicator]->total_delivered;
      $total_waiting[] = $state1->report[$indicator]->total_waiting;
      $tot_failed[] = $total_failed + $total_invalid;
    }
    ?>
    <script>
      window.onload = function () {
        var available_messages = <?php echo json_encode($available_messages); ?>;
        var total_msg = <?php echo json_encode($total_msg); ?>;
        var total_success = <?php echo json_encode($total_success); ?>;
	var total_delivered = <?php echo json_encode($total_delivered); ?>;
        var total_waiting = <?php echo json_encode($total_waiting); ?>;
        var tot_failed = <?php echo json_encode($tot_failed); ?>;
        var user_names = <?php echo json_encode($user_name); ?>;

        if (user_names && user_names.length > 0) {
          // Create data points for each user
          var total_msgDataPoints = user_names.map(function (user, index) {
            return { label: user + " (" + available_messages[index] + ")", y: total_msg[index] };
          });

          var inprocessingDataPoints = user_names.map(function (user, index) {
            return { label: user + " (" + available_messages[index] + ")", y: total_waiting[index] };
          });

          var failedDataPoints = user_names.map(function (user, index) {
            return { label: user + " (" + available_messages[index] + ")", y: tot_failed[index] };
          });

          var deliveredDataPoints = user_names.map(function (user, index) {
            return { label: user + " (" + available_messages[index] + ")", y: total_delivered[index] };
          });


/* var ctx = document.getElementById("chartContainer").getContext('2d');

var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Total MSG",	"Inprogress MSG",	"Failed MSG",	"Delivered MSG"],
        datasets: [{
            label: 'TDP', // Name the series
            data: [20,	20,	0,	0], // Specify the data values array
            fill: true,
            borderColor: '#2196f3', // Add custom color border (Line)
            backgroundColor: '#2196f3', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'User 1', // Name the series
            data: [16,	5,	1,	10], // Specify the data values array
            fill: true,
            borderColor: '#4CAF50', // Add custom color border (Line)
            backgroundColor: '#4CAF50', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
      responsive: true, // Instruct chart js to respond nicely.
      maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height 
    }
}); */


          var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
              text: "Summary"
            },
            axisX: {
              title: "User",
              interval: 1
            },
            axisY: {
              title: "Number of Messages"
            },
            legend: {
              verticalAlign: "top",
              horizontalAlign: "right",
              dockInsidePlotArea: true
            },
            toolTip: {
              shared: true
            },
            data: [
              {
                name: "Total Messages",
                showInLegend: true,
                legendMarkerType: "square",
                type: "area",
                color: "rgba(255,255,0,0.7)",
                markerSize: 0,
                dataPoints: total_msgDataPoints,
              }, {
                name: "In processing",
                showInLegend: true,
                legendMarkerType: "square",
                type: "area",
                color: "rgba(40,175,101,0.6)",
                markerSize: 0,
                dataPoints: inprocessingDataPoints,
              },
              {
                name: "Failed",
                showInLegend: true,
                legendMarkerType: "square",
                type: "area",
                color: "rgba(255,0,0,0.7)",
                markerSize: 0,
                dataPoints: failedDataPoints,
              },
              {
                name: "Delivered",
                showInLegend: true,
                legendMarkerType: "square",
                type: "area",
                color: "rgba(0,0,255,0.7)",
                markerSize: 0,
                dataPoints: deliveredDataPoints,
              }]
          });
          chart.render();
        } else {
          console.log("User names array is empty or null.");
        }
      }

    </script>
<? }
}

// Dashboard Page dashboard_count - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "dashboard_counts") {
  site_log_generate("Dashboard Page : User : " . $_SESSION['yjwatsp_user_name'] . " access this page on " . date("Y-m-d H:i:s"), '../');
  // To Send the request  API
  $replace_txt = '{
    "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
  }';

  $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';  // add the bearer
  // It will call "dashboard" API to verify, can we access for the dashboard details
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/dashboard/dashboard',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        $bearer_token,
        'Content-Type: application/json'

      ),
    )
  );

  // Send the data into API and execute 
  site_log_generate("Dashboard Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');

  //echo $response;
  $response = curl_exec($curl);
  curl_close($curl);

  // After got response decode the JSON result
  $state1 = json_decode($response, false);
  site_log_generate("Dashboard Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
  $total_msg = 0;
  $total_success = 0;
  $total_failed = 0;
  $total_invalid = 0;
  $total_waiting = 0;
  $total_delivered = 0;

  $total_msg = array();
  $total_success = array();
  $total_delivered = array();
  $total_waiting = array();
  $tot_failed = array();
  $tot_read = array();
  $available_messages = array();
  $user_name = array();
  // To get the one by one data
 if ($state1->response_status == 403 || $response == '') { ?>
          <script>
            window.location = "index"
          </script>
        <? }
 else if ($state1->response_code == 1) { // If the response is success to execute this condition
    for ($indicator = 0; $indicator < count($state1->report); $indicator++) {
      //Looping the indicator is less than the count of report.if the condition is true to continue the process.if the condition is false to stop the process 
      $header_title = $state1->report[$indicator]->header_title;
      $user_id = $state1->report[$indicator]->user_id;
      $user_master_id = $state1->report[$indicator]->user_master_id;
      $api_key = $state1->report[$indicator]->api_key;
      $total_failed = $state1->report[$indicator]->total_failed;
      $total_invalid = $state1->report[$indicator]->total_invalid;

      $user_name[] = $state1->report[$indicator]->user_name;
      $available_messages[] = $state1->report[$indicator]->available_messages;
      $total_msg[] = $state1->report[$indicator]->total_msg;
      $total_success[] = $state1->report[$indicator]->total_success;
      $total_delivered[] = $state1->report[$indicator]->total_delivered;
      $total_waiting[] = $state1->report[$indicator]->total_waiting;
      $tot_failed[] = $total_failed + $total_invalid;
      $tot_read[] = $state1->report[$indicator]->total_read;

    }
    
    ?>
    			<input type="hidden" class="form-control" name='user_name' id='user_name'
				value='<?= json_encode($user_name) ?>' />

    	<input type="hidden" class="form-control" name='available_messages' id='available_messages'
				value='<?= json_encode($available_messages) ?>' />

			<input type="hidden" class="form-control" name='total_msg' id='total_msg'
				value='<?= json_encode($total_msg) ?>' />
                            <input type="hidden" class="form-control" name='total_read' id='total_read'
                                value='<?= json_encode($total_read) ?>' />

			<input type="hidden" class="form-control" name='total_success' id='total_success'
				value='<?= json_encode($total_success) ?>' />
			<input type="hidden" class="form-control" name='total_delivered' id='total_delivered'
                                value='<?= json_encode($total_delivered) ?>' />

			<input type="hidden" class="form-control" name='total_waiting' id='total_waiting'
				value='<?= json_encode($total_waiting) ?>' />
        <input type="hidden" class="form-control" name='tot_failed' id='tot_failed'
				value='<?= json_encode($tot_failed) ?>' />
<? }
}


// template_list Page template_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "template_list") {
  site_log_generate("Template List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table            ?>
  <table class="table table-striped text-center" id="table-1">
    <thead>
      <tr class="text-center">
        <th>#</th>
        <th>User</th>
        <th>Template Label</th>
        <th>Template Name</th>
       <th>Campaign Type</th>
       <th>Communication Type</th>
        <?/*<th>Template Category</th>*/?>
        <th>Status</th>
        <th>Entry Date</th>
       <?/* <th>Approved Date</th> */?>
        <th>Action</th>
      </tr>
    </thead>
    <tbody style=" text-align: center;">
      <?
      // To Send the request API
      $replace_txt = '{
      "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
    }';

      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "p_template_list" API to verify, can we can we allow to view the template list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/p_template_list',
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
      site_log_generate("Template List Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      // echo($response);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      // print_r($sms);
      site_log_generate("Template List Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');

      $indicatori = 0;

      if ($sms->num_of_rows > 0) {
        // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $approve_date = '-';
          // To get the one by one data
          if ($sms->templates[$indicator]->template_entdate != '' and $sms->templates[$indicator]->template_entdate != '00-00-0000 12:00:00 AM') {
            $entry_date = date('d-m-Y h:i:s A', strtotime($sms->templates[$indicator]->template_entdate));
          }
          if ($sms->templates[$indicator]->approve_date != '' and $sms->templates[$indicator]->approve_date != '0000-00-00 00:00:00' and $sms->templates[$indicator]->approve_date != '00-00-0000 12:00:00 AM') {
            $approve_date = date('d-m-Y h:i:s A', strtotime($sms->templates[$indicator]->approve_date));
          }
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->templates[$indicator]->user_name; ?>
            </td>
            <td>
              <?= $sms->templates[$indicator]->template_label; ?>
            </td>
            <td>
              <?= $sms->templates[$indicator]->template_name; ?>
            </td>
           <td>
           <?= strtoupper($sms->templates[$indicator]->campaign_type); ?>

            </td>

            <td>
              <?= $sms->templates[$indicator]->template_category; ?>
            </td>
           <?/* <td class="text-left">
              <?= $sms->templates[$indicator]->template_category ?>
            </td>*/?>
            <td id='id_template_status_<?= $indicatori ?>'>
              <? if ($sms->templates[$indicator]->template_status == 'Y') { ?><a href="#!"
                  class="btn btn-outline-success btn-disabled" style="width:90px; text-align:center">Approved</a>
              <? } elseif ($sms->templates[$indicator]->template_status == 'N') { ?><a href="#!" class="btn
                btn-outline-warning btn-disabled" style="width:90px; text-align:center">Inactive</a>
              <? } elseif ($sms->templates[$indicator]->template_status == 'R') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled" style="width:90px; text-align:center">Rejected</a>
              <? } elseif ($sms->templates[$indicator]->template_status == 'F') { ?><a href="#!" class="btn
                  btn-outline-dark btn-disabled" style="width:90px; text-align:center">Failed</a>
              <? } elseif ($sms->templates[$indicator]->template_status == 'D') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled" style="width:90px; text-align:center">Deleted</a>
              <? } elseif ($sms->templates[$indicator]->template_status == 'S') { ?><a href="#!"
                  class="btn btn-outline-info btn-disabled" style="width:90px; text-align:center">Waiting</a>
              <? } ?>
            </td>
            <td>
            <?= date('d-m-Y h:i:s A', strtotime($sms->templates[$indicator]->template_entdate)); ?>
            </td>
           <? /* <td>
              <?= $approve_date ?>
            </td> */?>
            <td><a href="#!"
   onclick="call_getsingletemplate(
     `<?= htmlspecialchars($sms->templates[$indicator]->template_message, ENT_QUOTES, 'UTF-8') ?>`,
     '<?= htmlspecialchars($sms->templates[$indicator]->template_category, ENT_QUOTES, 'UTF-8') ?>'
   )">View</a>

              <? if ($sms->templates[$indicator]->template_status != 'D') { ?>/
                <a href="#!" onclick="remove_template_popup('<?= $sms->templates[$indicator]->unique_template_id ?>',
              '<?= $indicatori ?>')">Delete</a>
              <? } ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Template List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Template List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General Datatable JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 4, 5, 6]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 3, 4]
      }
      ]
    });
  </script>
  <?
}
// template_list Page template_list - End

// whatsapp_no_api_list Page whatsapp_no_api_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "whatsapp_no_api_list") {
  site_log_generate("Manage Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table            ?>
  <table class="table table-striped text-center" id="table-1">
    <thead>
      <tr class="text-center">
        <th>#</th>
        <th>User</th>
        <th>Mobile No</th>
        <th>Profile Details</th>
        <th>Available Credits</th>
        <th>Used Credits</th>
        <th>Status</th>
        <th>Entry Date</th>
        <th>Approved Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
        "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
      }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "p_template_list" API to verify, can we can we allow to view the template list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/sender_id_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      $response = curl_exec($curl);
      site_log_generate("Manage Sender ID List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');

      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Manage Sender ID List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) {
        // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->sender_id[$indicator]->whatspp_config_entdate));
          if ($sms->sender_id[$indicator]->whatspp_config_apprdate != '' and $sms->sender_id[$indicator]->whatspp_config_apprdate != '0000-00-00 00:00:00') {
            $approved_date = date('d-m-Y h:i:s A', strtotime($sms->sender_id[$indicator]->whatspp_config_apprdate));
          }
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= strtoupper($sms->sender_id[$indicator]->user_name) ?>
            </td>
            <td>
              <?= $sms->sender_id[$indicator]->country_code . $sms->sender_id[$indicator]->mobile_no ?>
            </td>
            <td>
              <? echo $sms->sender_id[$indicator]->wht_display_name . "<br>";
              if ($sms->sender_id[$indicator]->wht_display_logo != '') {
                echo "<img src='uploads/whatsapp_images/" . $sms->sender_id[$indicator]->wht_display_logo . "' style='width:100px; max-height: 200px;'>";
              } ?>
            </td>
            <td><b>
                <? if ($sms->sender_id[$indicator]->whatspp_config_status == 'Y') {
                  echo ($sms->sender_id[$indicator]->available_credit);
                } else {
                  echo "0";
                } ?>
              </b></td>
            <td><b>
                <? if ($sms->sender_id[$indicator]->whatspp_config_status == 'Y') {
                  echo $sms->sender_id[$indicator]->sent_count;
                } else {
                  echo "0";
                } ?>
              </b></td>
            <td>
              <? if ($sms->sender_id[$indicator]->whatspp_config_status == 'Y') { ?> <a href="#!"
                  class="btn btn-outline-success btn-disabled" style=" width : 100px; text-align:center">Active</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'D') { ?> <a href="#!"
                  class=" btn btn-outline-danger btn-disabled" style="width:100px; text-align:center">Deleted</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'B') { ?> <a href="#!"
                  class="btn btn-outline-dark btn-disabled" style="width:100px; text-align:center">Blocked</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'N') { ?> <a href="#!"
                  class="btn btn-outline-danger btn-disabled" style=" width : 100px; text-align:center">Inactive</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'M') { ?> <a href="#!"
                  class=" btn btn-outline-danger btn-disabled" style="width:100px; text-align:center">Mobile No
                  Mismatch</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'I') { ?> <a href="#!"
                  class="btn btn-outline-warning btn-disabled" style=" width : 100px; text-align:center">Invalid</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'P') { ?><a href="#!"
                  class="btn btn-outline-info btn-disabled" style="width:100px; text-align:center">Processing</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'R') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled" style="width:100px; text-align:center">Rejected</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'X') { ?> <a href="#!"
                  class=" btn btn-outline-primary btn-disabled" style="width:100px; text-align:center">Need
                  Rescan</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'L') { ?> <a href="#!"
                  class=" btn btn-outline-info btn-disabled" style="width:100px; text-align:center">Linked</a>
              <? } elseif ($sms->sender_id[$indicator]->whatspp_config_status == 'U') { ?> <a href="#!"
                  class="btn btn-outline-warning btn-disabled" style="width:100px; text-align:center">Unlinked</a>
              <? } ?>
            </td>
            <td>
              <?= $template_entdate ?>
            </td>
            <td>
              <?= $approved_date ?>
            </td>
            <td id='id_approved_lineno_<?= $indicatori ?>'>
              <? if ($sms->sender_id[$indicator]->whatspp_config_status != 'D' && $sms->sender_id[$indicator]->whatspp_config_status != 'R') { ?>
                <button type="button" title="Delete Sender ID"
                  onclick="remove_senderid_popup('<?= $sms->sender_id[$indicator]->whatspp_config_id ?>', 'D', '<?= $indicatori ?>')"
                  class="btn btn-icon btn-danger" style="padding: 0.3rem 0.41rem !important;">Delete</button>
              <? } else { ?>
                <a href="#!" class="btn btn-outline-light btn-disabled"
                  style="padding: 0.3rem 0.41rem !important;cursor: not-allowed;">Delete</a>
              <? } ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Manage Sender ID List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Manage Sender ID List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 4, 5, 6, 7, 8]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 5, 7, 8, 9]
      }
      ]
    });
  </script>
  <?
}
// whatsapp_no_api_list Page whatsapp_no_api_list - End

// approve_whatsapp_no_api Page approve_whatsapp_no_api - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "approve_whatsapp_no_api") {
  site_log_generate("Approve Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table               ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Mobile No</th>
        <th>Phone No ID</th>
        <th>Business Account ID</th>
        <th>Bearer Token</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      $replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "approve_whatsapp_no_api" API to verify, can we can we allow to view the approve_whatsapp_no_api
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/approve_whatsapp_no_api',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute  
      $response = curl_exec($curl);
      site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td style="text-align: center;">
              <?= $mobile_number = $sms->report[$indicator]->country_code . $sms->report[$indicator]->mobile_no ?>
            </td>

            <td><input type='text' class="form_control" autofocus id="phone_number_id_<?= $indicatori ?>"
                name="phone_number_id_<?= $indicatori ?>" value="<?= $sms->report[$indicator]->phone_number_id ?>"
                placeholder="Phone No ID" maxlength="15"
                onkeypress="return (event.charCode !=8 && event.charCode ==0 || ( event.charCode == 46 || (event.charCode >= 48 && event.charCode <= 57)))"
                style="width: 100%"></td>
            <td><input type='text' class="form_control" id="whatsapp_business_acc_id_<?= $indicatori ?>"
                name="whatsapp_business_acc_id_<?= $indicatori ?>"
                value="<?= $sms->report[$indicator]->whatsapp_business_acc_id ?>" placeholder="Business Account ID"
                maxlength="15"
                onkeypress="return (event.charCode !=8 && event.charCode==0 || ( event.charCode==46 ||(event.charCode >=48 && event.charCode <=57)))"
                style="width: 100%"></td>
            <td><input type='text' class="form_control" id="bearer_token_value_<?= $indicatori ?>"
                name="bearer_token_value_<?= $indicatori ?>" value="<?= $sms->report[$indicator]->bearer_token ?>"
                placeholder="Bearer Token" maxlength="300" style="text-transform: uppercase; width: 100%"
                onkeypress="return /^[a-zA-Z0-9]*$/.test(String.fromCharCode(event.charCode))">
            </td>

            <td style="text-align: center;">
              <?
              switch ($sms->report[$indicator]->whatspp_config_status) {
                case 'N':
                  ?><a href="#!" class="btn btn-outline-primary btn-disabled">New</a>
                  <?
                  break;

                case 'L':
                  ?><a href="#!" class="btn btn-outline-info btn-disabled">Whatsapp Linked</a>
                  <?
                  break;
                case 'U':
                  ?><a href="#!" class="btn btn-outline-warning btn-disabled">Whatsapp Unlinked</a>
                  <?
                  break;
                case 'X':
                  ?><a href="#!" class="btn btn-outline-primary btn-disabled">Rescan</a>
                  <?
                  break;

                case 'Y':
                  ?><a href="#!" class="btn btn-outline-success btn-disabled">Super Admin Approved</a>
                  <?
                  break;
                case 'R':
                  ?><a href="#!" class="btn btn-outline-danger btn-disabled">Super Admin Rejected</a>
                  <?
                  break;

                default:
                  ?><a href="#!" class="btn btn-outline-dark btn-disabled">Invalid</a>
                  <?
                  break;
              } ?>
            </td>
            <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
              <div class="btn-group mb-3" role="group" aria-label="Basic example">
                <button type="button" title="Approve"
                  onclick="func_save_phbabt_popup('<?= $sms->report[$indicator]->whatspp_config_id ?>', 'Y', '<?= $indicatori ?>','phone_number_id','whatsapp_business_acc_id','bearer_token_value', '<?= $mobile_number ?>')"
                  class="btn btn-icon btn-success"><i class="fas fa-check"></i></button>
                <button type="button" title="Reject"
                  onclick="change_status_popup('<?= $sms->report[$indicator]->whatspp_config_id ?>', 'R', '<?= $indicatori ?>')"
                  class="btn btn-icon btn-danger"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 6]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 5, 7]
      }
      ]
    });
  </script>
  <?
}
// approve_whatsapp_no_api Page approve_whatsapp_no_api - End

// approve_template Page approve_template - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "approve_template") {
  site_log_generate("Approve Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Template Name</th>
        <th>Communication Type</th>
        <th>Campaign Type</th>
        <th>Template Id</th>
        <th>Media box</th>
        <th>Template Date</th>
        <th>Status</th>
        <th>Action</th>
	<th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      $replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "approve_template" API to verify, can we can we allow to view the approve_template
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/approve_template_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute  
      $response = curl_exec($curl);
      // echo $response;

      site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      // print_r($sms);
      site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $template_entdate = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->template_entdate));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td style="text-align: center;">
              <?= $sms->report[$indicator]->unique_template_id ?>
            </td>
            <td style="text-align: center;">
              <?= $sms->report[$indicator]->template_category ?>
            </td>
            <td style="text-align: center;">
            <?= strtoupper($sms->report[$indicator]->campaign_type) ?>
            </td>
         <?/*  <td style="text-align: center;">
              <?= $sms->report[$indicator]->templateid ?>
            </td> */ ?>

           <td style="text-align:center">
    <? if ($sms->report[$indicator]) { ?>
        <input type='text' class="form_control"
                id="temp_id<?= $indicator ?>" name="temp_name<?= $indicator ?>"
              oninput="getData(event, 'temp_id',<?= $indicator ?>)" onkeypress="return (event.charCode !=8 && event.charCode ==0 ||  (event.charCode >= 48 && event.charCode <= 57))"
               placeholder="Template id" maxlength="15"
               style="width: 80%" required>
    <? } else { ?> 
        <!-- Handle the case where $sms->report[$indicator] does not exist -->
    <? } ?>
</td>

<td style="text-align:center">
    <? if ($sms->report[$indicator]) { ?>
        <input type='text' class="form_control"
               id="media_bx<?= $indicator ?>" name="media_bx<?= $indicator ?>"
               oninput="getData(event,'media_id', <?= $indicator ?>)"
               placeholder="Media box" maxlength="50"
               style="width: 80%" required>
    <? } else { ?> 
        <!-- Handle the case where $sms->report[$indicator] does not exist -->
    <? } ?>
</td>

            <td style="text-align: center;">
              <?= $template_entdate ?>
            </td>

            <td style="text-align: center;">
              <? if ($sms->report[$indicator]->template_status == 'N') { ?>
                <a href="#!" class="btn btn-outline-danger btn-disabled">Not approve</a>
              <? } ?>
            </td>
	    <td><a href="#!"
      onclick="call_getsingletemplate(
     `<?= htmlspecialchars($sms->report[$indicator]->template_message, ENT_QUOTES, 'UTF-8') ?>`,
     '<?= htmlspecialchars($sms->report[$indicator]->template_category, ENT_QUOTES, 'UTF-8') ?>'
   )">View</a>
            </td>
            <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
              <div class="btn-group mb-3" role="group" aria-label="Basic example">
              <button type="button" title="Approve"
        onclick="approve_popup('<?= $sms->report[$indicator]->unique_template_id ?>', 'Y', '<?= $indicatori ?>', '<?= $indicator ?>')"
        class="btn btn-icon btn-success"><i class="fas fa-check"></i></button>
<button type="button" title="Reject"
        onclick="change_status_popup('<?= $sms->report[$indicator]->unique_template_id ?>', 'R', '<?= $indicatori ?>')"
        class="btn btn-icon btn-danger"><i class="fas fa-times"></i></button>
              </div>
              <div id="approve_error_<?= $indicatori ?>" style="color: red; display: none;"></div>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 4, 5, 6]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 5, 7]
      }
      ]
    });
  </script>
  <?
}
// approve_template Page approve_template - End


// approve_compose_message Page approve_compose_message - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "approve_compose_message") {
  site_log_generate("Approve Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table               ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Template Name</th>
        <th>Template category</th>
        <th>Template_language</th>
        <th>Template Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      $replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "approve_compose_message" API to verify, can we can we allow to view the approve_compose_message
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/approve_compose_message',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute  
      $response = curl_exec($curl);
      site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $template_entdate = date('d-m-Y h:i:s A', strtotime($sms->sender_id[$indicator]->template_entdate));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td style="text-align: center;">
              <?= $sms->report[$indicator]->unique_template_id ?>
            </td>

            <td style="text-align: center;">
              <?= $sms->report[$indicator]->template_category ?>
            </td>

            <td style="text-align: center;">
              <?= $sms->report[$indicator]->language_name ?>
            </td>

            <td style="text-align: center;">
              <?= $template_entdate ?>
            </td>

            <td style="text-align: center;">
              <? if ($sms->report[$indicator]->template_status == 'N') { ?>
                <a href="#!" class="btn btn-outline-danger btn-disabled">Not approve</a>
              <? } ?>
            </td>
            <td style="text-align:center;" id='id_approved_lineno_
      <?= $indicatori ?>'>
              <div class="btn-group mb-3" role="group" aria-label="Basic example">
                <button type="button" title="Approve"
                  onclick="approve_popup('<?= $sms->report[$indicator]->unique_template_id ?>', 'Y', '<?= $indicatori ?>')"
                  class="btn btn-icon btn-success"><i class="fas fa-check"></i></button> <!-- <button type="button"
          title="Reject" onclick="change_status_popup('<?= $sms->report[$indicator]->whatspp_config_id ?>', 'R',
          '<?= $indicatori ?>')" class="btn btn-icon btn-danger"><i class="fas fa-times"></i></button> -->
              </div>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
// approve_compose_message Page approve_compose_message - End

// template_rcs_list Page template_rcs_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "template_rcs_list") {
  site_log_generate("Template Whatsapp List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped text-center" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th> 
        <th>Campaign Name</th>
       <th>Campaign Id</th>
        <th>Template Id</th>
        <th>Template Name </th>
        <th>Count</th>
       <? /* <th>Mobile No</th> */?>
        <th>Status</th>
     <th>Entry Date</th> 
<!--        <th>Delivery Date</th> -->
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
        "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
      }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "get_sent_messages_status_list" API to verify, can we can we allow to view the message list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/get_sent_messages_status_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      $response = curl_exec($curl);
      // print_r ($response);
      site_log_generate("Template Whatsapp List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Template Whatsapp List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $increment = 0;
      if ($sms->num_of_rows > 0) {
        // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          $increment++;

          $compose_whatsapp_id = $sms->report[$indicator]->compose_whatsapp_id;
          $user_id = $sms->report[$indicator]->user_id;
          $user_name = $sms->report[$indicator]->user_name;
          $campaign_name = $sms->report[$indicator]->campaign_name;
          $whatsapp_content = $sms->report[$indicator]->whatsapp_content;

          $message_type = $sms->report[$indicator]->message_type;
          $total_mobileno_count = $sms->report[$indicator]->total_mobileno_count;
          $content_char_count = $sms->report[$indicator]->content_char_count;
          $content_message_count = $sms->report[$indicator]->content_message_count;
          $mobile_no = $sms->report[$indicator]->country_code . $sms->report[$indicator]->mobile_no;
          // $template_id = $sms->report[$indicator]->template_id;
          //$sender = $sms->report[$indicator]->sender;
          $sender = isset($sms->report[$indicator]->sender) ? $sms->report[$indicator]->sender : '-';
          $send_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->comwtap_entry_date));
           $send_date =  !empty($send_date) ? $send_date : "-";
          if ($sms->report[$indicator]->response_date != '') {
            $response_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->response_date));
          } else {
            $response_date = '';
          }

          if ($sms->report[$indicator]->delivery_date != '') {
            $delivery_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->delivery_date));
          } else {
            $delivery_date = '';
          }

          if ($sms->report[$indicator]->read_date != '') {
            $read_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->read_date));
          } else {
            $read_date = '';
          }

          $response_status = $sms->report[$indicator]->response_status;
// echo $response_status;
 $templateid = $sms->report[$indicator]->templateid;
 $template_name = $sms->report[$indicator]->template_name;
 $campaign_id = $sms->report[$indicator]->campaign_id;
          $response_message = $sms->report[$indicator]->response_message;
          $response_id = $sms->report[$indicator]->response_id;
          $delivery_status = $sms->report[$indicator]->delivery_status;
          $read_status = $sms->report[$indicator]->read_status;

          $disp_stat = '';
          switch ($response_status) {
            case 'P':
                $disp_stat = '<div class="badge badge-warning">IN PROGRESS</div>';
                break;
                
            case 'V':
            case 'O':
            case 'C':
            case 'Y':
                $disp_stat = '<div class="badge badge-success">COMPLETED</div>';
                break;
        
            case 'F':
                $disp_stat = '<div class="badge badge-danger">FAILED</div>';
                break;
        
            case 'I':
                $disp_stat = '<div class="badge badge-secondary">INVALID</div>';
                break;
        
            case 'R':
                $disp_stat = '<div class="badge badge-danger">REJECTED</div>';
                break;
        
            default:
                $disp_stat = '<div class="badge badge-info">YET TO SEND</div>';
                break;
        }

          $disp_stat1 = '';
          switch ($delivery_status) {
            case 'Y':
              $disp_stat1 = '<div class="badge badge-success">DELIVERED</div>';
              break;

            default:
              $disp_stat1 = '<div class="badge badge-danger">NOT DELIVERED</div>';
              break;
          }

          $disp_stat2 = '';
          switch ($read_status) {
            case 'Y':
              $disp_stat2 = '<div class="badge badge-success">READ</div>';
              break;

            default:
              $disp_stat2 = '<div class="badge badge-danger">NOT READ</div>';
              break;
          }
          ?>
          <tr>
            <td>
              <?= $increment ?>
            </td>
             <td><?= $user_name ?>
                                                            </td> 
            <td>
              <?= $campaign_name ?>
            </td>
             <td>
              <?= $campaign_id ?>
            </td>
             <td>
              <?= $templateid ?>
            </td>
            <td>
              <?= $template_name ?>
            </td>

            <td>Total Mobile No :
              <?= $total_mobileno_count ?>
            </td>
           <?/* <td class="text-left" style='width: 180px !important;'>
              <div>
                <div style='float: left'>Sender : </div>
                <div style='float: right; width: 100px; margin-right: 15px;'><a href="#!" class="btn btn-outline-primary
            btn-disabled" style='width: 140px;'>
                    <?= $sender ?>
                  </a></div>
              </div>
              <div style='clear: both;'>
                <div style='float: left'>Receiver : </div>
                <div style='float: right; width:100px; margin-right: 15px;'><a href="#!"
                    class="btn btn-outline-success btn-disabled" style='width: 140px;'>
                    <?= $mobile_no ?>
                  </a></div>
              </div>
            </td> */?>
            <td>
              <?= $disp_stat ?>
            </td>
            <td>
              <?= $send_date?>
            </td>
           <!--  <td>
              <?= $delivery_date ?>
            </td> -->
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Template Whatsapp List Page: " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Template Whatsapp List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 4, 5, 6]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 5]
      }
      ]
    });
  </script>
  <?
}
// template_rcs_list Page template_rcs_list - End

// message_credit_list Page message_credit_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "message_credit_list") {
  site_log_generate("Message Credit List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>Parent User</th>
        <th>Receiver User</th>
        <th>Message Count</th>
        <th>Details</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
            "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
          }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "message_credit_list" API to verify, can we can we allow to view the message credit list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/message_credit_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      $response = curl_exec($curl);
      site_log_generate("Message Credit List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Message Credit List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
//Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->message_credit_log_entdate));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->parntname ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->usrname ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->provided_message_count ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->message_comments ?>
            </td>
            <td>
              <?= $entry_date ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Message Credit List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Message Credit List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },

      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 5]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 4]
      }
      ]
    });
  </script>
  <?
}
// message_credit_list Page message_credit_list - End

// purchase_message_credit_list Page purchase_message_credit_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "purchase_message_credit_list") {
  site_log_generate("Payment History Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Parent User</th>
        <th>Plan</th>
        <th>Message Credit / Amount</th>
        <th>Comments</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
            "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
          }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "purchase_message_credit_list" API to verify, can we can we allow to view the message credit list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/payment_history',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      $response = curl_exec($curl);
      site_log_generate("Payment History Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Payment History Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
//Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->usrsmscrd_entry_date));
          ?>
          <tr>
            <td class="text-center">
              <?= $indicatori ?>
            </td>
            <td class="text-center">
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td class="text-center">
              <?= $sms->report[$indicator]->parent_name ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->price_from . " - " . $sms->report[$indicator]->price_to . " [Rs." . $sms->report[$indicator]->price_per_message . "]" ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->raise_sms_credits . " / Rs." . $sms->report[$indicator]->sms_amount ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->usrsmscrd_comments ?>
            </td>
            <td class="text-center">
              <? switch ($sms->report[$indicator]->usrsmscrd_status) {
                case 'A':
                  echo '<a href="#!" class="btn btn-outline-success btn-disabled" title="Amount Paid" style="width:150px; text-align:center">Amount Paid</a>';
                  break;
                case 'C':
                  echo '<a href="#!" class="btn btn-outline-success btn-disabled" title="Message Credited" style="width:150px; text-align:center">Message Credited</a>';
                  break;
                case 'W':
                  echo '<a href="#!" class="btn btn-outline-info btn-disabled" style="width:150px; text-align:center" title="Amount Not Paid">Amount Not Paid</a>';
                  break;
                case 'F':
                  echo '<a href="#!" class="btn btn-outline-dark btn-disabled" title="Failed" style="width:150px; text-align:center">Failed</a>';
                  break;
                case 'N':
                  echo '<a href="#!" class="btn btn-outline-dark btn-disabled" title="Inactive" style="width:150px; text-align:center">Inactive</a>';
                  break;
                default:
                  echo '<a href="#!" class="btn btn-outline-info btn-disabled" style="width:150px; text-align:center" title="Amount Not Paid">Amount Not Paid</a>';
                  break;
              } ?>
            </td>
            <td class="text-center">
              <?= $entry_date ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Payment History Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Payment History Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
// purchase_message_credit_list Page purchase_message_credit_list - End

// approve_payment Page approve_payment - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "approve_payment") {
  site_log_generate("Approve Payment Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <form name="myform" id="myForm" method="post" action="message_credit">
    <input type="hidden" name="bar" id="bar" value="" />
  </form>

  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Parent User</th>
        <th>Plan</th>
        <th>Message Credit / Amount</th>
        <th>Comments</th>
        <th>Status</th>
        <th>Date</th>
        <th>Payment
          Details</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
            "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",
 "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
          }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "approve_payment" API to verify, can we can we allow to view the message credit list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/approve_payment',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
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
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Approve Payment Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
//Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->usrsmscrd_entry_date));
          ?>
          <tr>
            <td class="text-center">
              <?= $indicatori ?>
            </td>
            <td class="text-center">
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td class="text-center">
              <?= $sms->report[$indicator]->parent_name ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->price_from . " - " . $sms->report[$indicator]->price_to . " [Rs." . $sms->report[$indicator]->price_per_message . "]" ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->raise_sms_credits . " / Rs." . $sms->report[$indicator]->sms_amount ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->usrsmscrd_comments ?>
            </td>
            <td class="text-center">
              <? switch ($sms->report[$indicator]->usrsmscrd_status) {
                case 'A':
                  echo '<a href="#!" class="btn btn-outline-success btn-disabled" title="Approved" style="width:90px; text-align:center">Approved</a>';
                  break;
                case 'W':
                  echo '<a href="#!" class="btn btn-outline-info btn-disabled" style="width:90px; text-align:center" title="Waiting">Waiting</a>';
                  break;
                case 'F':
                  echo '<a href="#!" class="btn btn-outline-dark btn-disabled" title="Failed" style="width:90px; text-align:center">Failed</a>';
                  break;
                default:
                  echo '<a href="#!" class="btn btn-outline-info btn-disabled" style="width:90px; text-align:center" title="Waiting">Waiting</a>';
                  break;
              } ?>
            </td>
            <td class="text-center">
              <?= $entry_date ?>
            </td>
            <td class="text-center text-danger">
              <?= $sms->report[$indicator]->usrsmscrd_status_cmnts ?>
            </td>
            <td class="text-center"><a href="javascript:void(0)"
                data-val="<?= $sms->report[$indicator]->user_id ?>&<?= $sms->report[$indicator]->price_to ?>&<?= $sms->report[$indicator]->usrsmscrd_id ?>"
                class="btn btn-primary formAnchor">Add Message Credit</a></td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Approve Payment Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Approve Payment Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>

  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('.formAnchor').on('click', function (e) {
      e.preventDefault(); // prevents a window.location change to the href
      $('#bar').val($(this).data('val')); // sets to 123 or abc, respectively
      $('#myForm').submit();
    });

    $(".btn_msgcrdt").click(function () {
      var link = $(this).attr('var');
      alert("link : " + link);
      $('.post').attr("value", link);
      $('.redirect').submit();
    });

    $('#table-1').DataTable({
      dom: 'PlBfrtip',

      searchPanes: {
        cascadePanes: true
      },

      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 6, 7]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 7]
      }
      ]
    });
  </script>
  <?
}
// approve_payment Page approve_payment - End

// manage_users_list Page manage_users_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "manage_users_list") {
  site_log_generate("Manage Users List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
	<th>#</th>
        <th>User</th>
        <th>Parent</th>
        <th>User Status</th>
        <th>Contact Details</th>
        <th>Last Login</th>
        <th>Login Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
        }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "manage_users" API to verify, can we can we allow to view the manage_users list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/manage_users',
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
      site_log_generate("Manage Users List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Manage Users List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) {  // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->usr_mgt_entry_date));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->parent_name ?>
            </td>
	     <td>
              <? if ($sms->report[$indicator]->usr_mgt_status == 'Y') { ?>
                <div class="badge badge-success">Active</div>
              <? } elseif ($sms->report[$indicator]->usr_mgt_status == 'R') { ?>
                <div class="badge badge-danger">Rejected</div>
              <? } elseif ($sms->report[$indicator]->usr_mgt_status == 'N') { ?>
                <div class="badge badge-primary">Waiting for Approval</div>
              <? } elseif ($sms->report[$indicator]->usr_mgt_status == 'D') { ?>
              <div class="badge badge-danger">Deleted</div>
              <? } ?>
            </td>
            <td>Mobile :
              <?= $sms->report[$indicator]->user_mobile ?><br>Email :
              <?= $sms->report[$indicator]->user_email ?>
            </td>
            <td>
              <? if (!empty($sms->report[$indicator]->login_time)) { ?>
              <?= $sms->report[$indicator]->login_time ?>
              <? } else { ?>
                <div>-</div>
              <? } ?>
            </td>
            <td>
              <? if ($sms->report[$indicator]->user_log_status == 'I') { ?>
                <div class="badge badge-success">IN</div>
              <? } elseif ($sms->report[$indicator]->user_log_status == 'O') { ?>
                <div class="badge badge-danger">OUT</div>
              <? } else { ?>
                <div>-</div>
              <? } ?>
            </td>
            <td>
            <?php if ($sms->report[$indicator]->usr_mgt_status == 'Y'): ?>
                <a href="edit_users?user_name=<?= $sms->report[$indicator]->user_name?>&user_email=<?= urlencode($sms->report[$indicator]->user_email) ?>&user_mobile=<?= urlencode($sms->report[$indicator]->user_mobile) ?>&user_id=<?= urlencode($sms->report[$indicator]->user_id) ?>">Edit</a>
                / <a href="#!" onclick="delete_user('<?= $sms->report[$indicator]->user_id ?>', '<?= $indicatori ?>')">Delete</a>
            <?php elseif ($sms->report[$indicator]->usr_mgt_status == 'D'): ?>
                
                 <a href="#!" onclick="activate_user('<?= $sms->report[$indicator]->user_id ?>', '<?= $indicatori ?>')">Activate</a>
            <?php endif; ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Manage Users List Page  : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Manage Users List Page  : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter  using-->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 6]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 2, 4, 5]
      }
      ]
    });
    // $('#table-1').DataTable({
    //   dom: 'Bfrtip',
    //   colReorder: true,
    //   buttons: [{
    //     extend: 'copyHtml5',
    //     exportOptions: {
    //       columns: [0, ':visible']
    //     }
    //   }, {
    //     extend: 'csvHtml5',
    //     exportOptions: {
    //       columns: ':visible'
    //     }
    //   }, {
    //     extend: 'pdfHtml5',
    //     exportOptions: {
    //       columns: ':visible'
    //     }
    //   }, {
    //     extend: 'searchPanes',
    //     config: {
    //       cascadePanes: true
    //     }
    //   }, 'colvis'],
    //   columnDefs: [{
    //     searchPanes: {
    //       show: false
    //     },
    //     targets: [0]
    //   }]
    // });
  </script>
  <?
}
// manage_users_list Page manage_users_list - End


// activation_payment_list Page activation_payment_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "activation_payment_list") {
  site_log_generate("Activation Payment List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Mobile Number</th>
        <th>Email</th>
        <th>Payment Comments</th>
        <th>Payment Status</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
        }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "activation_payment_list" API to verify, can we can we allow to view the activation_payment_list list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/activation_payment_list',
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
      site_log_generate("Activation Payment List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Activation Payment List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) {  // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          // $entry_date = date('d-m-Y h:i:s A', strtotime($sms->payment_list[$indicator]->usr_mgt_entry_date));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->payment_list[$indicator]->user_name ?>
            </td>
            <td>
              <?= $sms->payment_list[$indicator]->mobile_no ?>
            </td>
            <td>
              <?= $sms->payment_list[$indicator]->email_id ?>
            </td>
            <td>
              <?= "Product : " . $sms->payment_list[$indicator]->product_name . "<br> Price : " . $sms->payment_list[$indicator]->price . "<br> Comments : " . $sms->payment_list[$indicator]->payment_comments; ?>
            </td>
            <td>
              <?= ($sms->payment_list[$indicator]->payment_status == 'Y') ? "PAID" : "PAYMENT FAILED"; ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Activation Payment List Page  : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Activation Payment List Page  : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter  using-->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 5]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0, 4]
      }
      ]
    });
  </script>
  <?
}
// activation_payment_list Page activation_payment_list - End

// request_demo_list Page request_demo_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "request_demo_list") {
  site_log_generate("Demo Request List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Email</th>
        <th>Mobile Number</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
        }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "request_demo_list" API to verify, can we can we allow to view the request_demo_list list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/request_demo_list',
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
      site_log_generate("Demo Request List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Demo Request List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) {  // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->request_demo_list[$indicator]->entry_date));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->request_demo_list[$indicator]->user_name ?>
            </td>
            <td>
              <?= $sms->request_demo_list[$indicator]->user_mail ?>
            </td>
            <td>
              <?= $sms->request_demo_list[$indicator]->user_mobileno ?>
            </td>
            <td>
              <?= $entry_date ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Demo Request List Page  : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Demo Request List Page  : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter  using-->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 4]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0]
      }
      ]
    });
  </script>
  <?
}
// request_demo_list Page request_demo_list - End

// messenger_responses Page messenger_responses - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "messenger_responses") {
  site_log_generate("Messenger Response List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped text-center" id="table-1">
    <thead>
      <tr class="text-center">
        <th>#</th>
        <th>Sender</th>
        <th>Receiver</th>
        <th>Message Type</th>
        <th>Status</th>
        <th>Entry Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
            "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
          }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "messenger_response_list" API to verify, can we can we allow to view the messenger_response_list 
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/report/messenger_response_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute  
      $response = curl_exec($curl);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Messenger Response List Page : User : " . $_SESSION['yjwatsp_user_name'] . " executed the Query reponse ($response) on " . date("Y-m-d H:i:s"));
      // To get the one by one data
      $indicatori = 0;
      if ($sms->response_status == 200) {// If the response is success to execute this condition
        for ($indicator = 0; $indicator < count($sms->report); $indicator++) {
          // Looping the indicator is less than the count of report.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->message_rec_date));
          $tr_bg_clr = "";
          $td_text_clr = " font-weight: bold;";
          $stat_view = 'Read ';
          if ($sms->report[$indicator]->message_is_read == 'N') {
            $tr_bg_clr = "background-color: #5bd4672b";
            $td_text_clr = "color: #00391b; font-weight: bold;";
            $stat_view = 'Unread ';
          }
          ?>
          <tr style="<?= $tr_bg_clr ?>">
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->message_from ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->message_to ?>
            </td>
            <td>
              <?= strtoupper($sms->report[$indicator]->message_type) ?>
            </td>
            <td>
              <? if ($sms->report[$indicator]->message_status == 'Y') { ?><a href="#!"
                  class="btn btn-outline-success btn-disabled">Active</a>
              <? } elseif ($sms->report[$indicator]->message_status == 'N') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled">Inactive</a>
              <? } ?>
            </td>
            <td>
              <?= $entry_date ?>
            </td>
            <td><a href="#!" style="<?= $td_text_clr ?>"
                onclick="func_view_response('<?= $sms->report[$indicator]->message_id ?>', '<?= $sms->report[$indicator]->message_from ?>', '<?= $sms->report[$indicator]->message_to ?>')">
                <?= $stat_view ?>View
              </a>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Messenger Response List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Messenger Response List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
// messenger_responses Page messenger_responses - End


// business_summary_report Page business_summary_report - Start
/*if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "business_summary_report") {
  site_log_generate("Business Summary Report Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>User</th>
        <th>Campaign</th>
        <th>Campaign Id</th>
        <th>Total Pushed</th>
        <th>Sent</th>
        <th>Delivered</th>
        <th>Read</th>
        <th>Failed</th>
        <th>In progress</th>
      </tr>
    </thead>
    <tbody>
      <?
      if ($_REQUEST['dates']) {
        $date = $_REQUEST['dates'];
      } else {
        $date = date('m/d/Y') . "-" . date('m/d/Y'); // 01/28/2023 - 02/27/2023 
      }

      $td = explode('-', $date);
      $thismonth_startdate = date("Y/m/d", strtotime($td[0]));
      $thismonth_today = date("Y/m/d", strtotime($td[1]));

      $replace_txt = '{
          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",';
      if ($date) {
        $replace_txt .= '"filter_date" : "' . $thismonth_startdate . ' - ' . $thismonth_today . '",';
      }
      // To Send the request API 
      $replace_txt = rtrim($replace_txt, ",");
      $replace_txt .= '}';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // To Get Api URL
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/report/summary_report',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute  
      site_log_generate("Business Summary Report Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Business Summary Report Page  : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->report) {
        // If the response is success to execute this condition
        for ($indicator = 0; $indicator < count($sms->report); $indicator++) {
          //Looping the indicator is less than the count of report.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $entry_date = date('d-m-Y', strtotime($sms->report[$indicator]->whatsapp_entry_date));
          $user_id = $sms->report[$indicator]->user_id;
          $user_name = $sms->report[$indicator]->user_name;
          $user_master_id = $sms->report[$indicator]->user_master_id;
          $user_type = $sms->report[$indicator]->user_type;
          $total_msg = $sms->report[$indicator]->total_msg;
          $credits = $sms->report[$indicator]->available_messages;
          $total_success = $sms->report[$indicator]->total_success;
          $total_delivered = $sms->report[$indicator]->total_delivered;
          $total_read = $sms->report[$indicator]->total_read;
          $total_failed = $sms->report[$indicator]->total_failed;
          $total_waiting = $sms->report[$indicator]->total_waiting;
          $total_invalid = $sms->report[$indicator]->total_invalid;
          $campaign_name = $sms->report[$indicator]->campaign_name;
 $campaign_id = $sms->report[$indicator]->campaign_id;


 if ($user_id != '') {
            $increment++;
            ?>
            <tr style="text-align: center !important">
              <td>
                <?= $increment ?>
              </td>
              <td>
                <?= $entry_date ?>
              </td>
              <td>
                <?= $user_name ?>
              </td>
              <td>
                <?= $campaign_name ?>
              </td>
                 <td>
                <?= $campaign_id ?>
              </td>
              <td>
                <?= $total_msg ?>
              </td>
              <td>
                <?= $total_success ?>
              </td>
              <td>
                <?= $total_delivered ?>
              </td>
              <td>
                <?= $total_read ?>
              </td>
              <td>
                <?= $total_failed ?>
              </td>
              <td>
                <?= $total_waiting ?>
              </td>
            </tr>

            <?
          }
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Business Summary Report Page  : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Business Summary Report Page  : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>

    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 4, 5, 6, 7, 8, 9]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0]
      }
      ]
    });
  </script>
  <?

}*/

// business_summary_report Page business_summary_report - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "business_summary_report") {
  site_log_generate("Business Summary Report Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>User</th>
        <th>Campaign</th>
        <th>Campaign Id</th>
        <th>Total Pushed</th>
        <th>Sent</th>
        <th>Delivered</th>
        <th>Read</th>
        <th>Failed</th>
        <th>In progress</th>
      </tr>
    </thead>
    <tbody>
      <?
      if ($_REQUEST['dates']) {
        $date = $_REQUEST['dates'];
      } else {
        $date = date('m/d/Y') . "-" . date('m/d/Y'); // 01/28/2023 - 02/27/2023 
      }

      $td = explode('-', $date);
      $thismonth_startdate = date("Y/m/d", strtotime($td[0]));
      $thismonth_today = date("Y/m/d", strtotime($td[1]));

      $replace_txt = '{
          "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",';
      if ($date) {
        $replace_txt .= '"filter_date" : "' . $thismonth_startdate . ' - ' . $thismonth_today . '",';
      }
      // To Send the request API 
      $replace_txt = rtrim($replace_txt, ",");
      $replace_txt .= '}';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // To Get Api URL
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/report/summary_report',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute  
      site_log_generate("Business Summary Report Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');

      $response = curl_exec($curl);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Business Summary Report Page  : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->report) {
        // If the response is success to execute this condition
        for ($indicator = 0; $indicator < count($sms->report); $indicator++) {
          //Looping the indicator is less than the count of report.if the condition is true to continue the process.if the condition is false to stop the process
          $indicatori++;
          $entry_date = date('d-m-Y', strtotime($sms->report[$indicator]->entry_date));
          $user_id = $sms->report[$indicator]->user_id;
          $user_name = $sms->report[$indicator]->user_name;
          $user_master_id = $sms->report[$indicator]->user_master_id;
          $user_type = $sms->report[$indicator]->user_type;
          $total_msg = $sms->report[$indicator]->total_msg;
          $credits = $sms->report[$indicator]->available_messages;
          $total_success = $sms->report[$indicator]->total_success;
          $total_delivered = $sms->report[$indicator]->total_delivered;
          $total_read = $sms->report[$indicator]->total_read;
          $total_failed = $sms->report[$indicator]->total_failed;
          $total_waiting = $sms->report[$indicator]->total_waiting;
          $total_invalid = $sms->report[$indicator]->total_invalid;
          $campaign_name = $sms->report[$indicator]->campaign_name;
 $campaign_id = $sms->report[$indicator]->campaign_id;
 if ($user_id != '') {
            $increment++;
            ?>
            <tr style="text-align: center !important">
              <td>
                <?= $increment ?>
              </td>
              <td>
                <?= $entry_date ?>
              </td>
              <td>
                <?= $user_name ?>
              </td>
              <td>
                <?= $campaign_name ?>
              </td>
                 <td>
                <?= $campaign_id ?>
              </td>
              <td>
                <?= $total_msg ?>
              </td>
              <td>
                <?= $total_success ?>
              </td>
              <td>
                <?= $total_delivered ?>
              </td>
              <td>
                <?= $total_read ?>
              </td>
              <td>
                <?= $total_failed ?>
              </td>
              <td>
                <?= $total_waiting ?>
              </td>
            </tr>

            <?
          }
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Business Summary Report Page  : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Business Summary Report Page  : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>

    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 4, 5, 6, 7, 8, 9]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0]
      }
      ]
    });
  </script>
  <?

}





// business_details_report Page business_details_report - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "business_details_report") {
  site_log_generate("Business Details Report Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table tablestriped text-center" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Campaign Name</th>
 <th>Campaign Id</th>
        <th>Template Name</th>
<th>Template Id</th>
        <th>Total Mobile No</th>
        <th>Entry Date</th>
	<th>Download</th>
      </tr>
    </thead>
    <tbody>
      <?
      $replace_txt = '{
        "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '",';

      if (($_REQUEST['dates'] != 'undefined') && ($_REQUEST['dates'] != '[object HTMLInputElement]')) {
        $date = $_REQUEST['dates'];
        $td = explode('-', $date);
        $thismonth_startdate = date("Y/m/d", strtotime($td[0]));
        $thismonth_today = date("Y/m/d", strtotime($td[1]));
        if ($date) {
          $replace_txt .= '"response_date_filter" : "' . $thismonth_startdate . ' - ' . $thismonth_today . '",';
        }
      } else {
        $currentDate = date('Y/m/d');
        $thirtyDaysAgo = date('Y/m/d', strtotime('-7 days', strtotime($currentDate)));
        $date = $thirtyDaysAgo . "-" . $currentDate; // 01/28/2023 - 02/27/2023 
        if ($date) {
          $replace_txt .= '"response_date_filter" : "' . $thirtyDaysAgo . ' - ' . $currentDate . '",';
        }
      }
      $replace_txt = rtrim($replace_txt, ",");
      $replace_txt .= '}';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "detailed_report" API to verify, can we can we allow to view the detailed_report list
// To Get Api URL
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/report/detailed_report',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute 
      site_log_generate("Business Details Report Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
  // echo $response;
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      // To get the one by one data
	$indicatori = 0;
          if ($sms->num_of_rows > 0) { // If the response is success to execute this condition


            for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
              //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
              $indicatori++;
              $whatsapp_entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->rcs_entry_date));
              ?>
              <tr>
                <td>
                  <?= $indicatori ?>
                </td>
                <td>
                  <?= $sms->report[$indicator]->user_name ?>
                </td>
                <td style="text-align: center;">
                  <?= $sms->report[$indicator]->campaign_name ?>
                </td>
                 <td style="text-align: center;">
                  <?= $sms->report[$indicator]->campaign_id ?>
                </td>
                <td style="text-align: center;">
                  <?= $sms->report[$indicator]->template_name ?>
                </td>
                <td style="text-align: center;">
                  <?= $sms->report[$indicator]->templateid ?>
                </td>
                <td style="text-align: center;">
                  <?= $sms->report[$indicator]->total_mobileno_count ?>
                </td>

		<td style="text-align: center;">
                  <?= $whatsapp_entry_date ?>
                </td>

                <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
                        <div class="btn-group mb-3" role="group" aria-label="Basic example">
                            <button type="button" title="Download"
                                onclick="approve_popup('<?= $sms->report[$indicator]->campaign_name ?>')"
                                class="btn btn-icon btn-success"><i class="fas fa-download"></i></button>
                        </div>
                    <div>
                </td>

            </tr>
          <?
          }
          } else if ($sms->response_status == 204) {
            site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
            $json = array("status" => 2, "msg" => $sms->response_msg);
          } else {
            site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
            $json = array("status" => 0, "msg" => $sms->response_msg);
          }
          ?>
          
    </tbody>
    
    </table>



  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
    $('#table-1').DataTable({
      dom: 'PlBfrtip',
      searchPanes: {
        cascadePanes: true
      },
      searchPanes: {
        initCollapsed: true
      },
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
      }, 'colvis'],
      columnDefs: [{
        searchPanes: {
          show: true
        },
        targets: [1, 2, 3, 4, 5, 6]
      },
      {
        searchPanes: {
          show: false
        },
        targets: [0]
      }
      ]
    });
  </script>
  <?
}
// business_details_report Page business_details_report - End



// manage_whatsappno_list Page manage_whatsappno_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "manage_whatsappno_list") {
  site_log_generate("Manage Whatsappno List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table
  ?>
  <table class="table table-striped text-center" id="table-1">
    <thead>
      <tr class="text-center">
        <th>#</th>
        <th>User</th>
        <th>Mobile No</th>
        <th>Status</th>
        <th>Entry Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
      "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
    }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "manage_whatsappno_list" API to verify, can we can we allow to view the manage_whatsappno_list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/manage_whatsappno_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      site_log_generate("Manage Whatsappno List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Manage Whatsappno List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num of rows.if the condition is true to continue the process.if the condition are false to stop the process
          $indicatori++;
          $entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->whatspp_config_entdate));
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= strtoupper($sms->report[$indicator]->user_name) ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->country_code . $sms->report[$indicator]->mobile_no ?>
            </td>
            <td>
              <? if ($sms->report[$indicator]->whatspp_config_status == 'Y') { ?><a href="#!"
                  class="btn btn-outline-success btn-disabled">Active</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'D') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled">Deleted</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'B') { ?><a href="#!"
                  class="btn btn-outline-dark btn-disabled">Blocked</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'N') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled">Inactive</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'M') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled">Mobile No Mismatch</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'I') { ?><a href="#!"
                  class="btn btn-outline-warning btn-disabled">Invalid</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'P') { ?><a href="#!"
                  class="btn btn-outline-info btn-disabled">Processing</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'R') { ?><a href="#!"
                  class="btn btn-outline-danger btn-disabled">Rejected</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'X') { ?><a href="#!" class="btn
        btn-outline-primary btn-disabled">Need Rescan</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'L') { ?><a href="#!"
                  class="btn btn-outline-info btn-disabled">Linked</a>
              <? } elseif ($sms->report[$indicator]->whatspp_config_status == 'U') { ?><a href=" #!"
                  class="btn btn-outline-warning btn-disabled">Unlinked</a>
              <? } ?>
            </td>
            <td>
              <?= $entry_date ?>
            </td>
            <td id='id_approved_lineno_<?= $indicatori ?>'>
              <? if ($sms->report[$indicator]->whatspp_config_status == 'D' or $sms->report[$indicator]->whatspp_config_status == 'N' or $sms->report[$indicator]->whatspp_config_status == 'M' or $sms->report[$indicator]->whatspp_config_status == 'I' or $sms->report[$indicator]->whatspp_config_status == 'X') { ?>
                <a href="manage_whatsapp_no?mob=<?= $sms->report[$indicator]->mobile_no ?>" class="btn btn-success">Scan</a>
              <? } else { ?>
                <a href="#!" class="btn btn-outline-light btn-disabled" style="cursor: not-allowed;">Scan</a>
              <? } ?>
              <? if ($sms->report[$indicator]->whatspp_config_status != 'D') { ?>
                <button type="button" title="Delete Sender ID"
                  onclick="remove_senderid('<?= $sms->report[$indicator]->whatspp_config_id ?>', 'D', '<?= $indicatori ?>')"
                  class="btn btn-icon btn-danger" style="padding: 0.3rem 0.41rem !important;">Delete</button>
              <? } else { ?> <a href="#!" class="btn btn-outline-light btn-disabled" style="padding: 0.3rem 0.41rem !important;cursor:
      not-allowed;">Delete</a>
              <? } ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Manage Whatsappno List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Manage Whatsappno List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
// manage_whatsappno_list Page manage_whatsappno_list - End

// approve_whatsapp_no Page approve_whatsapp_no - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "approve_whatsapp_no") {
  site_log_generate("Manage Whatsappno List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped text-center" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Mobile No</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
        "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
      }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "approve_whatsapp_no" API to verify, can we can we allow to view the approve_whatsapp_no list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/approve_whatsapp_no',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      site_log_generate("Manage Whatsappno List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Manage Whatsappno List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $indicatori = 0;
      if ($sms->num_of_rows > 0) {// If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num of rows.if the condition is true to continue the process.if the condition are false to stop the process
          $indicatori++;
          ?>
          <tr>
            <td>
              <?= $indicatori ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->user_name ?>
            </td>
            <td>
              <?= $sms->report[$indicator]->country_code . $sms->report[$indicator]->mobile_no ?>
            </td>
            <td>
              <?
              switch ($sms->report[$indicator]->whatspp_config_status) {
                case 'N':
                  ?><a href="#!" class="btn btn-outline-primary btn-disabled">New</a>
                  <?
                  break;

                case 'L':
                  ?><a href="#!" class="btn btn-outline-info btn-disabled">Whatsapp Linked</a>
                  <?
                  break;
                case 'U':
                  ?><a href="#!" class="btn btn-outline-warning btn-disabled">Whatsapp Unlinked</a>
                  <?
                  break;
                case 'X':
                  ?><a href="#!" class="btn btn-outline-primary btn-disabled">Rescan</a>
                  <?
                  break;

                case 'Y':
                  ?><a href="#!" class="btn btn-outline-success btn-disabled">Super Admin Approved</a>
                  <?
                  break;
                case 'R':
                  ?><a href=" #!" class="btn btn-outline-danger btn-disabled">Super Admin Rejected</a>
                  <?
                  break;

                default:
                  ?><a href="#!" class="btn btn-outline-dark btn-disabled">Invalid</a>
                  <?
                  break;
              } ?>
            </td>
            <td id='id_approved_lineno_<?= $indicatori ?>'>
              <div class="btn-group mb-3" role="group" aria-label="Basic example">
                <button type="button" title="Approve" onclick="change_status('<?= $sms->report[$indicator]->whatspp_config_id ?>', 'Y', '
    <?= $indicatori ?>')" class="btn btn-icon btn-success"><i class="fas fa-check"></i></button>
                <button type="button" title="Reject"
                  onclick="change_status('<?= $sms->report[$indicator]->whatspp_config_id ?>', 'N', '<?= $indicatori ?>')"
                  class="btn btn-icon btn-danger"><i class="fas fa-times"></i></button>
              </div>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Manage Whatsappno List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Manage Whatsappno List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
// approve_whatsapp_no Page approve_whatsapp_no - End

// whatsapp_list Page whatsapp_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "whatsapp_list") {
  site_log_generate("Manage Whatsappno List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table 
  ?>
  <table class="table table-striped" id="table-1">
    <thead>
      <tr>
        <th>#</th>
        <th>User</th>
        <th>Campaign</th>
        <th>Message Content</th>
        <th>Count</th>
        <th>Mobile No</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?
      // To Send the request API 
      $replace_txt = '{
      "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
    }';
      // Add bearer token
      $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
      // It will call "whatsapp_list" API to verify, can we can we allow to view the whatsapp list
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/whatsapp_list',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            $bearer_token,
            'Content-Type: application/json'
          ),
        )
      );
      // Send the data into API and execute   
      site_log_generate("Manage Whatsappno List Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      curl_close($curl);
      // After got response decode the JSON result
      $sms = json_decode($response, false);
      site_log_generate("Manage Whatsappno List Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
      // To get the one by one data
      $increment = 0;
      if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
          // Looping the indicator is less than the num of rows.if the condition is true to continue the process.if thec false to stop the process
          $increment++;
          $compose_whatsapp_id = $sms->report[$indicator]->compose_whatsapp_id;
          $user_id = $sms->report[$indicator]->user_id;
          $user_name = $sms->report[$indicator]->user_name;
          $campaign_name = $sms->report[$indicator]->campaign_name;
          $whatsapp_content = $sms->report[$indicator]->whatsapp_content;

          $message_type = $sms->report[$indicator]->message_type;
          $total_mobileno_count = $sms->report[$indicator]->total_mobileno_count;
          $content_char_count = $sms->report[$indicator]->content_char_count;
          $content_message_count = $sms->report[$indicator]->content_message_count;
          $mobile_no = $sms->report[$indicator]->country_code . $sms->report[$indicator]->mobile_no;

          $sender = $sms->report[$indicator]->sender;
          $send_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->comwtap_entry_date));
          if ($sms->report[$indicator]->response_date != '') {
            $response_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->response_date));
          } else {
            $response_date = '';
          }
          $response_status = $sms->report[$indicator]->response_status;
          $response_message = $sms->report[$indicator]->response_message;

          $response_id = $sms->report[$indicator]->response_id;
          $delivery_status = $sms->report[$indicator]->delivery_status;
          $read_status = $sms->report[$indicator]->read_status;

          $disp_stat = '';
          switch ($response_status) {
            case 'S':
              $disp_stat = '<div class="badge badge-success">SENT</div>';
              break;
            case 'F':
              $disp_stat = '<div class="badge badge-danger">FAILED</div>';
              break;
            case 'I':
              $disp_stat = '<div class="badge badge-warning">INVALID</div>';
              break;

            default:
              $disp_stat = '<div class="badge badge-info">YET TO SEND</div>';
              break;
          }
          ?>
          <tr>
            <td>
              <?= $increment ?>
            </td>
            <td>
              <?= $user_name ?>
            </td>
            <td>
              <?= $campaign_name ?>
            </td>
            <td>
              <?= $message_type ?> :
              <?= $whatsapp_content ?>
            </td>
            <td>Total Mobile No :
              <?= $total_mobileno_count ?><br>Total Messages :
              <?= $content_message_count ?>
            </td>
            <td>Sender : <a href="#!" class="btn btn-outline-primary btn-disabled">
                <?= $sender ?>
              </a><br>Receiver : <a href="#!" class="btn btn-outline-success btn-disabled">
                <?= $mobile_no ?>
              </a></td>
            <td>
              <?= $response_date . "<br>" . $disp_stat ?>
            </td>
          </tr>
          <?
        }
      } else if ($sms->response_status == 204) {
        site_log_generate("Manage Whatsappno List Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $sms->response_msg);
      } else {
        if ($sms->response_status == 403 || $response == '') { ?>
            <script>
              window.location = "index"
            </script>
        <? }
        site_log_generate("Manage Whatsappno List Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $sms->response_msg);
      }
      ?>
    </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
// whatsapp_list Page whatsapp_list - End

// approve_compose_message Page approve_compose_message - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "approve_message") {
  site_log_generate("Approve Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
  // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table    ?>
  <table class="table table-striped" id="table-1">
      <thead>
          <tr>
              <th>#</th>
              <th>User Name</th>
              <th>Campaign Name</th>
              <th>Mobile Count</th>
              <th>Message Type</th>
              <th>Entry Date</th>
              <th>Status</th>
              <th>Action</th>
          </tr>
      </thead>
      <tbody>
          <?
          $replace_txt = '{
"user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
          // Add bearer token
          $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
          // It will call "approve_compose_message" API to verify, can we can we allow to view the approve_compose_message
          $curl = curl_init();
          curl_setopt_array(
              $curl,
              array(
                  CURLOPT_URL => $api_url . '/list/approve_compose_message',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => $replace_txt,
                  CURLOPT_HTTPHEADER => array(
                      $bearer_token,
                      'Content-Type: application/json'
                  ),
              )
          );
          // Send the data into API and execute  
          $response = curl_exec($curl);
          site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
          curl_close($curl);
          // echo $response;
          // After got response decode the JSON result
          $sms = json_decode($response, false);
          site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
          // To get the one by one data
          $indicatori = 0;
          if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
              for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
                  //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
                  $indicatori++;
                  ?>
                  <tr>
                      <td><?= $indicatori ?></td>

                      <td style="text-align: center;"><?= $sms->report[$indicator]->user_name ?></td>

                      <td style="text-align: center;"><?= $sms->report[$indicator]->campaign_name ?></td>

                      <td style="text-align: center;"><?= $sms->report[$indicator]->total_mobileno_count ?></td>

                 <td style="text-align: center;"><?= $sms->report[$indicator]->template_category ?></td>


                      <td style="text-align: center;">
                          <?= date("Y-m-d H:i:s", strtotime($sms->report[$indicator]->rcs_entry_date)) ?>
                      </td>
                      <td style="text-align: center;">
                          <? if ($sms->report[$indicator]->rcs_status == 'W') { ?>
                              <a href="#!" class="btn btn-outline-danger btn-disabled" style="width:100px;">Not approve</a>
                          <? } else if ($sms->report[$indicator]->rcs_status == 'P' || $sms->report[$indicator]->rcs_status == 'V') { ?>
                                  <a href="#!" class="btn btn-outline-info btn-disabled" style="width:100px;">Processing</a>
                          <? } else if ($sms->report[$indicator]->rcs_status == 'S') { ?>
                                      <a href="#!" class="btn btn-outline-success btn-disabled" style="width:100px;">Success</a>
                          <? } ?>
                      </td>

                      <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
                          <? if ($sms->report[$indicator]->rcs_status == 'W') { ?>
                              <div class="btn-group mb-3" role="group" aria-label="Basic example">

                                  <button type="button" title="Approve"
                                      onclick="approve_popup('<?= $sms->report[$indicator]->user_id ?>', '<?= $sms->report[$indicator]->compose_rcs_id ?>', '<?= $sms->report[$indicator]->total_mobileno_count ?>', '<?= $indicatori ?>')"
                                      class="btn btn-icon btn-success"><i class="fas fa-check"></i></button>
                                  <button type="button" title="Reject"
                                      onclick="reject_status_popup('<?= $sms->report[$indicator]->compose_rcs_id ?>','<?= $sms->report[$indicator]->user_id ?>', 'R', '<?= $indicatori ?>')"
                                      class="btn btn-icon btn-danger"><i class="fas fa-times"></i></button>
                              <? } else { ?>
                                  <a href="#!" style="padding: 0.3rem 0.41rem !important;cursor: not-allowed;"> - </a>
                              <? } ?>
                          </div>

                      </td>

                      </td>
                  </tr>
                  <?
              }
          } else if ($sms->response_status == 204) {
              site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
              $json = array("status" => 2, "msg" => $sms->response_msg);
          } else {
              site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
              $json = array("status" => 0, "msg" => $sms->response_msg);
          }
          ?>
      </tbody>
  </table>
  <!-- General JS Scripts -->
  <script src="assets/js/jquery.dataTables.min.js"></script>
  <script src="assets/js/dataTables.buttons.min.js"></script>
  <script src="assets/js/dataTables.searchPanes.min.js"></script>
  <script src="assets/js/dataTables.select.min.js"></script>
  <script src="assets/js/jszip.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/buttons.html5.min.js"></script>
  <script src="assets/js/buttons.colVis.min.js"></script>
  <!-- filter using -->
  <script>
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
  <?
}
//manual_report Page manual_report_upload - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "manual_report") {
    site_log_generate("Approve Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
    // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table        ?>
    <table class="table table-striped" id="table-1">
        <thead>
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Campaign Name</th>
                <th>Campaign Id</th>
                <th>Message Type</th>
                <th>Mobile Number Count</th>
                <th>Campaign Status</th>
                <th>Campaign Date</th>
                <th>Upload Report</th>
                <th>Remarks</th>
                <th>Details</th>
                 <th>Sample Report</th>
            </tr>
        </thead>
        <tbody>
            <?
            $replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
            // Add bearer token
            $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
            // It will call "approve_compose_message" API to verify, can we can we allow to view the approve_compose_message
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $api_url . '/list/manual_upload_list',
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
            site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
            curl_close($curl);
            // After got response decode the JSON result
            $sms = json_decode($response, false);
            site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
            // To get the one by one data
            $indicatori = 0;
            if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        
                for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
                    //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
                    $indicatori++;
                    $rcs_entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->rcs_entry_date));
                    ?>
                    <tr>
                        <td>
                            <?= $indicatori ?>
                        </td>
                        <td>
                            <?= $sms->report[$indicator]->user_name ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->campaign_name ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->campaign_id ?>
                        </td>

                        <td style="text-align: center;">
                        <?= $sms->report[$indicator]->template_category ?>
                        </td>

                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->total_mobileno_count ?>
                        </td>

                        <td style="text-align: center;">
                            <? if ($sms->report[$indicator]->whatsapp_status == 'V') { ?>
                                <a href="#!" class="btn btn-outline-danger btn-disabled">Processing</a>
                            <? } else { ?>
                                <a href="#!" class="btn btn-outline-primary btn-disabled">Active</a>
                            <? } ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $rcs_entry_date ?>
                        </td>

                        <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
                            <?php if ($sms->report[$indicator]->rcs_status == 'C'): ?>
                                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                                    <button type="button" title="Upload"
                                        onclick="approve_popup('<?= $sms->report[$indicator]->compose_rcs_id ?>', 'TGBase', '<?= $sms->report[$indicator]->user_id ?>')"
                                        class="btn btn-icon btn-success"><i class="fas fa-upload"></i></button>
                                </div>
                            <?php endif; ?>
                            <div>
                                <span class="text-inverse" style="color:#FF0000 !important; font-weight: bold; cursor: pointer;">
                                    <?php 
                                        if ($sms->report[$indicator]->count_pj > 0 && $sms->report[$indicator]->rcs_status == 'C'):
                                        $total_updated_count = $sms->report[$indicator]->total_updated_count;
                                        $count_pj = $sms->report[$indicator]->count_pj;
                                        $pending_numbers = $count_pj - $total_updated_count; ?>
                                        Pending Numbers - <?= $pending_numbers; ?>
                                    <?php else: ?>
                                        
                                    <?php endif; ?>
                                </span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($sms->report[$indicator]->remarks): ?>
                                <?= $sms->report[$indicator]->remarks; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                         <?php
                             if (!empty($sms->report[$indicator]->upload_info)) {
                                    echo $sms->report[$indicator]->upload_info;
                                } else {
                              echo "-";
                                }
                                ?>
                              </td>
                          <td style="text-align:center;" id='id_download_<?= $indicatori ?>'>
                                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                                    <button type="button" title="Download"
                                        onclick="download_sample_report()"
                                        class="btn btn-icon btn-info"><i class="fas fa-download"></i></button>
                                </div>
                        </td>
                    </tr>
                    <?
                }
            } else if ($sms->response_status == 204) {
                site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
                $json = array("status" => 2, "msg" => $sms->response_msg);
            } else {
                site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
                $json = array("status" => 0, "msg" => $sms->response_msg);
            }
            ?>
        </tbody>
    </table>
    <!-- General JS Scripts -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/dataTables.searchPanes.min.js"></script>
    <script src="assets/js/dataTables.select.min.js"></script>
    <script src="assets/js/jszip.min.js"></script>
    <script src="assets/js/pdfmake.min.js"></script>
    <script src="assets/js/vfs_fonts.js"></script>
    <script src="assets/js/buttons.html5.min.js"></script>
    <script src="assets/js/buttons.colVis.min.js"></script>
    <!-- filter using -->
    <script>
        $('#table-1').DataTable({
            dom: 'PlBfrtip',
            searchPanes: {
                cascadePanes: true
            },
            searchPanes: {
                initCollapsed: true
            },
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
            }, 'colvis'],
            columnDefs: [{
                searchPanes: {
                    show: true
                },
                targets: [1, 2, 3, 4, 5]
            },
            {
                searchPanes: {
                    show: false
                },
                targets: [0]
            }
            ]
        });
    </script>
    <?
}
// manual_report Page manual_report_upload - End


// generate_report Page generate_auto_report - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "generate_auto_report") {
    site_log_generate("Generate Report Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');
    ?>
    <table class="table table-striped" id="table-1">
        <thead>
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Campaign Name</th>
                <th>Mobile Number Count</th>
                <th>Campaign Date</th>
                <th>Campaign Status</th>
                <th>Generate CG Report</th>
            </tr>
        </thead>
        <tbody>
            <?
            $replace_txt = '{
	  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
	}';

            // Add bearer token
            $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
            // It will call "approve_compose_message" API to verify, can we can we allow to view the approve_compose_message
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $api_url . '/list/manual_upload_list',
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
            site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
            curl_close($curl);
            // After got response decode the JSON result
            $sms = json_decode($response, false);

            site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
            // To get the one by one data
            $indicatori = 0;
            if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
        
                for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
                    //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
                    $indicatori++;
                    $whatsapp_entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->whatsapp_entry_date));
                    if ($sms->report[$indicator]->count_yj > 0) {
                        ?>
                        <tr>
                            <td>
                                <?= $indicatori ?>
                            </td>
                            <td>
                                <?= $sms->report[$indicator]->user_name ?>
                            </td>
                            <td style="text-align: center;">
                                <?= $sms->report[$indicator]->campaign_name ?>
                            </td>
                            <td style="text-align: center;">
                                <?= $sms->report[$indicator]->total_mobileno_count ?>
                            </td>
                            <td style="text-align: center;">
                                <?= $whatsapp_entry_date ?>
                            </td>

                            <td style="text-align: center;">
                                <? if ($sms->report[$indicator]->whatsapp_status == 'P' || $sms->report[$indicator]->whatsapp_status == 'V') { ?>
                                    <a href="#!" class="btn btn-outline-danger btn-disabled">Processing</a>
                                <? } else { ?>
                                    <a href="#!" class="btn btn-outline-primary btn-disabled">Completed</a>
                                <? } ?>
                            </td>

                            <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
                                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                                    <button type="button" title="Generate CGBase Report"
                                        onclick="delivery_status('<?= $dbname . '_' . $sms->report[$indicator]->user_id ?>', 'compose_whatsapp_status_tmpl_<?= $sms->report[$indicator]->user_id ?>', '<?= $sms->report[$indicator]->compose_whatsapp_id ?>', 'CGBase', '<?= $sms->report[$indicator]->user_id ?>')"
                                        class="btn btn-icon btn-success"><i class="fa fa-cogs" style="font-size: 30px;"></i></button>
                                </div>
                            </td>

                        </tr>
                        <?
                    }
                }
            } else if ($sms->response_status == 204) {
                site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
                $json = array("status" => 2, "msg" => $sms->response_msg);
            } else {
                site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
                $json = array("status" => 0, "msg" => $sms->response_msg);
            }
            ?>
        </tbody>
    </table>
    <!-- General JS Scripts -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/dataTables.searchPanes.min.js"></script>
    <script src="assets/js/dataTables.select.min.js"></script>
    <script src="assets/js/jszip.min.js"></script>
    <script src="assets/js/pdfmake.min.js"></script>
    <script src="assets/js/vfs_fonts.js"></script>
    <script src="assets/js/buttons.html5.min.js"></script>
    <script src="assets/js/buttons.colVis.min.js"></script>
    <!-- filter using -->
    <script>
        $('#table-1').DataTable({
            dom: 'PlBfrtip',
            searchPanes: {
                cascadePanes: true
            },
            searchPanes: {
                initCollapsed: true
            },
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
            }, 'colvis'],
            columnDefs: [{
                searchPanes: {
                    show: true
                },
                targets: [1, 2, 3, 4, 5]
            },
            {
                searchPanes: {
                    show: false
                },
                targets: [0]
            }
            ]
        });
    </script>
    <?
}
// generate_report Page generate_auto_report - End

// generate_report_list Page generate_report_list - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $call_function == "generate_report_list") {
    site_log_generate("Approve Sender ID List Page : User : " . $_SESSION['yjwatsp_user_name'] . " Preview on " . date("Y-m-d H:i:s"), '../');

    // Here we can Copy, Export CSV, Excel, PDF, Search, Column visibility the Table        ?>
    <table class="table table-striped" id="table-1">

        <thead>
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Campaign Name</th>
                <th>Campaign Id</th>
                <th>Message Type</th>
                <th>Mobile Number Count</th>
                <th>Campaign Status</th>
                <th>Campaign Date</th>
                <th>Generate Report</th>
            </tr>
        </thead>
        <tbody>
            <?
            $replace_txt = '{
  "user_id" : "' . $_SESSION['yjwatsp_user_id'] . '"
}';
            // Add bearer token
            $bearer_token = 'Authorization: ' . $_SESSION['yjwatsp_bearer_token'] . '';
            // It will call "approve_compose_message" API to verify, can we can we allow to view the approve_compose_message
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $api_url . '/list/generate_report_list',
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
            //  echo $response;
            site_log_generate("Approve Sender ID Page : " . $uname . " Execute the service [$replace_txt,$bearer_token] on " . date("Y-m-d H:i:s"), '../');
            curl_close($curl);
            // After got response decode the JSON result
            $sms = json_decode($response, false);
            site_log_generate("Approve Sender ID Page : " . $uname . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
            // To get the one by one data
            $indicatori = 0;
            if ($sms->num_of_rows > 0) { // If the response is success to execute this condition
                for ($indicator = 0; $indicator < $sms->num_of_rows; $indicator++) {
                    //Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition is false to stop the process
                    $indicatori++;
                    $rcs_entry_date = date('d-m-Y h:i:s A', strtotime($sms->report[$indicator]->rcs_entry_date));
                    ?>
                    <tr>
                        <td>
                            <?= $indicatori ?>
                        </td>
                        <td>
                            <?= $sms->report[$indicator]->user_name ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->campaign_name ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->compose_rcs_id ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->template_category ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $sms->report[$indicator]->total_mobileno_count ?>
                        </td>
                       <?/* <td style="text-align: center;">
                            <?= $sms->report[$indicator]->rcs_status ?>
                        </td>  */?>
  <td style="text-align: center;">
    <?php
    switch ($sms->report[$indicator]->rcs_status) {
        case 'O':
            echo '<a href="#!" class="btn btn-outline-success btn-disabled">Completed</a>';
            break;

        default:
            echo '<a href="#!" class="btn btn-outline-success btn-disabled">Completed</a>';
            break;
    }
    ?>
</td>



<td style="text-align: center;">
                            <?= $sms->report[$indicator]->rcs_entry_date ?>
                        </td>


                        <td style="text-align:center;" id='id_approved_lineno_<?= $indicatori ?>'>
                           
                                <div class="btn-group mb-3" role="group" aria-label="Basic example">
                                    <button type="button" title="Generate Report"
                                        onclick="approve_popup('<?= $sms->report[$indicator]->compose_rcs_id ?>', '<?= $sms->report[$indicator]->user_id ?>')"
                                        class="btn btn-icon btn-success">Generate Report</button>
                                </div>
                           
                        </td>

                    </tr>
                    <?
                }
            } else if ($sms->response_status == 204) {
                site_log_generate("Approve Sender ID Page : " . $user_name . "get the Service response [$sms->response_status] on " . date("Y-m-d H:i:s"), '../');
                $json = array("status" => 2, "msg" => $sms->response_msg);
            } else {
                site_log_generate("Approve Sender ID Page : " . $user_name . " get the Service response [$sms->response_msg] on  " . date("Y-m-d H:i:s"), '../');
                $json = array("status" => 0, "msg" => $sms->response_msg);
            }
            ?>

        </tbody>

    </table>

    <!-- General JS Scripts -->
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/dataTables.searchPanes.min.js"></script>
    <script src="assets/js/dataTables.select.min.js"></script>
    <script src="assets/js/jszip.min.js"></script>
    <script src="assets/js/pdfmake.min.js"></script>
    <script src="assets/js/vfs_fonts.js"></script>
    <script src="assets/js/buttons.html5.min.js"></script>
    <script src="assets/js/buttons.colVis.min.js"></script>
    <!-- filter using -->

    <script>
        $('#table-1').DataTable({
            dom: 'PlBfrtip',
            searchPanes: {
                cascadePanes: true
            },
            searchPanes: {
                initCollapsed: true
            },
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
            }, 'colvis'],
            columnDefs: [{
                searchPanes: {
                    show: true
                },
                targets: [1, 2, 3, 4, 5]
            },

            {
                searchPanes: {
                    show: false
                },
                targets: [0]
            }

            ]
        });
    </script>

    <?
}
// generate_report_list Page generate_report_list - End

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with HTML Response
header('Content-type: text/html');
echo $result_value;
