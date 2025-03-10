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
// AvailableCreditsList- start
async function AvailableCreditsList(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		
		// get all the req data
		var user_id = req.body.user_id;
var select_user_id = req.body.select_user_id;

  if(select_user_id){ //select userid are coming to execute this condition
	user_id = select_user_id;
   }
// query parameters
		logger_all.info("[AvailableCreditsList query parameters] : " + JSON.stringify(req.body));
// get_available_messages to execute this query
			logger_all.info("[select query request] : " + `SELECT lmt.total_messages, sum(lmt.available_messages) available_messages, lmt.expiry_date FROM message_limit lmt left join user_management usr on lmt.user_id = usr.user_id where lmt.message_limit_status = 'Y' and (usr.user_id = '${user_id}' or usr.parent_id = '${user_id}')`);
			const get_available_messages = await db.query(`SELECT lmt.total_messages, sum(lmt.available_messages) available_messages, lmt.expiry_date FROM message_limit lmt left join user_management usr on lmt.user_id = usr.user_id where lmt.message_limit_status = 'Y' and (usr.user_id = '${user_id}' or usr.parent_id = '${user_id}')`);
			logger_all.info("[select query response] : " + JSON.stringify(get_available_messages));
            var total_available_messages = get_available_messages[0].available_messages;
         if(get_available_messages.length){
				logger_all.info("[select query request] : " + `SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${user_id}' or usr.parent_id in (${user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);
	
				var sql_query_2 = await db.query( `SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${user_id}' or usr.parent_id in (${user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc` );
	
				logger_all.info("[select query request] : " + JSON.stringify(sql_query_2));
				var total_avmsg = 0;
				for(var i = 1; i < sql_query_2.length; i++){
					logger_all.info("[select query request] : " + `SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${sql_query_2[i].user_id}' or usr.parent_id in (${sql_query_2[i].user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);
					var below_user_ids =await db.query( `SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${sql_query_2[i].user_id}' or usr.parent_id in (${sql_query_2[i].user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);
					//  if the sql_query_2.length is greater than 1 if the contion is true.
					if(sql_query_2.length > 1){
						 // loop all number of available_messages is push in to the arrays.  available then process the will be continued
					for(var j = 1; j < below_user_ids.length; j++){
						total_avmsg = total_avmsg + below_user_ids[j].available_messages;
					}
					// ADD in two below user_ids
					var tot_ava_msg = +total_available_messages+ total_avmsg;
				}
				// JSON push in available_messages
				get_available_messages[0].available_messages = tot_ava_msg;
			}	
		
	}
// if the get_available_messages length is not available to send the no available data.otherwise it will be return the get_available_messages details.
			if (get_available_messages.length == 0) {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
			else {
				return { response_code: 1, response_status: 200, num_of_rows: get_available_messages.length, response_msg: 'Success', report: get_available_messages };
			}
	
}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[AvailableCreditsList failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// AvailableCreditsList - end

// using for module exporting
module.exports = {
	AvailableCreditsList,
}


