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
// approveTemplateList Function - start
async function approveTemplateList(req) {
    var logger_all = main.logger_all
    var logger = main.logger
    try {
        //  Get all the req header data
        const header_token = req.headers['authorization'];
        // declare the variables
        var user_id, user_master_id, template_list;
        // query parameters
        logger_all.info("[approveTemplateList query parameters] : " + JSON.stringify(req.body));
        // To get the User_id
        var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
        if (req.body.user_id) {
            get_user = get_user + `and user_id = '${req.body.user_id}' `;
        }
        logger_all.info("[select query request] : " + get_user);

        const get_user_id = await db.query(get_user);
        logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
        // If get_user not available send error response to client
        if (get_user_id.length == 0) {
            logger_all.info("Invalid Token")
            return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
        }
        else {// otherwise to get the user details
            user_id = get_user_id[0].user_id;
            user_master_id = get_user_id[0].user_master_id;
        }

        if (user_master_id == 1 && user_id == 1) { // primary admin are following this to use in the condition
            // to get_approve_rcs_no_api using
            template_list = `SELECT * from message_template mt left join user_management usr on mt.created_user = usr.user_id left join master_language ml on ml.language_id = mt.language_id where mt.template_status = 'N' order by template_entdate desc`;

        } else if (user_master_id == 1) {

            // to get_approve_rcs_no_api using
            template_list = `SELECT * from message_template mt left join user_management usr on mt.created_user = usr.user_id left join master_language ml on ml.language_id = mt.language_id where mt.template_status = 'N' and usr.parent_id = '${user_id}' order by template_entdate desc`;
        }
        logger_all.info("[select query request] : " + template_list);
        const get_approve_rcs_no_api = await db.query(template_list);

        logger_all.info("[select query response] : " + JSON.stringify(get_approve_rcs_no_api))
        // get_approve_rcs_no_api length is '0' to through the no data available message. 
        if (get_approve_rcs_no_api.length == 0) {
            return { response_code: 1, response_status: 204, response_msg: 'No data available' };
        }
        else { // otherwise get_approve_rcs_no_api to get the success message anag get_approve_rcs_no_api length and get_approve_rcs_no_api details
            return { response_code: 1, response_status: 200, num_of_rows: get_approve_rcs_no_api.length, response_msg: 'Success', report: get_approve_rcs_no_api };
        }

    }
    catch (e) { // any error occurres send error response to client
        logger_all.info("[approveTemplateList failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
    }
}
// approveTemplateList Function - end
// using for module exporting
module.exports = {
    approveTemplateList
}
