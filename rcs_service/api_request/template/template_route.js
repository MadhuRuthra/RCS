/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the template.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the report functions page
const Template = require("./template_api");
const TemplateNumbers = require("./template_number_api");
const CreateTemplate = require("./create_template");
const deleteTemplate = require("./delete_template");
const getSingleTemplate = require("./get_single_template");
const single_template = require("./single_template");
// Import the validation page
const getTemplateValidation = require("../../validation/get_template_validation");
const getTemplateNumberValidation = require("../../validation/get_template_number_validation");
const createTemplateValidation = require("../../validation/template_approval_validation");
const single_templatevalidation = require("../../validation/single_templatevalidation");
const deleteTemplateValidation = require("../../validation/delete_template_validation");
const getSingleTemplateValidation = require("../../validation/get_single_template_validation")
const ApproveRejectTemplateValidation = require("../../validation/approve_reject_template")
// Import the default validation middleware
const validator = require('../../validation/middleware')
const valid_user = require("../../validation/valid_user_middleware");
const main = require('../../logger');
const db = require("../../db_connect/connect");

// get_template - start
router.post(
  "/get_template",
  validator.body(getTemplateValidation),
  valid_user,
  async function (req, res, next) {
    try {// access the getTemplate function
      var logger = main.logger
      var result = await Template.getTemplate(req);
      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// get_template - end
// get_template_numbers - start
router.post(
  "/get_template_numbers",
  validator.body(getTemplateNumberValidation),
  valid_user,
  async function (req, res, next) {
    try {// access the CampaignReport function
      var logger = main.logger
      var result = await TemplateNumbers.getTemplateNumber(req);
      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// get_template_numbers - end
// p_get_template_numbers - start
router.post(
  "/p_get_template_numbers",
  validator.body(getTemplateNumberValidation),
  valid_user,
  async function (req, res, next) {
    try {// access the CampaignReport function
      var logger = main.logger
      var result = await TemplateNumbers.PgetTemplateNumber(req);
      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// p_get_template_numbers - end
// get_variable_count - start
router.post(
  "/get_variable_count",
  validator.body(getTemplateNumberValidation),
  valid_user,
  async function (req, res, next) {
    try {// access the CampaignReport function
      var logger = main.logger
      var result = await TemplateNumbers.getVariableCount(req);
      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// get_variable_count - end
// delete_template - start
router.delete(
  "/delete_template",
  validator.body(deleteTemplateValidation),
  valid_user,
  async function (req, res, next) {
    try {// access the CampaignReport function
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

      var result = await deleteTemplate.deleteTemplate(req);

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
// delete_template - end
// get_single_template - start
router.post(
  "/get_single_template",
  validator.body(single_templatevalidation),
  valid_user,
  async function (req, res, next) {
    try {// access the CampaignReport function
      var logger = main.logger
      var result = await getSingleTemplate.getSingleTemplate(req);
      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// get_single_template - end

// approve_reject_template - start
router.post(
  "/approve_reject_template",
  validator.body(ApproveRejectTemplateValidation),
  valid_user,
  async function (req, res, next) {
    try {// access the CampaignReport function
      var logger = main.logger
      var result = await getSingleTemplate.ApproveRejectTemplate(req);
      logger.info("[API RESPONSE] " + JSON.stringify(result))
      res.json(result);
    } catch (err) {// any error occurres send error response to client
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);
// approve_reject_template - end

// get_single_template - start
// router.post(
//   "/single_template",
//   validator.body(getSingleTemplateValidation),
//   valid_user,
//   async function (req, res, next) {
//     try {// access the CampaignReport function
//       var logger = main.logger
//       var result = await single_template.getSingleTemplate(req);
//       logger.info("[API RESPONSE] " + JSON.stringify(result))
//       res.json(result);
//     } catch (err) {// any error occurres send error response to client
//       console.error(`Error while getting data`, err.message);
//       next(err);
//     }
//   }
// );
// get_single_template - end

module.exports = router;
