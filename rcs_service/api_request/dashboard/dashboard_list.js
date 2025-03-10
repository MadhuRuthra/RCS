/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in dashboard functions which is used to get the dashboard details.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
const db = require("../../db_connect/connect");
require('dotenv').config()
const main = require('../../logger');
// dashboard otp Function - start
async function otp_Dash_Board(req) {
    var logger_all = main.logger_all
    var logger = main.logger
    try {
        //  Get all the req header data
        const header_token = req.headers['authorization'];
        // Get all the req data
        let user_id = req.body.user_id;
        // declare the variables
        let list_user_id_1, sql_query_2, user_master_id;

        // declare the array
        let array_list_user_id = [], total_response = [], total_available_messages = [], total_user_id = [], total_user_master_id = [], total_user_name = [];

        // To initialize a variable with an empty string value
        let list_user_id = '', dashboard_user = '', filter_condition = '';
        // to get the today date
        var day = new Date();
        var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
        // query parameters
        logger_all.info("[Dashboard query parameters] : " + JSON.stringify(req.body));
        // To get the User_id
        var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
        if (req.body.user_id) {
            get_user = get_user + `and user_id = '${req.body.user_id}' `;
        }
        logger_all.info("[select query request] : " + get_user);
        const get_user_id = await db.query(get_user);
        logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
        // If get_user not available send error response to client in ivalid token
        if (get_user_id.length == 0) {
            logger_all.info("Invalid Token")
            return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
        }
        else { // otherwise to get the user details
            user_id = get_user_id[0].user_id;
            user_master_id = get_user_id[0].user_master_id;
        }

        if (user_master_id == '1' && user_id == '1') {

            sql_query_2 = await db.query(`SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);

        } else {
            sql_query_2 = await db.query(`SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${user_id}' or usr.parent_id in (${user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);
        }

        logger_all.info("[select query request] : " + JSON.stringify(sql_query_2));
        // loop all number of available_messages,no of user_ids,no of user_master_id,no of user_name are push in to the arrays.  available then process the will be continued 
        for (var i = 0; i < sql_query_2.length; i++) {
            total_available_messages.push(sql_query_2[i].available_messages);
            list_user_id += sql_query_2[i].user_id + ",";
            array_list_user_id.push(sql_query_2[i].user_id);
            total_user_id.push(sql_query_2[i].user_id);
            total_user_master_id.push(sql_query_2[i].user_master_id);
            total_user_name.push(sql_query_2[i].user_name);
        }
        list_user_id_1 = list_user_id.slice(0, -1);
        total_available_messages.push(sql_query_2[0].available_messages);
        // loop sql_query_2 length are available then process the will be continued

        var below_user_ids;

        for (var i = 1; i < sql_query_2.length; i++) {
            if (user_master_id == '1' && user_id == '1') {

                below_user_ids = await db.query(`SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where  usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);

            } else {

                below_user_ids = await db.query(`SELECT usr.user_id, usr.user_name, usr.user_master_id, lmt.available_messages FROM user_management usr left join message_limit lmt on usr.user_id = lmt.user_id where (usr.user_id = '${sql_query_2[i].user_id}' or usr.parent_id in (${sql_query_2[i].user_id})) and usr.usr_mgt_status = 'Y' order by usr.user_master_id, usr.user_id asc`);

            }
            logger_all.info("[select query request] : " + JSON.stringify(below_user_ids));
            var totalavailableMessages = 0;
            // loop below_user_ids length are available then process the will be continued 
            for (const user of below_user_ids) {
                totalavailableMessages += user.available_messages;
            }
            total_available_messages.push(totalavailableMessages);
        }

        if (sql_query_2.length > 0) {
            // loop all number of array_list_user_id will available then process the will be continued
            for (var i = 0; i < array_list_user_id.length; i++) {

                var dashboard_user_1 = await db.query(`SELECT wht.user_id, usr.user_name,ussr.user_type,DATE_FORMAT(wht.com_entry_date, '%d-%m-%Y') AS entry_date,sum(wht.total_msg) as total_msg, sum(wht.total_waiting) as total_waiting,sum(wht.total_process) as total_process, sum(wht.total_success) as total_success, sum(wht.total_delivered) as total_delivered, sum(wht.total_failed) as total_failed ,sum(wht.total_read) as total_read  FROM rcs.user_summary_report wht LEFT JOIN rcs.user_management usr ON wht.user_id = usr.user_id LEFT JOIN rcs.user_master ussr ON usr.user_master_id = ussr.user_master_id WHERE (usr.user_id = '${array_list_user_id[i]}') AND (DATE(wht.com_entry_date) BETWEEN '${today_date}' AND '${today_date}') ${filter_condition} group by usr.user_master_id, date(wht.com_entry_date) order by usr.user_master_id, usr.user_name, entry_date desc`);
                // if the dashboard_user_1 length is not available to push the my obj datas.otherwise it will be return the push the dashboard_user_1 details.
                if (dashboard_user_1.length > 0) {
                    total_response.push(dashboard_user_1[0]);
                }
                if (dashboard_user_1.length == 0) {
                    var myObj = {
                        "header_title": "rc",     // push the total response
                        "user_name": total_user_name[i],
                        "user_id": total_user_id[i],
                        "user_master_id": total_user_master_id[i],
                        "available_messages": total_available_messages[i],
                        "total_msg": 0,
                        "total_success": 0,
                        "total_failed": 0,
                        "total_invalid": 0,
                        "total_waiting": 0,
                    }
                    total_response.push(myObj);
                }
            }
        }
        // if the get total response length is not available to send the no available data.otherwise it will be return the total_response dashboard today details.
        if (total_response.length > 0) {
            return {
                response_code: 1, response_status: 200, response_msg: 'Success', num_of_rows: total_response.length, report: total_response
            };
        } else {
            return { response_code: 1, response_status: 204, response_msg: 'No Data Available' };
        }
    }
    // any error occurres send error response to client
    catch (e) {
        logger_all.info("[DashBoard OTP failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
    }
}
// otp_Dash_Board - end

// using for module exporting
module.exports = {
    otp_Dash_Board,
};
