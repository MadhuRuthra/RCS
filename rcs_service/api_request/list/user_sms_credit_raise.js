/*
This api has chat API functions which is used to connect the mobile chat.
This page is act as a Backend page which is connect with Node JS API and PHP Frontend.
It will collect the form details and send it to API.
After get the response from API, send it back to Frontend.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
require("dotenv").config();
const main = require('../../logger');
// user_sms_credit_raise- start
async function User_Sms_Credit_Raise(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		
		// get all the req data
		var user_id = req.body.user_id;
        var parent_id = req.body.parent_id;
        var pricing_slot_id = req.body.pricing_slot_id;
        var exp_date = req.body.exp_date;
        var slt_expiry_date = req.body.slt_expiry_date;
        var raise_sms_credits = req.body.raise_sms_credits;
        var sms_amount = req.body.sms_amount;
        var paid_status_cmnts = req.body.paid_status_cmnts;
        var paid_status = req.body.paid_status;
        var usrcrdbt_comments = req.body.usrcrdbt_comments;
        // var sms_amount = req.body.sms_amount;
         // To get current Date and Time
  var day = new Date();
  var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
  var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
  var current_date = today_date + ' ' + today_time;

// query parameters
		logger_all.info("[CheckAvailableMsg query parameters] : " + JSON.stringify(req.body));
// user_sms_credit_raise to execute this query
			logger_all.info("[select query request] : " + `INSERT INTO user_sms_credit_raise (usrsmscrd_id, user_id, parent_id, pricing_slot_id, expiry_date, valdity_period, raise_sms_credits, sms_amount, usrsmscrd_comments, usrsmscrd_status, usrsmscrd_status_cmnts, usrsmscrd_entry_date) VALUES (NULL, '${user_id}', '${parent_id}', '${pricing_slot_id}', '${exp_date}', '${slt_expiry_date}', '${raise_sms_credits}', '${sms_amount}', '${usrcrdbt_comments}', '${paid_status}', '${paid_status_cmnts}', '${current_date}')
            `);
			const insert_user_sms_credit_raise = await db.query(`INSERT INTO user_sms_credit_raise (usrsmscrd_id, user_id, parent_id, pricing_slot_id, expiry_date, valdity_period, raise_sms_credits, sms_amount, usrsmscrd_comments, usrsmscrd_status, usrsmscrd_status_cmnts, usrsmscrd_entry_date) VALUES (NULL, '${user_id}', '${parent_id}', '${pricing_slot_id}', '${exp_date}', '${slt_expiry_date}', '${raise_sms_credits}', '${sms_amount}', '${usrcrdbt_comments}', '${paid_status}', '${paid_status_cmnts}', '${current_date}')
            `);
			logger_all.info("[select query response] : " + JSON.stringify(insert_user_sms_credit_raise));


            var insert_last_id = insert_user_sms_credit_raise.insertId;
    				
// if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
			if (insert_last_id) {
                return { response_code: 1, response_status: 200,response_msg: 'Success' };
			}
			else {
                return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			
			}
	
}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[CheckAvailableMsg failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// user_sms_credit_raise - end

// using for module exporting
module.exports = {
	User_Sms_Credit_Raise,
}


