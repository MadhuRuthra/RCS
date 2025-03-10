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

// AddActivation_payment Function - start
async function AddActivation_payment(req) {
	var logger_all = main.logger_all
	var logger = main.logger
	try {
		// //  Get all the req header data
		// const header_token = req.headers['authorization'];

		// get current Date and time
		var day = new Date();
		var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
                var nextyear_dt = (day.getFullYear() + 1) + '-' + (day.getMonth()+1) + '-' + (day.getDate());
                var next_year_date = nextyear_dt + ' ' + today_time;

		// get all the req data
               var user_name = req.body.user_name;
		var user_mobile = req.body.user_mobile;
		var user_email = req.body.user_email;
		var product_name = req.body.product_name;
        var price = req.body.price;

		// Query parameters 
		logger_all.info("[AddActivation_payment - query parameters] : " + JSON.stringify(req.body));
		
		
		// insert the Payment to request data
		logger_all.info("[insert query request] : " + `INSERT INTO activation_payment VALUES (NULL,'${user_name}','${user_mobile}', '${user_email}', '${product_name}', '${price}',NULL, 'N', 'N',CURRENT_TIMESTAMP,'${next_year_date}')`)
		const insert_payment = await db.query(`INSERT INTO activation_payment VALUES (NULL,'${user_name}','${user_mobile}', '${user_email}', '${product_name}', '${price}',NULL, 'N', 'N',CURRENT_TIMESTAMP,'${next_year_date}')`);
		logger_all.info("[insert query response] : " + JSON.stringify(insert_payment))	

		if (insert_payment.insertId) {   // update_succ to get the response message through the Payment Updated. 
			return {
				response_code: 1,
				response_status: 200,
				response_msg: 'Success'
			};

		} else { // otherwise send the No data available
			return {
				response_code: 0,
				response_status: 204,
				response_msg: 'No data insert'
			};
		}
	} catch (e) { // any error occurres send error response to client
		logger_all.info("[AddActivation_payment failed response] : " + e)
		return {
			response_code: 0,
			response_status: 201,
			response_msg: 'Error occured'
		};
	}
}
// AddActivation_payment Function - end
// using for module exporting
module.exports = {
	AddActivation_payment
}

