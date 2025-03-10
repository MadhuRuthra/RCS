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
// approvepayment - start
async function WaitingApprovalList (req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		//  Get all the req header data
		const header_token = req.headers['authorization'];

		// get all the req data
		var user_id = req.body.user_id;
		// query parameters
		logger_all.info("[PaymentHistory request query parameters] : " + JSON.stringify(req.body));
		// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
		if (req.body.user_id) {
			get_user = get_user + `and user_id = '${req.body.user_id}' `;
		}

		logger_all.info("[select query request] : " + get_user);
		const get_user_id = await db.query(get_user);
		logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
		// If get_user not available send error response to client
		if (get_user_id.length == 0) {
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}else{
            user_id = get_user_id[0].user_id;
        }
		//  Notification Waiting approval
        var approve_payment = `SELECT rse.*, usr.user_name, prn.user_name parent_name, pri.price_from, pri.price_to, pri.price_per_message FROM user_sms_credit_raise rse left join user_management usr on rse.user_id = usr.user_id left join user_management prn on rse.parent_id = prn.user_id left join pricing_slot pri on rse.pricing_slot_id = pri.pricing_slot_id where rse.parent_id = '${user_id}' and rse.usrsmscrd_status = 'A' order by rse.usrsmscrd_entry_date desc`;
		logger_all.info("[select query request] : " + approve_payment);
		const get_approve_payment = await db.query(approve_payment);
		 logger_all.info("[select query response] : " + JSON.stringify(get_approve_payment))

         //  Notification Waiting senderid
         var waiting_senderid = `SELECT config.* FROM rcs_config config left join user_management usr on config.user_id = usr.user_id where usr.parent_id = '${user_id}' and config.whatspp_config_status = 'N'`;
         logger_all.info("[select query request] : " + waiting_senderid);
         const waiting_senderid_count = await db.query(waiting_senderid);
          logger_all.info("[select query response] : " + JSON.stringify(waiting_senderid_count))

            //  Notification Waiting template
         var waiting_template = `SELECT temp.* FROM message_template temp left join user_management usr on temp.created_user = usr.user_id where usr.parent_id = '${user_id}' and temp.template_status = 'N'`;
         logger_all.info("[select query request] : " + waiting_template);
         const waiting_template_count = await db.query(waiting_template);
          logger_all.info("[select query response] : " + JSON.stringify(waiting_template_count))


//  Notification Waiting compose
		const userIdQuery = "SELECT user_id FROM rcs.user_management";
		const userIds = await db.query(userIdQuery);
		var array_userid = [];
		var waiting_compose = '';
		// Loop through each user ID
		for (let i = 0; i < userIds.length; i++) {
			array_userid.push(userIds[i].user_id);
		}
		var array_list_userid_string = array_userid.join("','");
		// Construct the SQL query to fetch approval details for the current user ID
		waiting_compose = waiting_compose + `SELECT user_id, compose_rcs_id FROM rcs.master_compose_rcs WHERE rcs_status = 'W' and user_id in ('${array_list_userid_string}') `;

		logger_all.info("[select query request] : " + waiting_compose);
		const waiting_compose_count = await db.query(waiting_compose);
		logger_all.info("[select query response] : " + JSON.stringify(waiting_compose_count))



   //  Notification Waiting template
   var waiting_users = `SELECT usr.* FROM user_management usr left join user_details usr_de on usr.user_id = usr_de.user_id where usr.parent_id = '${user_id}' and usr.usr_mgt_status = 'N' `;
   logger_all.info("[select query request] : " + waiting_users);
   const waiting_users_count = await db.query(waiting_users);
    logger_all.info("[select query response] : " + JSON.stringify(waiting_users_count))

		
			return { response_code: 1, response_status: 200, waiting_payment: get_approve_payment.length, waiting_senderid: waiting_senderid_count.length,waiting_template: waiting_template_count.length, waiting_compose: waiting_compose_count.length,waiting_users: waiting_users_count.length,response_msg: 'Success'};

	}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[PaymentHistory failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// approvepayment - end

// using for module exporting
module.exports = {
	WaitingApprovalList
}
