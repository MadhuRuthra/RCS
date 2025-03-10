/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
It is used to one of which is user input validation.
UsersUserType function to validate the user. This is a main page validation for all sub pages.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../db_connect/connect");
const jwt = require("jsonwebtoken")
const main = require('../logger');
// VerifyUser function start
const VerifyUser = async (req, res, next) => {
    var logger = main.logger
    var logger_all = main.logger_all

    try {

        // To get Time and Date
        var day = new Date();
        var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
        var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
        var current_date = today_date + ' ' + today_time;
        // Get all the req data
        var header_json = req.headers;
        let ip_address = header_json['x-forwarded-for'];
        //logger.info("select_query request :" + query);
        logger.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers) + " - " + ip_address)
        logger_all.info("[API REQUEST] " + req.originalUrl + " - " + JSON.stringify(req.body) + " - " + JSON.stringify(req.headers) + " - " + ip_address)
        // Declare the variable
        var user_id;
        const bearerHeader = req.headers['authorization'];

        if (bearerHeader) { // bearerHeader
            // To split the header token   
            var header_token_1 = bearerHeader.split('Bearer ')[1];
            // To check the bearerHeader is user_management table exists are not
            var check_bearer = `SELECT * FROM user_management WHERE bearer_token = '${bearerHeader}' AND usr_mgt_status in ('N', 'Y', 'R')`;
            var invalid_msg = 'Invalid Token';

            if (req.body.user_id && req.originalUrl != '/list/available_credits') {
                check_bearer = check_bearer + ' AND user_id = ' + req.body.user_id;
                invalid_msg = 'Invalid token or User ID'
            }

            logger_all.info("[select query request] : " + check_bearer);
            const check_bearer_response = await db.query(check_bearer);
            logger_all.info("[select query response] : " + JSON.stringify(check_bearer_response));
            // check_bearer_response length is '0' to through the response message
            if (check_bearer_response.length == 0) {
                logger_all.info(invalid_msg)
                logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 403, response_msg: invalid_msg }))
                // To return the res send message
                return res
                    .status(403)
                    .send({ response_code: 0, response_status: 403, response_msg: invalid_msg });
            }
            else {   // Otherwise to get userid and the process are continued
                user_id = check_bearer_response[0].user_id;

                try {
                    // To verify the ACCESS_TOKEN_SECRET and the header_token_1
                    jwt.verify(header_token_1, process.env.ACCESS_TOKEN_SECRET);
                    // To select the user_log_status = 'I' and to check the available user
                    //logger_all.info("[Valid User Middleware query request] : " + `select * from user_log where user_log_status = 'I' and user_id = '${user_id}' and login_date = '${today_date}' `);
                    //var get_user_log = await db.query(`select *  from user_log where user_log_status = 'I' and user_id = '${user_id}' and login_date = '${today_date}'  `);
                    //logger_all.info("[Valid User Middleware query Response] : " + JSON.stringify(get_user_log));
                    // get_user_log length is '0' to through the 'Invalid Token'.
                    //if (get_user_log.length == 0) {
                      //  logger_all.info("Invalid Token")
                     //   logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 403, response_msg: 'Invalid Token' }))
                        // To return the response message in 'Error Occurred'
                    //    return res
                     //       .status(403)
                     //       .send({ response_code: 0, response_status: 403, response_msg: 'Invalid Token' });
                   // }
                   // else {
//                        next();
			logger_all.info(JSON.stringify(check_bearer_response[0].user_master_id))
			req['body']['user_master_id']=check_bearer_response[0].user_master_id
			 next();
                   // }
                } catch (e) {
                    // any error occurres send error response to client and update the user_log_status = 'O' 
                    logger_all.info("[Valid User Middleware Update query request] : " + `UPDATE user_log  SET user_log_status = 'O',logout_time = '${current_date}' WHERE  user_id = '${user_id}'`);
                    var get_ex_time = await db.query(`UPDATE user_log  SET user_log_status = 'O',logout_time = '${current_date}' WHERE  user_id = '${user_id}'`);
                    logger_all.info("[Valid User Middleware  Update query Response] : " + JSON.stringify(get_ex_time));
                    logger_all.info("[Error occured] : " + e);
                    logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 403, response_msg: e.message }))
                    // To return the response message in 'Error Occurred'
                    return res
                        .status(403)
                        .send({ response_code: 0, response_status: 403, response_msg: 'Error Occurred.' });
                }

            }
        }
        else { // Otherwise to send the message in 'A token is required for in Header' to the user.
            logger_all.info("[Valid User Middleware failed response] : A token is required for in Header");
            logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 403, response_msg: 'A token is required for in Header' }))

            return res
                .status(403)
                .send({ response_code: 0, response_status: 403, response_msg: 'A token is required for in Header' });
        }
    }

    catch (e) { // any error occurres send error response to client
        logger_all.info("[Error occured] : " + e);
        logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: e.message }))

        res.json({ response_code: 0, response_status: 201, response_msg: 'Error occurred' });
    }
}
// VerifyUser function end
module.exports = VerifyUser;
