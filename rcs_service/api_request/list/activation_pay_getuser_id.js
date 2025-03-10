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
// AddActivation_paymentuserid- start
async function AddActivation_paymentuserid(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
// AddActivation_paymentuserid to execute this query
			logger_all.info("[select query request] : " + `SELECT MAX(payment_id) payment_id FROM activation_payment`);
			const get_payment_id = await db.query(`SELECT MAX(payment_id) payment_id FROM activation_payment`);
			logger_all.info("[select query response] : " + JSON.stringify(get_payment_id));
         // if the AddActivation_paymentuserid length is not available to send the no available data.otherwise it will be return the AddActivation_paymentuserid details.
			if (get_payment_id.length == 0) {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
			else {
				return { response_code: 1, response_status: 200, num_of_rows: 1, response_msg: 'Success', get_paymentid: get_payment_id };
			}
	
		
			}	
	catch (e) {// any error occurres send error response to client
		logger_all.info("[AddActivation_paymentuserid failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// AddActivation_paymentuserid - end

// using for module exporting
module.exports = {
	AddActivation_paymentuserid,
}



