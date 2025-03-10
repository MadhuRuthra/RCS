/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in reply functions which is used to reply message details.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger')
var util = require('util');
require('dotenv').config()
var axios = require('axios');
const env = process.env
const CHATBOT_URL = env.CHATBOT_URL;
const CHATBOT_User = env.CHATBOT_User;
const CHATBOT_Password = env.CHATBOT_Password;

// replyMsg - start
async function replyMsg(req) {
    try {
        var logger_all = main.logger_all
        //  Get all the req header data
        const header_token = req.headers['authorization'];

        // get all the req data
        let sender_id = req.body.sender_id;
        let reply_msg = req.body.reply_msg;
        sender_numbers = sender_id.split("+91");
	let reply_convert = btoa(reply_msg)
        // declare the variables
        var err;
        var message_id;
        // Generate a random 10-digit number
        const randomDigits = Array.from({ length: 10 }, () => Math.floor(Math.random() * 10)).join('');
        // console.log(randomDigits);

        // Define the data to be sent
        const send_data = {
            templateJson: JSON.stringify({
                contentMessage: {
                    text: reply_msg
                }
            }),
            corelationId: randomDigits
        };

        // Define the headers
        const header_data = {
            'user': CHATBOT_User,
            'password': CHATBOT_Password,
            'Content-Type': 'application/json'
        };

        // Define the Axios configuration
        const config = {
            method: 'post',
            url: `${CHATBOT_URL}${sender_numbers[1]}`,  // Ensure mobile_number is defined
            headers: header_data,
            data: send_data
        };
	var rec_number = "yeejai_technologies_lgzmaa9c_agent@rbm.goog"
        // query parameters
        logger_all.info("[reply msg query parameters] : " + JSON.stringify(req.body));
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
        }

//        const update_messenger_response_update = await db.query(`UPDATE messenger_response SET message_is_read = 'Y' WHERE message_id in (${message_id})`);
//		logger_all.info("[update query response] : " + JSON.stringify(update_messenger_response_update))
   
            // if the messenger_response date is 24 hours to select the select_responsetime
            logger_all.info("[select query request] : " + `SELECT * FROM messenger_response WHERE message_to = '${rec_number}' AND message_from = '${sender_id}' AND message_rec_date >= NOW() - INTERVAL 1 DAY`)
            const select_responsetime = await db.query(`SELECT * FROM messenger_response WHERE message_to = '${rec_number}' AND message_from = '${sender_id}' AND message_rec_date >= NOW() - INTERVAL 1 DAY`);
            logger_all.info("[select query response] : " + JSON.stringify(select_responsetime))
            // if the select_responsetime length is '0' to send You cannot reply to this number.
            if (select_responsetime.length == 0) {
                err = 'You cannot reply to this number.';
            }
            else { // otherwise the process will be continue

                // if the new reply message are send to insert the messenger_response
                logger_all.info("[replyMsg - insert query request] : " + `INSERT INTO messenger_response VALUES(NULL,0,'${sender_id}','${rec_number}','-','-','text','${JSON.stringify(config)}','${reply_convert}',NULL,NULL,NULL,NULL,NULL,NULL,'N','N',CURRENT_TIMESTAMP,'0000-00-00 00:00:00')`)
                const insert_reply = await db.query(`INSERT INTO messenger_response VALUES(NULL,0,'${sender_id}','${rec_number}','-','-','text','${JSON.stringify(config)}','${reply_convert}',NULL,NULL,NULL,NULL,NULL,NULL,'N','N',CURRENT_TIMESTAMP,'0000-00-00 00:00:00')`);
                logger_all.info("[replyMsg - insert query response] : " + JSON.stringify(insert_reply))

                await axios(config)
                    .then(async function (response) {
			 message_id = response.data.transactionId;

                        logger_all.info("[reply msg response] : " + JSON.stringify(response.data))
                        // to chcek the message_id and update the messenger_response 
                        logger_all.info("[replyMsg - update query request] : " + `UPDATE messenger_response SET message_resp_id = '${message_id}',message_status = 'Y',message_is_read='Y' WHERE message_id = ${insert_reply.insertId}`)
                        const update_success = await db.query(`UPDATE messenger_response SET message_resp_id = '${message_id}',message_status = 'Y',message_is_read='Y' WHERE message_id = ${insert_reply.insertId}`);
                        logger_all.info("[replyMsg - update query response] : " + JSON.stringify(update_success))

                        return { response_code: 1, response_status: 200, response_msg: 'Success' };

                    })
                    // if the message response is failed and any error are occured to the catch function
                    .catch(async function (error) {
                        logger_all.info("[reply msg failed response] : " + error)
                        // to chcek the message_id and update the messenger_response so update the failed status
                        logger_all.info("[replyMsg - update query request] : " + `UPDATE messenger_response SET message_status = 'F' WHERE message_id = ${insert_reply.insertId}`)
                        const update_failure = await db.query(`UPDATE messenger_response SET message_status = 'F' WHERE message_id = ${insert_reply.insertId}`);
                        logger_all.info("[replyMsg - update query response] : " + JSON.stringify(update_failure))

                        err = 'Error Occurred.';
                        return { response_code: 0, response_status: 201, response_msg: 'Error occurred ' };

                    })
            }
      
        if (err) {//if any error are occurred to execute the this condition 
            return { response_code: 0, response_status: 201, response_msg: err };
        }
        else { //otherwise to send the success message and message_id
            return { response_code: 1, response_status: 200, response_msg: 'Success', message_id: message_id };
        }
    }
    catch (e) {// any error occurres send error response to client
        logger_all.info("[reply msg failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occurred ' };
    }
}
// replyMsg - end

// using for module exporting
module.exports = {
    replyMsg
};
