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
// ApprovercsNo Function - start
async function ApprovercsNo(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		
		// get current Date and time
		var day = new Date();
		var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
		var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
		var current_date = today_date + ' ' + today_time;

		// get all the req data
		var whatspp_config_status = req.body.whatspp_config_status;
		var whatspp_config_id = req.body.whatspp_config_id;
// Query parameters 
		logger_all.info("[ApprovercsNo query parameters] : " + JSON.stringify(req.body));
// upadte the rcs_config for use the request query parameters
			logger_all.info("[update query request] : " + `UPDATE rcs_config SET whatspp_config_status = '${whatspp_config_status}', whatspp_config_apprdate = '${current_date}' WHERE whatspp_config_id = ${whatspp_config_id}`)
			const update_succ = await db.query(`UPDATE rcs_config SET whatspp_config_status = '${whatspp_config_status}', whatspp_config_apprdate = '${current_date}' WHERE whatspp_config_id = ${whatspp_config_id}`);
			logger_all.info("[update query response] : " + JSON.stringify(update_succ))

			// logger_all.info("[select query request] : " + `SELECT phone_number_id, rcs_business_acc_id, bearer_token, mobile_no FROM rcs_config where whatspp_config_id = ${whatspp_config_id}`);
			// const get_approve_rcsno = await db.query(`SELECT phone_number_id, rcs_business_acc_id, bearer_token, mobile_no FROM rcs_config where whatspp_config_id = ${whatspp_config_id}`);
			// logger_all.info("[select query response] : " + JSON.stringify(get_approve_rcsno))

			// update_succ to get the response message through the success message
			if (update_succ) {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: 1,
					response_msg: 'Success'
				};
				
			} else { // otherwise send the No data available
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
				
			}
		
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[ApprovercsNo failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// ApprovercsNo Function - end

// using for module exporting
module.exports = {
	ApprovercsNo
}
