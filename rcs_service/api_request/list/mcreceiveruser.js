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
// MCReceiverUser - start
async function MCReceiverUser(req) {
    var logger_all = main.logger_all
    var logger = main.logger
    try {
        //  Get all the req header data
        const header_token = req.headers['authorization'];

        // get all the req data
        var user_id, user_master_id;
        var get_mc_receiver_user;
        // query parameters
        logger_all.info("[MCReceiverUser query parameters] : " + JSON.stringify(req.body));
        // To get the User_id
        var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
        if (req.body.user_id) {
            get_user = get_user + `and user_id = '${req.body.user_id}' `;
        }
        logger_all.info("[select query request] : " + get_user);
        const get_user_id = await db.query(get_user);
        logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
        if (get_user_id.length == 0) {// If get_user not available send error response to client in ivalid token
            logger_all.info("Invalid Token")
            return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
        }
        else { // otherwise to get the user details
            user_id = get_user_id[0].user_id;
            user_master_id = get_user_id[0].user_master_id;
        }

        if (user_master_id == 1) { //if the usermasterid is 1 to execute the get_mc_receiver_user query
            logger_all.info("[select query request] : " + `SELECT user_id, user_name, user_email, user_mobile, api_key FROM user_management where user_master_id in ( 2) and usr_mgt_status = 'Y'`);
            get_mc_receiver_user = await db.query(`SELECT user_id, user_name, user_email, user_mobile, api_key FROM user_management where user_master_id in ( 2) and usr_mgt_status = 'Y'`);
            logger_all.info("[select query response] : " + JSON.stringify(get_mc_receiver_user))
        } else if (user_master_id == 2) {//if the usermasterid is 2 to execute the get_mc_receiver_user query
            logger_all.info("[select query request] : " + `SELECT user_id, user_name, user_email, user_mobile, api_key FROM user_management where user_master_id in (3, 4) and usr_mgt_status = 'Y' and user_id not in ('${user_id}') and parent_id in ('${user_id}')`);
            get_mc_receiver_user = await db.query(`SELECT user_id, user_name, user_email, user_mobile, api_key FROM user_management where user_master_id in (3, 4) and usr_mgt_status = 'Y' and user_id not in ('${user_id}') and parent_id in ('${user_id}')`);
            logger_all.info("[select query response] : " + JSON.stringify(get_mc_receiver_user))
        } else {// otherwise to execute the get_mc_receiver_user query
            logger_all.info("[select query request] : " + `SELECT user_id, user_name, user_email, user_mobile, api_key FROM user_management where user_master_id in (3,4) and usr_mgt_status = 'Y' and user_id not in ('${user_id}') and parent_id in ('${user_id}')`);

            get_mc_receiver_user = await db.query(`SELECT user_id, user_name, user_email, user_mobile, api_key FROM user_management where user_master_id in (3,4) and usr_mgt_status = 'Y' and user_id not in ('${user_id}') and parent_id in ('${user_id}')`);

            logger_all.info("[select query response] : " + JSON.stringify(get_mc_receiver_user))
            // if the get_mc_receiver_user length is '0' to get the no available data.otherwise it will be return the get_mc_receiver_user details.get_mc_receiver_user is empty is to get the no available data.
            if (get_mc_receiver_user == "") {
                return {
                    response_code: 1,
                    response_status: 204,
                    response_msg: 'No data available'
                };
            }
        }
        if (get_mc_receiver_user.length > 0) {
            return {
                response_code: 1,
                response_status: 200,
                num_of_rows: get_mc_receiver_user.length,
                response_msg: 'Success',
                report: get_mc_receiver_user
            };

        } else {
            return {
                response_code: 1,
                response_status: 204,
                response_msg: 'No data available'
            };

        }

    } catch (e) { // any error occurres send error response to client
        logger_all.info("[MCReceiverUser failed response] : " + e)
        return {
            response_code: 0,
            response_status: 201,
            response_msg: 'Error occured'
        };
    }
}
// MCReceiverUser - end

// using for module exporting
module.exports = {
    MCReceiverUser
}
