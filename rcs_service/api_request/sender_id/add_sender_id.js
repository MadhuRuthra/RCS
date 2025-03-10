/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in senderid functions which is used to senderid details.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger');
require('dotenv').config()
// AddSenderId function - start
async function AddSenderId(req) {
  try {
    var logger_all = main.logger_all
    //  Get all the req header data
    const header_token = req.headers['authorization'];

    // get current Date and time
    var day = new Date();
    var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
    var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
    var current_date = today_date + ' ' + today_time;

    // get all the req data
    var user_id = req.body.user_id;
    var country_code = req.body.country_code;
    var mobile_no = req.body.mobile_no;
    var profile_name = req.body.profile_name;
    var profile_image = req.body.profile_image;
    var service_category = req.body.service_category;
    // declare the variables
    var country_code_id;
    // To initialize a variable
    var qr_code_allowed = "A";
    // query parameters
    logger_all.info("[AddSenderId query parameters] : " + JSON.stringify(req.body));
    // To get the User_id
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
    else {// otherwise to get the user details
      user_id = get_user_id[0].user_id;
    }
    //  to select the master countries from phonecode
    logger_all.info("[select query request] : " + `SELECT * FROM master_countries WHERE phonecode = ${country_code}`)
    const get_ccode = await db.query(`SELECT * FROM master_countries WHERE phonecode = ${country_code}`);
    logger_all.info("[select query response] : " + JSON.stringify(get_ccode));
    // if the get_ccode length is equal to '0' to send the Country not available message
    if (get_ccode.length == 0) {
      return { response_code: 0, response_status: 201, response_msg: 'Country not available' };
    }

    else { // otherwise the process will be continued
      // to get country_code_id
      country_code_id = get_ccode[0].id;
      // to check the whatspp_config_status is 'Y' and is_qr_code = 'N' are include to get the get_number query 
      logger_all.info("[select query request] : " + `SELECT * FROM rcs_config WHERE mobile_no = '${mobile_no}' AND country_code = '${country_code}' AND whatspp_config_status in ('Y', 'N') AND is_qr_code = 'N'`)
      const get_number = await db.query(`SELECT * FROM rcs_config WHERE mobile_no = '${mobile_no}' AND country_code = '${country_code}' AND whatspp_config_status in ('Y', 'N') AND is_qr_code = 'N'`);
      logger_all.info("[select query response] : " + JSON.stringify(get_number));
      // if the get number value '0' tho insert the rcs_config status to use the request datas.
      if (get_number.length == 0) {
        var response_result = await db.query(`INSERT INTO rcs_config VALUES(NULL, '${user_id}','1', '${mobile_no}','${qr_code_allowed}', 'N', '${current_date}', NULL, NULL, NULL,'${profile_name}', '${profile_image}','${service_category}','0', '0', NULL,'${country_code_id}', '${country_code}', 'N')`);

        logger_all.info("[AddSenderId - insert query request] : " + `INSERT INTO rcs_config VALUES(NULL, '${user_id}','1', '${mobile_no}','${qr_code_allowed}', 'N', '${current_date}', NULL, NULL, NULL,'${profile_name}', '${profile_image}','${service_category}','0', '0', NULL,'${country_code_id}',' ${country_code}', 'N')`);

        logger_all.info("[AddSenderId - insert query response] : " + JSON.stringify(response_result));

        // if the any data are not insert to add senderid Failure
        if (response_result.affectedRows != 1) {
          return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
        }
        else {
          // otherwise to send the add senderid Success
          return { response_code: 1, response_status: 200, response_msg: 'Sender ID added successfully!' };
        }
      }

      else { // otherwise the User already exists the reponse message send
        return { response_code: 0, response_status: 201, response_msg: 'User already exists' };
      }
    }

  }
  catch (e) { // any error occurres send error response to client
    logger_all.info("[Add Sender Id failed response] : " + e)
    return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
  }
}
// AddSenderId function - end

//Function Delete Sender Id - start
async function deleteSenderId(req) {
  try {
    var logger_all = main.logger_all
    //  Get all the req header data
    const header_token = req.headers['authorization'];

    // get all the req data
    var user_id = req.body.user_id;
    var whatspp_config_id = req.body.whatspp_config_id;
    // query parameters
    logger_all.info("[deleteSenderId query parameters] : " + JSON.stringify(req.body));
   

    // to check the whatspp_config_id and userid
    var check_user_id = await db.query(`SELECT * from rcs_config where whatspp_config_id  =  '${whatspp_config_id}'`);

    logger_all.info("[select query request] : " + `SELECT * from rcs_config where whatspp_config_id  =  '${whatspp_config_id}'`);

    logger_all.info("[select query response] : " + JSON.stringify(check_user_id));
    // if any check_user_id length is greater than '0' the process will be continue
    if (check_user_id.length > 0) {

      //to update the rcs_config status is 'D' in the rcs_config table
      var response_result = await db.query(`UPDATE rcs_config SET whatspp_config_status = 'D' WHERE whatspp_config_id = ${whatspp_config_id}`);

      logger_all.info("[deleteSenderId - update query request] : " + `UPDATE whatspp_config SET rcs_config_status = 'D' WHERE whatspp_config_id = ${whatspp_config_id}`);

      logger_all.info("[deleteSenderId - update query response] : " + JSON.stringify(response_result));
      return { response_code: 1, response_status: 200, response_result, response_msg: 'Success' };
    }

    else { // otherwise to send the No Mobile Number available
      return { response_code: 0, response_status: 201, response_msg: 'Sender ID not found.' };
    }

  }
  catch (e) { // any error occurres send error response to client
    logger_all.info("[Delete Sender id report failed response] : " + e)
    return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
  }
}
// deleteSenderId - end

// using for module exporting
module.exports = {
  AddSenderId,
  deleteSenderId
};
