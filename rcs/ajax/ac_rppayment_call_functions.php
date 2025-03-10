<?php
session_start();
error_reporting(E_ALL);
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

// Include configuration.php
include_once "../api/configuration.php";
// Paytm Operation - Start
extract($_REQUEST);
$current_date = date("Y-m-d H:i:s");
$milliseconds = round(microtime(true) * 1000);

// user_management Page razorpay_payment - Start
if ($_SERVER['REQUEST_METHOD'] == "POST" and $action_process == "razorpay_payment") {

  // It will call "add_message_credit" API to verify, can we access for the add_message_credit list  
  $curl = curl_init();
  curl_setopt_array(
    $curl,
    array(
      CURLOPT_URL => $api_url . '/list/act_pay_user_id',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $replace_txt,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    )
  );

  // Send the data into API and execute 
  site_log_generate("Payment Page : " . $_SESSION['clientname_txt'] . " Execute the service 0 [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
  $response = curl_exec($curl);
  curl_close($curl);
  // After got response decode the JSON result
  $header = json_decode($response, false);
  site_log_generate("Payment Page : " . $_SESSION['clientname_txt'] . " get the Service response 0 [$response] on " . date("Y-m-d H:i:s"), '../');

  $cda = '';
  if ($header->response_status == 200) {
    for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
      // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
      $cda = $header->get_paymentid[$indicator]->payment_id;
    }
  } else if ($header->response_status == 403 || $response == '') { ?>
      <script>
        window.location = "index"
      </script>
  <? }
  if ($cda != '') {
    $_SESSION['user_cda'] = $cda;
    $sms_amount = '3500';
    $orderId = time();
    $txnAmount = $sms_amount;
    $custId = 3;
    $mobileNo = $_SESSION['mobile_no_txt'];
    $email = $_SESSION['user_email'];
    $paytmParams = array();
    $paytmParams["ORDER_ID"] = $orderId;
    $paytmParams["CUST_ID"] = $custId;
    $paytmParams["MOBILE_NO"] = $mobileNo;
    $paytmParams["EMAIL"] = $email;
    $paytmParams["TXN_AMOUNT"] = $txnAmount;
    $data = [
      'payment_id' => htmlspecialchars(strip_tags(isset($_POST['razorpay_payment_id']) ? $_POST['razorpay_payment_id'] : "")),
      'amount' => htmlspecialchars(strip_tags(isset($_POST['totalAmount']) ? $_POST['totalAmount'] : "")),
      'product_id' => htmlspecialchars(strip_tags(isset($_POST['product_id']) ? $_POST['product_id'] : "")),
    ];
    //check payment is authrized or not via API call
    $razorPayId = htmlspecialchars(strip_tags(isset($_POST['razorpay_payment_id']) ? $_POST['razorpay_payment_id'] : ""));
    $ch = curl_init('https://api.razorpay.com/v1/payments/' . $razorPayId . '');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_USERPWD, $rp_keyid . ":" . $rp_keysecret); // Input your Razorpay Key Id and Secret Id here
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch));
    // check that payment is authorized by razorpay or not
    if ($response->status == 'authorized') {
      $respval = array('msg' => 'Payment successfully credited', 'status' => true, 'productCode' => $_POST['product_id'], 'paymentID' => $_POST['razorpay_payment_id'], 'userEmail' => $_POST['useremail']);
      $respval1 = 'msg:Payment successfully credited, status:true, productCode:' . $_POST['product_id'] . ', paymentID:' . $_POST['razorpay_payment_id'] . ', userEmail' . $_POST['useremail'];

      $replace_txt = '{
            "payment_id" : "' . $cda . '",
            "payment_status" : "Y",
            "active_status" : "Y",
            "payment_comments" : "' . $respval1 . '"
          }';
      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/update_activation_payment',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        )
      );
      site_log_generate("approve page sender_id reject Page : " . $_SESSION['clientname_txt'] . " Execute the service 2 [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      curl_close($curl);
      $sms = json_decode($response, false);
      if ($sms->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
      <? }
      site_log_generate("approve page sender_id reject Page : " . $_SESSION['clientname_txt'] . " get the Service response 2 [$response] on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 1, "msg" => "success");
    } else {
      $respval = array('msg' => 'Payment failed', 'status' => false, 'productCode' => $_POST['product_id'], 'paymentID' => $_POST['razorpay_payment_id'], 'userEmail' => $_POST['useremail']);
      $respval1 = 'msg:Payment failed, status:false, productCode:' . $_POST['product_id'] . ', paymentID:' . $_POST['razorpay_payment_id'] . ', userEmail:' . $_POST['useremail'];
      $replace_txt = '{
            "payment_id" : "' . $cda . '",
            "payment_status" : "F",
            "active_status" : "F",
            "payment_comments" : "' . $respval1 . '"
          }';
      // To Get Api URL

      $curl = curl_init();
      curl_setopt_array(
        $curl,
        array(
          CURLOPT_URL => $api_url . '/list/update_activation_payment',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'PUT',
          CURLOPT_POSTFIELDS => $replace_txt,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        )
      );
      site_log_generate("approve page sender_id reject Page : " . $_SESSION['clientname_txt'] . " Execute the service 3 [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
      $response = curl_exec($curl);
      curl_close($curl);
      $sms = json_decode($response, false);
      if ($sms->response_status == 403 || $response == '') { ?>
        <script>
          window.location = "index"
        </script>
      <? }
      site_log_generate("approve page sender_id reject Page : " . $_SESSION['clientname_txt'] . " get the Service response 3 [$response] on " . date("Y-m-d H:i:s"), '../');
      $json = array("status" => 0, "msg" => "Payment Processing is Stop");
    }
  }
}
// user_management Page razorpay_payment - End

// Finally Close all Opened Mysql DB Connection
$conn->close();
// Output header with JSON Response
header('Content-type: application/json');
echo json_encode($json);
?>