<?php
/*
Authendicated users to use this site footer page for all main pages.
This page is used to give the footer details for all main pages.
It is used to some main activities are used.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 04-Jul-2023
*/
?>
<!-- script files -->
<script src="assets/js/jquery-3.4.0.min.js"></script>
<script src="assets/js/jToast.js"></script>

<link rel="stylesheet" href="assets/modules/izitoast/css/iziToast.min.css">
<!-- JS Libraies -->
<script src="assets/modules/izitoast/js/iziToast.min.js"></script>
<!-- Page Specific JS File -->
<script src="assets/js/page/modules-toastr.js"></script>
<!-- footer designs page -->
<div id="scrolltop" style="background-color: #031b36 !important"><a class="top-button" href="#top"><img
      src="assets/img/up_arrow.png" style="width: 18px; height: 18px; text-align: center; margin-bottom: 5px;"></a>
</div>

<footer class="main-footer">
  <div class="footer-left">
    Copyright &copy;
    <?= date("Y") ?>
    <div class="bullet"></div> <a href="#">RCS</a>
  </div>
  <div class="footer-right">
    <a href="faq">FAQ</a> | <a href="#!">Privacy Policy</a>
  </div>
</footer>

<script>
  $(document).ready(function () {
  });

  function find_blocked_senderid() {
    $.ajax({
      type: 'post',
      url: "ajax/message_call_functions.php?tmpl_call_function=find_blocked_senderid",
      dataType: 'json',
      success: function (response) {
        if (response.status == 1) {
          iziToast.error({
            title: '',
            message: '<div class="text-center" style="font-size: 20px; text-decoration: underline; padding-bottom: 10px;"><b>Notification</b></div>Hi, Following Sender ID has Blocked<br>' + response.msg + '<br>',
            position: 'bottomRight'
          });
        }
      },
      error: function (response, status, error) { }
    });
  }
</script>
