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
// FAQList- start
async function FAQList(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		// query parameters
		logger_all.info("[FAQList query parameters] : " + JSON.stringify(req.body));

		// to get get_faq using
		logger_all.info("[select query request] : " + `SELECT * from rcs_faq where rcs_faq_status = 'Y' ORDER BY rcs_faq_heading, rcs_faq_id asc`);
		const get_faq = await db.query(`SELECT * from rcs_faq where rcs_faq_status = 'Y' ORDER BY rcs_faq_heading, rcs_faq_id asc`);
		logger_all.info("[select query response] : " + JSON.stringify(get_faq))
		// get_faq length is coming to get the get_faq details. otherwise get_faq length is '0'.to get response message to send no data available
		if (get_faq.length == 0) {
			return {
				response_code: 1,
				response_status: 204,
				response_msg: 'No data available'
			};
		} else {
			return {
				response_code: 1,
				response_status: 200,
				num_of_rows: get_faq.length,
				response_msg: 'Success',
				report: get_faq
			};
		}

	} catch (e) { // any error occurres send error response to client
		logger_all.info("[FAQList failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// FAQList - end

// using for module exporting
module.exports = {
	FAQList
}