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
// approveComposeMessage Function - start
const moment = require('moment');
const { createObjectCsvWriter } = require('csv-writer');
const fs = require('fs');
const env = process.env
const DB_NAME = env.DB_NAME;

async function generatereport(req, res) {
    var logger = main.logger

    var logger_all = main.logger_all;

    const user_id = req.body.compose_user_id;
    const campaign_id = req.body.compose_id;
    logger_all.info(user_id);
    logger_all.info(campaign_id);
    const currentDate = new Date();

    // Format date and time without spaces

    logger_all.info('CSV file processing started.');
    // Use a stream to read the CSV file
    var data = [];
    var csvFilePath;
    // var select_query = `SELECT usr.user_name,cmm.rcs_entry_date,cmm.campaign_name,cmm.campaign_id as compose_rcs_id,tmp.templateid as template_id,cmm.rcs_entry_date,cmp.mobile_no,cmp.delivery_status,cmp.delivery_date from rcs_${user_id}.compose_rcs_status_tmpl_${user_id} cmp
    // LEFT JOIN rcs_${user_id}.compose_rcs_tmpl_${user_id} cmm ON cmm.compose_rcs_id = cmp.compose_rcs_id LEFT JOIN rcs.message_template tmp ON tmp.unique_template_id = cmm.unique_template_id LEFT JOIN rcs.user_management usr ON usr.user_id = cmm.user_id 
    // WHERE cmp.compose_rcs_id = '${campaign_id}' order by rcs_entry_date;`

    var select_query = `SELECT 
    usr.user_name,
    cmm.campaign_name AS campaign_name,
    tmp.templateid AS template_id,
    cmp.mobile_no,
    CASE 
        WHEN cmp.response_status = 'F' THEN 'Failure'
        ELSE 'SUCCESS'
    END AS response_status,
    cmp.response_date,
    CASE 
        WHEN cmp.response_status = 'F' THEN NULL
        ELSE CASE 
            WHEN cmp.delivery_status IS NULL THEN 'Not Delivered'
            ELSE 'Delivered'
        END
    END AS delivery_status,
    CASE 
        WHEN cmp.response_status = 'F' THEN NULL
        ELSE cmp.delivery_date
    END AS delivery_date
FROM 
    rcs_${user_id}.compose_rcs_status_tmpl_${user_id} cmp
    LEFT JOIN rcs_${user_id}.compose_rcs_tmp_${user_id} cmm ON cmm.compose_rcs_id = cmp.compose_rcs_id 
    LEFT JOIN rcs.message_template tmp ON tmp.unique_template_id = cmm.unique_template_id 
    LEFT JOIN rcs.user_management usr ON usr.user_id = cmm.user_id 
WHERE 
    cmp.compose_rcs_id = '${campaign_id}' 
ORDER BY 
    cmm.rcs_entry_date;`
    logger_all.info(select_query);
    var report_data = await db.query(select_query);

    if (report_data.length == 0) {
        return { response_code: 0, response_status: 201, response_msg: 'No data available.' };
    }
console.log(report_data[0])
    var campaign_name_file = report_data[0].campaign_name.replaceAll("\n", "");
    var csvFilePath = `/var/www/html/rcs/uploads/pj_report_file/${campaign_name_file}.csv`
	// csvFilePath = `/var/www/html/rcs/uploads/compose_variables/${csvFilePath}`
    for (var i = 0; i < report_data.length; i++) {
        var single_data = report_data[i]
        logger_all.info(single_data)

        // Format the datetime to yyyy-mm-dd
        var formattedDateTime = moment(single_data.response_date).format('YYYY-MM-DD HH:mm:ss');

        formattedDateTime = formattedDateTime == 'Invalid date' ? "" : formattedDateTime;

        // Format the datetime to yyyy-mm-dd hh:ii:ss
        var formattedDelDateTime =moment(single_data.delivery_date).format('YYYY-MM-DD HH:mm:ss');
        formattedDelDateTime =formattedDelDateTime == 'Invalid date' ? "" : formattedDelDateTime;
        

        // data.push(
        //     { date: only_date, user_name: single_data.user_name, campaign_name: campaign_name, campaign_id: single_data.compose_rcs_id.replaceAll("\n", ""), template_id: single_data.template_id, mobile_no: single_data.mobile_no, delivery_time: formattedDateTime, delivery_status: single_data.delivery_status }
        // )
        data.push(
            {
                user_name: single_data.user_name,  
                rcs_campaign: single_data.campaign_name, 
                template_id: single_data.template_id, 
                mobile_no: single_data.mobile_no,  
                response_status: single_data.response_status, 
                response_date: formattedDateTime,  
                delivery_status: single_data.delivery_status,  // Delivery status (Delivered/Not Delivered/NULL)
                delivery_date: formattedDelDateTime  // Delivery date (or NULL if response_status is 'F')
            }
        );
        console.log(data)
    }


    // Define CSV file headers
    const csvWriter = createObjectCsvWriter({
        path: csvFilePath,  // File path to save the CSV
        header: [
            { id: 'user_name', title: 'User Name' },
            { id: 'rcs_campaign', title: 'Campaign Name' },
            { id: 'template_id', title: 'Template ID' },
            { id: 'mobile_no', title: 'Mobile No' },
            { id: 'response_status', title: 'Response Status' },
            { id: 'response_date', title: 'Response Date' },
            { id: 'delivery_status', title: 'Delivery Status' },
            { id: 'delivery_date', title: 'Delivery Date' }
        ]
    });
    // const csvWriter = createObjectCsvWriter({
    //     path: filename,
    //     header: [
    //         { id: 'date', title: 'Date' },
    //         { id: 'user_name', title: 'User name' },
    //         { id: 'campaign_name', title: 'Campaign name' },
    //         { id: 'campaign_id', title: 'Campaign ID' },
    //         { id: 'template_id', title: 'Template ID' },
    //         { id: 'mobile_no', title: 'Mobile no' },
    //         { id: 'delivery_time', title: 'Delivery date' },
    //         { id: 'delivery_status', title: 'Delivery status' }
    //     ]
    // });

   await csvWriter.writeRecords(data)
        .then(async () => {
            logger_all.info('CSV file written successfully - ' + csvFilePath);

            // var update_cam_status = ` UPDATE rcs_${user_id}.compose_rcs_tmpl_${user_id} SET rcs_status = 'S' WHERE compose_rcs_id = '${campaign_id}';`
            // logger.info(update_cam_status);
            // var update_cam_status_result = await db.query(update_cam_status);

            // var update_compose = await db.query("UPDATE master_compose_rcs " + "SET rcs_status = 'S' WHERE compose_rcs_id = ? and user_id = ?", [campaign_id, user_id]);

            // logger_all.info(JSON.stringify(update_compose));

            var update_cam_data = ` UPDATE rcs_${user_id}.compose_rcs_status_tmpl_${user_id} SET campaign_status = 'Y' WHERE compose_rcs_id = '${campaign_id}';`
            logger.info(update_cam_data);
            var update_cam_data_result = await db.query(update_cam_data);

            const update_status_sts = `UPDATE master_compose_rcs SET rcs_status = "Y" WHERE compose_rcs_id = '${campaign_id}' AND user_id = '${user_id}'AND rcs_status = "O"`;
            console.log(update_status_sts)
            logger_all.info(update_status_sts)
            const get_update_status_sts = await db.query(update_status_sts);
            logger_all.info("[select query response] : " + JSON.stringify(get_update_status_sts));

            const update_status_final = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "Y" WHERE compose_rcs_id = '${campaign_id}' AND rcs_status = "O"`;
            console.log(update_status_final)
            logger_all.info(update_status_final)
            const get_update_status_final = await db.query(update_status_final);
            logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final));

            const update_status_summary = `UPDATE user_summary_report SET generate_status = "Y" WHERE com_msg_id = '${campaign_id}' AND user_id = '${user_id}'`;
            console.log(update_status_summary)
            logger_all.info(update_status_summary)
            const get_update_status_summary = await db.query(update_status_summary);
            logger_all.info("[select query response] : " + JSON.stringify(get_update_status_summary));



            // var select_status_count = `SELECT COUNT(DISTINCT CASE WHEN delivery_status = 'SENT' THEN comwtap_status_id END) AS total_success,COUNT(DISTINCT CASE WHEN delivery_status = 'DELIVERED' THEN comwtap_status_id END) AS total_delivered, COUNT(DISTINCT CASE WHEN delivery_status IN ('READ', 'BLOCKED') THEN comwtap_status_id END) AS total_read,COUNT(DISTINCT CASE WHEN delivery_status IN ('INCAPABLE', 'NOT AVAILABLE', 'FAILED','UNAVAILABLE') THEN comwtap_status_id END) AS total_failed, COUNT(DISTINCT CASE WHEN delivery_status = 'INVALID' THEN comwtap_status_id END) AS total_invalid FROM rcs_${user_id}.compose_rcs_status_tmpl_${user_id} where compose_rcs_id = '${campaign_id}'`;

            // logger_all.info("[select_summary_report] : " + select_status_count);
            // var status_count_result = await db.query(select_status_count);
            // logger_all.info("[select_summary_report response] : " + JSON.stringify(status_count_result))

            // var failed_count = status_count_result[0].total_failed + status_count_result[0].total_invalid;
            // var success_count = status_count_result[0].total_success;
            // var read_count = status_count_result[0].total_read;
            // var delivery_count = status_count_result[0].total_delivered;

            // var update_summary = `UPDATE rcs.user_summary_report SET total_waiting = 0,total_process = 0,total_failed = ${failed_count},total_read = ${read_count},total_delivered = ${delivery_count},total_success = ${success_count},sum_end_date = CURRENT_TIMESTAMP,report_status = 'Y' WHERE com_msg_id = '${campaign_id}' and user_id = '${user_id}'`;

            // logger_all.info("[update_summary_report] : " + update_summary);
            // update_summary_results = await db.query(update_summary);
            // logger_all.info("[update_summary_report response] : " + JSON.stringify(update_summary_results))


            return { response_code: 1, response_status: 200, response_msg: 'Success.' };

        })
        .catch((error) => {
            console.error('Error writing CSV file:', error);
            return { response_code: 0, response_status: 201, response_msg: 'Error Occurred.' };
        });
}
// using for module exporting
module.exports = {
    generatereport
}
