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

async function generatereportlist(req) {

    try {
        const header_token = req.headers['authorization'];
        var user_id = req.body.user_id;
        var user_ids = [];
        const userIdQuery = "SELECT user_id FROM rcs.user_management";
        const userIds = await db.query(userIdQuery);

        var report_generate_list = "";
        for (let i = 0; i < userIds.length; i++) {
            user_ids.push(userIds[i].user_id);
        }
        var array_list_userid_string = user_ids.join("','");


        report_generate_list = `SELECT c.campaign_name,tmp.template_category, c.total_mobileno_count, c.rcs_entry_date, u.user_name,c.compose_rcs_id,c.rcs_status, c.user_id,c.total_updated_count AS total_updated_count FROM master_compose_rcs AS c JOIN rcs.user_management AS u ON c.user_id = u.user_id JOIN message_template tmp ON tmp.templateid = c.template_id where c.user_id in ('${array_list_userid_string}') and c.rcs_status in ('O') ORDER BY rcs_entry_date DESC`;

        const get_approve_rcs_no_api = await db.query(report_generate_list);

        if (get_approve_rcs_no_api.length == 0) {
            return { response_code: 1, response_status: 204, response_msg: 'No data available' };
        } else {
            return { response_code: 1, response_status: 200, num_of_rows: get_approve_rcs_no_api.length, response_msg: 'Success', report: get_approve_rcs_no_api };
        }
    } catch (e) {
        console.error("Error occurred:", e);
        return { response_code: 0, response_status: 201, response_msg: 'Error occurred' };
    }
}

// approveComposeMessage Function - end
// using for module exporting
module.exports = {
    generatereportlist
}
