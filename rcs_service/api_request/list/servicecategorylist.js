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

// ServiceCategoryList function - start
async function ServiceCategoryList(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {

		// query parameters
		logger_all.info("[ServiceCategoryList query parameters] : " + JSON.stringify(req.body));
		// to get the get_service_category_list 
		logger_all.info("[select query request] : " + `SELECT * FROM message_category where message_category_status = 'Y' ORDER BY message_category_title Asc`);
		const get_service_category_list = await db.query(`SELECT * FROM message_category where message_category_status = 'Y' ORDER BY message_category_title Asc`);
		logger_all.info("[select query response] : " + JSON.stringify(get_service_category_list))
		// if the get_service_category_list length is '0' to send the no available data.otherwise it will be return the get_service_category_list details.
		if (get_service_category_list.length == 0) {
			return { response_code: 1, response_status: 204, response_msg: 'No data available' };
		}
		else {
			return { response_code: 1, response_status: 200, num_of_rows: get_service_category_list.length, response_msg: 'Success', report: get_service_category_list };
		}
	}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[ServiceCategoryList failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// ServiceCategoryList - end

// using for module exporting
module.exports = {
	ServiceCategoryList,
}