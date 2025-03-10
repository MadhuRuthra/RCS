const db = require("../../db_connect/connect");
const dynamic_db = require("../../db_connect/dynamic_connect");

const express = require("express");
require("dotenv").config();
const main = require('../../logger')
const router = express.Router();
require("dotenv").config();
const fs = require('fs');
const csvParser = require('csv-parser');
const moment = require('moment');

async function report_update(req) {
    var logger_all = main.logger_all
    var logger = main.logger

    try {
        var database = req.body.database;
        var table_name = req.body.table_name;
        var compose_rcs_id = req.body.compose_rcs_id;
        var report_group = req.body.report_group;
        var user_id = req.body.compose_user;

        logger_all.info("CG report generation")
        var status = ["BLOCKED", "DELIVERED", "INCAPABLE", "INVALID", "READ", "SENT", "NOT AVAILABLE", "FAILED"]
        var status_failure = ["BLOCKED", "INCAPABLE", "INVALID", "NOT AVAILABLE", "FAILED"]
        var status_success = ["DELIVERED", "SENT", "READ"]

        var select_date = `SELECT rcs_entry_date,campaign_name,campaign_id FROM ${database}.compose_rcs_tmpl_${user_id} WHERE compose_rcs_id = '${compose_rcs_id}'`
        logger_all.info("[Select query request] : " + select_date);
        var select_date_result = await db.query(select_date);
        logger_all.info("[Select query response] : " + JSON.stringify(select_date_result))
        // var limit_count = 500;
        var com_date = select_date_result[0].rcs_entry_date;
        var campaign_name = select_date_result[0].campaign_name;
        var campaign_id = select_date_result[0].campaign_id;


        var select_summary = `select * from rcs.user_summary_report WHERE com_msg_id = '${compose_rcs_id}' and campaign_name = '${campaign_name}' and campaign_id = '${campaign_id}'`;
        logger_all.info("[select_summary_report] : " + select_summary);
        select_summary_results = await db.query(select_summary);

        logger_all.info("[select_summary_report response] : " + JSON.stringify(select_summary_results))
        if (select_summary_results[0].generate_status == 'N') {

            var select_count = `SELECT * FROM master_compose_rcs WHERE compose_rcs_id = '${compose_rcs_id}' AND user_id = '${user_id}'`;

            logger_all.info("[Select query request] : " + select_count);
            var select_count_result = await db.query(select_count);

            logger_all.info("[Select query response] : " + JSON.stringify(select_count_result))

            var total_count_data = select_count_result[0].cgbase_count;
            var cg_base_file = select_count_result[0].cg_base;

            let count = 0;
            var invalid_date = '';
            var total_mobilenos = [];

            const csvFilePath = "/var/www/html/rcs_portal/uploads/report_csv_files/" + cg_base_file;
            logger_all.info(csvFilePath);
            fs.createReadStream(csvFilePath)
                .pipe(csvParser())
                .on('data', async (row, index) => {

                    // Skip the header row (assuming it's the first row)
                    if (index === 0) return;

                    // Extract data from the CSV row
                    const mobileNumber = row['contacts'];

                    // Validate and process data
                    if (mobileNumber) {
                        total_mobilenos.push(mobileNumber);
                        count++;

                    }
                })
                .on('end', async () => {
                    try {
                        const response = await file_processing();
                        // Return the response here
                    } catch (error) {
                        console.error('Error processing file:', error);
                    }
                })
                .on('error', (error) => {
                    console.error('Error reading CSV file:', error);
                });

            async function file_processing() {

                logger_all.info(total_mobilenos.length)
                logger_all.info(total_mobilenos)
                logger_all.info("File processing total_mobilenos");

                const randomMinutes = Math.floor(Math.random() * 6) + 30;
                const randomSeconds = Math.floor(Math.random() * 59) + 1;
                // Get current time in IST
                const ISTTime = new Date(com_date);
                ISTTime.setMinutes(ISTTime.getMinutes() + randomMinutes);
                ISTTime.setSeconds(randomSeconds);
                const currentTime = new Date(ISTTime.getTime() + (5.5 * 60 * 60 * 1000));
                var batch_size = 1000;
                var batch_count = Math.ceil(total_count_data / batch_size);

                // Generate a random time between current time and one hour ahead time in IST
                const randomTime = new Date(currentTime.getTime() + Math.floor(Math.random() * 3600000));
                const formattedRandomTime = randomTime.toISOString().slice(0, 19).replace('T', ' ');
                const insert_query = `INSERT INTO ${database}.${table_name} VALUES`;

                //Loop through the batches
                for (var batch = 0; batch < batch_count; batch++) {
                    logger_all.info("!!!!");
                    // Clear the insert query
                    var batch_insert_query = insert_query;
                    // Calculate the start and end indices for the current batch
                    var start_index = batch * batch_size;
                    var end_index = Math.min((batch + 1) * batch_size, total_count_data);
                    // Construct the insert query for the current batch
                    for (var i = start_index; i < end_index; i++) {
                        // Shuffle the statuses array for each mobile number
                        const shuffledStatuses = status_success.slice().sort(() => Math.random() - 0.5);
                        // Select a random status from the shuffled array
                        const randomStatus = shuffledStatuses[Math.floor(Math.random() * shuffledStatuses.length)];

                        batch_insert_query += `(NULL,'${compose_rcs_id}',NULL,'${total_mobilenos[i]}','CGBase','-','N',CURRENT_TIMESTAMP,NULL,NULL,NULL,NULL, '${randomStatus}','${formattedRandomTime}',NULL,NULL,'N'),`;
                    }
                    // Remove the trailing comma
                    batch_insert_query = batch_insert_query.substring(0, batch_insert_query.length - 1);
                    try {
                        //logger_all.info(batch_insert_query);
                        const insert_mobile_numbers = await db.query(batch_insert_query);
                        logger_all.info("[insert query response]: " + JSON.stringify(insert_mobile_numbers));
                    } catch (error) {
                        logger_all.error("Error inserting data:", error);
                    };
                }

                var select_status_count = `SELECT COUNT(*) as totalcg_count FROM rcs_${user_id}.compose_rcs_status_tmpl_${user_id} where compose_rcs_id = '${compose_rcs_id}' and report_group = '${report_group}'`;

                logger_all.info("[select totalcg_count] : " + select_status_count);
                var status_count_result = await db.query(select_status_count);
                logger_all.info("[totalcg_count response] : " + JSON.stringify(status_count_result))

                var totalcg_count = status_count_result[0].totalcg_count;

                var update_summary = `UPDATE rcs.user_summary_report SET generate_status = 'Y' WHERE com_msg_id = '${compose_rcs_id}' and user_id = '${user_id}'`;
                logger_all.info("[update_summary_report] : " + update_summary);
                update_summary_results = await db.query(update_summary);
                logger_all.info("[update_summary_report response] : " + JSON.stringify(update_summary_results))

                var select_compose = await db.query(`select * from rcs.master_compose_rcs WHERE compose_rcs_id = ? and user_id = ?`, [compose_rcs_id, user_id]);
                logger_all.info("[master_compose_rcs] : " + JSON.stringify(select_compose));
                var update_compose;


                if (select_compose[0].total_updated_count == select_compose[0].tgbase_count) {

                    var select_status_count = `SELECT COUNT(*) as total_count FROM rcs_${user_id}.compose_rcs_status_tmpl_${user_id} where compose_rcs_id = '${compose_rcs_id}'`;

                    logger_all.info("[select totaltg_count] : " + select_status_count);
                    var status_count_result = await db.query(select_status_count);
                    logger_all.info("[totaltg_count response] : " + JSON.stringify(status_count_result))

                    var total_count = status_count_result[0].total_count;

                    logger_all.info(`UPDATE rcs.master_compose_rcs SET updated_cgbase = '${totalcg_count}',total_updated_count = '${total_count}' WHERE compose_rcs_id = ? and user_id = ?`[compose_rcs_id, user_id]);

                    var update_compose = await db.query(`UPDATE rcs.master_compose_rcs SET updated_cgbase = '${totalcg_count}',total_updated_count = '${total_count}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_rcs_id, user_id]);

                } else {
                    logger_all.info("Coming Else");

                    logger_all.info(`UPDATE rcs.master_compose_rcs SET updated_cgbase = '${totalcg_count}' WHERE compose_rcs_id = ? and user_id = ?`[compose_rcs_id, user_id]);

                    var update_compose = await db.query(`UPDATE rcs.master_compose_rcs SET updated_cgbase = '${totalcg_count}' WHERE compose_rcs_id = ? and user_id = ?`, [compose_rcs_id, user_id]);
                }

                logger_all.info(JSON.stringify(update_compose));
            }

        }
        return { response_code: 1, response_status: 200, response_msg: 'Success' };
    }

    catch (err) {
        // Failed - call_index_signin Sign in function
        logger_all.info("[country list report] Failed - " + err);
        return { response_code: 0, response_status: 201, response_msg: 'Error Occurred.' };
    }
}

module.exports = {
    report_update
};
