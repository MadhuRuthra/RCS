/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in template function which is used to get a template
details.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger')
require('dotenv').config();
// getTemplate - start
async function getTemplate(req) {
  try {
    var logger_all = main.logger_all
    //  Get all the req header data
    const header_token = req.headers['authorization'];

    // get all the req data
    let user_id;
    var select_template = [];
    var user_type;
    // query parameters
    logger_all.info("[get template query parameters] : " + JSON.stringify(req.body));
    // To get the User_id
    var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
    if (req.body.user_id) {
      get_user = get_user + `and user_id = '${req.body.user_id}' `;
    }
    logger_all.info("[select query request] : " + get_user);
    const get_user_id = await db.query(get_user);
    logger_all.info("[select query response] : " + JSON.stringify(get_user_id));

    if (get_user_id.length == 0) { // If get_user not available send error response to client in ivalid token
      logger_all.info("Invalid Token")
      return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
    }
    else {// otherwise to get the user details
      user_id = get_user_id[0].user_id;
      user_type = get_user_id[0].user_master_id;
    }


    if (user_type == 4) { //if the user type is '4' the process are executed.and to get the template details to the available user.
      logger_all.info("[select query request] : " + `SELECT distinct tmp.unique_template_id template_id,tmp.template_name,tmp.media_type,lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where con.user_id = '${user_id}' and tmp.template_status = 'Y' ORDER BY tmp.template_id DESC`)
      select_template = await db.query(`SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where con.user_id = '${user_id}' and tmp.template_status = 'Y' GROUP BY unique_template_id  ORDER BY tmp.template_id DESC`);
      // logger_all.info("[select query response] : " + JSON.stringify(select_template))

    }
    else if (user_type == 3) { //if the user type is '3' the process are executed.and to get the userid will act as a parent id.
      logger_all.info("[select query request] : " + `SELECT user_id, user_name FROM user_management 
          where parent_id = '${user_id}' ORDER BY user_master_id, user_id ASC`)
      const select_usr = await db.query(`SELECT user_id, user_name FROM user_management 
          where parent_id = '${user_id}' ORDER BY user_master_id, user_id ASC`);
      logger_all.info("[select query response] : " + JSON.stringify(select_usr))

      var user_ids = '';
      // if the select_usr length is '0' will be execute.
      if (select_usr.length == 0) {
        // logger_all.info("- No Sender ID available")
        // return { response_code: 0, response_status: 201, response_msg: 'No Sender ID available' };
        user_ids = "," + user_id;
      }
      // if number of select_usr length  is available then process the will be continued .loop all the get the user ids to act as the user_ids.
      for (var i = 0; i < select_usr.length; i++) {
        user_ids = user_ids.concat(`,${select_usr[i].user_id}`)
      }
      // to get the select_template in the available user.
      logger_all.info("[select query request] : " + `SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where (con.user_id ='${user_id}' or con.user_id in (${user_ids.substring(1)})) and tmp.template_status = 'Y' ORDER BY tmp.template_id DESC`)
      select_template = await db.query(`SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where (con.user_id ='${user_id}' or con.user_id in (${user_ids.substring(1)})) and tmp.template_status = 'Y' GROUP BY unique_template_id ORDER BY tmp.template_id DESC`);
      // logger_all.info("[select query response] : " + JSON.stringify(select_template))

    }
    else if (user_type == 2) {//if the user type is '2' the process are executed.and to get the select_usr to the available user.
      logger_all.info("[select query request] : " + `SELECT user_id FROM user_management where parent_id = '${user_id}' ORDER BY user_master_id, user_id ASC`)
      const select_usr = await db.query(`SELECT user_id FROM user_management where parent_id = '${user_id}' ORDER BY user_master_id, user_id ASC`);
      logger_all.info("[select query response] : " + JSON.stringify(select_usr))

      var user_ids = '';
      // if the select_usr length is '0' will be execute.
      if (select_usr.length == 0) {
        //logger_all.info("- No Sender ID available")
        //return { response_code: 0, response_status: 201, response_msg: 'No Sender ID available' };
        user_ids = "," + user_id;
      }
      // if number of select_usr length  is available then process the will be continued .loop all the get the user ids to act as the user_ids.
      for (var i = 0; i < select_usr.length; i++) {
        logger_all.info("[select query request] : " + `SELECT user_id FROM user_management where parent_id = 'select_usr[i].user_id' ORDER BY user_master_id, user_id ASC`)
        const select_usr_id = await db.query(`SELECT user_id FROM user_management where parent_id = '${user_id}' ORDER BY user_master_id, user_id ASC`);
        logger_all.info("[select query response] : " + JSON.stringify(select_usr_id))
        // if number of select_usr length  is available then process the will be continued .loop all the get the user ids to act as the user_ids.
        for (var j = 0; j < select_usr.length; j++) {
          user_ids = user_ids.concat(`,${select_usr_id[j].user_id}`)
        }
        user_ids = user_ids.concat(`,${select_usr[i].user_id}`)
      }
      // to get the select_template to the available user.
      logger_all.info("[select query request] : " + `SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where (tmp.created_user = '${user_id}' or con.user_id in (${user_ids.substring(1)})) and tmp.template_status = 'Y' ORDER BY tmp.template_id DESC`);
      select_template = await db.query(`SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where (tmp.created_user = '${user_id}' or con.user_id in (${user_ids.substring(1)})) and tmp.template_status = 'Y' GROUP BY unique_template_id ORDER BY tmp.template_id DESC`);
      //logger_all.info("[select query response] : " + JSON.stringify(select_template))

    }

    else if (user_type == 1) { // if the user type is '1' the process are executed and to get the select_template to the template_status is 'Y'.
      logger_all.info("[select query request] : " + `SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where tmp.template_status = 'Y' ORDER BY tmp.template_id DESC`)
      select_template = await db.query(`SELECT distinct tmp.unique_template_id template_id,tmp.template_name, tmp.media_type, lng.language_code, tmp.body_variable_count,tmp.template_message FROM message_template tmp left join rcs_config con on tmp.rcs_config_id = con.whatspp_config_id left join master_language lng on lng.language_id = tmp.language_id left join user_management usr on con.user_id = usr.user_id left join user_management crt on tmp.created_user = crt.user_id where tmp.template_status = 'Y' GROUP BY unique_template_id ORDER BY tmp.template_id DESC`);
      //logger_all.info("[select query response] : " + JSON.stringify(select_template))
    }
    // to return the success message 
    return { response_code: 1, response_status: 200, response_msg: 'Success ', num_of_rows: select_template.length, templates: select_template };
  }
  catch (e) { // any error occurres send error response to client
    logger_all.info("[get template failed response] : " + e)
    return { response_code: 0, response_status: 201, response_msg: 'Error Occurred ' };
  }
}
// getTemplate - end

// using for module exporting
module.exports = {
  getTemplate,
};
