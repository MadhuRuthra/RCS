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
// UsernameGenerate function - start
async function UsernameGenerate(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {

		// get all the req data
		var user_master_id = req.body.user_type;
		var super_admin = req.body.super_admin;
		var dept_admin = req.body.dept_admin;
        var user_id = req.body.user_id;
		  // declare the variables
		var get_username_generate, return_value;
		 // To initialize a variable with an empty string value
		var get_username_generate = "";

		logger_all.info("[UsernameGenerate query parameters] : " + JSON.stringify(req.body));
// if the dept_admin is null to send the no data available message.
		if(dept_admin == 'null'){
			return {
				response_code: 1,
				response_status: 204,
				response_msg: 'No data available'
			};
		}
// if the user_master_id '2' get_username_generate condition will be executed
			if (user_master_id == 2) {
				logger_all.info("[select query request] : " + `SELECT (count(user_id) + 1) cnt_admin FROM user_management usr where usr.user_master_id in (2) ORDER BY usr.user_name Asc`);
				get_username_generate = await db.query(`SELECT (count(user_id) + 1) cnt_admin FROM user_management usr where usr.user_master_id in (2) ORDER BY usr.user_name Asc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_username_generate))
				 // if number of get_username_generate length  is available then process the will be continued
                    // loop all the get the return value.
				for (var i = 0; i < get_username_generate.length; i++) {
					return_value = "spd" + get_username_generate[i].cnt_admin;
				}// if the user_master_id '3' get_username_generate condition will be executed
			} else if (user_master_id == 3) {
				if (super_admin != '') { //if the superadmin is not empty to execute the query.
					var super_admin_1 = super_admin.split("~~");
				}
            
                // get_username_generate 
				logger_all.info("[select query request] : " + `SELECT (count(user_id) + 1) cnt_admin FROM user_management usr where usr.user_master_id in (3) and usr.parent_id = '${super_admin_1[0]}' ORDER BY usr.user_name Asc`);
				get_username_generate = await db.query(`SELECT (count(user_id) + 1) cnt_admin FROM user_management usr where usr.user_master_id in (3) and usr.parent_id = '${super_admin_1[0]}' ORDER BY usr.user_name Asc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_username_generate))
				// if number of get_username_generate length  is available then process the will be continued
				 // loop all the get the return value.
				for (var i = 0; i < get_username_generate.length; i++) {
					return_value = super_admin_1[1] + "_dpa" + get_username_generate[i].cnt_admin;
				}
				// if the get_username_generate is empty to send the no data available
				if (get_username_generate == "") {
					return {
						response_code: 1,
						response_status: 204,
						response_msg: 'No data available'
					};
				} //otherwise usermaster id is '4' to exexcute the this condition
			} else if (user_master_id == 4) {
				if (super_admin != '') { //superadmin is not empty to execute
					var super_admin_1 = super_admin.split("~~");
				}
				if (dept_admin != '') { //dept_admin is not empty to execute
					var dept_admin_1 = dept_admin.split("~~");
				}
// get_username_generate to get 
				logger_all.info("[select query request] : " + `SELECT (count(user_id) + 1) cnt_admin FROM user_management usr where usr.user_master_id in (4) and usr.parent_id = '${dept_admin_1[0]}' ORDER BY usr.user_name Asc`);
				get_username_generate = await db.query(`SELECT (count(user_id) + 1) cnt_admin FROM user_management usr where usr.user_master_id in (4) and usr.parent_id = '${dept_admin_1[0]}' ORDER BY usr.user_name Asc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_username_generate))
					// if number of get_username_generate length  is available then process the will be continued
				 // loop all the get the return value.
				for (var i = 0; i < get_username_generate.length; i++) {
					return_value = super_admin_1[1] + "_" + dept_admin_1[1] + "_agt" + get_username_generate[i].cnt_admin;
				}
	// if the get_username_generate is empty to send the no data available
				if (get_username_generate == "") {
					return {
						response_code: 1,
						response_status: 204,
						response_msg: 'No data available'
					};
				}
			}
// if the get_username_generate is length is '0' to send the no data available
			if (get_username_generate.length == 0) {
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
			} else { //otherwise to get the success message and return the value
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: 1,
					response_msg: 'Success',
					report: return_value
				};
			}
	
	} catch (e) {// any error occurres send error response to client
		logger_all.info("[UsernameGenerate failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// UsernameGenerate function - end
// using for module exporting
module.exports = {
	UsernameGenerate
}
