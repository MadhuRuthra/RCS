/*
This api has dashboard API functions which is used to routing the dashboard.
This page is used to create the url for dashboard API functions .
It will be used to run the dashboard process to check and the connect database to get the response for this function.
After get the response from API, send it back to callfunctions.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const express = require("express");
const router = express.Router();
require("dotenv").config();
const fs = require('fs');
const csvParser = require('csv-parser');
const moment = require('moment');
const main = require('../../logger');
// Define the function containing the logic you want to run periodically
async function cronfolder() {

  var logger_all = main.logger_all
  var logger = main.logger

  try {

    /*var queue_check = `SELECT * FROM queue_process where status = 'P'`;
    var queue_check_result = await db.query(queue_check);
    logger_all.info("************88")
    logger_all.info(queue_check_result);

    if (queue_check_result.length > 0) {
      // throw new Error("No available data for queue process");
      return;
    }*/

 logger_all.info("Cron process coming .....");


    var total_mobilenos = []
    var select_queue = `SELECT * FROM queue_process where status = 'Y' limit 1`;
    var select_queue_result = await db.query(select_queue);

    if (select_queue_result.length == 0) {
      logger_all.info("No available data for queue process");
      return;
    }

    logger_all.info(select_queue_result);
    var user_id = select_queue_result[0].user_id;
    var compose_id = select_queue_result[0].compose_message_id;
    var csvFilePath = select_queue_result[0].file_path;
    var reportType = select_queue_result[0].report_type;
    var tgbase_count;

    var select_query = `SELECT * from rcs_${user_id}.compose_rcs_tmpl_${user_id} WHERE rcs_status = 'V' AND compose_rcs_id = '${compose_id}'  and user_id = '${user_id}'`
    logger_all.info(select_query);

    var data = await db.query(select_query);

    if (data.length == 0) {
      logger_all.info(`No available data for compose_rcs_tmpl Table For 'V' Status.`);
      // return;
    } else {

      var select_query = `SELECT * from rcs.master_compose_rcs WHERE rcs_status = 'V' AND compose_rcs_id = '${compose_id}'  and user_id = '${user_id}'`
      logger_all.info(select_query);

      var select_query_result = await db.query(select_query);

      tgbase_count = select_query_result[0].tgbase_count;

    }

    var update_queue = `Update queue_process set status = 'P' where compose_message_id = '${compose_id}' and user_id = '${user_id}'`;
    var update_queue_result = await db.query(update_queue);

    logger_all.info(JSON.stringify(update_queue_result));


    let count = 0;
    var invalid_date = '';
    var check_columns = '';
    var total_mobilenos = [];
    var delivery_date = [];
    var delivery_status = [];

    fs.createReadStream(csvFilePath)
      .pipe(csvParser())
      .on('data', async (row, index) => {

        // Skip the header row (assuming it's the first row)
        if (index === 0) return;

        // Extract data from the CSV row
        const mobileNumber = row['Receiver'];
        const deliveryStatusValue = row['Status'] == 1 ? "NOT AVAILABLE" : row['Status'];
        const parsedDate = moment(row['Delivery Date'], ['DD-MM-YYYY', 'YYYY-MM-DD']);
        const convertedDate = parsedDate.format('YYYY-MM-DD');
        const deliveryDate = `${convertedDate} ${row['Delivery Time']}`;

        // Validate and process data
        if (mobileNumber && typeof mobileNumber === 'string' && moment(deliveryDate, 'YYYY-MM-DD HH:mm:ss', true).isValid()) {

          total_mobilenos.push(mobileNumber);
          delivery_status.push(deliveryStatusValue.toUpperCase());
          delivery_date.push(deliveryDate);
          count++;

        } else if (!mobileNumber || !deliveryDate || !deliveryStatusValue) {

          logger_all.info("Check mobile numbers columns");
          check_columns++;
        } else {
          logger_all.info("Invalid data at index ${index}. Skipping..");
          invalid_date++;
        }

      })
      .on('end', async () => {
        file_processing();
      })
      .on('error', (error) => {
        console.error('Error reading CSV file:', error);
      });

    async function file_processing() {

      logger_all.info("File Processing coming !");
      logger_all.info("tgbase_count" + tgbase_count);
      logger_all.info(total_mobilenos.length + "total_mobilenos.length");
      if (check_columns) {

        invalid_date = "Mimatch column headings";
      } else if (invalid_date) {

        invalid_date = invalid_date + " Invalid delivery status or delivery date";

      } else if (tgbase_count > total_mobilenos.length) {

        invalid_date = tgbase_count - total_mobilenos.length + " Mobile number is missing";
        logger_all.info("coming Missing mobile numbers ");

      } else if (tgbase_count < total_mobilenos.length) {

        invalid_date = total_mobilenos.length - tgbase_count + " Mimatch mobile numbers count";
        logger_all.info("coming Mismatch Total mobile numbers ");

      }

      if (invalid_date) {
        logger_all.info("invalid_date coming ")
        var update_queue = `Update master_compose_rcs set remarks = '${invalid_date}', rcs_status = 'P'  where compose_rcs_id = '${compose_id}' and user_id = '${user_id}'`;
        logger_all.info(update_queue);
        var update_queue_result = await db.query(update_queue);
        logger_all.info(JSON.stringify(update_queue_result));

        var update_2 = await db.query("UPDATE rcs_" + user_id + ".compose_rcs_tmpl_" + user_id + " SET rcs_status = 'P' WHERE compose_rcs_id = ?", [compose_id]);
        logger_all.info(`Updated rcs_status to 'P' for compose ID ${compose_id}`);

      } else {

        logger_all.info("insert process ;")

        const db_name = `rcs_${user_id}`;
        const table_names = `rcs_${user_id}.compose_rcs_status_tmpl_${user_id}`;
        const insert_query = `INSERT INTO ${table_names} VALUES`;

        const batch_size = 1000;
        const batch_count = Math.ceil(total_mobilenos.length / batch_size);

        for (let batch = 0; batch < batch_count; batch++) {
          logger_all.info("coming;")
          const start_index = batch * batch_size;
          const end_index = Math.min((batch + 1) * batch_size, total_mobilenos.length);
          let batch_insert_query = insert_query;
          // Construct the insert query for the current batch
          for (let i = start_index; i < end_index; i++) {
            logger_all.info("inserted")
            batch_insert_query += `(NULL,'${compose_id}',NULL,'${total_mobilenos[i]}','TGBase','-','N',CURRENT_TIMESTAMP,NULL,NULL,NULL,NULL, '${delivery_status[i]}','${delivery_date[i]}',NULL,NULL,'N'),`;
          }
          // Remove the trailing comma if there are values in the batch_insert_query
          if (end_index > start_index) {
            batch_insert_query = batch_insert_query.substring(0, batch_insert_query.length - 1);
          }
          // Execute the insertion query for the current batch
          try {
            const insert_mobile_numbers = await db.query(batch_insert_query, null, db_name);
            logger_all.info(" [insert query response] : " + JSON.stringify(insert_mobile_numbers));
          } catch (error) {
            logger_all.info("Error inserting data:", error);
          }
        }


        var select_status_count = `SELECT COUNT(*) as totaltg_count FROM rcs_${user_id}.compose_rcs_status_tmpl_${user_id} where compose_rcs_id = '${compose_id}' and report_group = '${reportType}'`;
        logger_all.info("[select totaltg_count] : " + select_status_count);
        var status_count_result = await db.query(select_status_count);
        logger_all.info("[totaltg_count response] : " + JSON.stringify(status_count_result))

        var totaltg_count = status_count_result[0].totaltg_count;

        var select_compose = await db.query(`select * from rcs.master_compose_rcs WHERE compose_rcs_id = ? and user_id = ?`, [compose_id, user_id]);
        logger_all.info("[master_compose_rcs] : " + JSON.stringify(select_compose));
        var update_compose;

        if (select_compose[0].cgbase_count > 0) {

          var select_status_count = `SELECT COUNT(*) as total_count FROM rcs_${user_id}.compose_rcs_status_tmpl_${user_id} where compose_rcs_id = '${compose_id}'`;

          logger_all.info("[select totaltg_count] : " + select_status_count);
          var status_count_result = await db.query(select_status_count);
          logger_all.info("[totaltg_count response] : " + JSON.stringify(status_count_result))

          var total_count = status_count_result[0].total_count;


          logger_all.info(`UPDATE rcs.master_compose_rcs SET updated_tgbase = '${select_compose[0].tgbase_count}',total_updated_count = '${total_count}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_id, user_id]);

          update_compose = await db.query(`UPDATE rcs.master_compose_rcs SET updated_tgbase = '${select_compose[0].tgbase_count}',total_updated_count = '${total_count}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_id, user_id]);

        } else {

          logger_all.info(`UPDATE rcs.master_compose_rcs SET updated_tgbase = '${totaltg_count}',total_updated_count = '${totaltg_count}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_id, user_id]);

          update_compose = await db.query(`UPDATE rcs.master_compose_rcs SET updated_tgbase = '${totaltg_count}',total_updated_count = '${totaltg_count}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_id, user_id]);
        }
        logger_all.info("[master_compose_rcs] : " + JSON.stringify(update_compose));
      }
      // await sleep(120000)

      var update_queue = `Update queue_process set status = 'C' where compose_message_id = '${compose_id}' and user_id = '${user_id}'`;
      var update_queue_result = await db.query(update_queue);
      logger_all.info(JSON.stringify(update_queue_result));
      logger_all.info('CSV file processing completed.');

      await sleep(60000)

      cronfolder();

    }
  } catch (err) {// any error occurres send error response to client
    logger_all.info("[Cron Process - failed response] : " + err)
    console.error(err)
    return {
      response_code: 0,
      response_status: 201,
      response_msg: 'Error occured'
    };
  }
}
// manual_report_pj - end

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// Export the function so that it can be called from outside
module.exports = cronfolder;
