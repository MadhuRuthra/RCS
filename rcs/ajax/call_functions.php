<?php
/*
This page has some functions which is access from Frontend.
This page is act as a Backend page which is connect with Node JS API and PHP Frontend.
It will collect the form details and send it to API.
After get the response from API, send it back to Frontend.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 02-Jul-2023
*/

session_start(); // start session
error_reporting(E_ALL); // The error reporting function
include_once "../api/configuration.php"; // Include configuration.php
extract($_REQUEST); // Extract the request
$bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . ""; // To get bearertoken
$current_date = date("Y-m-d H:i:s"); // To get currentdate function
$milliseconds = round(microtime(true) * 1000);  // milliseconds in time

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

// Index Page Signin - Start
if ($_SERVER["REQUEST_METHOD"] == "POST" and $call_function == "signin") {
    // Get data
    $uname = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_username"])
            ? $conn->real_escape_string($_REQUEST["txt_username"])
            : ""
        )
    );
    $password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_password"])
            ? $conn->real_escape_string($_REQUEST["txt_password"])
            : ""
        )
    );
    $upass = md5($password); // For authenticating digital signatures
    $ip_address = $_SERVER["REMOTE_ADDR"]; // To Get IP address
    site_log_generate("Index Page : Username => " . $uname . " trying to login on " . date("Y-m-d H:i:s"), "../");

    // To Send the request  API
    $replace_txt =
        '{
    "txt_username" : "' .
        $uname .
        '",
    "txt_password" : "' .
        $password .
        '",
    "request_id" : "' .
        $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three .
        '"
    }';

    // It will call "p_login" API to verify, can we allow to login the already existing user for access the details
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/login/p_login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    ]);

    // Send the data into API and execute  
    // Log file generate
    site_log_generate(
        "Index Page : " .
        $uname .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);

    // After got response decode the JSON result
    $state1 = json_decode($response, false);

    // Log file generate
    site_log_generate(
        "Index Page : " .
        $uname .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    // To get the API response one by one data and assign to Session
    if ($state1->response_code == 1) {
        // Looping the indicator is less than the count of response_result.if the condition is true to continue the process.if the condition are false to stop the process
        for (
            $indicator = 0;
            $indicator < count($state1->response_result);
            $indicator++
        ) {
            $_SESSION["yjwatsp_parent_id"] =
                $state1->response_result[$indicator]->parent_id;
            $_SESSION["yjwatsp_user_id"] =
                $state1->response_result[$indicator]->user_id;
            $_SESSION["yjwatsp_user_master_id"] =
                $state1->response_result[$indicator]->user_master_id;
            $_SESSION["yjwatsp_user_name"] =
                $state1->response_result[$indicator]->user_name;
            $_SESSION["yjwatsp_user_short_name"] =
                $state1->response_result[$indicator]->user_short_name;
            $_SESSION["yjwatsp_api_key"] =
                $state1->response_result[$indicator]->api_key;
            $_SESSION["yjwatsp_user_permission"] =
                $state1->response_result[$indicator]->user_permission;
            $_SESSION["yjwatsp_bearer_token"] =
                $state1->response_result[$indicator]->bearer_token;
            $_SESSION["yjwatsp_login_id"] =
                $state1->response_result[$indicator]->login_id;
            $_SESSION["yjwatsp_user_email"] =
                $state1->response_result[$indicator]->user_email;
            $_SESSION["yjwatsp_user_mobile"] =
                $state1->response_result[$indicator]->user_mobile;
            $_SESSION["yjwatsp_price_per_sms"] =
                $state1->response_result[$indicator]->price_per_sms;
            $_SESSION["yjwatsp_netoptid"] =
                $state1->response_result[$indicator]->network_operators_id;
            $_SESSION["yjwatsp_usraprstat"] =
                $state1->response_result[$indicator]->user_approval_status;
            $_SESSION["yjwatsp_usr_mgt_status"] =
                $state1->response_result[$indicator]->usr_mgt_status;
            $_SESSION["yjwatsp_login_time"] = $state1->login_time;
        }

        // To log file generate
        site_log_generate(
            "Index Page : " .
            $uname .
            " logged in success on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 1, "info" => $result];  // Send the Success response to Frontend
    } else { // otherwise it willbe execute 
        // Log file generate
        if ($state1->response_status == 403 || $response == '') { ?>
            <script>
                window.location = "index"
            </script>
        <? }
        site_log_generate(
            "Index Page : " .
            $uname .
            " logged in failed [$state1->response_msg] on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 0, "msg" => $state1->response_msg]; // Send the Failure response to Frontend
    }
}
// Index Page Signin - End

// Manage Users Page signup - Start
if ($_SERVER["REQUEST_METHOD"] == "POST" and $call_function == "signup") {
    // Get data
    $user_type = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_user_type"]) ? $_REQUEST["slt_user_type"] : ""
        )
    );
    $clientname_txt = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["clientname_txt"]) ? $_REQUEST["clientname_txt"] : ""
        )
    );
    $user_short_name = substr($clientname_txt, 0, 3);

    $txt_new_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_new_password"])
            ? $_REQUEST["txt_new_password"]
            : ""
        )
    );

    $txt_confirm_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_confirm_password"])
            ? $_REQUEST["txt_confirm_password"]
            : ""
        )
    );

    $txt_user_email = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_user_email"])
            ? $_REQUEST["txt_user_email"]
            : ""
        )
    );
    $txt_user_mobile = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_user_mobile"])
            ? $_REQUEST["txt_user_mobile"]
            : ""
        )
    );


    // Default User permission
    $user_permission = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["user_permission"])
            ? $_REQUEST["user_permission"]
            : "2"
        )
    );
    // Log file generate
    site_log_generate(
        "Manage Users Page : " .
        $loginid .
        " trying to create a new account in our site on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    // To Send the request API
    $replace_txt =
        '{
        "user_type" : "' . $user_type . '",
        "user_name" : "' . $clientname_txt . '",
        "user_password" : "' . $txt_confirm_password . '",
        "user_email" : "' . $txt_user_email . '",
        "user_mobile" : "' . $txt_user_mobile . '",
        "login_shortname" : "' . $user_short_name . '",
       "user_permission" : "' . $user_permission . '",
       "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
    // It will call "signup" API to verify, can we allow to  the Add the new user for signup option 
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // add the bearer
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/login/signup",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute 
    site_log_generate(
        "Manage Users Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);
    // To Generate the log file
    site_log_generate(
        "Manage Users Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    if ($header->response_status == 200) { // If the response is success to execute this condition
        $replace_txt = '{"messaging_product":"whatsapp","to":"919686193535","type":"template","template":{"name":"te_ad1_dhd1_t00000000_231013_449","language":{"code":"en_US"},"components":[{"type":"body","parameters":[{"type":"text","text":"' . $user_name . '"},{"type":"text","text":"' . $user_mobile . '"},{"type":"text","text":"' . $user_email . '"}]}]}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/114726254883383/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $replace_txt,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer EAAlaTtm1XV0BANV3Lc8mA5kEO4BqWsCKudO6lNWGcVyl6O6wIK7mJqXCtPtpyjhO36ZA1eEGLra4Q21T7aEWns1VxqwcOFVR4BtQsxShdMB9zBIPjN4gaj3KTz5ZBHnEtO3WVkC26UdLpM75vIZBIZCw8eCRVus4NcZC7FZC3NhBFqpF3ntmGh13ZAZBdUcVtwJ9Mcout3A1ZCwZDZD',
                'Content-Type: application/json'
            ),
        )
        );

        $response = curl_exec($curl);
        site_log_generate(
            "Manage Users Page : " .
            $_SESSION["yjwatsp_user_name"] .
            " get the Service response [$response] on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 1, "msg" => "New User created. Kindly login!!"];

    } else if ($header->response_status == 201) {
        site_log_generate(
            "Manage Users Page : " .
            $user_name .
            " account creation Failed [$header->response_msg] on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 2, "msg" => $header->response_msg];
    } else { // otherwise it willbe execute
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        site_log_generate(
            "Manage Users Page : " .
            $user_name .
            " account creation Failed [$header->response_msg] on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 0, "msg" => $header->response_msg];
    }
}
// Manage Users Page signup - End



// edit Users Page edit_users - Start
if ($_SERVER["REQUEST_METHOD"] == "POST" and $call_function == "edit_users") {
    // Get data
    $user_type = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_user_type"]) ? $_REQUEST["slt_user_type"] : ""
        )
    );

    $user_id = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["clientid_txt"])
            ? $_REQUEST["clientid_txt"]
            : ""
        )
    );

    $clientname_txt = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["clientname_txt"]) ? $_REQUEST["clientname_txt"] : ""
        )
    );
    $user_short_name = substr($clientname_txt, 0, 3);

    $txt_new_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_new_password"])
            ? $_REQUEST["txt_new_password"]
            : ""
        )
    );

    $txt_confirm_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_confirm_password"])
            ? $_REQUEST["txt_confirm_password"]
            : ""
        )
    );

    $txt_user_email = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_user_email"])
            ? $_REQUEST["txt_user_email"]
            : ""
        )
    );
    $txt_user_mobile = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_user_mobile"])
            ? $_REQUEST["txt_user_mobile"]
            : ""
        )
    );


    // Default User permission
    $user_permission = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["user_permission"])
            ? $_REQUEST["user_permission"]
            : "2"
        )
    );
    // Log file generate
    site_log_generate(
        "Manage Users Page : " .
        $loginid .
        " trying to create a new account in our site on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    // To Send the request API
    $replace_txt =
        '{
        "user_id" : "' . $user_id . '",
        "user_name" : "' . $clientname_txt . '",
        "user_password" : "' . $txt_confirm_password . '",
        "user_mobile" : "' . $txt_user_mobile . '"
  }';
  //echo($replace_txt);
    // It will call "signup" API to verify, can we allow to  the Add the new user for signup option 
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // add the bearer
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/login/edit_users",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute 
    site_log_generate(
        "Manage Users Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);
    // To Generate the log file
    site_log_generate(
        "Manage Users Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    if ($header->num_of_rows > 0) { // If the response is success to execute this condition
        // for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
        //     // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
        //     $replace_login_id = $header->report;
        // }
        $replace_login_id = $header->response_msg;
        $json = ["status" => 1, "msg" => $replace_login_id];
    } else if ($header->response_status == 204) {
        site_log_generate("Manage Whatsappno List Page  : " . $user_name . "get the Service response [$header->response_msg] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $header->response_msg);
    } else { //Otherwise it will be execute
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        $json = ["status" => 0, "msg" => $header->response_msg];
    }
}
// Edit Users Page edit_users - End


// Manage Users Page display_login_id - Start
if (
    $_SERVER["REQUEST_METHOD"] == "POST" and
    $tmpl_call_function == "display_login_id"
) {
    site_log_generate(
        "Manage Users - Generate Login ID Page : User : " .
        $_SESSION["yjwatsp_user_name"] .
        " access the page on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    // Get data
    $slt_user_type = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_user_type"])
            ? $conn->real_escape_string($_REQUEST["slt_user_type"])
            : ""
        )
    );
    $slt_super_admin = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_super_admin"])
            ? $conn->real_escape_string($_REQUEST["slt_super_admin"])
            : ""
        )
    );
    $slt_dept_admin = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_dept_admin"])
            ? $conn->real_escape_string($_REQUEST["slt_dept_admin"])
            : ""
        )
    );
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    if ($slt_user_type == 2) {
        // To Send the request API For Super Admin
        $replace_txt =
            '{
      "user_id" : "' .
            $_SESSION["yjwatsp_user_id"] .
            '",
      "user_type" : "' .
            $slt_user_type .
            '"
    }';
    } elseif ($slt_user_type == 3) {
        // To Send the request API For Dept Admin
        $replace_txt =
            '{  
      "user_id" : "' .
            $_SESSION["yjwatsp_user_id"] .
            '",
      "user_type" : "' .
            $slt_user_type .
            '",
      "super_admin" : "' .
            $slt_super_admin .
            '"
    }';
    } elseif ($slt_user_type == 4) {
        // To Send the request API For Agent
        $replace_txt =
            '{
      "user_id" : "' .
            $_SESSION["yjwatsp_user_id"] .
            '",
      "user_type" : "' .
            $slt_user_type .
            '",
      "super_admin" : "' .
            $slt_super_admin .
            '",
      "dept_admin" : "' .
            $slt_dept_admin .
            '"
    }';
    }
    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // It will call "username_generate" API to verify, can we Auto generate the Add the New Username,Login Id option
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/list/username_generate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute 
    site_log_generate(
        "Manage Users - Generate Login ID Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);
    site_log_generate(
        "Manage Users - Generate Login ID Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    if ($header->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
            // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
            $replace_login_id = $header->report;
        }
        $json = ["status" => 1, "msg" => $replace_login_id];
    } else if ($header->response_status == 204) {
        site_log_generate("Manage Whatsappno List Page  : " . $user_name . "get the Service response [$header->response_msg] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $header->response_msg);
    } else { //Otherwise it will be execute
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        $json = ["status" => 0, "msg" => $header->response_msg];
    }
}
// Manage Users Page display_login_id - End

