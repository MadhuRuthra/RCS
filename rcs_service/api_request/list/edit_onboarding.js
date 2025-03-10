/*
This api has chat API functions which is used to connect the mobile chat.
This page is act as a Backend page which is connect with Node JS API and PHP Frontend.
It will collect the form details and send it to API.
After get the response from API, send it back to Frontend.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 28-Jul-2023
*/

// Import the required packages and libraries
const db = require("../../db_connect/connect");
require("dotenv").config();
const main = require("../../logger");

// EditOnboarding - start
async function EditOnboarding(req) {
  var logger_all = main.logger_all;
  var logger = main.logger;
  try {
    //  Get all the req header data
    const header_token = req.headers["authorization"];

    // get all the req data
    var ex_password = req.body.ex_password;
    var new_password = req.body.new_password;

    // query parameters
    logger_all.info("[Edit Onboarding query parameters] : " + JSON.stringify(req.body));

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
      logger_all.info("Invalid Token");
      return {
        response_code: 0,
        response_status: 201,
        response_msg: "Invalid Token",
      };
    } else {
      // otherwise to get the user details
      user_id = get_user_id[0].user_id;
    }

    // get_Edit_onboarding this condition is true.process will be continued. otherwise process are stoped.
    logger_all.info("[select query request] : " + `SELECT * FROM user_details where user_id = '${user_id}'`);
    const get_Edit_onboarding = await db.query(
      `SELECT * FROM user_details where user_id = '${user_id}'`
    );
    logger_all.info("[select query response] : " + JSON.stringify(get_Edit_onboarding));
    // if the get_Edit_onboarding length is not available to send the Invalid Existing Password. Kindly try again!.otherwise the process was continued
    if (get_Edit_onboarding.length == 0) {
      return {
        response_code: 0,
        response_status: 201,
        response_msg: "Invalid User details. Kindly try again!",
      };
    } else {
      return {
        // to return the success message
        response_code: 1,
        response_status: 200,
        num_of_rows: get_Edit_onboarding.length,
        response_msg: 'Success',
        report: get_Edit_onboarding
      };
    }
  } catch (e) {
    // any error occurres send error response to client
    logger_all.info("[Edit Onboarding failed response] : " + e);
    return {
      response_code: 0,
      response_status: 201,
      response_msg: "Error occured",
    };
  }
}
// EditOnboarding - end

// using for module exporting
module.exports = {
  EditOnboarding,
};

