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
// DisplayDeptAdmin - start
async function DisplayDeptAdmin(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		
		// get all the req data
		var super_admin = req.body.super_admin;
// query parameters
		logger_all.info("[DisplayDeptAdmin query parameters] : " + JSON.stringify(req.body));
		// using super_admin is not empty
			if (super_admin != '') {
				var super_admin_1 = super_admin.split("~~");
			}
// to get get_display_dept_admin using
			logger_all.info("[select query request] : " + `SELECT usr.user_id, usr.user_master_id, ums.user_type, usr.user_name, usr.api_key, usr.user_short_name FROM user_management usr left join user_master ums on usr.user_master_id = ums.user_master_id and ums.user_master_status = 'Y' where ums.user_master_id in (3) and usr.parent_id = '${super_admin_1[0]}' ORDER BY usr.user_name Asc`);
			const get_display_dept_admin = await db.query(`SELECT usr.user_id, usr.user_master_id, ums.user_type, usr.user_name, usr.api_key, usr.user_short_name FROM user_management usr left join user_master ums on usr.user_master_id = ums.user_master_id and ums.user_master_status = 'Y' where ums.user_master_id in (3) and usr.parent_id = '${super_admin_1[0]}' ORDER BY usr.user_name Asc`);
			logger_all.info("[select query response] : " + JSON.stringify(get_display_dept_admin))
// get_display_dept_admin length is coming to get the get_display_dept_admin details. otherwise get_display_dept_admin length is '0'.to get response message to send no data available
			if (get_display_dept_admin.length == 0) {
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
			} else {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: get_display_dept_admin.length,
					response_msg: 'Success',
					report: get_display_dept_admin
				};
			}
		
	} catch (e) {// any error occurres send error response to client
		logger_all.info("[DisplayDeptAdmin failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// DisplayDeptAdmin - end

// using for module exporting
module.exports = {
	DisplayDeptAdmin
}
