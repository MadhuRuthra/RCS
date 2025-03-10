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
// MasterLanguage - start
async function MasterLanguage(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
	
		// query parameters
		logger_all.info("[master_language - query parameters] : " + JSON.stringify(req.body));
		// to get get_master_language query
			logger_all.info("[select query request] : " + `SELECT language_id, language_name, language_code, language_id FROM master_language where language_status = 'Y' Order by language_name Asc`);
			const get_master_language = await db.query(`SELECT language_id, language_name, language_code, language_id FROM master_language where language_status = 'Y' Order by language_name Asc`);
			logger_all.info("[select query response] : " + JSON.stringify(get_master_language))
// if the get_master_language length is '0' to get the no available data.otherwise it will be return the get_master_language details.
			if (get_master_language.length == 0) {
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
			} else {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: get_master_language.length,
					response_msg: 'Success',
					report: get_master_language
				};
			}
		
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[master_language - failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// MasterLanguage - end

// using for module exporting
module.exports = {
	MasterLanguage
}