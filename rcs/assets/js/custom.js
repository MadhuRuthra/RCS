/**
 *
 * This page has some functions, which is used in the whole site.
 * Version : 1.0
 * Author : Madhubala (YJ0009)
 * Date : 04-Jul-2023
 *
 */

"use strict";

// This function is used to find the available balance of a user
function get_available_balance() {
	var txt_receiver_user = $("#txt_receiver_user").val();
	const split_credit = txt_receiver_user.split("~~");
	$.ajax({
		type: 'post',
		url: "ajax/call_functions.php",
		data: {
			get_available_balance: 'get_available_balance',
			txt_receiver_user: txt_receiver_user
		}, // Data
		success: function(response) { // Success
			$("#id_count_display").html(split_credit[2]+" : "+response.msg);
		},
		error: function(response, status, error) { // Error
		}
	});
}

// This function is used to get the Parent User of a particular user
function getParentUser() {
	var txt_parent_user = $("#txt_parent_user").val();
	$.ajax({
		type: 'post',
		url: "ajax/call_functions.php",
		data: {
			getParentUser: 'getParentUser',
			txt_parent_user: txt_parent_user
		}, // Data
		success: function(response) { // Success
			$("#txt_receiver_user").html(response.msg);
		},
		error: function(response, status, error) { // Error
		}
	});
}

// This function is used to get the state from choosing country
function getStateByCountry() {
	var txt_country = $("#txt_country").val();
	$.ajax({
		type: 'post',
		url: "ajax/call_functions.php",
		data: {
			getStateByCountry: 'getStateByCountry',
			txt_country: txt_country
		}, // Data
		success: function(response) { // Success
			$("#txt_state").html(response.msg);
		},
		error: function(response, status, error) { // Error
		}
	});
}

// This function is used to get the City from choosing State
function getCityByState() {
	var txt_state = $("#txt_state").val();
	$.ajax({
		type: 'post',
		url: "ajax/call_functions.php",
		data: {
			getCityByState: 'getCityByState',
			txt_state: txt_state
		}, // Data
		success: function(response) { // Success
			$("#txt_city").html(response.msg);
		},
		error: function(response, status, error) { // Error
		}
	});
}

// This function is used to open tab (Sign up / Sign in / Forget Password) in index page
function func_open_tab(newtab) {
	if (newtab == 'signup') { // To open Sign up
		$("#tab_signin").css("display", "none");
		$("#tab_forgotpwd").css("display", "none");
		$("#tab_signup").css("display", "block");
		$("#txt_user_name").focus();
	}
	if (newtab == 'signin') { // To open Sign in
		$("#tab_forgotpwd").css("display", "none");
		$("#tab_signup").css("display", "none");
		$("#tab_signin").css("display", "block");
		$("#txt_username").focus();
	}
	if (newtab == 'forgotpwd') { // To open Forget Password
		$("#tab_signin").css("display", "none");
		$("#tab_signup").css("display", "none");
		$("#tab_forgotpwd").css("display", "block");
		$("#txt_user_email_fp").focus();
	}
}

// This function is used to show / hide the password in Index
function password_visible() {
	var x = document.getElementById("txt_password");
	if (x.type === "password") { // Password Visible
		x.type = "text";
		$('#id_display_visiblitity').html('<i class="icofont icofont-eye"></i>');
	} else { // Password not Visible
		x.type = "password";
		$('#id_display_visiblitity').html('<i class="icofont icofont-eye-blocked"></i>');
	}
}

/*
function getStateByCountry() {
	var txt_country = $("#txt_country").val();
	$.ajax({
		type: 'post',
		url: "ajax/call_functions.php",
		data: {
			getStateByCountry: 'getStateByCountry',
			txt_country: txt_country
		},
		success: function(response) {
			$("#txt_state").html(response.msg);
		},
		error: function(response, status, error) {}
	});
}

function getCityByState() {
	var txt_state = $("#txt_state").val();
	$.ajax({
		type: 'post',
		url: "ajax/call_functions.php",
		data: {
			getCityByState: 'getCityByState',
			txt_state: txt_state
		},
		success: function(response) {
			$("#txt_city").html(response.msg);
		},
		error: function(response, status, error) {}
	});
}
*/

// This function is used to Validate Mobile No, it must not start 1 to 5
function call_validate_mobileno() {
	var txt_user_mobile = $("#txt_user_mobile").val();
	var stt = -1;
	if(txt_user_mobile.length > 9) { // If Mobile no length > 9
			var letter = txt_user_mobile.charAt(0);
			// console.log("!!!"+letter);
			if(letter == 0 || letter == 1 || letter == 2 || letter == 3 || letter == 4 || letter == 5) {
				stt = 0;
			} else {
				stt = 1;
			}

			if(stt == 0)
				$('#txt_user_mobile').css('border-color','red'); 
			else
				$('#txt_user_mobile').css('border-color','#ccc'); 
	}
	return stt;
}

// This function is used to check the Password Strength
function checkPasswordStrength() {
	var number = /([0-9])/;
	var alphabets = /([a-zA-Z])/;
	var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
	console.log($('#txt_user_password').val());
	if($('#txt_user_password').val().length<8) { // If password length below 8 character
		console.log("Weak (should be atleast 8 characters.)");
		$('#txt_user_password').css('border-color','red'); 
		return false;
	} else { // If password length moretan 8 character
		if($('#txt_user_password').val().match(number) && $('#txt_user_password').val().match(alphabets) && $('#txt_user_password').val().match(special_characters)) { // If Number, Alphabets, Special Characters available
			console.log("Strong");
			$('#txt_user_password').css('border-color','#a0a0a0'); 
			return true;
		} else { // If Number or Alphabets or Special Characters are not available
			console.log("Medium (should include alphabets, numbers and special characters.)");
			$('#txt_user_password').css('border-color','red'); 
			return false;
		}
	}
}

// This function is allowed only Alpha numberic without special characters
function clsAlphaNoOnly(e) { // Accept only alpha numerics, no special characters 
	var key = e.keyCode;
	if ((key >= 65 && key <= 90) || (key >= 97 && key <= 122) || (key >= 48 && key <= 57) || key == 32 || key == 95) {
		return true;
	}
	return false;
}

// This function is used to scroll to top of the screen
const scrollTop = document.getElementById('scrolltop');
window.onscroll = () => {
	if (window.scrollY > 0) {
		scrollTop.style.visibility = "visible";
		scrollTop.style.opacity = 1;
	} else {
		scrollTop.style.visibility = "hidden";
		scrollTop.style.opacity = 0;
	}
};
