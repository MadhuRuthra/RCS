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
const fs = require('fs');
const csvParser = require('csv-parser');
const moment = require('moment');
const cronfolder = require("./../cron/route");

// ManualReportPJ - start
async function ManualReportPJ(req) {
  var logger_all = main.logger_all
  var logger = main.logger

  try {
    const admin_user_id = req.body.user_id;
    const user_id = req.body.compose_user_id;
    const compose_id = req.body.compose_id;
    const file = req.body.file;
    const reportType = req.body.reportType;
    const upload_info = req.body.upload_info;

    // query parameters
    logger_all.info("[ManualReportPJ - query parameters] : " + JSON.stringify(req.body));

    logger_all.info(reportType);
    logger_all.info(user_id);
    logger_all.info(compose_id);
    logger_all.info(file);

    logger_all.info("CSV file process started")
    const csvFilePath = "/var/www/html/rcs_portal/uploads/pj_report_file/" + file;

    var select_query = `SELECT * from rcs_${user_id}.compose_rcs_tmpl_${user_id} WHERE rcs_status = 'P' AND compose_rcs_id = '${compose_id}'`
    logger_all.info(select_query);

    var data = await db.query(select_query);

    if (data.length == 0) {

      return { response_code: 0, response_status: 201, response_msg: 'Report generation in progress.' };

    } else if (data.length > 0) {

      var insert_queue = `INSERT INTO queue_process VALUES (NULL,'${user_id}','${compose_id}','${csvFilePath}','${reportType}','Y',NULL,CURRENT_TIMESTAMP)`;

      var insert_queue_result = await db.query(insert_queue);

      logger_all.info(JSON.stringify(insert_queue_result));

      var update_wht_report = await db.query("UPDATE rcs_" + user_id + ".compose_rcs_tmpl_" + user_id + " SET rcs_status = 'V' WHERE compose_rcs_id = ?", [compose_id]);

      logger_all.info(JSON.stringify(update_wht_report));

  var update_compose = await db.query(`UPDATE master_compose_rcs SET rcs_status = 'V',upload_info = '${upload_info}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_id, user_id]);

      logger_all.info(JSON.stringify(update_compose));

      // cron process
      cronfolder();
      logger_all.info(`Updated rcs_status to 'V' for compose ID ${compose_id}`);
      return { response_code: 1, response_status: 200, response_msg: 'Success' };

    }
  } catch (err) {// any error occurres send error response to client
    logger_all.info("[ManualReportPJ - failed response] : " + err)
    return {
      response_code: 0,
      response_status: 201,
      response_msg: 'Error occured'
    };
  }
}
// ManualReportPJ - end

// using for module exporting
module.exports = {
  ManualReportPJ
}
