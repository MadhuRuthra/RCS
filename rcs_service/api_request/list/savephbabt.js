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
// SavePHBABT function - start
async function SavePHBABT(req) { 
	var logger_all = main.logger_all
    var logger = main.logger
	// Save phone_number_id, rcs_business_acc_id, bearer_token
	try {
		// var header_token;
		const header_token = req.headers['authorization'];
	
		// get all the req data
		var whatspp_config_id = req.body.whatspp_config_id;
		var phone_number_id = req.body.phone_number_id;
		var rcs_business_acc_id = req.body.rcs_business_acc_id;
		var bearer_token = req.body.bearer_token;
// declare the variable
		var  update_wpcnf;
// query parameters
		logger_all.info("[SavePHBABT query parameters] : " + JSON.stringify(req.body));

			logger_all.info("[SavePHBABT] : " + whatspp_config_id + "=>" + phone_number_id + "=>" + rcs_business_acc_id + "=>" + bearer_token + "");
// if the phone_number_id is not empty and phone_number_id is not undefined the process will be executed
			if (phone_number_id != '' && phone_number_id != undefined) {
				logger_all.info("[update query request] : " + `UPDATE rcs_config SET phone_number_id = '${phone_number_id}' WHERE whatspp_config_id = ${whatspp_config_id}`)
				update_wpcnf = await db.query(`UPDATE rcs_config SET phone_number_id = '${phone_number_id}' WHERE whatspp_config_id = ${whatspp_config_id}`);
				// if the rcs_business_acc_id is not empty and rcs_business_acc_id is not undefined the process will be executed
			} else if (rcs_business_acc_id != '' && rcs_business_acc_id != undefined) {
				logger_all.info("[update query request] : " + `UPDATE rcs_config SET rcs_business_acc_id = '${rcs_business_acc_id}' WHERE whatspp_config_id = ${whatspp_config_id}`)
				update_wpcnf = await db.query(`UPDATE rcs_config SET rcs_business_acc_id = '${rcs_business_acc_id}' WHERE whatspp_config_id = ${whatspp_config_id}`);
				// if the bearer_token is not empty and bearer_token is not undefined the process will be executed
			} else if (bearer_token != '' && bearer_token != undefined) {
				logger_all.info("[update query request] : " + `UPDATE rcs_config SET bearer_token = '${bearer_token}' WHERE whatspp_config_id = ${whatspp_config_id}`)
				update_wpcnf = await db.query(`UPDATE rcs_config SET bearer_token = '${bearer_token}' WHERE whatspp_config_id = ${whatspp_config_id}`);
			}
			logger_all.info("[update query response] : " + JSON.stringify(update_wpcnf))
// if the update_wpcnf is successfully updated to return the success message
			if (update_wpcnf) {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: 1,
					response_msg: 'Success'
				};
			} else {
				return { // otherwise the failed the message response.
					response_code: 1,
					response_status: 204,
					response_msg: 'Failed'
				};
			}
	

	} catch (e) { // any error occurres send error response to client
		logger_all.info("[SavePHBABT failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}

// 	 SavePHBABT - end

// using for module exporting
module.exports = {
	 SavePHBABT

}