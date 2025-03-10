/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in login functions which is used in login.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../db_connect/connect");
const md5 = require("md5")
const main = require('../logger')
require("dotenv").config();
const dynamic_db = require("../db_connect/dynamic_connect");
const jwt = require('jsonwebtoken');
const { response } = require("express");

// portal login Function - Start
async function login(req) {

  var logger = main.logger
  var logger_all = main.logger_all

  // To get current Date and Time
  var day = new Date();
  var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
  var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
  var current_date = today_date + ' ' + today_time;

  // declare the variables
  var user_id;
  // get all the req data
  let txt_username = req.body.txt_username;
  let txt_password = md5(req.body.txt_password);
  var header_json = req.headers;
  let ip_address = header_json['x-forwarded-for'];

  logger.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers) + " - " + ip_address)
  logger_all.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers) + " - " + ip_address)

  try { // To check the login_id if the user_management table already exists are not
   // logger_all.info("[select query request] : " + "SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'W') ORDER BY user_id ASC");
    //const sql_stat = await db.query(
      //"SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'W') ORDER BY user_id ASC"
    //);
    // If sql_stat any length are available to send the error message in Inactive or Not Approved User. Kindly contact your admin!
    //if (sql_stat.length > 0) {
      // Failed [Inactive or Not Approved User] - call_index_signin Sign in function
      //logger_all.info(": [call_index_signin] Failed - Inactive or Not Approved User. Kindly contact your admin!");
      //return { response_code: 0, response_status: 201, response_msg: "Inactive or Not Approved User. Kindly contact your admin!" };
    //} else { // Otherwise To check the where Login id and Usr_mgt_status
      logger_all.info("[select query request] : " + "SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC")
      const sql_login = await db.query(
        "SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC"
      );
      // If any sql_login length are availble to send the error message Invalid User. Kindly try again with the valid User!
      if (sql_login.length <= 0) {
        // Failed [Invalid User] - call_index_signin Sign in function
        logger_all.info(": [call_index_signin] Failed - Invalid User. Kindly try again with the valid User!");
        return { response_code: 0, response_status: 201, response_msg: "Invalid User. Kindly try again with the valid User!" };
      } else { // Otherwise to check login_id and login_password and usr_mgt_status
        logger_all.info("[select query request] : " + "SELECT * FROM user_management where login_id = '" + txt_username + "' and login_password = '" + txt_password + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC");
        const response_result = await db.query(
          "SELECT * FROM user_management where login_id = '" + txt_username + "' and login_password = '" + txt_password + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC"
        );
        // If response_result length are available to send the message in Invalid Password. Kindly try again with the valid details!
        if (response_result.length <= 0) {
          // Failed [Invalid Password] - call_index_signin Sign in function
          logger_all.info(": [call_index_signin] Failed  - Invalid Password. Kindly try again with the valid details!");
          return { response_code: 0, response_status: 201, response_msg: "Invalid Password. Kindly try again with the valid details!" };
        } else {
          // Otherwise the process are continue to create the JWT token
          user_id = response_result[0].user_id;
          const user =
          {
            username: req.body.txt_username,
            user_password: req.body.txt_password,
          }
          // Sign the jwt 
          const accessToken_1 = jwt.sign(user, process.env.ACCESS_TOKEN_SECRET, {
            expiresIn: process.env.ONEWEEK

          });
          process.env['TOKEN_SECRET'] = "Bearer " + accessToken_1;
          var bearer_token = process.env.TOKEN_SECRET;
          // To update the bearer token in the usermanagement table
          logger_all.info("[Update query request] : " + `UPDATE user_management SET bearer_token = '${bearer_token}' WHERE user_id = '${user_id}'`);
          var token = await db.query(`UPDATE user_management SET bearer_token = '${bearer_token}' WHERE  user_id = '${user_id}'`);
          logger_all.info("[Update query Response] : " + JSON.stringify(token));
          // To Login Success - If the user_log_status is 'I' and login date are already exists are not.
          logger_all.info("[select query request] : " + `SELECT user_id,user_log_status,login_date FROM user_log where user_id ='${response_result[0].user_id}' and user_log_status = 'I' and date(login_date) ='${today_date}'`);
          const check_login = await db.query(`SELECT user_id,user_log_status,login_date FROM user_log where user_id ='${response_result[0].user_id}' and user_log_status = 'I' and date(login_date) ='${today_date}'`);

          if (check_login.length == 0) {
            // check login length is 0 .the process was continue.To insert the user_log table in the values
            logger_all.info("[insert query request] : " + `INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            const insert_login = await db.query(`INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            // Json Push
            response_result[0]['bearer_token'] = bearer_token;
            // To send the Success message and user details in the user
            logger_all.info(": [call_index_signin] Success ");
            return { response_code: 1, response_status: 200, response_msg: "Success", login_time: current_date, response_result };

          }
          else { // Otherwise to update the userlog table to set the value is user_log_status = 'O' and login date
            logger_all.info("[update query request] : " + `UPDATE user_log SET user_log_status = 'O', logout_time = '${current_date}' WHERE user_id = '${response_result[0].user_id}' AND user_log_status = 'I' AND login_date = '${today_date}'`);
            const update_logout = await db.query(`UPDATE user_log SET user_log_status = 'O', logout_time = '${current_date}' WHERE user_id = '${response_result[0].user_id}' AND user_log_status = 'I' AND login_date = '${today_date}'`);
            // And user_log table to insert the details.
            logger_all.info("[insert query request] : " + `INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            const insert_login = await db.query(`INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            // Json Push
            response_result[0]['bearer_token'] = bearer_token;
            // To send the Success message and user details in the user
            logger_all.info(": [call_index_signin] Success ");
            return { response_code: 1, response_status: 200, response_msg: "Success", login_time: current_date, response_result };
          }

        }
      }
    }
 //} 
catch (err) {
    // Failed - call_index_signin Sign in function
    logger_all.info(": [call_index_signin] Failed - " + err);
    return { response_code: 0, response_status: 201, response_msg: 'Error Occurred.' };
  }
}
//login Function - end

//api login Function - Start
async function api_login(req) {
  var logger = main.logger
	var logger_all = main.logger_all

  // get current Date and time
  var day = new Date();
  var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
  var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
  var current_date = today_date + ' ' + today_time;

  // let query = " ";
  // get all the req data
  let txt_username = req.body.txt_username;
  let txt_password = md5(req.body.txt_password);
  var header_json = req.headers;
  let ip_address = header_json['x-forwarded-for'];

  logger.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers) + " - " + ip_address)
  logger_all.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers) + " - " + ip_address)

  try { // To check the login_id if the user_management table already exists are not
    //logger_all.info("[select query request] : " + "SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'W') ORDER BY user_id ASC");
    //const sql_stat = await db.query(
      //"SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'W') ORDER BY user_id ASC"
    //);
    // If sql_stat any length are available to send the error message in Inactive or Not Approved User. Kindly contact your admin!
    //if (sql_stat.length > 0) {
      // Failed [Inactive or Not Approved User] - call_index_signin Sign in function
      //logger_all.info(": [call_index_signin] Failed - Inactive or Not Approved User. Kindly contact your admin!");

      //return { response_code: 0, response_status: 201, response_msg: "Inactive or Not Approved User. Kindly contact your admin!" };
    //} else { // Otherwise To check the where Login id and Usr_mgt_status
      logger_all.info("[select query request] : " + "SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC")
      const sql_login = await db.query(
        "SELECT * FROM user_management where login_id = '" + txt_username + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC"
      );
      // If any sql_login length are availble to send the error message Invalid User. Kindly try again with the valid User!
      if (sql_login.length <= 0) {
        // Failed [Invalid User] - call_index_signin Sign in function
        logger_all.info(": [call_index_signin] Failed - Invalid User. Kindly try again with the valid User!");
        return { response_code: 0, response_status: 201, response_msg: "Invalid User. Kindly try again with the valid User!" };
      } else {  // Otherwise to check login_id and login_password and usr_mgt_status
        logger_all.info("[select query request] : " + "SELECT * FROM user_management where login_id = '" + txt_username + "' and login_password = '" + txt_password + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC");
        const response_result = await db.query(
          "SELECT * FROM user_management where login_id = '" + txt_username + "' and login_password = '" + txt_password + "' and usr_mgt_status in ('N', 'Y', 'R') ORDER BY user_id ASC"
        );
        // If response_result length are available to send the message in Invalid Password. Kindly try again with the valid details!
        if (response_result.length <= 0) {
          // Failed [Invalid Password] - call_index_signin Sign in function
          logger_all.info(": [call_index_signin] Failed  - Invalid Password. Kindly try again with the valid details!");
          return { response_code: 0, response_status: 201, response_msg: "Invalid Password. Kindly try again with the valid details!" };
        } else {
          // Otherwise the process are continue to create the JWT token
          user_id = response_result[0].user_id;
          const user =
          {
            username: req.body.txt_username,
            user_password: req.body.txt_password,
          }
          // Sign the jwt 
          const accessToken_1 = jwt.sign(user, process.env.ACCESS_TOKEN_SECRET, {
            expiresIn: process.env.ONEWEEK
            // expiresIn:"18s"

          });

          var bearer_token = "Bearer " + accessToken_1;
          // To update the bearer token in the usermanagement table
          logger_all.info("[Update query request] : " + `UPDATE user_management  SET bearer_token = '${bearer_token}' WHERE user_id = '${user_id}'`);
          var token = await db.query(`UPDATE user_management SET bearer_token = '${bearer_token}' WHERE user_id = '${user_id}'`);
          logger_all.info("[Update query Response] : " + JSON.stringify(token));

          // To Login Success - If the user_log_status is 'I' and login date are already exists are not.
          logger_all.info("[select query request] : " + `SELECT user_id,user_log_status,login_date FROM user_log where user_id ='${response_result[0].user_id}' and user_log_status = 'I' and date(login_date) ='${today_date}'`);
          const check_login = await db.query(`SELECT user_id,user_log_status,login_date FROM user_log where user_id ='${response_result[0].user_id}' and user_log_status = 'I' and date(login_date) ='${today_date}'`);

          if (check_login.length == 0) {  // check login length is 0 .the process was continue.To insert the user_log table in the values
            logger_all.info("[insert query request] : " + `INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            const insert_login = await db.query(`INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            // To send the Success message and user details in the user
            logger_all.info(": [call_index_signin] Success ");
            return { response_code: 1, response_status: 200, response_msg: "Success", bearer_token,user_short_name:response_result[0].user_short_name };

          }
          else { // Otherwise to update the userlog table to set the value is user_log_status = 'O' and login date
            logger_all.info("[update query request] : " + `UPDATE user_log SET user_log_status = 'O',logout_time = '${current_date}' WHERE user_id = '${response_result[0].user_id}' AND user_log_status = 'I' AND login_date = '${today_date}'`);
            const update_logout = await db.query(`UPDATE user_log SET user_log_status = 'O', logout_time = '${current_date}' WHERE user_id = '${response_result[0].user_id}' AND user_log_status = 'I' AND login_date = '${today_date}'`);
            // And user_log table to insert the details.
            logger_all.info("[insert query request] : " + `INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            const insert_login = await db.query(`INSERT INTO user_log VALUES(NULL, '${response_result[0].user_id}', '${ip_address}', '${current_date}', '${current_date}', NULL, 'I', '${current_date}')`);
            // To send the Success message and user details in the user
            logger_all.info(": [call_index_signin] Success ");
            return { response_code: 1, response_status: 200, response_msg: "Success", bearer_token,user_short_name:response_result[0].user_short_name  };
          }

        }
      }
    }
 //}
 catch (err) {
    // Failed - call_index_signin Sign in function error
    logger_all.info(": [call_index_signin] Failed - " + err);
    return { response_code: 0, response_status: 201, response_msg: 'Error Occurred.' };
  }
}

//login Function - End 

//signup Function - start 
async function Signup(req) {
  var logger_all = main.logger_all

  try {
    // let query = " ";
    let user_type = req.body.user_type;
    let user_name = req.body.user_name;
    let user_email = req.body.user_email;
    let user_mobile = req.body.user_mobile;
    let login_shortname = req.body.login_shortname;
    let user_password = md5(req.body.user_password);
    let user_permission = req.body.user_permission;
    var insert_user;

// Get today's date
let today = new Date();

// Add one year to today's date
let oneYearValidity = new Date(today);
oneYearValidity.setFullYear(oneYearValidity.getFullYear() + 1);

// Format today's date
let formattedToday = today.toISOString().replace('T', ' ').replace(/\.\d+Z$/, '');
// Format one year validity date
let formattedValidity = oneYearValidity.toISOString().replace('T', ' ').replace(/\.\d+Z$/, '');
console.log("Today's Date:", formattedToday);
console.log("One Year Validity:", formattedValidity);

    // To check the login_id and user_email already exists are not.if already exists to send the error message
    logger_all.info(" [signup - select query request] : " + `SELECT * FROM user_management where login_id = '${user_name}' or user_email = '${user_email}'`);
    const sql_stat = await db.query(`SELECT * FROM user_management where login_id = '${user_name}'`);
    if (sql_stat.length > 0) {
      // Failed [Inactive or Not Approved User] - call_index_signin Sign in function
      logger_all.info(" : [signup] Failed - Client Name already used. Kindly try with some others!!");
      return {
        response_code: 0,
        response_status: 201,
        response_msg: "Client Name already used. Kindly try with some others!!"
      };
    } else {

    // To generate the random characters in the apikey
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let length = 32;
    let result = ' ';
    const charactersLength = characters.length;
    // loop the length To create the random number in the apikey
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    string = result.substring(0, 15);
    apikey = string.toUpperCase();

    // Otherwise the process was continued.to check the user type
    switch (user_type) {
      case "2": // Super Admin
        user_type = 2;
        parent_id = 1;
        // To insert the user_management table from the request values
        logger_all.info(" [signup - insert query request] : " + `INSERT INTO user_management VALUES (NULL, '${user_type}', '1', '${user_name}', '${login_shortname}', '${apikey}', '${user_name}', '${user_password}', '${user_email}', '${user_mobile}', NULL, NULL, NULL, NULL, '${user_permission}', 'Y', CURRENT_TIMESTAMP,'-')`)

        insert_user = await db.query(`INSERT INTO user_management VALUES (NULL, '${user_type}', '1', '${user_name}', '${login_shortname}', '${apikey}', '${user_name}', '${user_password}', '${user_email}', '${user_mobile}', NULL, NULL, NULL, NULL, '${user_permission}', 'Y', CURRENT_TIMESTAMP,'-')`);
        logger_all.info(" [signup - insert query response] : " + JSON.stringify(insert_user))
        var lastid = insert_user.insertId;
        break;
      case "3": // Dept Admin
        var explod1 = slt_super_admin.split("~~");
        parent_id = explod1[0];
        user_type = 3;
        // To insert the user_management table from the request values
        logger_all.info(" [signup - insert query request] : " + `INSERT INTO user_management VALUES (NULL, '${user_type}', '${parent_id}', '${user_name}', '${login_shortname}', '${apikey}', '${user_name}', '${user_password}', '${user_email}', '${user_mobile}', NULL, NULL, NULL, NULL, '${user_permission}', 'Y', CURRENT_TIMESTAMP,'-')`)
        insert_user = await db.query(`INSERT INTO user_management VALUES (NULL, '${user_type}', '${parent_id}', '${user_name}', '${login_shortname}', '${apikey}', '${user_name}', '${user_password}', '${user_email}', '${user_mobile}', NULL, NULL, NULL, NULL, '${user_permission}', 'Y', CURRENT_TIMESTAMP,'-')`);
        logger_all.info(" [signup - insert query response] : " + JSON.stringify(insert_user))
        var lastid = insert_user.insertId; break;
      case "4": // Agent
        var explod2 = slt_dept_admin.split("~~");
        parent_id = explod2[0];
        user_type = 4;
        break;
      default:
        // default
        break;
    }


    // user Details insert values
    logger_all.info(" [signup - insert query request] : " + `
            INSERT INTO user_details (user_details_id, user_id, cont_person, cont_designation, cont_mobile_no, cont_email, billing_address, company_name, company_website, parent_company_name, company_display_name, description_business, profile_image, business_category, sender,sender2, sender_1, sender_2, message_type, opt_process, enquiry_approval, privacy_terms, terms_conditions, document_proof, proof_doc_name, volume_day_expected, updated_date) 
            VALUES (NULL,'${lastid}', NULL,NULL, NULL, NULL, NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL,NULL,CURRENT_TIMESTAMP) `);
    const insert_user_details = await db.query(` INSERT INTO user_details (user_details_id, user_id, cont_person, cont_designation, cont_mobile_no, cont_email, billing_address, company_name, company_website, parent_company_name, company_display_name, description_business, profile_image, business_category, sender,sender2, sender_1, sender_2, message_type, opt_process, enquiry_approval, privacy_terms, terms_conditions, document_proof, proof_doc_name, volume_day_expected, updated_date) 
            VALUES (NULL,'${lastid}', NULL,NULL, NULL, NULL, NULL, NULL,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,NULL,NULL,CURRENT_TIMESTAMP)
            `);
    logger_all.info(" [signup - insert query response] : " + JSON.stringify(insert_user_details));

    // To create the new DB for the sign the new user --
    let exp_result1 = await db.query(`CREATE DATABASE IF NOT EXISTS rcs_${lastid} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci`);

    logger_all.info(" [signup - create DB response] : " + JSON.stringify(exp_result1))
    var db_name = `rcs_${lastid}`;


    // The process is continued.To create the new table in the new database
    let exp_result4 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS compose_rcs_status_tmpl_${lastid}(
        comwtap_status_id int NOT NULL ,
        compose_rcs_id int NOT NULL,
        country_code int DEFAULT NULL,
        mobile_no varchar(13) NOT NULL,
        report_group varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
        comments varchar(100) NOT NULL,
        comwtap_status char(1) NOT NULL,
        comwtap_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        response_status char(1) DEFAULT NULL,
        response_message varchar(100) DEFAULT NULL,
        response_id varchar(100) DEFAULT NULL,
        response_date timestamp NULL DEFAULT '0000-00-00 00:00:00',
        delivery_status varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
        delivery_date timestamp NULL DEFAULT NULL,
        read_date timestamp NULL DEFAULT NULL,
        read_status char(1) DEFAULT NULL,
        campaign_status char(1) NOT NULL
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3`, null, `${db_name}`);

    logger_all.info(` [signup - create table compose_rcs_status_tmpl_ - 1] : ` + JSON.stringify(exp_result4))

    let alter_11 = await dynamic_db.query(`ALTER TABLE compose_rcs_status_tmpl_${lastid}
      MODIFY comwtap_status_id INT NOT NULL AUTO_INCREMENT,
      ADD PRIMARY KEY (comwtap_status_id),
      ADD KEY compose_rcs_id (compose_rcs_id),
      ADD KEY mobile_no (mobile_no),
      ADD KEY report_group (report_group)`, null, `${db_name}`);

    logger_all.info(`[signup - Alter compose_rcs_status_tmpl_${lastid} response - 2]: ` + JSON.stringify(alter_11));

    // let alter_22 = await dynamic_db.query(`ALTER TABLE compose_rcs_status_tmpl_${lastid}
    // MODIFY comwtap_status_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1)`, null, `${db_name}`);
    // logger_all.info(`[signup - Alter compose_rcs_tmpl_${lastid} response - 3]: ` + JSON.stringify(alter_22));

    let exp_result12 = await dynamic_db.query(`
      CREATE TABLE IF NOT EXISTS compose_rcs_tmpl_${lastid} (
        compose_rcs_id int NOT NULL,
        user_id int NOT NULL,
        store_id int NOT NULL,
        whatspp_config_id int NOT NULL,
        mobile_nos longblob NOT NULL,
        sender_mobile_nos longblob NOT NULL,
        variable_values longblob,
	media_values longblob,
        rcs_content varchar(1000) NOT NULL,
        message_type varchar(50) NOT NULL,
        total_mobileno_count int DEFAULT NULL,
        content_char_count int NOT NULL,
        content_message_count int NOT NULL,
        campaign_name varchar(30) DEFAULT NULL,
        campaign_id varchar(10) DEFAULT NULL,
        mobile_no_type varchar(1) NOT NULL,
        unique_template_id varchar(30) NOT NULL,
        template_id varchar(10) DEFAULT NULL,
        rcs_status char(1) NOT NULL,
        rcs_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        media_url varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
      ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3`, null, `${db_name}`);

    logger_all.info(`[signup - create table compose_rcs_status_tmpl_${lastid} response - 4]: ` + JSON.stringify(exp_result12));

    let alter_1 = await dynamic_db.query(`ALTER TABLE compose_rcs_tmpl_${lastid}  MODIFY compose_rcs_id INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (compose_rcs_id),ADD KEY user_id (user_id,store_id,whatspp_config_id)`, null, `${db_name}`);

    logger_all.info(`[signup - Alter compose_rcs_tmpl_${lastid} response - 5]: ` + JSON.stringify(alter_1));

  let alter_2 = await dynamic_db.query(`ALTER TABLE compose_rcs_tmpl_${lastid} ADD tg_base VARCHAR(50) NULL AFTER media_url, ADD cg_base VARCHAR(50) NULL AFTER tg_base, ADD reject_reason VARCHAR(50) NULL AFTER cg_base`, null, `${db_name}`);
    logger_all.info(`[signup - Alter compose_rcs_tmpl_${lastid} response - 5]: ` + JSON.stringify(alter_2));

    // let alter_2 = await dynamic_db.query(`ALTER TABLE compose_rcs_tmpl_${lastid} 
    // MODIFY compose_rcs_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1)`, null, `${db_name}`);
    // logger_all.info(`[signup - Alter compose_rcs_tmpl_${lastid} response - 6]: ` + JSON.stringify(alter_2));

    // To insert the message_limit values
    logger_all.info(" [signup message_limit - insert query request] : " + `INSERT INTO message_limit VALUES (NULL, '${lastid}', 1000, 1000, 'Y', CURRENT_TIMESTAMP, '${formattedValidity}')`)
    const insert_msglimit = await db.query(`INSERT INTO message_limit VALUES (NULL, '${lastid}', 1000, 1000, 'Y', CURRENT_TIMESTAMP, '${formattedValidity}')`);
    logger_all.info(` [signup message_limit - insert query response] : ` + JSON.stringify(insert_msglimit))
    // insert_msglimit length is '0' to through the no data available message.

    if (insert_msglimit) {
      return {
        response_code: 1,
        response_status: 200,
        num_of_rows: 1,
        response_msg: 'Success'
      };
    }
    }
  } catch (err) {
    // any error occurres send error response to client
    logger_all.info(" : [signup] Failed - " + err);
    return {
      response_code: 0,
      response_status: 201,
      response_msg: err.message
    };
  }
}
//signup Function - end 


//Edit user Function - start 
async function EditUser(req) {
  var logger_all = main.logger_all

  try {
    // let query = " ";
    console.log("edit user function");
    let user_id = req.body.user_id;
    let user_name = req.body.user_name;
    let user_mobile = req.body.user_mobile;
    let user_password = md5(req.body.user_password);
    

          // Update user_log_status to 'O' and set the logout time to current time
          const updateQuery = `UPDATE rcs.user_log SET user_log_status = 'O', logout_time = NOW() WHERE user_id = '${user_id}' AND user_log_status = 'I'`;
          const user_log_update = await db.query(updateQuery);
          logger_all.info("[update query response] : " + JSON.stringify(user_log_update))
      
    

    var edit_user_update = `UPDATE rcs.user_management SET user_name = '${user_name}',login_id = '${user_name}', user_mobile = '${user_mobile}', login_password = '${user_password}' WHERE user_id = ${user_id}`;
		logger_all.info("[select query request for delete user] : " + edit_user_update);
		const edit_user = await db.query(edit_user_update);
		logger_all.info("[select query response] : " + JSON.stringify(edit_user))
        
		if (edit_user.affectedRows === 0) {
			return { response_code: 0, response_status: 404, response_msg: 'User not found or already deleted' };
		} else {
			return { response_code: 1, response_status: 200, num_of_rows: 1, response_msg: 'Success' };
		}

    
  } catch (err) {
    // any error occurres send error response to client
    logger_all.info(" : [EditUser] Failed - " + err);
    return {
      response_code: 0,
      response_status: 201,
      response_msg: err.message
    };
  }
}
//Edit User Function - end 


//onboarding signup Function - start 
async function onboardingSignup(req) {
	var logger_all = main.logger_all
  try {
    // let query = " ";
    let client_name = req.body.client_name;
    let official_email = req.body.official_email;
    let official_mobile_no = req.body.official_mobile_no;
    let reg_official_address_cpy = req.body.reg_official_address_cpy;
    // let slt_super_admin = req.body.slt_super_admin;
    // let slt_dept_admin = req.body.slt_dept_admin;
    let login_shortname = req.body.login_shortname;
    let user_password = md5(req.body.user_password);  
    let user_permission = req.body.user_permission;
 let user_type = 2;
    let cont_person = req.body.contact_person;
    let cont_designation = req.body.contact_person_designation;
    let cont_person_mobileno = req.body.contact_person_mobileno;
    let cont_email = req.body.contact_person_email_id;
    let billing_address = req.body.billing_address;
    let company_name = req.body.company_name;
    let company_website = req.body.company_website;
    let parent_company_name = req.body.parent_company_name;
    let company_display_name = req.body.company_display_name;
    let description_business = req.body.description_business;
    let profile_image = req.body.profile_image;
    let business_category = req.body.business_category;
    let sender = req.body.sender;
    let sender_txt_2 = req.body.sender_txt_2;
    let sender1 = req.body.sender1;
    let sender2 = req.body.sender2;
    let message_type = req.body.message_type;
    let otp_process = req.body.otp_process;
    let enquiry_approval = req.body.enquiry_approval;
    let privacy_terms = req.body.privacy_terms;
    let terms_conditions = req.body.terms_conditions;
    let proof_doc_name = req.body.proof_doc_name;
    let document_proof = req.body.document_proof;
    let volume_day_expected = req.body.volume_day_expected;
    // To check the login_id and user_email already exists are not.if already exists to send the error message
    // query parameters
		logger_all.info("[onboarding signup query parameters] : " + JSON.stringify(req.body));
    logger_all.info(" [signup - select query request] : " + `SELECT * FROM user_management where login_id = '${client_name}' or user_email = '${official_email}'`);
    const sql_stat = await db.query(`SELECT * FROM user_management where login_id = '${client_name}' or user_email = '${official_email}'`);
    if (sql_stat.length > 0) {
      // Failed [Inactive or Not Approved User] - call_index_signin Sign in function
      logger_all.info(" : [signup] Failed - Client Name / Email already used. Kindly try with some others!!");
      return {
        response_code: 0,
        response_status: 201,
        response_msg: "Client Name / Email already used. Kindly try with some others!!"
      };
    } else {
      // Otherwise the process was continued.to check the user type
      //var explod1 = user_type.split("~~");
var parent_id = 1;
      switch (user_type) {
        case "2": // Super Admin
        // var explod1 = user_type.split("~~");
        //parent_id = explod1[0];
          user_type = 2;
          parent_id = 1;
          break;
        case "3": // Dept Admin
          // var explod1 = user_type.split("~~");
          parent_id = explod1[0];
          user_type = 3;
          break;
        case "4": // Agent
          // var explod2 = user_type.split("~~");
          parent_id = explod2[0];
          user_type = 4;
          break;
        default:
          // default
          break;
      }
      // To generate the random characters in the apikey
      const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      let length = 32;
      let result = ' ';
      const charactersLength = characters.length;
      // loop the length To create the random number in the apikey
      for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }

      string = result.substring(0, 15);
      apikey = string.toUpperCase();
      // To insert the user_management table from the request values
      logger_all.info(" [signup - insert query request] : " + `INSERT INTO user_management VALUES (NULL, '${user_type}', '${parent_id}', '${client_name}', '${login_shortname}', '${apikey}', '${client_name}', '${user_password}', '${official_email}', '${official_mobile_no}','${reg_official_address_cpy}', NULL, NULL, NULL, '${user_permission}', 'N', CURRENT_TIMESTAMP,'-')`);

      const insert_template = await db.query(`INSERT INTO user_management VALUES (NULL, '${user_type}', '${parent_id}', '${client_name}', '${login_shortname}', '${apikey}', '${client_name}', '${user_password}', '${official_email}', '${official_mobile_no}','${reg_official_address_cpy}', NULL, NULL, NULL, '${user_permission}', 'N', CURRENT_TIMESTAMP,'-')`);

      logger_all.info(" [signup - insert query response] : " + JSON.stringify(insert_template))
      var lastid = insert_template.insertId;
      // user Details insert values
      var insert_query =  `INSERT INTO user_details (user_details_id, user_id, cont_person, cont_designation, cont_mobile_no, cont_email, billing_address, company_name, company_website, parent_company_name, company_display_name, description_business, profile_image, business_category, sender,sender2, sender_1, sender_2, message_type, opt_process, enquiry_approval, privacy_terms, terms_conditions, document_proof, proof_doc_name, volume_day_expected, updated_date) 
      VALUES (NULL, '${lastid}','${cont_person}', '${cont_designation}', '${cont_person_mobileno}', '${cont_email}', '${billing_address}','${company_name}', '${company_website}', '${parent_company_name}', '${company_display_name}','${description_business}', '${profile_image}', '${business_category}', '${sender}', '${sender_txt_2}', '${sender1}', '${sender2}', '${message_type}', '${otp_process}','${enquiry_approval}', '${privacy_terms}', '${terms_conditions}','${proof_doc_name}', '${document_proof}', '${volume_day_expected}',CURRENT_TIMESTAMP)`
      logger_all.info(" [signup - insert query request] : " + insert_query );


      const insert_user_details = await db.query(insert_query);
      logger_all.info(" [signup - insert query response] : " + JSON.stringify(insert_user_details))

      // To create the new DB for the sign the new user --
      let exp_result1 = await db.query(`CREATE DATABASE IF NOT EXISTS rcs_${lastid} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci`);
      logger_all.info(" [signup - create DB response] : " + JSON.stringify(exp_result1))
      var db_name = `rcs_${lastid}`;
      // The process is continued.To create the new table in the new database
      let exp_result2 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS compose_rcs_${lastid} (
        compose_rcs_id int NOT NULL,
        user_id int NOT NULL,
        store_id int NOT NULL,
        whatspp_config_id int NOT NULL,
        mobile_nos longblob NOT NULL,
        sender_mobile_nos longblob NOT NULL,
        rcs_content varchar(1000) NOT NULL,
        message_type varchar(50) NOT NULL,
        total_mobileno_count int DEFAULT NULL,
        content_char_count int NOT NULL,
        content_message_count int NOT NULL,
        campaign_name varchar(30) DEFAULT NULL,
        rcs_status char(1) NOT NULL,
        rcs_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8`, null, `${db_name}`);
      logger_all.info(` [signup - create table compose_rcs_${lastid} response] : ` + JSON.stringify(exp_result2))
      // The process is continued.To create the new table in the new database
      let exp_result3 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS compose_rcs_status_${lastid} (
        comwtap_status_id int NOT NULL,
        compose_rcs_id int NOT NULL,
        country_code int DEFAULT NULL,
        mobile_no varchar(13) NOT NULL,
        comments varchar(100) NOT NULL,
        comwtap_status char(1) NOT NULL,
        comwtap_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        response_status char(1) DEFAULT NULL,
        response_message varchar(100) DEFAULT NULL,
        response_id varchar(100) DEFAULT NULL,
        response_date timestamp NULL DEFAULT '0000-00-00 00:00:00',
        delivery_status char(1) DEFAULT NULL,
        delivery_date timestamp NULL DEFAULT NULL,
        read_date timestamp NULL DEFAULT NULL,
        read_status char(1) DEFAULT NULL
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8`, null, `${db_name}`);
      logger_all.info(" [signup - create table compose_rcs_status_${lastid} response] : " + JSON.stringify(exp_result3))
      // The process is continued.To create the new table in the new database
      let exp_result4 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS rcs_text_${lastid} (
        rcs_text_id int NOT NULL,
        compose_rcs_id int NOT NULL,
        sms_type varchar(50) NOT NULL,
        rcs_text_title varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        text_data varchar(2000) DEFAULT NULL,
        text_reply varchar(50) DEFAULT NULL,
        text_number varchar(13) DEFAULT NULL,
        text_address varchar(100) DEFAULT NULL,
        text_name varchar(30) DEFAULT NULL,
        text_url varchar(100) DEFAULT NULL,
        text_title varchar(200) DEFAULT NULL,
        text_description varchar(200) DEFAULT NULL,
        text_start_time timestamp NULL DEFAULT NULL,
        text_end_time timestamp NULL DEFAULT NULL,
        carousel_fileurl varchar(100) DEFAULT NULL,
        carousel_srno int DEFAULT NULL,
        rcs_text_status char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        rcs_text_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT`, null, `${db_name}`);
      logger_all.info(` [signup - create table rcs_text_${lastid} response] : ` + JSON.stringify(exp_result4))
      // The process is continued.To create the new table in the new database
      let exp_result5 = await dynamic_db.query(`ALTER TABLE compose_rcs_${lastid} ADD PRIMARY KEY (compose_rcs_id),ADD KEY user_id (user_id,store_id,whatspp_config_id)`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_${lastid} response] : ` + JSON.stringify(exp_result5))
      // The process is continued.To create the new table in the new database
      let exp_result6 = await dynamic_db.query(`ALTER TABLE compose_rcs_status_${lastid} ADD PRIMARY KEY (comwtap_status_id), ADD KEY compose_rcs_id (compose_rcs_id)`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_status_${lastid} response] : ` + JSON.stringify(exp_result6))
      // The process is continued.To create the new table in the new database
      let exp_result7 = await dynamic_db.query(`ALTER TABLE rcs_text_${lastid} ADD PRIMARY KEY (rcs_text_id), ADD KEY compose_rcs_id (compose_rcs_id)`, null, `${db_name}`);
      logger_all.info(` [signup - alter table rcs_text_${lastid} response] : ` + JSON.stringify(exp_result7))
      // The process is continued.To create the new table in the new database
      let exp_result8 = await dynamic_db.query(`ALTER TABLE compose_rcs_${lastid} MODIFY compose_rcs_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_${lastid} response] : ` + JSON.stringify(exp_result8))
      // The process is continued.To create the new table in the new database
      let exp_result9 = await dynamic_db.query(`ALTER TABLE compose_rcs_status_${lastid} MODIFY comwtap_status_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1`, null, `${db_name}`);
      // The process is continued.To create the new table in the new database
      logger_all.info(` [signup - alter table compose_rcs_status_${lastid} response] : ` + JSON.stringify(exp_result9))
      // The process is continued.To create the new table in the new database
      let exp_result10 = await dynamic_db.query(`ALTER TABLE rcs_text_${lastid} MODIFY rcs_text_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1`, null, `${db_name}`);
      logger_all.info(` [signup - alter table rcs_text_${lastid} response] : ` + JSON.stringify(exp_result10))
      // The process is continued.To create the new table in the new database
      let exp_result11 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS compose_rcs_tmpl_${lastid} (
        compose_rcs_id int NOT NULL,
        user_id int NOT NULL,
        store_id int NOT NULL,
        whatspp_config_id int NOT NULL,
        mobile_nos longblob NOT NULL,
        sender_mobile_nos longblob NOT NULL,
        rcs_content varchar(1000) NOT NULL,
        message_type varchar(50) NOT NULL,
        total_mobileno_count int DEFAULT NULL,
        content_char_count int NOT NULL,
        content_message_count int NOT NULL,
        campaign_name varchar(30) DEFAULT NULL,
        mobile_no_type varchar(1) DEFAULT NULL,
        unique_template_id varchar(30) DEFAULT NULL,
        rcs_status char(1) NOT NULL,
        rcs_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8`, null, `${db_name}`);
      logger_all.info(` [signup - create table compose_rcs_tmpl_${lastid} response] : ` + JSON.stringify(exp_result11))
      // The process is continued.To create the new table in the new database
      let exp_result12 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS compose_rcs_status_tmpl_${lastid} (
        comwtap_status_id int NOT NULL,
        compose_rcs_id int NOT NULL,
        country_code int DEFAULT NULL,
        mobile_no varchar(13) NOT NULL,
        comments varchar(100) NOT NULL,
        comwtap_status char(1) NOT NULL,
        comwtap_entry_date timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
        response_status char(1) DEFAULT NULL,
        response_message varchar(100) DEFAULT NULL,
        response_id varchar(100) DEFAULT NULL,
        response_date timestamp NULL DEFAULT '0000-00-00 00:00:00',
        delivery_status char(1) DEFAULT NULL,
        delivery_date timestamp NULL DEFAULT NULL,
        read_date timestamp NULL DEFAULT NULL,
        read_status char(1) DEFAULT NULL
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8`, null, `${db_name}`);
      logger_all.info(` [signup - create table compose_rcs_status_tmpl_${lastid} response] : ` + JSON.stringify(exp_result12))
      // The process is continued.To create the new table in the new database
      let exp_result13 = await dynamic_db.query(`CREATE TABLE IF NOT EXISTS rcs_text_tmpl_${lastid} (
        compose_rcs_msgid int(11) NOT NULL,
        compose_rcs_id int(11) NOT NULL,
        whatspp_template varchar(600) NOT NULL,
        rcs_tmpl_category varchar(50) NOT NULL,
        rcs_tmpl_name varchar(500) NOT NULL,
        rcs_tmpl_language varchar(20) NOT NULL,
        rcs_tmpl_hdtext varchar(60) DEFAULT NULL,
        rcs_tmpl_body varchar(2000) NOT NULL,
        rcs_tmpl_footer varchar(60) DEFAULT NULL,
        rcs_tmpl_status char(1) NOT NULL,
        rcs_tmpl_entrydate datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT`, null, `${db_name}`);
      logger_all.info(` [signup - create table rcs_text_tmpl_${lastid} response] : ` + JSON.stringify(exp_result13))
      // The process is continued.To create the new table in the new database
      let exp_result14 = await dynamic_db.query(`ALTER TABLE compose_rcs_tmpl_${lastid} ADD PRIMARY KEY (compose_rcs_id), ADD KEY user_id (user_id,store_id,whatspp_config_id)`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_tmpl_${lastid} response] : ` + JSON.stringify(exp_result14))
      // The process is continued.To assign the primary key value in the new tables
      let exp_result15 = await dynamic_db.query(`ALTER TABLE compose_rcs_status_tmpl_${lastid} ADD PRIMARY KEY (comwtap_status_id), ADD KEY compose_rcs_id (compose_rcs_id)`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_status_tmpl_${lastid} response] : ` + JSON.stringify(exp_result15))
      // The process is continued.To assign the primary key value in the new tables
      let exp_result16 = await dynamic_db.query(`ALTER TABLE rcs_text_tmpl_${lastid} ADD PRIMARY KEY (compose_rcs_msgid), ADD KEY compose_rcs_id (compose_rcs_id)`, null, `${db_name}`);
      logger_all.info(` [signup - alter table rcs_text_tmpl_${lastid} response] : ` + JSON.stringify(exp_result16))
      // The process is continued.To MODIFY the AUTO_INCREMENT value in the new tables
      let exp_result17 = await dynamic_db.query(`ALTER TABLE compose_rcs_tmpl_${lastid} MODIFY compose_rcs_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_tmpl_${lastid} response] : ` + JSON.stringify(exp_result17))
      // The process is continued.To MODIFY the AUTO_INCREMENT value in the new tables
      let exp_result18 = await dynamic_db.query(`ALTER TABLE compose_rcs_status_tmpl_${lastid} MODIFY comwtap_status_id int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1`, null, `${db_name}`);
      logger_all.info(` [signup - alter table compose_rcs_status_tmpl_${lastid} response] : ` + JSON.stringify(exp_result18))
      // The process is continued.To MODIFY the AUTO_INCREMENT value in the new tables
      let exp_result19 = await dynamic_db.query(`ALTER TABLE rcs_text_tmpl_${lastid} MODIFY compose_rcs_msgid int NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1`, null, `${db_name}`);
      logger_all.info(` [signup - alter table rcs_text_tmpl_${lastid} response] : ` + JSON.stringify(exp_result19))
      // To insert the message_limit values
      logger_all.info(" [signup message_limit - insert query request] : " + `INSERT INTO message_limit VALUES (NULL, '${lastid}', 0, 0, 'Y', CURRENT_TIMESTAMP, NULL)`)
      const insert_msglimit = await db.query(`INSERT INTO message_limit VALUES (NULL, '${lastid}', 0, 0, 'Y', CURRENT_TIMESTAMP, NULL)`);
      logger_all.info(` [signup message_limit - insert query response] : ` + JSON.stringify(insert_msglimit))
      // insert_msglimit length is '0' to through the no data available message.

   if (insert_msglimit) {
        return {
          response_code: 1,
          response_status: 200,
          num_of_rows: 1,
          response_msg: 'Success'
        }
      } 
    }
  } catch (err) {
    // any error occurres send error response to client
    logger_all.info(" : [signup] Failed - " + err);
    return {
      response_code: 0,
      response_status: 201,
      response_msg: err.message
    };
  }
}
//On boarding signup Function - end


// using for module exporting
module.exports = {
  login,
  api_login,
  Signup,
   EditUser,
  onboardingSignup
};

