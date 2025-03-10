/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This API is used in report functions which is used to get summary and detailed report data from rcs and SMS.
This page also have report generation for rcs, SMS and RCS

Version : 1.0
Author : Sabena yasmin (YJ0008)
Date : 30-Sep-2023
*/
// Import necessary modules and dependencies
const db = require("../../db_connect/connect");
require("dotenv").config();
const main = require('../../logger')
const json2csv = require('json2csv');
var axios = require('axios');
const fs = require('fs');
// Start function to create a summary report
async function SummaryReport(req) {
    try {
        var logger_all = main.logger_all
        var logger = main.logger
        logger_all.info(" [summary report] - " + req.body);

        logger.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers));

        //  Get all the req header data
        const header_token = req.headers['authorization'];

        // get current Date and time
        var day = new Date();
        var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();

        // get all the req filter data
        let filter_date = req.body.filter_date;
        var user_id = req.body.user_id;
        var report_query = '';
        var getsummary;
        var get_summary_report;
        var filter_date_1;
        // To initialize a variable with an empty string value

        // declare the array
        var total_response = [];
        // Query parameters
        logger_all.info("[Otp summary report query parameters] : " + JSON.stringify(req.body));

        var filter_condition = ` `;


        // To initialize a variable with an empty string value
        get_summary_report = ``;
        if (filter_date) {
            // date function for looping in one by one date
            filter_date_1 = filter_date.split("-");
            report_query = `SELECT wht.user_id, usr.user_name,ussr.user_type,wht.campaign_name,wht.campaign_id, DATE_FORMAT(wht.com_entry_date, '%d-%m-%Y') AS entry_date,wht.total_msg,(CASE WHEN wht.report_status = 'Y' THEN wht.total_waiting ELSE 0 END) AS total_waiting,(CASE WHEN wht.report_status = 'Y' THEN wht.total_process ELSE 0 END) AS total_process,(CASE WHEN wht.report_status = 'Y' THEN wht.total_success ELSE 0 END) AS total_success,(CASE WHEN wht.report_status = 'Y' THEN wht.total_failed ELSE 0 END) AS total_failed,(CASE WHEN wht.report_status = 'Y' THEN wht.total_delivered ELSE 0 END) AS total_delivered, (CASE WHEN wht.report_status = 'Y' THEN wht.total_read ELSE 0 END) AS total_read FROM rcs.user_summary_report wht LEFT JOIN rcs.user_management usr ON wht.user_id = usr.user_id LEFT JOIN rcs.user_master ussr ON usr.user_master_id = ussr.user_master_id WHERE (usr.user_id = '${user_id}' or usr.parent_id in (${user_id})) AND (DATE(wht.com_entry_date) BETWEEN '${filter_date_1[0]}' AND '${filter_date_1[1]}') and wht.report_status = 'Y' ${filter_condition} group by campaign_name order by wht.com_entry_date desc `;

            logger_all.info('[select query request] : ' + report_query);
            getsummary = await db.query(report_query, null, `rcs_${user_id}`);
            logger.info("[API SUCCESS RESPONSE - Total response] : " + JSON.stringify(getsummary));

        } //filter date condition

        // getsummary length is '0'.to send the Success message and to send the total_response datas.
        logger.info("[API SUCCESS RESPONSE - Total response] : " + JSON.stringify(total_response));
        if (getsummary == 0) {
            logger.info("[API SUCCESS RESPONSE - Total response] : " + JSON.stringify({
                response_code: 1,
                response_status: 201,
                response_msg: 'No data available',
            }))
            return {
                response_code: 1,
                response_status: 201,
                response_msg: 'No data available',
            };
        } else { //otherwise to send the success message and get summarydetails
            logger.info("[API SUCCESS RESPONSE - get summary] : " + JSON.stringify({
                response_code: 1,
                response_status: 200,
                response_msg: 'Success',
                report: getsummary

            }))
            return {
                response_code: 1,
                response_status: 200,
                response_msg: 'Success',
                report: getsummary
            };
        }
    } catch (e) { // any error occurres send error response to client
        logger_all.info("[summary report - error] : " + e)

        logger.info("[Failed response - Error occured] : " + JSON.stringify({
            response_code: 0,
            response_status: 201,
            response_msg: 'Error occured'
        }))
        return {
            response_code: 0,
            response_status: 201,
            response_msg: 'Error occured'
        };
    }
}
// End function to create a summary report

module.exports = {
    SummaryReport,
};
