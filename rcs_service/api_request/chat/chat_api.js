/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in chat functions which is used to connect the mobile chat.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger')
require('dotenv').config()

// getNumbers Function - start
async function getNumbers(req) {
    var logger_all = main.logger_all
    var logger = main.logger

    try {
        //  Get all the req header data
        const header_token = req.headers['authorization'];

        // Get all the req data
        var user_id;
        // Query parameters 
        logger_all.info("[get numbers query parameters] : " + JSON.stringify(req.body));
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
        else { // Otherwise to get the userid
            user_id = get_user_id[0].user_id;
        }
        // To check the given mobile number is available or not
        logger_all.info("[select query request] : " + `SELECT cnf.wht_display_name,cnf.wht_display_logo,concat(cnf.country_code,cnf.mobile_no) mobile_number FROM rcs_config cnf left join user_management usr on cnf.user_id = usr.user_id AND usr.usr_mgt_status = 'Y' WHERE (usr.user_id in (${user_id}) or usr.parent_id in (${user_id})) AND cnf.whatspp_config_status = 'Y' AND cnf.is_qr_code = 'N'`)
        const get_numbers = await db.query(`SELECT cnf.wht_display_name,cnf.wht_display_logo,concat(cnf.country_code,cnf.mobile_no) mobile_number FROM rcs_config cnf left join user_management usr on cnf.user_id = usr.user_id AND usr.usr_mgt_status = 'Y' WHERE (usr.user_id in (${user_id}) or usr.parent_id in (${user_id})) AND cnf.whatspp_config_status = 'Y' AND cnf.is_qr_code = 'N'`);
        logger_all.info("[select query response] : " + JSON.stringify(get_numbers))

        if (get_numbers.length == 0) { // if mobile number not available send error response to client
            return { response_code: 1, response_status: 204, response_msg: 'No data available' };
        }
        else {
            // if mobile number is available then process the will be continued
            // loop all the get_numbers to send same numbers request for the given sender id

            for (var i = 0; i < get_numbers.length; i++) {
                logger_all.info("[select query request] : " + ` SELECT * FROM messenger_response WHERE message_to = '${get_numbers[i].country_code}${get_numbers[i].mobile_number}' AND message_is_read = 'N'`)
                const get_msg_count = await db.query(`SELECT * FROM messenger_response WHERE message_to = '${get_numbers[i].country_code}${get_numbers[i].mobile_number}' AND message_is_read = 'N'`);
                logger_all.info("[select query response] : " + JSON.stringify(get_msg_count))
                // check if the unread message count default is '0' otherwise we are total number of the unread message count.
                if (get_msg_count.length == 0) {
                    get_numbers[i]['unread_msg_count'] = 0
                }
                else {
                    get_numbers[i]['unread_msg_count'] = get_msg_count.length
                }
                //get_numbers[i]['mobile_number'] = get_numbers[i].country_code+""+get_numbers[i].mobile_no;
                // check if the select the get mobile numbers message_status = 'Y' the process will be executed. otherwise process are through the no available datas
                logger_all.info("[select query request] : " + ` SELECT distinct message_from, MAX(message_rec_date) max_message_rec_date FROM messenger_response WHERE message_to = '${get_numbers[i].mobile_number}' AND message_status = 'Y' group by message_from order by MAX(message_rec_date) DESC`)
                const get_dist_number = await db.query(` SELECT distinct message_from, MAX(message_rec_date) max_message_rec_date FROM messenger_response WHERE message_to = '${get_numbers[i].mobile_number}' AND message_status = 'Y' group by message_from order by MAX(message_rec_date) DESC`);
                logger_all.info("[select query response] : " + JSON.stringify(get_dist_number))

                var dist_num = []// declare array

                // loop all the get_dist_number to send message from number and message to number request

                for (var j = 0; j < get_dist_number.length; j++) {
                    logger_all.info("[select query request] : " + `SELECT * FROM messenger_response WHERE message_to = '${get_numbers[i].mobile_number}' AND message_from = '${get_dist_number[j].message_from}' AND message_is_read = 'N'`)
                    const get_dist_number_count = await db.query(`SELECT * FROM messenger_response WHERE message_to = '${get_numbers[i].mobile_number}' AND message_from = '${get_dist_number[j].message_from}' AND message_is_read = 'N'`);
                    logger_all.info("[select query response] : " + JSON.stringify(get_dist_number_count))
                    // check if the  number of get_dist_number_count is '0' it will be push the array
                    if (get_dist_number_count.length == 0) {
                        dist_num.push({ from_number: get_dist_number[j].message_from, msg_count: 0 })
                    }
                    else { //otherwise it will be push the array.
                        dist_num.push({ from_number: get_dist_number[j].message_from, msg_count: get_dist_number_count.length })
                    }
                }
                // json push numbers
                get_numbers[i]['unread_msg'] = dist_num;

            }
            // get_numbers length is '0' to through the no data available message.
            if (get_numbers.length == 0) {
                return { response_code: 1, response_status: 204, response_msg: 'No data available.' };
            }
            else { // otherwise to get numbers values.
                return { response_code: 1, response_status: 200, response_msg: 'Success', data: get_numbers };
            }
        }

    }
    catch (e) { // any error occurres send error response to client
        logger_all.info("[get numbers report failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
    }
}
// getNumbers Function - end

// getChat function - start
async function getChat(req) {
    var logger_all = main.logger_all
    var logger = main.logger

    try {

        // get all the req data
        var sender_id = req.body.sender_id;
        var mobile_number = req.body.mobile_number;
        // To check the given sender id number and mobile number is available or not and then message_status = 'Y' is available
        logger_all.info("[select query request] : " + `SELECT message_id,user_id,message_to,message_from,message_from_profile,message_resp_id,message_type,message_data,msg_text,msg_media,msg_media_type,msg_media_caption,msg_reply_button,msg_reaction,msg_list,message_is_read,message_status,DATE_FORMAT(message_rec_date,'%d-%m-%Y %h:%i:%s %p') message_rec_date,DATE_FORMAT(message_read_date,'%d-%m-%Y %h:%i:%s %p') message_read_date FROM messenger_response WHERE message_to in('${sender_id}','${mobile_number}') AND message_from in('${sender_id}','${mobile_number}') AND message_status = 'Y' ORDER BY message_id DESC`)
        const get_messages = await db.query(`SELECT message_id,user_id,message_to,message_from,message_from_profile,message_resp_id,message_type,message_data,msg_text,msg_media,msg_media_type,msg_media_caption,msg_reply_button,msg_reaction,msg_list,message_is_read,message_status,DATE_FORMAT(message_rec_date,'%d-%m-%Y %h:%i:%s %p') message_rec_date,DATE_FORMAT(message_read_date,'%d-%m-%Y %h:%i:%s %p') message_read_date FROM messenger_response WHERE message_to in('${sender_id}','${mobile_number}') AND message_from in('${sender_id}','${mobile_number}') AND message_status = 'Y' ORDER BY message_id DESC`);
        logger_all.info("[select query response] : " + JSON.stringify(get_messages))
        // To check the given sender id number and mobile number with using the time time interval now to limit in oneday are chat is available otherwise chat will not available.
        logger_all.info("[select query request] : " + `SELECT message_id,user_id,message_to,message_from,message_from_profile,message_resp_id,message_type,message_data,msg_text,msg_media,msg_media_type,msg_media_caption,msg_reply_button,msg_reaction,msg_list,message_is_read,message_status,DATE_FORMAT(message_rec_date,'%d-%m-%Y %h:%i:%s %p') message_rec_date,DATE_FORMAT(message_read_date,'%d-%m-%Y %h:%i:%s %p') message_read_date FROM messenger_response WHERE message_from = '${mobile_number}' AND message_to = '${sender_id}' AND message_rec_date >= NOW() - INTERVAL 1 DAY ORDER BY message_rec_date DESC  `)
        const select_responsetime = await db.query(`SELECT message_id,user_id,message_to,message_from,message_from_profile,message_resp_id,message_type,message_data,msg_text,msg_media,msg_media_type,msg_media_caption,msg_reply_button,msg_reaction,msg_list,message_is_read,message_status,DATE_FORMAT(message_rec_date,'%d-%m-%Y %h:%i:%s %p') message_rec_date,DATE_FORMAT(message_read_date,'%d-%m-%Y %h:%i:%s %p') message_read_date FROM messenger_response WHERE message_from = '${mobile_number}' AND message_to = '${sender_id}' AND message_rec_date >= NOW() - INTERVAL 1 DAY ORDER BY message_rec_date DESC `);
        logger_all.info("[select query response] : " + JSON.stringify(select_responsetime))

        var isChat; //declear variable
        // if response time is not available to declear the chat value is 'false'.otherwise it is 'true'.
        if (select_responsetime.length == 0) {
            isChat = 'false';
        }
        else {
            isChat = 'true';
        }
        // if the get message length is not available to send the no available data.otherwise it will be return the get_messages details.
        if (get_messages.length == 0) {
            return { response_code: 1, response_status: 204, response_msg: 'No data available' };
        }
        else {
            return { response_code: 1, response_status: 200, response_msg: 'Success', num_of_rows: get_messages.length, isChat: isChat, report: get_messages };
        }
    }
    catch (e) { // any error occurres send error response to client
        logger_all.info("[get chat report failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
    }
}
// using for module exporting
module.exports = {
    getNumbers,
    getChat
};
