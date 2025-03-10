/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in update device token functions which is used to generate the device token.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger');

//devicetokenupdate function - start
async function devicetokenupdate(req) {
    try {
        var logger_all = main.logger_all
        //  Get all the req header data
        const header_token = req.headers['authorization'];
       
        // get current Date and time
        var day = new Date();
        var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
        var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
        var current_date = today_date + ' ' + today_time;

        // get all the req data
        var user_id = req.body.user_id;
        var device_token = req.body.device_token;
        var response_result;
// query parameters
        logger_all.info("[update device Token query parameters] : " + JSON.stringify(req.body));
        // To get the User_id
        var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
    if(req.body.user_id){
        get_user = get_user + `and user_id = '${req.body.user_id}' `;
    }
    logger_all.info("[select query request] : " +  get_user);
    const get_user_id = await db.query(get_user);
    logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
// If get_user not available send error response to client in ivalid token
        if (get_user_id.length == 0) {
            logger_all.info("Invalid Token")
            return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
        }
        else { // otherwise to get the user details
            user_id = get_user_id[0].user_id;
        }
            // to check the device_user_id in the user_device_list
            var check_user_id = await db.query(`SELECT * FROM user_device_list where device_user_id = '${user_id}'`);
            logger_all.info(`SELECT * FROM user_device_list where device_user_id = '${user_id}'`);

            if (check_user_id.length > 0) {
              // if the check_user_id length is greater than '0' to execute the process.to check the device_user_id and update the user_device_list to set the device_token
                var response_result = await db.query(`UPDATE user_device_list SET device_token = '${device_token}' WHERE device_user_id = '${user_id}'`);

                logger_all.info("[update query request] : " + `UPDATE user_device_list SET device_token = '${device_token}' WHERE device_user_id = '${user_id}'`);

                logger_all.info("[update query response] : " + JSON.stringify(response_result));
            }
            else {
                // otherwise to insert the user_device_list values
                var response_result = await db.query(`INSERT INTO user_device_list VALUES(NULL, '${user_id}', '${device_token}','-', 'Y', '${current_date}')`);

                logger_all.info("[insert query request] : " + `INSERT INTO user_device_list VALUES(NULL, '${user_id}', '${device_token}','-', 'Y', '${current_date}')`);

                logger_all.info("[insert query response] : " + JSON.stringify(response_result));
            }
                // to return the success message  
            return { response_code: 1, response_status: 200, response_result, response_msg: 'Success' };
        }
    catch (e) { // any error occurres send error response to client
        logger_all.info("[Update DeviceToken failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
    }
}
//devicetokenupdate function - end

// using for module exporting
module.exports = {
    devicetokenupdate,
};