// Compose Whatsapp Page senderid_template - Start
if (
    $_SERVER["REQUEST_METHOD"] == "POST" and
    $tmpl_call_function == "senderid_template"
) {
    site_log_generate(
        "Compose Whatsapp - Validate Campaign Page : User : " .
        $_SESSION["yjwatsp_user_name"] .
        " access the page on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    // Get data
    $slt_whatsapp_template = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_whatsapp_template"])
            ? strtolower($_REQUEST["slt_whatsapp_template"])
            : ""
        )
    );
    $expl = explode("!", $slt_whatsapp_template);
    // To Send the request API Load Templates
    $load_templates =
        '{
      "template_id" : "' .
        $expl[3] .
        '"
  }';
    site_log_generate(
        "Compose Whatsapp - Validate Campaign Page : User : " .
        $_SESSION["yjwatsp_user_name"] .
        " executed the query ($load_templates) on " .
        date("Y-m-d H:i:s")
    );
    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // It will call "p_get_template_numbers" API to verify, can we use the template details
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/template/p_get_template_numbers",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $load_templates,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute 
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $state1 = json_decode($response, false);
    //generate the log file
    site_log_generate(
        "Compose Whatsapp - Validate Campaign Page : User : " .
        $_SESSION["yjwatsp_user_name"] .
        " executed the query response ($response) on " .
        date("Y-m-d H:i:s")
    );

    $rsmsg .= '<table style="width: 100%;">';

    if ($state1->response_code == 1) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < count($state1->data); $indicator++) {
            // Looping the indicator is less than the count of data.if the condition is true to continue the process.if the condition are false to stop the process
            $cntmonth =
                $state1->data[$indicator]->available_credit -
                $state1->data[$indicator]->sent_count;
            if ($cntmonth > 0) {
                if ($indicator % 2 == 0) {
                    $rsmsg .= "<tr>";
                }
                $rsmsg .=
                    '<td>
          <input type="checkbox" checked class="cls_checkbox" id="txt_whatsapp_mobno" name="txt_whatsapp_mobno[]" tabindex="1" autofocus value="' .
                    $state1->data[$indicator]->store_id .
                    "~~" .
                    $state1->data[$indicator]->whatspp_config_id .
                    "~~" .
                    $state1->data[$indicator]->country_code .
                    $state1->data[$indicator]->mobile_no .
                    "~~" .
                    $state1->data[$indicator]->bearer_token .
                    "~~" .
                    $whatsapp_tmplate_url .
                    $state1->data[$indicator]->whatsapp_business_acc_id .
                    "~~0~~" .
                    $whatsapp_tmplate_url .
                    $state1->data[$indicator]->phone_number_id .
                    '"> <label class="form-label"> ' .
                    $state1->data[$indicator]->country_code .
                    $state1->data[$indicator]->mobile_no .
                    " [Avl. Credits : <b>" .
                    $cntmonth .
                    '</b>]</label>
        </td>';

                if ($indicator % 2 == 1) {
                    $rsmsg .= "</tr>";
                }
            }
        }
    } else if ($state1->response_status == 204) {
        site_log_generate("Compose Whatsapp - Validate Campaign Page  : " . $user_name . "get the Service response [$state1->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $state1->response_msg);
    } else {
        if ($state1->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        site_log_generate("Compose Whatsapp - Validate Campaign Page : " . $user_name . " get the Service response [$state1->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $state1->response_msg);
    }

    $rsmsg .= "</table>";
    $json = ["status" => 1, "msg" => $rsmsg];
}
// Compose Whatsapp Page senderid_template - End

// Change Password Page change_pwd - Start
if (
    $_SERVER["REQUEST_METHOD"] == "POST" and
    $pwd_call_function == "change_pwd"
) {
    site_log_generate(
        "Change Password Page : User : " .
        $_SESSION["yjwatsp_user_name"] .
        " access the page on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    // Get data
    $ex_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_ex_password"])
            ? $_REQUEST["txt_ex_password"]
            : ""
        )
    );
    $new_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_new_password"])
            ? $_REQUEST["txt_new_password"]
            : ""
        )
    );
    //$ex_pass = md5($ex_password); // For authenticating digital signatures
    //$upass = md5($new_password); // For authenticating digital signatures
    // To Send the request API 
    $replace_txt =
        '{
    "user_id" : "' .
        $_SESSION["yjwatsp_user_id"] .
        '",
    "ex_password" : "' .
        $ex_password .
        '",
    "new_password" : "' .
        $new_password .
        '",
 "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';
    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // To Get Api Response URL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/list/change_password",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute 
    site_log_generate(
        "Change Password Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);

    if ($header->response_status == 403 || $response == '') { ?>
        <script>
            window.location = "index"
        </script>
    <? }
    site_log_generate(
        "Change Password Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    // To get the response message
    $json = [
        "status" => $header->response_code,
        "msg" => $header->response_msg,
    ];
}
// Change Password Page change_pwd - End

// Message Credit Page find get_available_balance - Start
if (isset($_POST["get_available_balance"]) == "get_available_balance") {
    // Get data
    $txt_receiver_user = htmlspecialchars(
        strip_tags(
            isset($_POST["txt_receiver_user"])
            ? $conn->real_escape_string($_POST["txt_receiver_user"])
            : ""
        )
    );
    $expl = explode("~~", $txt_receiver_user); // explode function using
// To Send the request API 
    $replace_txt =
        '{
    "user_id" : "' . $_SESSION["yjwatsp_user_id"] . '",
    "select_user_id" : "' . $expl[0] . '"
  }';
    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // It will call "available_credits" API to verify, can we view the available credits to the particular user
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/list/available_credits",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute
    site_log_generate(
        "Message Credit Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);
    site_log_generate(
        "Message Credit Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    if ($header->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
            // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
            $stateData = $header->report[$indicator]->available_messages;
        }
        $json = ["status" => 1, "msg" => "Available Credits : " . $stateData];
    } else if ($header->response_status == 204) {
        site_log_generate("Add Message Credit Page   : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $header->response_msg);
    } else { // Otherwise It willbe execute
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        site_log_generate("Add Message Credit Page  : " . $user_name . " get the Service response [$header->response_msg] on  " . date("Y-m-d H:i:s"), '../');
        $json = [
            "status" => 0,
            "msg" =>
                "Invalid Inputs. Kindly try again with the correct Inputs!",
        ];
    }
}
// Message Credit Page find get_available_balance - End

// Manage Users Page find display_dept_admin - Start
if (
    $_SERVER["REQUEST_METHOD"] == "POST" and
    $tmpl_call_function == "display_dept_admin"
) {
    // Get data
    $slt_user_type = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_user_type"])
            ? $conn->real_escape_string($_REQUEST["slt_user_type"])
            : ""
        )
    );
    $slt_super_admin = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_super_admin"])
            ? $conn->real_escape_string($_REQUEST["slt_super_admin"])
            : ""
        )
    );
    // To Send the request API 
    $replace_txt =
        '{
    "user_id" : "' .
        $_SESSION["yjwatsp_user_id"] .
        '",
    "super_admin" : "' .
        $slt_super_admin .
        '"
  }';
    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // It will call "display_dept_admin" API to verify, can we display the department head admin
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/list/display_dept_admin",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute  
    $response = curl_exec($curl);
    curl_close($curl);
    site_log_generate(
        "Manage Users Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    // After got response decode the JSON result
    $header = json_decode($response, false);
    site_log_generate(
        "Manage Users Page : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    if ($header->num_of_rows > 0) { // If the response is success to execute this condition
        for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
            $stateData .=
                '<option value="' .
                $header->report[$indicator]->user_id .
                "~~" .
                $header->report[$indicator]->user_short_name .
                '">' .
                $header->report[$indicator]->user_name .
                "</option>";
        }
        $json = ["status" => 1, "msg" => $stateData];
    } else if ($header->response_status == 204) {
        site_log_generate("Manage Users Page  : " . $user_name . "get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $header->response_msg);
    } else { // Otherwise It willbe execute
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        $json = ["status" => 0, "msg" => "NO Data Available!"];
    }
}
// Manage Users Page find display_dept_admin - End

