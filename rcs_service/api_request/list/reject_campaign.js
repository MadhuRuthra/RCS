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
// Reject_Campaign function - start
async function Reject_Campaign(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	// Save phone_number_id, rcs_business_acc_id, bearer_token
	try {
		const header_token = req.headers['authorization'];

		// get all the req data
		var compose_message_id = req.body.compose_message_id;
		var campaign_status = req.body.campaign_status;
		var selected_userid = req.body.selected_userid;
                var reason = req.body.reason;
		// declare the variable
		var update_campaign_set,update_credit, update_create_value;
		// query parameters
		logger_all.info("[Reject_Campaign query parameters] : " + JSON.stringify(req.body));

		var get_campaign_status = `SELECT total_mobileno_count,rcs_status FROM rcs_${selected_userid}.compose_rcs_tmpl_${selected_userid} WHERE rcs_status = 'W' and compose_rcs_id = '${compose_message_id}' `;

		logger_all.info("[Select query request] : " + get_campaign_status);
		get_campaign = await db.query(get_campaign_status);

		if (get_campaign.length > 0) {
			
			var total_mobileno = get_campaign[0].total_mobileno_count;

			var update_campaign = `UPDATE rcs_${selected_userid}.compose_rcs_tmpl_${selected_userid} SET rcs_status = '${campaign_status}',reject_reason = '${reason}' WHERE rcs_status = 'W' and compose_rcs_id = '${compose_message_id}'`;
			logger_all.info("[update query request] : " + update_campaign);

			update_campaign_set = await db.query(update_campaign);
			logger_all.info("[update query response] : " + JSON.stringify(update_campaign_set))

			 update_credit = `UPDATE message_limit SET available_messages = available_messages + ${total_mobileno} WHERE user_id = '${selected_userid}' and message_limit_status = 'Y'`;
			logger_all.info("[update query request] : " + update_credit);

			update_credit_value = await db.query(update_credit);
			logger_all.info("[update query response] : " + JSON.stringify(update_credit_value))
			// if the update_wpcnf is successfully updated to return the success message
			if (update_campaign_set.affectedRows) {
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
		} else {
			logger_all.info("[update query response] : response_code: 1,response_status: 204,response_msg: 'Campaign Not Found.'")
			return { // otherwise the failed the message response.
				response_code: 1,
				response_status: 204,
				response_msg: 'Campaign Not Found.'
			};
		}
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[Reject_Campaign failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// Reject_Campaign - end

// using for module exporting
module.exports = {
	Reject_Campaign
}
