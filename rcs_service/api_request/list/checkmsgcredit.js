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
// AvailableCreditsList- start
async function CheckAvailableMsg(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		
		// get all the req data
		var user_id = req.body.user_id;

// query parameters
		logger_all.info("[CheckAvailableMsg query parameters] : " + JSON.stringify(req.body));
// get_available_message to execute this query
			logger_all.info("[select query request] : " + `SELECT available_messages FROM message_limit where user_id = '${user_id}' order by user_id desc limit 1`);
			const get_available_message = await db.query(`SELECT available_messages FROM message_limit where user_id = '${user_id}' order by user_id desc limit 1 `);
			logger_all.info("[select query response] : " + JSON.stringify(get_available_message));

				
// if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
			if (get_available_message.length == 0) {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
			else {
				return { response_code: 1, response_status: 200, num_of_rows: get_available_message.length, response_msg: 'Success', report: get_available_message };
			}
	
}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[CheckAvailableMsg failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// AvailableCreditsList - end

// using for module exporting
module.exports = {
	CheckAvailableMsg,
}


