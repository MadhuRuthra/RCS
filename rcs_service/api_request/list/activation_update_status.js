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
// Activation update status- start
async function Activationupdatestatus(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {

		// get all the req data
		var payment_id = req.body.payment_id;
		var payment_status = req.body.payment_status;
		var active_status = req.body.active_status;
		var payment_comments = req.body.payment_comments;

		// Query parameters 
		logger_all.info("[AddActivation_payment - query parameters] : " + JSON.stringify(req.body));
		
// Activation update status to execute this query
logger_all.info("[select query request] : " + `UPDATE activation_payment SET payment_status = '${payment_status}', active_status = '${active_status}',payment_comments = '${payment_comments}' WHERE payment_id = ' ${payment_id}' and active_status = 'N' `);
const Rppayment_usrsmscrd_id = await db.query(`UPDATE activation_payment SET payment_status = '${payment_status}', active_status = '${active_status}',payment_comments = '${payment_comments}' WHERE payment_id = ' ${payment_id}' and active_status = 'N'`);
logger_all.info("[select query response] : " + JSON.stringify(Rppayment_usrsmscrd_id));
	
// if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
if (Rppayment_usrsmscrd_id.affectedRows == 0) {
	return { response_code: 1, response_status: 204, response_msg: 'No data update' };
}
else {
	return { response_code: 1, response_status: 200, response_msg: 'Success' };
}
	
		
			}	
	catch (e) {// any error occurres send error response to client
		logger_all.info("[Activation update status failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// Activation update status - end

// using for module exporting
module.exports = {
	Activationupdatestatus
}



