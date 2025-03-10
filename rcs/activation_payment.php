<?
/*
This page is used to authendicate the user.
Every valid user can login here to access their
role based services.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 01-Jul-2023
*/
session_start(); // To start session
error_reporting(E_ALL); // The error reporting function
extract($_REQUEST);
include_once('api/configuration.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Activation Payment :: Celebdigital
    </title>
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <style>
        .progress {
            height: 0.3rem !important;
        }

        .tab_signup {
            width: 100%;
        }

        .label {
            top: 5px;
            font-weight: bold;
        }

        select option {
            text-transform: capitalize;
        }

        .nav-tabs .nav-item .nav-link.active {
            color: #FFF;
            background-color: #568381;
        }

        .btn:not(:disabled):not(.disabled) {
            line-height: 25px !important;
        }

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
    </style>

<body style="background:url(assets/img/bg_home.jpg); background-repeat:no-repeat; background-size:cover;">
    <div class="theme-loader"></div>
    <div id="app">
        <section class="section">

            <form class="needs-validation" novalidate action="#" name="frm_signup" id='frm_signup'
                enctype="multipart/form-data" method="post">
                <div class="mt-5">
                    <div class="row" style="margin-right: 15px;">

                        <!-- Signup -->
                        <div class="col-12" id="tab_signup" style="display:block;">
                            <div class=" col-sm-12 col-md-5 login-brand" style="float: left;">
                                <img src="assets/img/cm-logo.png" alt="logo" style="width: 75%">
                            </div>

                            <div class="col-sm-12 col-md-1" style="float: left">&nbsp;</div>
                            <div class="card card-success col-sm-12 col-md-5">

                                <div class="col-12">
                                    <div class="row m-t-1">
                                        <!-- <div class="col-md-12 text-right"><a class="nav-link" href="uploads/imports/onboarding_form_help.pdf" download style="color:#FF0000; font-weight: bold" title="Download the Onboarding Help Document Here">Download Help Document</a></div> -->
                                    </div>
                                    <div class="row m-b-20">

                                        <div class="col-md-12">
                                            <h3 class="text-center"><i class="icofont icofont-sign-in"></i>Activation
                                                Payment</h3>
                                        </div>
                                    </div>

                                    <!-- <div class="card-outline-tabs"> -->
                                    <!-- <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist" style="width: 100%;">
                  <li class="nav-item" style="width: 30%; text-align: center; font-weight: bold">
                    <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true" style="font-weight: bold">Basic Information</a>
                  </li>
                  <li class="nav-item" style="width: 30%; text-align: center; font-weight: bold">
                    <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false" style="font-weight: bold">Company Information</a>
                  </li>
                  <li class="nav-item" style="width: 40%; text-align: center; font-weight: bold">
                    <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill" href="#custom-tabs-four-settings" role="tab" aria-controls="custom-tabs-four-settings" aria-selected="false" style="font-weight: bold">Communication Information</a>
                  </li>
                </ul>
              </div> -->

                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-four-tabContent">
                                            <div class="tab-pane fade show active" id="custom-tabs-four-home"
                                                role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">

                                                <div class="row mt-2">
                                                    <div class="col-6 label">
                                                        User Name<label style="color:#FF0000">*</label>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" name="clientname_txt" id="clientname_txt"
                                                            class="form-control" value="" maxlength="50" tabindex="1"
                                                            autofocus="" required="" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="User Name" placeholder="User Name"
                                                            pattern="[a-zA-Z0-9 -_]+"
                                                            onkeypress="return clsAlphaNoOnly(event)"
                                                            onpaste="return false;">
                                                    </div>
                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-6 label">
                                                        Mobile Number<label style="color:#FF0000">*</label>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" name="mobile_no_txt" id="mobile_no_txt"
                                                            class="form-control" value="" maxlength="10" tabindex="4"
                                                            autofocus="" required="" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            onblur="mobile_call_validate()"
                                                            data-original-title="Mobile Number"
                                                            placeholder="Mobile Number"
                                                            onkeypress="return (event.charCode !=8 && event.charCode ==0 ||  (event.charCode >= 48 && event.charCode <= 57))">
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-6 label">
                                                        Email ID<label style="color:#FF0000">*</label>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" name="email_id_contact" id="email_id_contact"
                                                            class="form-control" value="" maxlength="100" tabindex="5"
                                                            autofocus="" required="" data-toggle="tooltip"
                                                            onblur="email_validate()" data-placement="top" title=""
                                                            data-original-title="Email ID" placeholder="Email ID">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- </div> -->
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                </div>

                                <div class="card-body tab_signup">
                                    <div class="row">
                                        <div class="col-md-12" style="text-align:left;">
                                            <span class="text-inverse"
                                                style="color:#FF0000 !important; font-weight: bold;">* Fields are
                                                Mandatory</span>
                                        </div>

                                    </div>
                                    <div class="row m-t-30">
                                        <div class="col-md-12" style="text-align:center;">
                                            <span class="error_display text-center"
                                                id='id_error_display_onboarding'></span>&nbsp;
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>

                                    <div class="row  m-t-30">
                                        <div class="col-md-12" style="text-align:center">
                                            <input type="hidden" class="form-control" name='tmpl_call_function'
                                                id='tmpl_call_function' value='activation_payment' />
                                            <input type="submit" name="submit_signup" id="submit_signup"
                                                style="width:150px;margin-left:auto;margin-right:auto" tabindex="30"
                                                value="Submit"
                                                class="btn btn-success btn-md btn-block waves-effect waves-light text-center ">
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <!-- Signup -->

    </div>
    <div class="simple-footer">
        Copyright &copy;
        <?= $site_title ?> -
        <?= date("Y") ?>
    </div>
    </div>
    </div>
    </div>
    </section>
    </div>


    <!-- General JS Scripts -->
    <script src="assets/modules/jquery.min.js"></script>
    <script src="assets/modules/popper.js"></script>
    <script src="assets/modules/tooltip.js"></script>
    <script src="assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="assets/modules/moment.min.js"></script>
    <script src="assets/js/stisla.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->

    <!-- Template JS File -->
    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>


        // start function document
        $(function () {
            $('.theme-loader').fadeOut("slow");
            init();
        });

        $(".alert-ajax").click(function () {
            $("#id_modal_display").load("uploads/imports/terms.htm", function () {
                $('#default-Modal').modal({ show: true });
            });
        });

        document.body.addEventListener("click", function (evt) {
            setInterval(function () {
                // $("#id_error_display_signup").html("");
                //$("#id_error_display_onboarding").html("");
            }, 2000);
        })

        // Sign up submit Button function Start
        $(document).on("submit", "form#frm_signup", function (e) {
            $("#id_error_display_signup").html("");
            e.preventDefault();
            //get input field values 

            var clientname_txt = $('#clientname_txt').val();
            var mobile_no_txt = $('#mobile_no_txt').val();
            var email_id_contact = $('#email_id_contact').val();
            var flag = true;

            if (mobile_no_txt == "") {
                $('#mobile_no_txt').css('border-color', 'red');
                flag = false;
            }
            if (clientname_txt == "") {
                $('#clientname_txt').css('border-color', 'red');
                flag = false;
            }
            if (email_id_contact == "") {
                $('#email_id_contact').css('border-color', 'red');
                flag = false;
            }

            var mobile_no_txt = document.getElementById('mobile_no_txt').value;
            if (mobile_no_txt.length != 10) {
                $("#id_error_display_onboarding").html("Please enter a valid mobile number");
                flag = false;
            }
            if (!(mobile_no_txt.charAt(0) == "9" || mobile_no_txt.charAt(0) == "8" || mobile_no_txt.charAt(0) == "6" || mobile_no_txt.charAt(0) == "7")) {
                $("#id_error_display_onboarding").html("Please enter a valid mobile number");
                document.getElementById('mobile_no_txt').focus();
                flag = false;
            }

            /************************************/
            var email_id_contact = $('#email_id_contact').val();
            /* Email field validation  */
            var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
            if (filter.test(email_id_contact)) {
                // flag = true;
            } else {
                $("#id_error_display_onboarding").html("Email is invalid");
                flag = false;
                e.preventDefault();
            }

            /********Validation end here ****/

            /* If all are ok then we send ajax request to call_functions.php *******/
            if (flag) {
                var data_serialize = $("#frm_signup").serialize();
                var fd = new FormData(this);
                $.ajax({
                    type: 'post',
                    url: "ajax/call_functions.php",
                    dataType: 'json',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend: function () { // Before Send to Ajax
                        $('#submit').attr('disabled', true);
                        $('.theme-loader').show();
                        $("#id_error_display_onboarding").html("");
                    },
                    complete: function () { // After complete the Ajax
                        $('#submit').attr('disabled', false);
                        $('.theme-loader').hide();
                    },
                    success: function (response) { // Success
                        if (response.status == 0) { // Failure Response
                            $('#submit').attr('disabled', false);
                            $("#id_error_display_onboarding").html(response.msg);
                        } else if (response.status == 1) { // Success Response
                            $('#submit').attr('disabled', false);
                            //$("#id_error_display_onboarding").html(response.msg);
                            $('#mobile_no_txt').val('');
                            $('#email_id_contact').val('');
                            $('#clientname_txt').val('');
                            $("#id_error_display_onboarding").html("Payment Processing..");
                            setInterval(function () {
                                $("#id_error_display_onboarding").html("");
                            }, 2000)
                            var getAmount = '35000';
                            var product_id = 3;
                            var useremail = email_id_contact;
                            var totalAmount = getAmount * 100;
                            var options = {
                                "key": "<?= $rp_keyid ?>", // your Razorpay Key Id
                                "amount": totalAmount,
                                "name": clientname_txt,
                                "description": "Activation Payment",
                                "image": "https://www.codefixup.com/wp-content/uploads/2016/03/logo.png",
                                "handler": function (response) {
                                    $.ajax({
                                        url: 'ajax/ac_rppayment_call_functions.php?action_process=razorpay_payment',
                                        type: 'post',
                                        dataType: 'json',
                                        data: {
                                            razorpay_payment_id: response.razorpay_payment_id, totalAmount: totalAmount, product_id: product_id, useremail: useremail,
                                        },
                                        success: function (response) {
                                            if (response.status == 1) {
                                                setInterval(function () {
                                                    $("#id_error_display_onboarding").html("");
                                                    window.location = "onboarding";
                                                }, 2000);
                                            } else {
                                                $("#id_error_display_onboarding").html(response.msg);
                                            }

                                        },
                                        error: function (response, status, error) {
                                            window.location = "activation_payment";
                                        }
                                    });
                                },
                                "theme": {
                                    "color": "#528FF0"
                                }
                            };
                            var rzp1 = new Razorpay(options);
                            rzp1.open();
                            e.preventDefault();
                        } else if (response.status == 2) {
                            //alert(response.msg);
                            $('#submit').attr('disabled', false);
                            $("#id_error_display_onboarding").html(response.msg);
                        }
                    },
                    error: function (response, status, error) { // If any error occurs
                        $('#txt_password').val('');
                        $('#submit').attr('disabled', false);
                        $("#id_error_display_onboarding").html(response.msg);
                    }
                });
            }
        });
        // Sign in submit Button function End

        function clsAlphaNoOnly(e) { // Accept only alpha numerics, no special characters 
            var key = e.keyCode;
            if ((key >= 65 && key <= 90) || (key >= 97 && key <= 122) || (key >= 48 && key <= 57) || (key == 32) || (key == 95)) {
                return true;
            }
            return false;
        }
        function mobile_call_validate() {
            var mobile_no_txt = document.getElementById('mobile_no_txt').value;
            if (mobile_no_txt.length != 10) {
                $("#id_error_display_onboarding").html("Please enter a valid mobile number");
                flag = false;
            }
            if (!(mobile_no_txt.charAt(0) == "9" || mobile_no_txt.charAt(0) == "8" || mobile_no_txt.charAt(0) == "6" || mobile_no_txt.charAt(0) == "7")) {
                $("#id_error_display_onboarding").html("Please enter a valid mobile number");
                document.getElementById('mobile_no_txt').focus();
                flag = false;
            } else {
                if (mobile_no_txt.length == 10) {
                    var mobile_no_value = $("#mobile_no_txt").val();
                    $.ajax({
                        type: 'post',
                        url: "ajax/call_functions.php?tmp_call_fucntions=autofill",
                        dataType: 'json', // Adjust the dataType based on your server response type
                        data: {
                            mobile_no_txt: mobile_no_value
                        },
                        beforeSend: function () {
                            $('#submit').attr('disabled', true);
                        },
                        complete: function () {
                            $('#submit').attr('disabled', false);
                        },
                        success: function (response) {
                            if (response.status == 1) {
                                var inputString = response.msg;
                                var parts = inputString.split('&');
                                console.log(parts);
                                if (parts[1] !== '') {
                                    // If the second element is not an empty string, it means there is a mobile number
                                    //$('#mobile_no_txt').val(parts[1]);
                                    $("#id_error_display_onboarding").html("This mobile number has already paid the activation payment");
                                    $("#mobile_no_txt").val("");
                                }
                            }
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            // Handle the error
                        }
                    });
                }
            }
        }


        function email_validate() {
            var email_id_contact = $('#email_id_contact').val();
            /* Email field validation  */
            var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
            if (filter.test(email_id_contact)) {
                $.ajax({
                    type: 'post',
                    url: "ajax/call_functions.php?tmp_call_fucntions=autofill",
                    dataType: 'json', // Adjust the dataType based on your server response type
                    data: {
                        email_id_contact: email_id_contact
                    },
                    beforeSend: function () {
                        $('#submit').attr('disabled', true);
                    },
                    complete: function () {
                        $('#submit').attr('disabled', false);
                    },
                    success: function (response) {
                        if (response.status == 1) {
                            var inputString = response.msg;
                            var parts = inputString.split('&');
                            console.log(parts);
                            if (parts[1] !== '') {
                                // If the second element is not an empty string, it means there is a mobile number
                                $("#id_error_display_onboarding").html("This email id has already paid the activation payment");
                                //$('#mobile_no_txt').val(parts[1]);
                                $("#email_id_contact").val("");
                            }
                        }

                    },
                    error: function (xhr, textStatus, errorThrown) {
                        // Handle the error
                    }
                });
            } else {
                if (email_id_contact != '')
                    $("#id_error_display_onboarding").html("Email is invalid");
            }
        }

    </script>
</body>

</html>