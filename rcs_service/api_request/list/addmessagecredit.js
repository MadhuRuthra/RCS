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

// AddMessageCredit Function - start
async function AddMessageCredit(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		//  Get all the req header data
		const header_token = req.headers['authorization'];

		// get current Date and time
		var day = new Date();
		var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
		var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
		var current_date = today_date + ' ' + today_time;
                var nextyear_dt = (day.getFullYear() + 1) + '-' + (day.getMonth()+1) + '-' + (day.getDate());
                var next_year_date = nextyear_dt + ' ' + today_time;

		// get all the req data
		var parent_user = req.body.parent_user;
		var receiver_user = req.body.receiver_user;
		var message_count = req.body.message_count;
                var credit_raise_id = req.body.credit_raise_id;

		// declare variable
		var user_id;
		// Query parameters 
		logger_all.info("[Add Message Credit - query parameters] : " + JSON.stringify(req.body));
		// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
		if (req.body.user_id) {
			get_user = get_user + `and user_id = '${req.body.user_id}' `;
		}
		logger_all.info("[select query request] : " + get_user);
		const get_user_id = await db.query(get_user);
		logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
		// If get_user not available send error response to client
		if (get_user_id.length == 0) {
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}
		else {
			user_id = get_user_id[0].user_id;
		}

		var exp1 = parent_user.split("~~");
		var exp2 = receiver_user.split("~~");
		var message_comments = '${message_count} Messages allocated to ${exp2[2]} by ${exp1[1]}';

		logger_all.info("[select query request] : " + `SELECT available_messages from message_limit WHERE user_id = '${exp1[0]}' AND message_limit_status = 'Y'`)
		const select_credit = await db.query(`SELECT available_messages from message_limit WHERE user_id = '${exp1[0]}' AND message_limit_status = 'Y'`);
		logger_all.info("[select query response] : " + JSON.stringify(select_credit))
		
		if(select_credit[0].available_messages < message_count){
			return {
				response_code: 0,
				response_status: 201,
				response_msg: 'Insufficient credits.'
			};
		}
		// insert the message_credit_log to request data
		logger_all.info("[insert query request] : " + `INSERT INTO message_credit_log VALUES (NULL, '${exp1[0]}', '${exp2[0]}', '${message_count}', '${message_count} Messages allocated to ${exp2[2]} by ${exp1[1]}', 'Y', CURRENT_TIMESTAMP)`)
		const insert_template = await db.query(`INSERT INTO message_credit_log VALUES (NULL, '${exp1[0]}', '${exp2[0]}', '${message_count}', '${message_count} Messages allocated to ${exp2[2]} by ${exp1[1]}', 'Y', CURRENT_TIMESTAMP)`);
		logger_all.info("[insert query response] : " + JSON.stringify(insert_template))
		// upadte the message_limit to request data
		logger_all.info("[update query request] : " + `UPDATE message_limit SET available_messages = available_messages + '${message_count}', total_messages = total_messages + '${message_count}', expiry_date = '${next_year_date}' WHERE user_id = ${exp2[0]}`)
		const update_succ = await db.query(`UPDATE message_limit SET available_messages = available_messages + '${message_count}', total_messages = total_messages + '${message_count}', 
expiry_date = '${next_year_date}' WHERE user_id = ${exp2[0]}`);
		logger_all.info("[update query response] : " + JSON.stringify(update_succ))

		if (exp1[0] != 1) {  // upadte the message_limit for expect the primaryadmin using the condition
		logger_all.info("[update query request] : " + `UPDATE message_limit SET available_messages = available_messages - '${message_count}' WHERE user_id = ${exp1[0]}`)
		const update_succ2 = await db.query(`UPDATE message_limit SET available_messages = available_messages - '${message_count}' WHERE user_id = ${exp1[0]}`);
		logger_all.info("[update query response] : " + JSON.stringify(update_succ2))

		// update_succ2 to get the response message through theMessage Credit updated. 
		if (update_succ2) {
			return {
				response_code: 1,
				response_status: 200,
				num_of_rows: 1,
				response_msg: 'Message Credit updated.'
			};

		} else {// otherwise send the No data available
			return {
				response_code: 1,
				response_status: 204,
				response_msg: 'No data available'
			};
		}
	}

		logger_all.info("credit_raise_id===="+credit_raise_id);
                if(credit_raise_id != undefined) { // Purchase Credit Raised and Approved
                        logger_all.info("[update query request - user_sms_credit_raise] : " + `UPDATE user_sms_credit_raise SET usrsmscrd_status = 'C' WHERE usrsmscrd_id = ${credit_raise_id} and usrsmscrd_status = 'A'`)
                        const update_succ3 = await db.query(`UPDATE user_sms_credit_raise SET usrsmscrd_status = 'C' WHERE usrsmscrd_id = ${credit_raise_id} and usrsmscrd_status = 'A'`);
                        logger_all.info("[update query response - user_sms_credit_raise] : " + JSON.stringify(update_succ3))
                }

		if (update_succ) {   // update_succ to get the response message through theMessage Credit updated. 
			return {
				response_code: 1,
				response_status: 200,
				num_of_rows: 1,
				response_msg: 'Message Credit updated.'
			};

		} else { // otherwise send the No data available
			return {
				response_code: 1,
				response_status: 204,
				response_msg: 'No data available'
			};
		}
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[AddMessageCredit failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// AddMessageCredit Function - end
// using for module exporting
module.exports = {
	AddMessageCredit
}
