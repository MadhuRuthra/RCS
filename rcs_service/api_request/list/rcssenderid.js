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
// rcsSenderID function- start
async function rcsSenderID(req) {
	var logger_all = main.logger_all
	try {
	//  Get all the req header data
		const header_token = req.headers['authorization'];
	
  // declare the variables
		var user_id, user_master_id, get_rcs_senderid;
// query parameters
		logger_all.info("[rcs_senderid query parameters] : " + JSON.stringify(req.body));
		// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
        if(req.body.user_id){
            get_user = get_user + `and user_id = '${req.body.user_id}' `;
        }
        logger_all.info("[select query request] : " +  get_user);
        const get_user_id = await db.query(get_user);
        logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
		 // If get_user not available send error response to client in ivalid token
		if (get_user_id.length == 0) {
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}
		else { // otherwise to get the user details

			user_id = get_user_id[0].user_id;
			user_master_id = get_user_id[0].user_master_id;
		}
	

			if (user_master_id == 1 || user_master_id == 2 || user_master_id == 3) {// primary admin - admin - Dept Head are following this to get the get_rcs_senderid
				logger_all.info("[select query request] : " + `SELECT wht.whatspp_config_id, str.store_id, str.store_owner, wht.mobile_no, wht.qr_code_allowed, wht.is_qr_code, wht.country_id, ctr.phonecode, ctr.shortname FROM rcs_config wht left join user_management usr on wht.user_id = usr.user_id left join  str on wht.store_id = str.store_id and str.store_status =  'Y' left join master_countries ctr on ctr.id = wht.country_id where (wht.user_id = '${user_id}' or usr.parent_id = '${user_id}') and wht.is_qr_code =  'Y' and wht.whatspp_config_status =  'Y' ORDER BY wht.mobile_no ASC`);
				get_rcs_senderid = await db.query(`SELECT wht.whatspp_config_id, str.store_id, str.store_owner, wht.mobile_no, wht.qr_code_allowed, wht.is_qr_code, wht.country_id, ctr.phonecode, ctr.shortname FROM rcs_config wht left join user_management usr on wht.user_id = usr.user_id left join  str on wht.store_id = str.store_id and str.store_status =  'Y' left join master_countries ctr on ctr.id = wht.country_id where (wht.user_id = '${user_id}' or usr.parent_id = '${user_id}') and wht.is_qr_code =  'Y' and wht.whatspp_config_status =  'Y' ORDER BY wht.mobile_no ASC`);
				logger_all.info("[select query response] : " + JSON.stringify(get_rcs_senderid))
			} else {// otherwise following this to get the get_rcs_senderid
				logger_all.info("[select query request] : " + `SELECT wht.whatspp_config_id, str.store_id, str.store_owner, wht.mobile_no, wht.qr_code_allowed, wht.is_qr_code, wht.country_id, ctr.phonecode, ctr.shortname FROM rcs_config wht left join  str on wht.store_id = str.store_id and str.store_status = 'Y' left join master_countries ctr on ctr.id = wht.country_id where wht.user_id = '${user_id}' and wht.is_qr_code = 'Y' and wht.whatspp_config_status = 'Y' ORDER BY wht.mobile_no ASC`);
				get_rcs_senderid = await db.query(`SELECT wht.whatspp_config_id, str.store_id, str.store_owner, wht.mobile_no, wht.qr_code_allowed, wht.is_qr_code, wht.country_id, ctr.phonecode, ctr.shortname FROM rcs_config wht left join  str on wht.store_id = str.store_id and str.store_status = 'Y' left join master_countries ctr on ctr.id = wht.country_id where wht.user_id = '${user_id}' and wht.is_qr_code = 'Y' and wht.whatspp_config_status = 'Y' ORDER BY wht.mobile_no ASC`);
				logger_all.info("[select query response] : " + JSON.stringify(get_rcs_senderid))
			}
            // if the get_rcs_senderid length is '0' to send the no available data.otherwise it will be return the get_rcs_senderid details.
			if (get_rcs_senderid.length == 0) {
				return {
					response_code: 1,
					response_status: 204,
					response_msg: 'No data available'
				};
			} else {
				return {
					response_code: 1,
					response_status: 200,
					num_of_rows: get_rcs_senderid.length,
					response_msg: 'Success',
					report: get_rcs_senderid
				};
			}
		
	} catch (e) {   // any error occurres send error response to client
		logger_all.info("[rcs_senderid - failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// rcsSenderID - end
// using for module exporting
module.exports = {
rcsSenderID
}