// Edit On Boarding Page edit_onboarding - Start
if ($_SERVER["REQUEST_METHOD"] == "POST" and $call_function == "edit_onboarding") {
    site_log_generate("Edit On Boarding Page : User : " . $_SESSION["yjwatsp_user_name"] . " access the page on " . date("Y-m-d H:i:s"), "../");
    // Get data
    $clientname_txt = htmlspecialchars(strip_tags(isset($_REQUEST["clientname_txt"]) ? $conn->real_escape_string($_REQUEST["clientname_txt"]) : ""));
    $contact_person_txt = htmlspecialchars(strip_tags(isset($_REQUEST["contact_person_txt"]) ? $conn->real_escape_string($_REQUEST["contact_person_txt"]) : ""));
    $designation_txt = htmlspecialchars(strip_tags(isset($_REQUEST["designation_txt"]) ? $conn->real_escape_string($_REQUEST["designation_txt"]) : ""));
    $mobile_no_txt = htmlspecialchars(strip_tags(isset($_REQUEST["mobile_no_txt"]) ? $conn->real_escape_string($_REQUEST["mobile_no_txt"]) : ""));
    $email_id_contact = htmlspecialchars(strip_tags(isset($_REQUEST["email_id_contact"]) ? $conn->real_escape_string($_REQUEST["email_id_contact"]) : ""));

    $bill_address = htmlspecialchars(strip_tags(isset($_REQUEST["bill_address"]) ? $conn->real_escape_string($_REQUEST["bill_address"]) : ""));
    $business_name = htmlspecialchars(strip_tags(isset($_REQUEST["business_name"]) ? $conn->real_escape_string($_REQUEST["business_name"]) : ""));
    $cpy_website_details = htmlspecialchars(strip_tags(isset($_REQUEST["cpy_website_details"]) ? $conn->real_escape_string($_REQUEST["cpy_website_details"]) : ""));
    $parent_company_name = htmlspecialchars(strip_tags(isset($_REQUEST["parent_company_name"]) ? $conn->real_escape_string($_REQUEST["parent_company_name"]) : ""));
    $cpy_display_name = htmlspecialchars(strip_tags(isset($_REQUEST["cpy_display_name"]) ? $conn->real_escape_string($_REQUEST["cpy_display_name"]) : ""));
    $cpy_website_details = str_replace("'", "\'", $cpy_website_details);
    $cpy_website_details = str_replace('"', '\"', $cpy_website_details);
    // $cpy_website_details = str_replace('\\', '\/', $cpy_website_details);

    $desp_business_txt = htmlspecialchars(strip_tags(isset($_REQUEST["desp_business_txt"]) ? $conn->real_escape_string($_REQUEST["desp_business_txt"]) : ""));
    $desp_business_txt = str_replace("'", "", $desp_business_txt);
    $desp_business_txt = str_replace('"', '\"', $desp_business_txt);
    $desp_business_txt = str_replace("\&quot;", '&quot;', $desp_business_txt);
    $reg_add_business = htmlspecialchars(strip_tags(isset($_REQUEST["reg_add_business"]) ? $conn->real_escape_string($_REQUEST["reg_add_business"]) : ""));
    $email_id_txt = htmlspecialchars(strip_tags(isset($_REQUEST["email_id_txt"]) ? $conn->real_escape_string($_REQUEST["email_id_txt"]) : ""));
    $contact_no_cmpy = htmlspecialchars(strip_tags(isset($_REQUEST["contact_no_cmpy"]) ? $conn->real_escape_string($_REQUEST["contact_no_cmpy"]) : ""));
    // $profile_display_pic = htmlspecialchars(strip_tags(isset($_REQUEST["profile_display_pic"]) ? $conn->real_escape_string($_REQUEST["profile_display_pic"]) : ""));

    $slt_service_category = htmlspecialchars(strip_tags(isset($_REQUEST["slt_service_category"]) ? $conn->real_escape_string($_REQUEST["slt_service_category"]) : ""));
    $sender_id_txt = htmlspecialchars(strip_tags(isset($_REQUEST["sender_id_txt"]) ? $conn->real_escape_string($_REQUEST["sender_id_txt"]) : ""));
    $sender_id_txt = str_replace("'", "\'", $sender_id_txt);
    $sender_id_txt = str_replace('"', '\"', $sender_id_txt);
    $sender_id_txt = str_replace('\\', '\/', $sender_id_txt);

    $sender_id_2_txt = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["sender_id2_txt"])
            ? $conn->real_escape_string($_REQUEST["sender_id2_txt"])
            : ""
        )
    );
    $sender_id_2_txt = str_replace("'", "\'", $sender_id_2_txt);
    $sender_id_2_txt = str_replace('"', '\"', $sender_id_2_txt);
    $sender_id_2_txt = str_replace('\\', '/', $sender_id_2_txt);
    //$sender_id_txt_1_txt = htmlspecialchars(strip_tags(isset($_REQUEST["sender_id_txt_1_txt"]) ? $conn->real_escape_string($_REQUEST["sender_id_txt_1_txt"]) : ""));
    $sender_id_txt_1_txt = str_replace("'", "\'", $sender_id_txt_1_txt);
    $sender_id_txt_1_txt = str_replace('"', '\"', $sender_id_txt_1_txt);
    $sender_id_txt_1_txt = str_replace('\\', '\/', $sender_id_txt_1_txt);
    // $sender_id_txt_2_txt = htmlspecialchars(strip_tags(isset($_REQUEST["sender_id_txt_2_txt"]) ? $conn->real_escape_string($_REQUEST["sender_id_txt_2_txt"]) : ""));
    $sender_id_txt_2_txt = str_replace("'", "", $sender_id_txt_2_txt);
    $sender_id_txt_2_txt = str_replace('"', '\"', $sender_id_txt_2_txt);
    // $sender_id_txt_2_txt = str_replace('\\', '\/', $sender_id_txt_2_txt);
    $type_of_message = htmlspecialchars(strip_tags(isset($_REQUEST["type_of_message"]) ? $conn->real_escape_string($_REQUEST["type_of_message"]) : ""));

    $otp_in_process = htmlspecialchars(strip_tags(isset($_REQUEST["otp_in_process"]) ? $conn->real_escape_string($_REQUEST["otp_in_process"]) : ""));
    $enquiry_approve_txt = htmlspecialchars(strip_tags(isset($_REQUEST["enquiry_approve_txt"]) ? $conn->real_escape_string($_REQUEST["enquiry_approve_txt"]) : ""));
    $privacy_terms_txt = htmlspecialchars(strip_tags(isset($_REQUEST["privacy_terms_txt"]) ? $conn->real_escape_string($_REQUEST["privacy_terms_txt"]) : ""));
    $terms_condition_txt = htmlspecialchars(strip_tags(isset($_REQUEST["terms_condition_txt"]) ? $conn->real_escape_string($_REQUEST["terms_condition_txt"]) : ""));
    $proof_document_slt = htmlspecialchars(strip_tags(isset($_REQUEST["proof_document_slt"]) ? $conn->real_escape_string($_REQUEST["proof_document_slt"]) : ""));

    // $proof_document = htmlspecialchars(strip_tags(isset($_REQUEST["proof_document"]) ? $conn->real_escape_string($_REQUEST["proof_document"]) : ""));
    $expected_volumes_day = htmlspecialchars(strip_tags(isset($_REQUEST["expected_volumes_day"]) ? $conn->real_escape_string($_REQUEST["expected_volumes_day"]) : ""));

    if ($_FILES['profile_display_pic']['name'] != '') {
        $path_parts = pathinfo($_FILES["profile_display_pic"]["name"]);
        $extension = $path_parts['extension'];
        $filename_profile = $_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $extension;

        /* Location */
        $location = "../uploads/whatsapp_images/" . $filename_profile;
        $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
        $imageFileType = strtolower($imageFileType);

        switch ($imageFileType) {
            case 'jpg':
            case 'jpeg':
                $mime_type = "image/jpeg";
                break;
            case 'png':
                $mime_type = "image/png";
                break;
        }

        /* Valid extensions */
        $valid_extensions = array("jpg", "jpeg", "png");

        $rspns = '';
        if (move_uploaded_file($_FILES['profile_display_pic']['tmp_name'], $location)) {
            site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
        }
    } else {
        $filename_profile = '';
    }

    if ($_FILES['proof_document']['name'] != '') {
        $path_parts = pathinfo($_FILES["proof_document"]["name"]);
        $extension = $path_parts['extension'];
        $filename_proof_document = $_SESSION['yjwatsp_user_id'] . "_" . $milliseconds . "." . $extension;

        /* Location */
        $location = "../uploads/whatsapp_docs/" . $filename_proof_document;
        $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
        $imageFileType = strtolower($imageFileType);

        $rspns = '';
        if (move_uploaded_file($_FILES['proof_document']['tmp_name'], $location)) {
            site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
        }
    } else {
        $filename_proof_document = '';
    }

    $rep_profile_display_pic = "";
    if ($filename_profile != '') {
        $rep_profile_display_pic = '"profile_display_pic" : "' . $filename_profile . '",';
    }

    $rep_proof_document = "";
    if ($filename_proof_document != '') {
        $rep_proof_document = '"proof_document" : "' . $filename_proof_document . '",';
    }

    // To Send the request API
    $replace_txt = '{
    "user_id" : "' . $_SESSION["yjwatsp_user_id"] . '",
    "clientname_txt" : "' . $clientname_txt . '",
    "contact_person_txt" : "' . $contact_person_txt . '",
    "designation_txt" : "' . $designation_txt . '",
    "mobile_no_txt" : "' . $mobile_no_txt . '",
    "email_id_contact" : "' . $email_id_contact . '",
  
    "bill_address" : "' . $bill_address . '",
    "business_name" : "' . $business_name . '",
    "cpy_website_details" : "' . $cpy_website_details . '",
    "parent_company_name" : "' . $parent_company_name . '",
    "cpy_display_name" : "' . $cpy_display_name . '",

    "desp_business_txt" : "' . $desp_business_txt . '",
    "reg_add_business" : "' . $reg_add_business . '",
    "email_id_txt" : "' . $email_id_txt . '",
    "contact_no_cmpy" : "' . $contact_no_cmpy . '",
    ' . $rep_profile_display_pic . '

    "slt_service_category" : "' . $slt_service_category . '",
    "sender_id_txt" : "' . $sender_id_txt . '",
    "sender_id_2_txt " : "' . $sender_id_2_txt . '",
    "sender_id_txt_1_txt" : "' . $sender_id_txt_1_txt . '",
    "sender_id_txt_2_txt" : "' . $sender_id_txt_2_txt . '",
    "type_of_message" : "' . $type_of_message . '",

    "otp_in_process" : "' . $otp_in_process . '",
    "enquiry_approve_txt" : "' . $enquiry_approve_txt . '",
    "privacy_terms_txt" : "' . $privacy_terms_txt . '",
    "terms_condition_txt" : "' . $terms_condition_txt . '",
    "proof_document_slt" : "' . $proof_document_slt . '",

    ' . $rep_proof_document . '
    "expected_volumes_day" : "' . $expected_volumes_day . '",
    "request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
    }';

    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";

    // To Get Api Response URL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url . '/list/edit_onboarding',
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
    site_log_generate("Edit On Boarding Page : " . $_SESSION["yjwatsp_user_name"] . " Execute the service [$replace_txt, $bearer_token] on " . date("Y-m-d H:i:s"), "../");
    $response = curl_exec($curl);
    curl_close($curl);

    // After got response decode the JSON result
    $header = json_decode($response, false);
    site_log_generate("Edit On Boarding Page : " . $_SESSION["yjwatsp_user_name"] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), "../");

    // To get the response message
    if ($header->response_status == 200) {
        site_log_generate("Edit On Boarding Page : " . $user_name . " On Boarding form updation Success on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 1, "msg" => "On Boarding form updated successfully");
    } else if ($header->response_status == 201) {
        site_log_generate("Edit On Boarding Page : " . $user_name . " get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $header->response_msg);
    } else {
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        site_log_generate("Edit On Boarding Page : " . $user_name . " On Boarding form updation Failed [Invalid Inputs] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => "On Boarding form updation failed [Invalid Inputs]. Kindly try again with the correct Inputs!");
    }
}
// Edit On Boarding Page edit_onboarding - End

// Onboarding Page signup - Start
if ($_SERVER["REQUEST_METHOD"] == "POST" and $temp_call_function == "onboarding_signup") {
    // Get data
    $client_name = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["clientname_txt"]) ? $_REQUEST["clientname_txt"] : ""
        )
    );
    $user_short_name = substr($client_name, 0, 3);
    $contact_person = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["contact_person_txt"]) ? $_REQUEST["contact_person_txt"] : ""
        )
    );
    $contact_person_designation = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["designation_txt"])
            ? $_REQUEST["designation_txt"]
            : ""
        )
    );
    $contact_person_mobileno = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["mobile_no_txt"]) ? $_REQUEST["mobile_no_txt"] : ""
        )
    );
    $contact_person_email_id = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["email_id_contact"])
            ? $_REQUEST["email_id_contact"]
            : ""
        )
    );
    $bill_address = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["bill_address"])
            ? $_REQUEST["bill_address"]
            : ""
        )
    );
    /*******************************************************/
    $business_name = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["business_name"])
            ? $_REQUEST["business_name"]
            : ""
        )
    );
    $cpy_website_details = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["cpy_website_details"])
            ? $_REQUEST["cpy_website_details"]
            : ""
        )
    );
    $parent_company_name = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["parent_company_name"]) ? $_REQUEST["parent_company_name"] : ""
        )
    );


    $cpy_website_details = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["cpy_website_details"])
            ? $_REQUEST["cpy_website_details"]
            : ""
        )
    );

    $cpy_website_details = str_replace("'", "\'", $cpy_website_details);
    $cpy_website_details = str_replace('"', '\"', $cpy_website_details);
    $cpy_website_details = str_replace('\\', '/', $cpy_website_details);

    $parent_company_name = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["parent_company_name"]) ? $_REQUEST["parent_company_name"] : ""
        )
    );
    $cpy_display_name = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["cpy_display_name"])
            ? $_REQUEST["cpy_display_name"]
            : ""
        )
    );

    $desp_business_txt = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["desp_business_txt"])
            ? $_REQUEST["desp_business_txt"]
            : ""
        )
    );
    $desp_business_txt = str_replace("'", "", $desp_business_txt);
    $desp_business_txt = str_replace('"', '&quot;', $desp_business_txt);
    //$desp_business_txt = str_replace('\\', '/', $desp_business_txt);

    $reg_add_business = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["reg_add_business"])
            ? $_REQUEST["reg_add_business"]
            : ""
        )
    );
    $official_email = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["email_id_txt"])
            ? $_REQUEST["email_id_txt"]
            : ""
        )
    );
    $official_mobile_no = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["contact_no_cmpy"])
            ? $_REQUEST["contact_no_cmpy"]
            : ""
        )
    );

    if ($_FILES['profile_display_pic']['name'] != '') {
        $image_size = $_FILES['profile_display_pic']['size'];
        $image_type = $_FILES['profile_display_pic']['type'];
        $file_type = explode("/", $image_type);
        $filename_profile = $client_name . "_" . $milliseconds . "." . $file_type[1];
        $location = $full_pathurl . "uploads/whatsapp_images/" . $filename_profile;
        $location_1 = $site_url . "uploads/whatsapp_images/" . $filename_profile;
        $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
        $imageFileType = strtolower($imageFileType);

        switch ($imageFileType) {
            case 'jpg':
            case 'jpeg':
                $mime_type = "image/jpeg";
                break;
            case 'png':
                $mime_type = "image/png";
                break;
        }

        /* Valid extensions */
        $valid_extensions = array("jpg", "jpeg", "png");

        $rspns = '';
        if (move_uploaded_file($_FILES['profile_display_pic']['tmp_name'], $location)) {
            site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
        }
    } else {
        $filename_profile = '';
    }
    $slt_service_category = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_service_category"])
            ? $_REQUEST["slt_service_category"]
            : ""
        )
    );

    $sender = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["sender_id_txt"])
            ? $_REQUEST["sender_id_txt"]
            : ""
        )
    );
    $sender = str_replace("'", "\'", $sender);
    $sender = str_replace('"', '\"', $sender);
    $sender = str_replace('\\', '/', $sender);

    $sender_id_2_txt = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["sender_id2_txt"])
            ? $_REQUEST["sender_id2_txt"]
            : ""
        )
    );
    $sender_id_2_txt = str_replace("'", "\'", $sender_id_2_txt);
    $sender_id_2_txt = str_replace('"', '\"', $sender_id_2_txt);
    $sender_id_2_txt = str_replace('\\', '/', $sender_id_2_txt);

    $sender1 = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["sender_id_txt_1_txt"]) ? $_REQUEST["sender_id_txt_1_txt"] : ""
        )
    );
    $sender1 = str_replace("'", "\'", $sender1);
    $sender1 = str_replace('"', '\"', $sender1);
    $sender1 = str_replace('\\', '/', $sender1);
    $sender2 = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["sender_id_txt_2_txt"])
            ? $_REQUEST["sender_id_txt_2_txt"]
            : ""
        )
    );
    $sender2 = str_replace("'", "\'", $sender2);
    $sender2 = str_replace('"', '\"', $sender2);
    $sender2 = str_replace('\\', '/', $sender2);
    $type_of_message = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["type_of_message"])
            ? $_REQUEST["type_of_message"]
            : ""
        )
    );
    $otp_in_process = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["otp_in_process"])
            ? $_REQUEST["otp_in_process"]
            : ""
        )
    );
    $enquiry_approval = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["enquiry_approve_txt"])
            ? $_REQUEST["enquiry_approve_txt"]
            : ""
        )
    );
    $enquiry_approval = str_replace("'", "\'", $enquiry_approval);
    $enquiry_approval = str_replace('"', '\"', $enquiry_approval);
    $enquiry_approval = str_replace('\\', '/', $enquiry_approval);

    $privacy_terms = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["privacy_terms_txt"])
            ? $_REQUEST["privacy_terms_txt"]
            : ""
        )
    );
    $privacy_terms = str_replace("'", "\'", $privacy_terms);
    $privacy_terms = str_replace('"', '\"', $privacy_terms);
    $privacy_terms = str_replace('\\', '/', $privacy_terms);
    $terms_conditions = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["terms_condition_txt"])
            ? $_REQUEST["terms_condition_txt"]
            : ""
        )
    );
    $terms_conditions = str_replace("'", "\'", $terms_conditions);
    $terms_conditions = str_replace('"', '\"', $terms_conditions);
    $terms_conditions = str_replace('\\', '/', $terms_conditions);

    $proof_document_slt = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["proof_document_slt"])
            ? $_REQUEST["proof_document_slt"]
            : ""
        )
    );

    if ($_FILES['proof_document']['name'] != '') {
        $image_size = $_FILES['proof_document']['size'];
        $image_type = $_FILES['proof_document']['type'];
        $file_type = explode("/", $image_type);

        $filename_proof_document = $client_name . "_" . $milliseconds . "." . $file_type[1];
        $location = $full_pathurl . "uploads/whatsapp_docs/" . $filename_proof_document;
        $location_1 = $site_url . "uploads/whatsapp_docs/" . $filename_proof_document;
        $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
        $imageFileType = strtolower($imageFileType);
        // switch ($imageFileType) {
        //  case 'pdf':
        //   case 'jpeg':
        //     $mime_type = "image/jpeg";
        //     break;
        //   case 'png':
        //     $mime_type = "image/png";
        //     break;
        // }

        /* Valid extensions */
        $valid_extensions = array("pdf");

        $rspns = '';
        if (move_uploaded_file($_FILES['proof_document']['tmp_name'], $location)) {
            site_log_generate("Manage Sender ID Page : User : " . $_SESSION['yjwatsp_user_name'] . " whatsapp_images file moved into Folder on " . date("Y-m-d H:i:s"), '../');
        }
    } else {
        $filename_proof_document = '';
    }
    $volume_day_expected = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["expected_volumes_day"])
            ? $_REQUEST["expected_volumes_day"]
            : ""
        )
    );
    $user_type = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["slt_super_admin"]) ? $_REQUEST["slt_super_admin"] : ""
        )
    );
    // Sign user - default password for all user
    $user_password = "Password@123";
    $confirm_password = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["txt_confirm_password"])
            ? $_REQUEST["txt_confirm_password"]
            : ""
        )
    );
    // Default User permission
    $user_permission = htmlspecialchars(
        strip_tags(
            isset($_REQUEST["user_permission"])
            ? $_REQUEST["user_permission"]
            : "2"
        )
    );
    // Log file generate
    site_log_generate(
        "Onboarding Page signup  : " .
        $loginid .
        " trying to create a new account in our site on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    // To Send the request API
    $replace_txt =
        '{
        "slt_super_admin" : "NUll",
        "slt_dept_admin" : "NUll",
        "login_shortname" : "' . $user_short_name . '",
        "user_password" : "' . $user_password . '",
       "user_permission" : "' . $user_permission . '",
       "client_name" : "' . $client_name . '",
       "contact_person" : "' . $contact_person . '",
       "contact_person_designation" : "' . $contact_person_designation . '",
       "contact_person_mobileno" : "' . $contact_person_mobileno . '",
       "contact_person_email_id" : "' . $contact_person_email_id . '",
       "billing_address" : "' . $bill_address . '",
       "company_name" : "' . $business_name . '",
       "company_website" : "' . $cpy_website_details . '",
        "parent_company_name" : "' . $parent_company_name . '",
        "company_display_name" : "' . $cpy_display_name . '",
        "description_business" : "' . $desp_business_txt . '",
        "reg_official_address_cpy" : "' . $reg_add_business . '",
        "official_email" : "' . $official_email . '",
        "official_mobile_no" : "' . $official_mobile_no . '",
        "profile_image" : "' . $filename_profile . '",
        "business_category" : "' . $slt_service_category . '",
        "sender" : "' . $sender . '",
        "sender_txt_2" : "' . $sender_id_2_txt . '",
        "sender1" : "' . $sender1 . '",
        "sender2" : "' . $sender2 . '",
        "message_type" : "' . $type_of_message . '",
        "otp_process" : "' . $otp_in_process . '",
        "enquiry_approval" : "' . $enquiry_approval . '",
        "privacy_terms" : "' . $privacy_terms . '",
        "terms_conditions" : "' . $terms_conditions . '",
        "document_proof" : "' . $filename_proof_document . '",
        "proof_doc_name" : "' . $proof_document_slt . '",
        "volume_day_expected" : "' . $volume_day_expected . '",
        "request_id" : "' . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
  }';

    // It will call "onboarding_signup" API to verify, can we allow to  the Add the new user for signup option 
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";
    // add the bearer
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url . "/login/onboarding_signup",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $replace_txt,
        CURLOPT_HTTPHEADER => [$bearer_token, "Content-Type: application/json"],
    ]);
    // Send the data into API and execute 
    site_log_generate(
        "Onboarding Page signup : " .
        $_SESSION["yjwatsp_user_name"] .
        " Execute the service [$replace_txt] on " .
        date("Y-m-d H:i:s"),
        "../"
    );
    $response = curl_exec($curl);
    curl_close($curl);
    // After got response decode the JSON result
    $header = json_decode($response, false);
    // To Generate the log file
    site_log_generate(
        "Onboarding Page signup  : " .
        $_SESSION["yjwatsp_user_name"] .
        " get the Service response [$response] on " .
        date("Y-m-d H:i:s"),
        "../"
    );

    if ($header->response_status == 200) { // If the response is success to execute this condition
        $replace_txt = '{"messaging_product":"whatsapp","to":"919686193535","type":"template","template":{"name":"te_ad1_dhd1_t00000000_231013_449","language":{"code":"en_US"},"components":[{"type":"body","parameters":[{"type":"text","text":"' . $client_name . '"},{"type":"text","text":"' . $official_mobile_no . '"},{"type":"text","text":"' . $official_email . '"}]}]}}';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v15.0/114726254883383/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $replace_txt,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer EAAlaTtm1XV0BANV3Lc8mA5kEO4BqWsCKudO6lNWGcVyl6O6wIK7mJqXCtPtpyjhO36ZA1eEGLra4Q21T7aEWns1VxqwcOFVR4BtQsxShdMB9zBIPjN4gaj3KTz5ZBHnEtO3WVkC26UdLpM75vIZBIZCw8eCRVus4NcZC7FZC3NhBFqpF3ntmGh13ZAZBdUcVtwJ9Mcout3A1ZCwZDZD',
                'Content-Type: application/json'
            ),
        )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        site_log_generate(
            "Manage Users Page : " .
            $_SESSION["yjwatsp_user_name"] .
            " get the Service response [$replace_txt ---> $response] on " .
            date("Y-m-d H:i:s"),
            "../"
        );

        site_log_generate(
            "Onboarding Page signup : " .
            $user_name .
            " account created successfully on " .
            date("Y-m-availd H:i:s"),
            "../"
        );
        $json = ["status" => 1, "msg" => "New User created. Kindly login!!"];
    } else if ($header->response_status == 201) {
        site_log_generate(
            "Onboarding Page signup : " .
            $user_name .
            " account creation Failed [$header->response_msg] on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 2, "msg" => $header->response_msg];
    } else { // otherwise it willbe execute
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        site_log_generate(
            "Onboarding Page signup  : " .
            $user_name .
            " account creation Failed [$header->response_msg] on " .
            date("Y-m-d H:i:s"),
            "../"
        );
        $json = ["status" => 0, "msg" => $header->response_msg];
    }
}
// Onboarding Page signup - End

