/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in logout functions which is used in logout.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../db_connect/connect");
require("dotenv").config();
const jwt = require('jsonwebtoken');
const main = require('../logger')
// logout function - start
async function logout(req) {
	var logger_all = main.logger_all

  // To get current Date and Time
  var day = new Date();
  var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
  var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
  var current_date = today_date + ' ' + today_time;

  // declare the variables
  let user_id;
  var slt_logout;
  //  Get all the req header data
  const header_token = req.headers['authorization'];
  // query parameters
  logger_all.info("[Logout query parameters] : " + JSON.stringify(req.body));
  try {
    // To get the User_id
    logger_all.info("[select query request] : " + `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `);
    var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
    if (req.body.user_id) {
      get_user = get_user + `and user_id = '${req.body.user_id}' `;
    }
    logger_all.info("[select query request] : " + get_user);
    const get_user_id = await db.query(get_user);
    logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
    // If get_user not available send error response to client in ivalid token
    if (get_user_id.length == 0) {
      logger_all.info("Invalid Token")
      return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
    }
    else { // otherwise to get the user details
      user_id = get_user_id[0].user_id;
      api_key = get_user_id[0].api_key;
    }
    //  To check the user_log_status = 'I' and login date and userid
    logger_all.info("[Logout Select query request] : " + `Select * from user_log WHERE user_id ='${user_id}' and  login_date ='${today_date}' and user_log_status = 'I'`);
    slt_logout = await db.query(
      `Select * from user_log WHERE user_id ='${user_id}' and login_date = '${today_date}' and user_log_status = 'I'`);
    logger_all.info("[Logout Select query response] : " + JSON.stringify(slt_logout))
    // If slt_logout any length are available to update the logout time and user_log_status ='O' 
    if (slt_logout.length > 0) {
 logger_all.info("[Update query request] : " + `UPDATE user_management SET bearer_token= '-' WHERE user_id ='${user_id}' `);
    const user_logout = await db.query(
      `UPDATE user_management SET bearer_token= '-' WHERE user_id ='${user_id}'`);
    logger_all.info("[Update query response] : " + JSON.stringify(user_logout));

      logger_all.info("[Logout Update query request] : " + `UPDATE user_log SET logout_time = '${current_date}',user_log_status ='O' WHERE user_id ='${user_id}' and login_date ='${today_date}' and user_log_status = 'I' `);
      const sql_logout = await db.query(
        `UPDATE user_log SET logout_time = '${current_date}',user_log_status ='O' WHERE user_id ='${user_id}' and  login_date ='${today_date}' and user_log_status = 'I' `);
      logger_all.info("[ Logout Update query response] : " + JSON.stringify(sql_logout))
      // And to update the device_token = '0' 
      return { response_code: 1, response_status: 200, response_msg: "Success" };
    }
    else { //otherwise to send the success message to the user
      return { response_code: 0, response_status: 201, response_msg: 'No active user found.' };
    }
  }

  catch (err) {
    // Failed - call_index_signin Sign in function
    logger_all.info(": [Logout ] Failed - " + err);
    return { response_code: 0, response_status: 201, response_msg: 'Error Occurred.' };
  }
}
// logout - end
// using for module exporting
module.exports = {
  logout
};
