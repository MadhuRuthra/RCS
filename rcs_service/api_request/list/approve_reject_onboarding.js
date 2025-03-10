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

// ApproveRejectOnboarding - start
async function ApproveRejectOnboarding(req) {
  var logger_all = main.logger_all
  var logger = main.logger
  try {
    // get all the req data
    var user_id = req.body.user_id;
    var change_user_id = req.body.change_user_id;
    var txt_remarks = req.body.txt_remarks;
    var aprj_status = req.body.aprj_status;

    var update_query_values = '';
    var update_query_value1 = '';

    var update_profile_details1;
    // query parameters
    if (txt_remarks) {
      update_query_value1 += `UPDATE user_details SET rejected_comments = '${txt_remarks}' WHERE user_id = ${change_user_id} `;
    }
    if (aprj_status == 'A') {
      update_query_values += `UPDATE user_management SET usr_mgt_status = 'Y' WHERE user_id = ${change_user_id}`;
    } else if (aprj_status == 'R') {
      update_query_values += `UPDATE user_management SET usr_mgt_status = 'R' WHERE user_id = ${change_user_id}`;
    }
    logger_all.info("[ApproveRejectOnboarding query parameters] : " + JSON.stringify(req.body));

    if (update_query_value1) {
      // ApproveRejectOnboarding to execute this query
      logger_all.info("[Update query request - User details1] : " + `${update_query_value1}`);
      update_profile_details1 = await db.query(`${update_query_value1}`);
      logger_all.info("[Update query request - User details1] : " + JSON.stringify(update_profile_details1));
      // if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
    }

    // ApproveRejectOnboarding to execute this query
    logger_all.info("[Update query request - User details] : " + ` ${update_query_values}`);
    const update_profile_details = await db.query(`${update_query_values}`);
    logger_all.info("[Update query request - User details] : " + JSON.stringify(update_profile_details));

    // if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
    if (update_profile_details) {
      return { response_code: 1, response_status: 200, num_of_rows: 1, response_msg: 'Success' };
    } else {
      return { response_code: 1, response_status: 204, response_msg: 'No data available' };
    }

  }
  catch (e) {// any error occurres send error response to client
    logger_all.info("[ApproveRejectOnboarding failed response] : " + e)
    return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
  }
}
// ApproveRejectOnboarding - end

// using for module exporting
module.exports = {
  ApproveRejectOnboarding,
}

