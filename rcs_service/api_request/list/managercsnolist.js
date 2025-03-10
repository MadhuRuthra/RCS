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
// ManagercsNoList - start
async function ManagercsNoList(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		 //  Get all the req header data
		const header_token = req.headers['authorization'];
		
		// declare the variables
		var user_id, user_master_id, get_manage_rcsno_list;
// query parameters
		logger_all.info("[ManagercsNoList query parameters] : " + JSON.stringify(req.body));
			// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
        if(req.body.user_id){
            get_user = get_user + `and user_id = '${req.body.user_id}' `;
        }
        logger_all.info("[select query request] : " +  get_user);
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
		
			if (user_master_id == 4) { //if the usermasterid is 4 to execute the get_manage_rcsno_list query
				logger_all.info("[select query request] : " + `SELECT wht.whatspp_config_id, wht.user_id, usr.user_name, str.store_id, str.store_owner, str.store_email_address, str.store_mobile_no, wht.mobile_no, wht.qr_code_allowed, wht.whatspp_config_status, wht.whatspp_config_entdate, wht.country_id, wht.country_code, ctr.phonecode, ctr.shortname FROM rcs_config wht left join user_management usr on usr.user_id = wht.user_id left join  str on str.store_id = wht.store_id left join master_countries ctr on ctr.id = wht.country_id where wht.user_id = '${user_id}' and is_qr_code = 'Y' order by whatspp_config_entdate desc`);
				get_manage_rcsno_list = await db.query(`SELECT wht.whatspp_config_id, wht.user_id, usr.user_name, str.store_id, str.store_owner, str.store_email_address, str.store_mobile_no, wht.mobile_no, wht.qr_code_allowed, wht.whatspp_config_status, wht.whatspp_config_entdate, wht.country_id, wht.country_code, ctr.phonecode, ctr.shortname FROM rcs_config wht left join user_management usr on usr.user_id = wht.user_id left join  str on str.store_id = wht.store_id left join master_countries ctr on ctr.id = wht.country_id where wht.user_id = '${user_id}' and is_qr_code = 'Y' order by whatspp_config_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_manage_rcsno_list))
			} else { // otherwise get_manage_rcsno_list it will be executed
				logger_all.info("[select query request] : " + `SELECT wht.whatspp_config_id, wht.user_id, usr.user_name, str.store_id, str.store_owner, str.store_email_address, str.store_mobile_no, wht.mobile_no, wht.qr_code_allowed, wht.whatspp_config_status, wht.whatspp_config_entdate, wht.country_id, wht.country_code, ctr.phonecode, ctr.shortname FROM rcs_config wht left join user_management usr on usr.user_id = wht.user_id left join  str on str.store_id = wht.store_id left join master_countries ctr on ctr.id = wht.country_id where (wht.user_id = '${user_id}' or usr.parent_id = '${user_id}') and is_qr_code = 'Y' order by whatspp_config_entdate desc`);
				get_manage_rcsno_list = await db.query(`SELECT wht.whatspp_config_id, wht.user_id, usr.user_name, str.store_id, str.store_owner, str.store_email_address, str.store_mobile_no, wht.mobile_no, wht.qr_code_allowed, wht.whatspp_config_status, wht.whatspp_config_entdate, wht.country_id, wht.country_code, ctr.phonecode, ctr.shortname FROM rcs_config wht left join user_management usr on usr.user_id = wht.user_id left join  str on str.store_id = wht.store_id left join master_countries ctr on ctr.id = wht.country_id where (wht.user_id = '${user_id}' or usr.parent_id = '${user_id}') and is_qr_code = 'Y' order by whatspp_config_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_manage_rcsno_list))
			}
 // if the get_manage_rcsno_list length is '0' to get the no available data.otherwise it will be return the get_manage_rcsno_list details.
			if (get_manage_rcsno_list.length == 0) {
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
			} else {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: get_manage_rcsno_list.length,
					response_msg: 'Success',
					report: get_manage_rcsno_list
				};
			}
	
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[ManagercsNoList failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// ManagercsNoList - end

// using for module exporting
module.exports = {
	ManagercsNoList
}