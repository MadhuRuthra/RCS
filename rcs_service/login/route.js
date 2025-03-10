/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the login route.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the Login functions page
const Login = require("./login");
// Import the default validation middleware
const validator = require('../validation/middleware')
// Import the validation page
const LoginValidation = require("../validation/login_validation");
const SignupValidation = require("../validation/signup_validation");
const main = require('../logger');
// login function - start
const db = require("../db_connect/connect");
const Onboarding_signupValidation = require("../validation/onboarding_signup_validation");
const EditUsersValidation = require("../validation/edituser_validation");


router.post(
  "/",
  validator.body(LoginValidation),
  async function (req, res, next) {
    try {// access the api_login function
      var logger = main.logger
var logger_all = main.logger_all;

var header_json = req.headers;
let ip_address = header_json['x-forwarded-for'];

const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
logger_all.info("[insert query request] : " + insert_api_log);
const insert_api_log_result = await db.query(insert_api_log);
logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))

const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
logger_all.info("[select query request] : " + check_req_id);
const check_req_id_result = await db.query(check_req_id);
logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));

if (check_req_id_result.length != 0) {

  logger_all.info("[failed response] : Request already processed");
  logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed' }))

  var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
  logger.silly("[update query request] : " + log_update);
  const log_update_result = await db.query(log_update);
  logger.silly("[update query response] : " + JSON.stringify(log_update_result))

  return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });

}
      var result = await Login.api_login(req);

      result['request_id'] = req.body.request_id;

      if (result.response_code == 0) {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }
      else {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }

      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) { // any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// login function - end
// signup function - start

router.post(
  "/signup",
  validator.body(SignupValidation),
  async function (req, res, next) {
    try { // access the api_login function
      var logger = main.logger
      var logger_all = main.logger_all;

      var header_json = req.headers;
      let ip_address = header_json['x-forwarded-for'];
    
      const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
      logger_all.info("[insert query request] : " + insert_api_log);
      const insert_api_log_result = await db.query(insert_api_log);
      logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))
    
      const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
      logger_all.info("[select query request] : " + check_req_id);
      const check_req_id_result = await db.query(check_req_id);
      logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));
    
      if (check_req_id_result.length != 0) {
    
        logger_all.info("[failed response] : Request already processed");
        logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed' }))
    
        var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
        logger.silly("[update query request] : " + log_update);
        const log_update_result = await db.query(log_update);
        logger.silly("[update query response] : " + JSON.stringify(log_update_result))
    
        return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });
    
      }

      var result = await Login.Signup(req);

      result['request_id'] = req.body.request_id;

      if (result.response_code == 0) {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }
      else {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }

      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) { // any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// signup function - end


// edit user function - start
router.post(
  "/edit_users",
  validator.body(EditUsersValidation),
  async function (req, res, next) {
    try { // access the api_login function
      var logger = main.logger
      var logger_all = main.logger_all;

      var result = await Login.EditUser(req);

      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) { // any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// edit user function - end


// login function - start
router.post(
  "/p_login",
  validator.body(LoginValidation),
  async function (req, res, next) {
    try { // access the api_login function
      var logger = main.logger
      var logger_all = main.logger_all;

      var header_json = req.headers;
      let ip_address = header_json['x-forwarded-for'];
    
      const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
      logger_all.info("[insert query request] : " + insert_api_log);
      const insert_api_log_result = await db.query(insert_api_log);
      logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))
    
      const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
      logger_all.info("[select query request] : " + check_req_id);
      const check_req_id_result = await db.query(check_req_id);
      logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));
    
      if (check_req_id_result.length != 0) {
    
        logger_all.info("[failed response] : Request already processed");
        logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed' }))
    
        var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
        logger.silly("[update query request] : " + log_update);
        const log_update_result = await db.query(log_update);
        logger.silly("[update query response] : " + JSON.stringify(log_update_result))
    
        return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });
    
      }

      var result = await Login.login(req);

      result['request_id'] = req.body.request_id;

      if (result.response_code == 0) {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }
      else {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }

      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);

// onboarding_signup function - start
router.post(
  "/onboarding_signup",
  validator.body(Onboarding_signupValidation),
  async function (req, res, next) {
    try { // access the api_login function
      var logger = main.logger

      var logger_all = main.logger_all;

      var header_json = req.headers;
      let ip_address = header_json['x-forwarded-for'];
    
      const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
      logger_all.info("[insert query request] : " + insert_api_log);
      const insert_api_log_result = await db.query(insert_api_log);
      logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))
    
      const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
      logger_all.info("[select query request] : " + check_req_id);
      const check_req_id_result = await db.query(check_req_id);
      logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));
    
      if (check_req_id_result.length != 0) {
    
        logger_all.info("[failed response] : Request already processed");
        logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed' }))
    
        var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
        logger.silly("[update query request] : " + log_update);
        const log_update_result = await db.query(log_update);
        logger.silly("[update query response] : " + JSON.stringify(log_update_result))
    
        return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });
    
      }

      var result = await Login.onboardingSignup(req);

      result['request_id'] = req.body.request_id;

      if (result.response_code == 0) {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = '${result.response_msg}' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }
      else {
        logger.silly("[update query request] : " + `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        const update_api_log = await db.query(`UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`);
        logger.silly("[update query response] : " + JSON.stringify(update_api_log))
      }

      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) { // any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// onboarding_signup function - end
// login function - end
module.exports = router;
