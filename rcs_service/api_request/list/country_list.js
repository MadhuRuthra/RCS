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
// CountryList - start
async function CountryList(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		// query parameters
		logger_all.info("[CountryList query parameters] : " + JSON.stringify(req.body));

		// to get the master_countries details
		logger_all.info("[select query request] : " + `SELECT shortname, phonecode, id FROM master_countries order by shortname`);
		const get_country_list = await db.query(`SELECT shortname, phonecode, id FROM master_countries order by shortname`);
		logger_all.info("[select query response] : " + JSON.stringify(get_country_list))
		// if the master_countries length is coming to get the master_countries details.otherwise to send the no data available message.
		if (get_country_list.length == 0) {
			return { response_code: 1, response_status: 204, response_msg: 'No data available' };
		}
		else {
			return { response_code: 1, response_status: 200, num_of_rows: get_country_list.length, response_msg: 'Success', report: get_country_list };
		}
	}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[CountryList failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// 	CountryList - end

// using for module exporting
module.exports = {
	CountryList
}