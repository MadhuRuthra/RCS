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
// Activation_details- start
async function Activation_details(req) {
	var logger_all = main.logger_all
    var logger = main.logger

    var condition ='';
	try {
        // get all the req data
		var mobile_no = req.body.user_mobile;
		var email_id = req.body.user_email;

        if(mobile_no != undefined){
            condition = `and mobile_no = '${mobile_no}'`;
        }
        if(email_id != undefined){
            condition = `and email_id = '${email_id}'`;
        }
        	// Query parameters 
		logger_all.info("[AddActivation_payment - query parameters] : " + JSON.stringify(req.body));
// Activation_details to execute this query
			logger_all.info("[select query request] : " + `SELECT * FROM activation_payment where payment_status = 'Y' and active_status = 'Y' ${condition}`);
			const get_payment_id = await db.query(`SELECT * FROM activation_payment where payment_status = 'Y' and active_status = 'Y' ${condition}`);
			logger_all.info("[select query response] : " + JSON.stringify(get_payment_id));
         // if the Activation_details length is not available to send the no available data.otherwise it will be return the Activation_details details.
			if (get_payment_id.length == 0) {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
			else {
				return { response_code: 1, response_status: 200, num_of_rows: 1, response_msg: 'Success', get_payment: get_payment_id };
			}
	
		
			}	
	catch (e) {// any error occurres send error response to client
		logger_all.info("[Activation_details failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// Activation_details - end

// using for module exporting
module.exports = {
	Activation_details,
}



