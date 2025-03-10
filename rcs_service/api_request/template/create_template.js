/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in create template function which is used to create a new template.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger')
const getHeaderFile = require('./getHeader')
var util = require('util');
require('dotenv').config();
var exec = require('child_process').exec;
const env = process.env
const api_url = env.API_URL;
const media_bearer = env.MEDIA_BEARER;

var axios = require('axios');

async function createTemplate(req) {

    try {
        var logger_all = main.logger_all
        //  Get all the req header data
        const header_token = req.headers['authorization'];

        // get all the req data
        let temp_name = req.body.name;
        let language = req.body.language;
        let temp_category = req.body.category;
        let temp_components = req.body.components;
        let mobile_number = req.body.mobile_number;
        let media_url = req.body.media_url;
        let temp_label = req.body.temp_label;
        let camp_type = req.body.camp_type;
        // declare the array
        var succ_array = [];
        var error_array = [];
        let temp_insert_ids = [];
        let sender_number = [];
        let sender_number_business_id = [];
        let sender_number_bearer_token = [];
        // declare the variables
        let temp_lang;
        var user_id;
        // to initialize the variable
        var count = 0;
        var variable_count = 0;
        // query parameters
        logger_all.info("[template approval query parameters] : " + JSON.stringify(req.body));
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
        else {  // otherwise to get the user details
            user_id = get_user_id[0].user_id;
        }
        // if temp_components length is greeterthan '0' then process the will be continued
        // loop all the temp_components details.
        for (var p = 0; p < temp_components.length; p++) {
            if (temp_components[p]['type'] == 'body' || temp_components[p]['type'] == 'BODY') { //body condition
                temp_components[p]['text'] = temp_components[p]['text'].replace(/&amp;/g, "&")
                if (temp_components[p]['example']) {
                    variable_count = temp_components[p]['example']['body_text'][0].length
                }
            }
            if (temp_components[p]['type'] == 'HEADER') { //header condition
                temp_components[p]['text'] = temp_components[p]['text'].replace(/&amp;/g, "&")
            }
            if (temp_components[p]['type'] == 'FOOTER') { //footer condition
                temp_components[p]['text'] = temp_components[p]['text'].replace(/&amp;/g, "&")
            }
        }
        // loop for all the mobile_number template in the mobile numbers
        for (var i = 0; i < mobile_number.length; i++) {
            logger_all.info("[select query request] : " + `SELECT * from rcs_config WHERE concat(country_code, mobile_no) = '${mobile_number[i]}' AND whatspp_config_status = 'Y'`)
            const select_details = await db.query(`SELECT * from rcs_config WHERE concat(country_code, mobile_no) = '${mobile_number[i]}' AND whatspp_config_status = 'Y'`);
            logger_all.info("[select query response] : " + JSON.stringify(select_details))
            // if any number are available to process will be continued.select details length is not equal to '0' to select the language from master_language table 
            if (select_details.length != 0) {
                logger_all.info("[select query request] : " + `SELECT * from master_language WHERE language_code = '${language}' AND language_status = 'Y'`)
                const select_lang = await db.query(`SELECT * from master_language WHERE language_code = '${language}' AND language_status = 'Y'`);
                logger_all.info("[select query response] : " + JSON.stringify(select_lang))
                // if any number are available to process will be continued.select_lang length is not equal to '0' to insert the message_template table
                if (select_lang.length != 0) {

                    logger_all.info("[insert query request] : " + `INSERT INTO message_template VALUES(NULL,${select_details[0].whatspp_config_id},'${temp_name}',${select_lang[0].language_id},'${temp_category}','${JSON.stringify(temp_components)}','-','${user_id}','N',CURRENT_TIMESTAMP,'0000-00-00 00:00:00',${variable_count},${temp_label},${camp_type})`)
                    const insert_template = await db.query(`INSERT INTO message_template VALUES(NULL,${select_details[0].whatspp_config_id},'${temp_name}',${select_lang[0].language_id},'${temp_category}','${JSON.stringify(temp_components)}','-','${user_id}','N',CURRENT_TIMESTAMP,'0000-00-00 00:00:00',${variable_count},,${temp_label},${camp_type})`);
                    logger_all.info("[insert query response] : " + JSON.stringify(insert_template))

                    temp_lang = select_lang[0].language_id;
                    temp_insert_ids.push(insert_template.insertId)
                    sender_number.push(mobile_number[i])
                    sender_number_business_id.push(select_details[0].rcs_business_acc_id)
                    sender_number_bearer_token.push(select_details[0].bearer_token)
                }
                else { // otherwise the language is not available send message
                    logger_all.info("[template approval failed number] : " + mobile_number[i] + " - language not available in DB")
                    error_array.push({ mobile_number: mobile_number[i], reason: 'Language not available' })
                    return { response_code: 0, response_status: 201, response_msg: 'Language not available' };

                }

            }
            else { // otherwise the mobile is not available send the message
                logger_all.info("[template approval failed number] : " + mobile_number[i] + " - Not available in DB")
                error_array.push({ mobile_number: mobile_number[i], reason: 'Not available' })
                return { response_code: 0, response_status: 201, response_msg: 'Mobile Number not available' };

            }
        }
        // if any sender_number are available to send the success message
        if (sender_number.length == 0) {
            return { response_code: 1, response_status: 200, response_msg: 'Success ', success: succ_array, failure: error_array };

        }
        else { // otherwise media are checking condition
            if (media_url) {

                var h_file = await getHeaderFile.getHeaderFile(media_url);

                var command = `curl -X POST \
                "${api_url}${h_file[0]}" \
                --header "Authorization: OAuth ${media_bearer}" \
                --header "file_offset: 0" \
                --data-binary @${h_file[1]}`

                child = exec(command, async function (error, stdout, stderr) {

                    logger_all.info(' stdout: ' + stdout);
                    logger_all.info(' stderr: ' + stderr);

                    var curl_output = JSON.parse(stdout);

                    temp_components.push({
                        "type": "HEADER",
                        "format": h_file[2],
                        "example": { "header_handle": [curl_output.h] }
                    })
                    // loop for all the mobile_number template in the mobile numbers
                    for (var i = 0; i < sender_number.length; i++) {

                        api_url_updated = `${api_url}${sender_number_business_id[i]}/message_templates`

                        var data = {
                            name: temp_name,
                            language: language,
                            category: temp_category,
                            components: temp_components
                        };

                        var temp_msg = {
                            method: 'post',
                            url: api_url_updated,
                            headers: {
                                'Authorization': 'Bearer ' + sender_number_bearer_token[i],
                            },
                            params: data
                        };
                        // if(whtsap_send){
                        //   send_msg['data']['template']
                        // }

                        logger_all.info("[template approval request] : " + JSON.stringify(temp_msg))

                        await axios(temp_msg)
                            .then(async function (response) {
                                logger_all.info("[template approval success number] : " + sender_number[i] + " - " + util.inspect(response.data))
                                succ_array.push({ mobile_number: sender_number[i], id: response.data.id })
                                // to update the message_template table in response data id,temp components to check the temp_insert_ids
                                logger_all.info("[update query request] : " + `UPDATE message_template SET template_response_id = '${response.data.id}', template_status = 'S',template_message = '${JSON.stringify(temp_components)}' WHERE template_id = ${temp_insert_ids[i]}`)
                                const update_succ = await db.query(`UPDATE message_template SET template_response_id = '${response.data.id}', template_status = 'S',template_message = '${JSON.stringify(temp_components)}' WHERE template_id = ${temp_insert_ids[i]}`);
                                logger_all.info("[update query response] : " + JSON.stringify(update_succ))

                                count++

                            })
                            .catch(async function (error) {
                                // any error occurres send error response to client and then update the message_template in failure status 
                                logger_all.info("[template approval failed number] : " + sender_number[i] + " - " + error)
                                error_array.push({ mobile_number: sender_number[i], reason: error.message })

                                logger_all.info("[update query request] : " + `UPDATE message_template SET template_status = 'F' WHERE template_id = ${temp_insert_ids[i]}`)
                                const update_fail = await db.query(`UPDATE message_template SET template_status = 'F' WHERE template_id = ${temp_insert_ids[i]}`);
                                logger_all.info("[update query response] : " + JSON.stringify(update_fail))

                                count++

                            })
                        // }

                    }
                    // if any is not equal to null the process will be continue
                    if (error !== null) {
                        logger_all.info("[upload file failed number] : " + error)
                        // if number of temp_insert_ids length is available then process the will be continued
                        // loop all the get the temp_insert_ids to get the mobile numbers.
                        for (var f = 0; f < temp_insert_ids; f++) {
                            logger_all.info("[update query request] : " + `UPDATE message_template SET template_status = 'F' WHERE template_id = ${temp_insert_ids[f]}`)
                            const update_fail = await db.query(`UPDATE message_template SET template_status = 'F' WHERE template_id = ${temp_insert_ids[f]}`);
                            logger_all.info("[update query response] : " + JSON.stringify(update_fail))
                            error_array.push({ mobile_number: sender_number[f], reason: 'Image upload failed' })

                        }

                    }

                });

            }
            else {  // loop all the sender_number to get message_templates details.
                for (var i = 0; i < sender_number.length; i++) {
                    api_url_updated = `${api_url}${sender_number_business_id[i]}/message_templates`

                    var data = {
                        name: temp_name,
                        language: language,
                        category: temp_category,
                        components: temp_components
                    };

                    var temp_msg = {
                        method: 'post',
                        url: api_url_updated,
                        headers: {
                            'Authorization': 'Bearer ' + sender_number_bearer_token[i],
                        },
                        params: data
                    };

                    logger_all.info("[template approval request] : " + JSON.stringify(temp_msg))
                    // then the process will be continued.to update the message_template table in response id and temp components 
                    await axios(temp_msg)
                        .then(async function (response) {
                            logger_all.info("[template approval success number] : " + sender_number[i] + " - " + util.inspect(response.data))
                            succ_array.push({ mobile_number: sender_number[i], id: response.data.id })

                            logger_all.info("[update query request] : " + `UPDATE message_template SET template_response_id = '${response.data.id}', template_status = 'S',template_message = '${JSON.stringify(temp_components)}' WHERE template_id = ${temp_insert_ids[i]}`)
                            const update_succ = await db.query(`UPDATE message_template SET template_response_id = '${response.data.id}', template_status = 'S',template_message = '${JSON.stringify(temp_components)}' WHERE template_id = ${temp_insert_ids[i]}`);
                            logger_all.info("[update query response] : " + JSON.stringify(update_succ))

                            count++

                        })
                        .catch(async function (error) {
                            // any error occurres send error response to client and then update the message_template in failure status 
                            logger_all.info("[template approval failed number] : " + sender_number[i] + " - " + error)
                            error_array.push({ mobile_number: sender_number[i], reason: error.message })

                            logger_all.info("[update query request] : " + `UPDATE message_template SET template_status = 'F' WHERE template_id = ${temp_insert_ids[i]}`)
                            const update_fail = await db.query(`UPDATE message_template SET template_status = 'F' WHERE template_id = ${temp_insert_ids[i]}`);
                            logger_all.info("[update query response] : " + JSON.stringify(update_fail))

                            count++

                        })

                }
            }
        }
        // if the count is equal to sender_number length to sned the success message
        if (count == sender_number.length) {
            return { response_code: 1, response_status: 200, response_msg: 'Success ', success: succ_array, failure: error_array };

        }
        // function res_send() {
        //     return { response_code: 1, response_status: 200, response_msg: 'Success ', success: succ_array, failure: error_array };
        // }

    }
    catch (e) {  // any error occurres send error response to client
        logger_all.info("[template approval failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occurred ', success: succ_array, failure: error_array };

    }
}
// createTemplate - end

// using for module exporting
module.exports = {
    createTemplate,
};
