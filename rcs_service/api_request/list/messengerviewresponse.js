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
// MessengerViewResponse function start
async function MessengerViewResponse(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		// var header_token;
		const header_token = req.headers['authorization'];

		// get all the req data
		var message_from = req.body.message_from;
		var message_to = req.body.message_to;
		var user_id;
		// query parameters
		logger_all.info("[MessengerViewResponse - query parameters] : " + JSON.stringify(req.body));
		// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
		if (req.body.user_id) {
			get_user = get_user + `and user_id = '${req.body.user_id}' `;
		}
		logger_all.info("[select query request] : " + get_user);
		const get_user_id = await db.query(get_user);
		logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
		// If get_user not available send error response to client in ivalid token
		if (get_user_id.length == 0) {
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}
		else { // otherwise to get the user details
			user_id = get_user_id[0].user_id;
		}
		// and (usr.user_id = '${user_id}' or usr.parent_id = '${user_id}') and (res.message_from = '${message_to}' or res.message_to = '${message_to}') and (res.message_from = '${message_from}' or res.message_to = '${message_from}')
		const get_datas = `SELECT res.message_id, usr.user_id,res.message_data,res.msg_list, usr.user_name, res.message_from, res.message_to, res.message_from_profile, upper(res.message_type) message_type, res.msg_text, res.msg_media, res.msg_media_type, res.msg_media_caption, res.msg_reply_button, res.msg_reaction, res.message_is_read, res.message_status,DATE_FORMAT(res.message_rec_date,'%d-%m-%Y %h:%i:%s %p') message_rec_date , res.message_read_date FROM messenger_response res left join user_management usr on usr.user_id = res.user_id where res.message_status = 'Y' and (res.message_from = '${message_to}' or res.message_to = '${message_to}') and (res.message_from = '${message_from}' or res.message_to = '${message_from}') order by res.message_id asc`
		// get_messenger_view_response to get the query
		logger_all.info("***********8")
		logger_all.info("[select query request] : " + get_datas);
		const get_messenger_view_response = await db.query(get_datas);
		//logger_all.info("[select query response] : " + JSON.stringify(get_messenger_view_response))
		// if the get get_messenger_view_response length is '0' to send the no available data.otherwise it will be return the get_messenger_view_response details.
		if (get_messenger_view_response.length == 0) {
			return {
				response_code: 1,
				response_status: 204,
				response_msg: 'No data available'
			};
		} else {
			return {
				response_code: 1,
				response_status: 200,
				num_of_rows: get_messenger_view_response.length,
				response_msg: 'Success',
				report: get_messenger_view_response
			};
		}

	} catch (e) {// any error occurres send error response to client
		logger_all.info("[MessengerViewResponse - failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// MessengerViewResponse function - end

// using for module exporting
module.exports = {
	MessengerViewResponse
}