// View On Boarding Page apprej_onboarding - Start
if ($_SERVER["REQUEST_METHOD"] == "POST" and $call_function == "apprej_onboarding") {
    site_log_generate("View On Boarding Page : User : " . $_SESSION["yjwatsp_user_name"] . " access the page on " . date("Y-m-d H:i:s"), "../");
    // Get data
    $txt_user = htmlspecialchars(strip_tags(isset($_REQUEST["txt_user"]) ? $conn->real_escape_string($_REQUEST["txt_user"]) : ""));
    $txt_remarks = htmlspecialchars(strip_tags(isset($_REQUEST["txt_remarks"]) ? $conn->real_escape_string($_REQUEST["txt_remarks"]) : ""));
    $aprj_status = htmlspecialchars(strip_tags(isset($_REQUEST["aprj_status"]) ? $conn->real_escape_string($_REQUEST["aprj_status"]) : ""));

    $rep_txt_remarks = "";
    if ($txt_remarks != '') {
        $rep_txt_remarks = '"txt_remarks" : "' . $txt_remarks . '",';
    }

    // To Send the request API
    $replace_txt = '{
		"user_id" : "' . $_SESSION["yjwatsp_user_id"] . '",
		"change_user_id" : "' . $txt_user . '",
		' . $rep_txt_remarks . '
		"aprj_status" : "' . $aprj_status . '",
		"request_id" : "' . $_SESSION["yjwatsp_user_short_name"] . "_" . $year . $julian_dates . $hour_minutes_seconds . "_" . $random_generate_three . '"
		}';

    // Add bearer token
    $bearer_token = "Authorization: " . $_SESSION["yjwatsp_bearer_token"] . "";

    // To Get Api Response URL
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $api_url . '/list/approve_reject_onboarding',
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
    site_log_generate("View On Boarding Page : " . $_SESSION["yjwatsp_user_name"] . " Execute the service approve_reject_onboarding [$replace_txt, $bearer_token] on " . date("Y-m-d H:i:s"), "../");
    $response = curl_exec($curl);
    curl_close($curl);

    // After got response decode the JSON result
    $header = json_decode($response, false);
    site_log_generate("View On Boarding Page : " . $_SESSION["yjwatsp_user_name"] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), "../");

    // To get the response message
    if ($header->response_status == 200) {
        site_log_generate("View On Boarding Page : " . $user_name . " On Boarding form updation Success on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 1, "msg" => "On Boarding form updated successfully");
    } else if ($header->response_status == 201) {
        site_log_generate("View On Boarding Page : " . $user_name . " get the Service response [$header->response_status] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 2, "msg" => $header->response_msg);
    } else {
        if ($header->response_status == 403 || $response == '') { ?>
                <script>
                    window.location = "index"
                </script>
        <? }
        site_log_generate("View On Boarding Page : " . $user_name . " On Boarding form updation Failed [Invalid Inputs] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => "On Boarding form updation failed [Invalid Inputs]. Kindly try again with the correct Inputs!");
    }
}
// View On Boarding Page apprej_onboarding - End

// View On Boarding Page apprej_onboarding - End

if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmp_call_fucntions == "autofill") {
    // Get data
    $user_email = htmlspecialchars(strip_tags(isset($_REQUEST['email_id_contact']) ? $_REQUEST['email_id_contact'] : ""));
    $mobile_no_txt = htmlspecialchars(strip_tags(isset($_REQUEST['mobile_no_txt']) ? $_REQUEST['mobile_no_txt'] : ""));

    $replace_txt = '{';
    if ($user_email != '') {
        $replace_txt .= '"user_email" : "' . $user_email . '",';
    }
    if ($mobile_no_txt != '') {
        $replace_txt .= '"user_mobile" : "' . $mobile_no_txt . '",';
    }
    $replace_txt = str_replace(",", "", $replace_txt);
    $replace_txt .= '}';
    // echo $replace_txt;
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $api_url . '/list/activation_details',
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
                'Content-Type: application/json'
            ),
        )
    );
    site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
    $response = curl_exec($curl);
    curl_close($curl);
    // echo $response;
    if ($response == '') { ?>
        <script>window.location = "index"</script>
    <? }

    $header = json_decode($response, false);
    site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
    if ($header->response_status == 403) { ?>
        <script>window.location = "index"</script>
    <? }
    if ($header->response_status == 200) {
        for ($indicator = 0; $indicator < $header->num_of_rows; $indicator++) {
            // Looping the indicator is less than the num_of_rows.if the condition is true to continue the process.if the condition are false to stop the process
            $email_id = $header->get_payment[$indicator]->email_id;
            $mobile_no = $header->get_payment[$indicator]->mobile_no;
        }
        site_log_generate("Manage Users Page : " . $user_name . " account created successfully on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 1, "msg" => $email_id . "&" . $mobile_no);
    } else {
        site_log_generate("Manage Users Page : " . $user_name . " account creation Failed [$header->response_msg] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => "This User is not pay the activation payment. Kindly pay it first");
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" and $tmpl_call_function == "activation_payment") {
    // Get data
    $_SESSION['clientname_txt'] = htmlspecialchars(strip_tags(isset($_REQUEST['clientname_txt']) ? $_REQUEST['clientname_txt'] : ""));
    $_SESSION['user_email'] = htmlspecialchars(strip_tags(isset($_REQUEST['email_id_contact']) ? $_REQUEST['email_id_contact'] : ""));
    $_SESSION['mobile_no_txt'] = htmlspecialchars(strip_tags(isset($_REQUEST['mobile_no_txt']) ? $_REQUEST['mobile_no_txt'] : ""));
    $replace_txt = '{
    "user_email" : "' . $_SESSION['user_email'] . '",
    "user_mobile" : "' . $_SESSION['mobile_no_txt'] . '",
    "user_name" : "' . $_SESSION['clientname_txt'] . '",
    "product_name" : "Celebmedia whatsapp service activation charges" ,
    "price" : "35000"
  }';

    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $api_url . '/list/activation_payment',
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
                'Content-Type: application/json'
            ),
        )
    );
    site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " Execute the service [$replace_txt] on " . date("Y-m-d H:i:s"), '../');
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response == '') { ?>
        <script>window.location = "index"</script>
    <? }

    $header = json_decode($response, false);
    site_log_generate("Manage Users Page : " . $_SESSION['yjwatsp_user_name'] . " get the Service response [$response] on " . date("Y-m-d H:i:s"), '../');
    if ($header->response_status == 403) { ?>
        <script>window.location = "index"</script>
    <? }
    if ($header->response_status == 200) {
        site_log_generate("Manage Users Page : " . $user_name . " account created successfully on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 1, "msg" => "Success");
    } else {
        site_log_generate("Manage Users Page : " . $user_name . " account creation Failed [$header->response_msg] on " . date("Y-m-d H:i:s"), '../');
        $json = array("status" => 0, "msg" => $header->response_msg);
    }
}

// Finally Close all Opened Mysql DB Connection
$conn->close();

// Output header with JSON Response
header("Content-type: application/json");
echo json_encode($json);
