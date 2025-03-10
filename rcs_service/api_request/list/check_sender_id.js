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

// CheckSenderId - start
async function CheckSenderId(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		  //  Get all the req header data
		const header_token = req.headers['authorization'];
		 // define the variable
		var whrcondition = ` `;
		var query_select = ``;
		
		// get all the req filter data
		var user_id = req.body.user_id;
		// query parameters
		logger_all.info("[CheckSenderId query parameters] : " + JSON.stringify(req.body));
// To get the User_id
        var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
        if(req.body.user_id){
            get_user = get_user + `and user_id = '${req.body.user_id}' `;
        }
        logger_all.info("[select query request] : " +  get_user);
        const get_user_id = await db.query(get_user);
        logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
 // If get_user not available send error response to client
		if (get_user_id.length == 0) {
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}
		else { // otherwise to get the user details
			user_id = get_user_id[0].user_id;
		}
				// query_select to get the query
				 query_select = ` SELECT wht.whatspp_config_id, wht.user_id,usr.user_name, concat(wht.country_code,wht.mobile_no) mobile_no, wht.whatspp_config_status, DATE_FORMAT(wht.whatspp_config_entdate,'%d-%m-%Y %h:%i:%s %p') whatspp_config_entdate, wht.wht_display_name, wht.wht_display_logo, wht.sent_count, wht.available_credit - wht.sent_count available_credit, DATE_FORMAT(wht.whatspp_config_apprdate,'%d-%m-%Y %h:%i:%s %p') whatspp_config_apprdate FROM rcs_config wht left join user_management usr on usr.user_id = wht.user_id left join  str on str.store_id = wht.store_id where wht.user_id = '${user_id}'  and is_qr_code = 'N'  and whatspp_config_status = 'Y'`;
                 
			get_manage_sender_id = await db.query(query_select + ` order by wht.whatspp_config_entdate desc`);

			logger_all.info("[select query request] : " + query_select + ` order by wht.whatspp_config_entdate desc`);

			logger_all.info("[select query response] : " + JSON.stringify(get_manage_sender_id));
// if the get_manage_sender_id is '0' to send the no available data.otherwise it will be return the get_manage_sender_id details.
			if (get_manage_sender_id.length == 0) {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
			else {
				return { response_code: 1, response_status: 200, num_of_rows: get_manage_sender_id.length, response_msg: 'Success', sender_id: get_manage_sender_id };
			}
		
	}
	catch (e) { // any error occurres send error response to client
		logger_all.info("[CheckSenderId failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// CheckSenderId - end

// using for module exporting
module.exports = {
	CheckSenderId
};

