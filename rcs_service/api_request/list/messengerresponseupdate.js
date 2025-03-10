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
// MessengerResponseUpdate Function - start
async function MessengerResponseUpdate(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		
		// get all the req data
		var message_id = req.body.message_id;
		// declare the variable
		var user_id;
// query parameters
		logger_all.info("[MessengerResponseUpdate query parameters] : " + JSON.stringify(req.body));
// update_messenger_response_update query
			logger_all.info("[update query request] : " + `UPDATE messenger_response SET message_is_read = 'Y' WHERE message_id in (${message_id})`)
			const update_messenger_response_update = await db.query(`UPDATE messenger_response SET message_is_read = 'Y' WHERE message_id in (${message_id})`);
			logger_all.info("[update query response] : " + JSON.stringify(update_messenger_response_update))
// if the get update_messenger_response_update length is '0' to send the no available data.otherwise it will be return the update_messenger_response_update details.	
			if (update_messenger_response_update.length == 0) {
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
			} else {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: 1,
					response_msg: 'Success'
				};
			}
	
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[MessengerResponseUpdate - failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// MessengerResponseUpdate - end

// using for module exporting
module.exports = {
	MessengerResponseUpdate
}