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
const dynamic_db = require("../../db_connect/dynamic_connect");
require("dotenv").config();
const main = require('../../logger');

// TemplatercsList function - start
async function TemplatercsList(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		//  Get all the req header data
		const header_token = req.headers['authorization'];
		// get all the req filter data
		var prntid;
		var user_id;
		var user_master_id;
		var get_template_rcs_list;
		var newdb;
		var list_user_id_1;
		var list_user_id;
		var newdb;
		var get_campaign_report;
		var query_1;
		// To initialize a variable with an empty string value
		var list_user_id = '';
		// declare the array
		var array_list_user_id = [];
		var total_available_messages = [];
		var total_user_id = [];
		var total_user_master_id = [];
		var total_user_name = [];
		// Query parameters 
		logger_all.info("[Otp summary report query parameters] : " + JSON.stringify(req.body));
		// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
		if (req.body.user_id) {
			get_user = get_user + `and user_id = '${req.body.user_id}' `;
		}
		logger_all.info("[select query request] : " + get_user);
		const get_user_id = await db.query(get_user);
		logger_all.info("[select query response] : " + JSON.stringify(get_user_id));

		if (get_user_id.length == 0) { // If get_user not available send error response to client in ivalid token
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}
		else {// otherwise to get the user details
			user_id = get_user_id[0].user_id;
			user_master_id = get_user_id[0].user_master_id;
		}

		// To initialize a variable with an empty string value
		var prnt_id = ` `;
		prntid = ` `;
		whrcondition = ` `;
		get_campaign_report = ``;

		var query_1 = `SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${user_id}' or usr.parent_id in (${user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`;
		logger_all.info(query_1);


		var sql_query_4 = await db.query(query_1);
		for (var i = 0; i < sql_query_4.length; i++) {
			total_available_messages.push(sql_query_4[i].available_messages);
			total_user_id.push(sql_query_4[i].user_id);
			total_user_master_id.push(sql_query_4[i].user_master_id);
			total_user_name.push(sql_query_4[i].user_name);
		}
		// to get the select query
		logger_all.info(` SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id in ('${user_id}'${prnt_id}))${whrcondition} `);
		var select_query = await db.query(` SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id in ('${user_id}' '${prnt_id}'))${whrcondition} `);

		logger_all.info("[select query response] : " + JSON.stringify(select_query))
		// if number of select_query length  is available then process the will be continued
		// loop all the get the user id to push the list_user_id, the array_list_user_id array
		if (select_query.length > 0) {

			for (var i = 0; i < select_query.length; i++) {

				list_user_id += "," + select_query[i].user_id;
				array_list_user_id.push(select_query[i].user_id);
			}

			var array_list_userid_string = array_list_user_id.join("','");
			list_user_id_1 = list_user_id.trimStart(',');
			list_user_id = list_user_id_1.substring(1);

			get_campaign_report = `SELECT DISTINCT wht.compose_rcs_id,wht.campaign_id, usr.user_name,wht.campaign_name,wht.rcs_status as response_status, tmp.templateid,tmp.template_name,wht.total_mobileno_count,wht.rcs_entry_date, DATE_FORMAT(wht.rcs_entry_date,'%d-%m-%Y %h:%i:%s %p') comwtap_entry_date FROM master_compose_rcs wht left join rcs.user_management usr on wht.user_id = usr.user_id left join rcs.message_template tmp ON tmp.template_name = wht.template_name where wht.user_id in ('${array_list_userid_string}')`;

			// get_campaign_report = get_campaign_report.slice(0, -7);

			logger_all.info('[Select Query Request]' + get_campaign_report + `  order by rcs_entry_date DESC `);
			get_template_rcs_list = await db.query(get_campaign_report + `  order by rcs_entry_date desc`, null, `rcs_${user_id}`);

			//logger_all.info("[select query response] : " + JSON.stringify(get_template_rcs_list))

			// if the get get_template_rcs_list length is '0' available to send the no available data.otherwise it will be return the get_template_rcs_list details.
			if (get_template_rcs_list.length == 0) {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
			else {
				return { response_code: 1, response_status: 200, num_of_rows: get_template_rcs_list.length, response_msg: 'Success', report: get_template_rcs_list };
			}

		}
	}
	catch (e) { // any error occurres send error response to client
		logger_all.info("[TemplatercsList failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// TemplatercsList - end
// using for module exporting
module.exports = {
	TemplatercsList
}
