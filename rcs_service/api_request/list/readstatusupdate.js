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
// ReadStatusUpdate function - start
async function ReadStatusUpdate(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {

		// get all the req data
		var sender_no = req.body.sender_no;
		var receiver_no = req.body.receiver_no;
// query parameters
		logger_all.info("[Read Status Update query parameters] : " + JSON.stringify(req.body));
// update the messenger_response to message_is_read status is 'Y' and message_from to receiver number
			logger_all.info("[update query parameters] : " + `UPDATE messenger_response SET message_is_read= 'Y' WHERE message_to = '${sender_no}' and message_from = '${receiver_no}' `);
			update_read_status = await db.query(`UPDATE messenger_response SET message_is_read= 'Y' WHERE message_to = '${sender_no}' and message_from = '${receiver_no}'`);
			logger_all.info("[update query response] : " + JSON.stringify(update_read_status))
// if the messenger_response is successfully updated to return the success message
			if (update_read_status) {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows:1,
					response_msg: 'Success'

				};
			}
			else { // otherwise the failed the message response.
				return {
					response_code: 0,
					response_status: 204,
					response_msg: 'Failed'
				};
			}
	
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[ReadStatusUpdate failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// ReadStatusUpdate - end

// using for module exporting
module.exports = {
    ReadStatusUpdate
}