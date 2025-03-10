/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in upload functions which is used in upload files.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const main = require('../../logger')
const fs = require('fs');
const dotenv = require('dotenv');
dotenv.config();
const env = process.env

const media_storage = env.MEDIA_STORAGE;
const media_url_static = env.MEDIA_URL;
// uplaodmedia function - start
async function uplaodmedia(req) {
  try {
    var logger_all = main.logger_all
    //  Get all the req header data
    const header_token = req.headers['authorization'];
   
    var day = new Date();
    // get all the req data
    let media_data = req.body.media_data;
    let media_type = req.body.media_type;
    // declare the variables
    let user_id;
    // query parameters
    logger_all.info("[uplaodmedia query parameters] : " + JSON.stringify(req.body));
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
    // decodeBase64Image function
    function decodeBase64Image(dataString) {
      var response = {};
      response.type = media_type;
      response.data = new Buffer(dataString, 'base64');
      return response;
    }
    // media file creating
    var media_decoded = decodeBase64Image(media_data);
    let media_name = `${media_storage}/uploads/rcs_images/${user_id}_${day.getDate()}${day.getHours()}${day.getMinutes()}${day.getSeconds()}.${media_type}`;
    let media_url = `${media_url_static}/uploads/rcs_images/${user_id}_${day.getDate()}${day.getHours()}${day.getMinutes()}${day.getSeconds()}.${media_type}`;
    // query parameters
    logger_all.info("[upload image query parameters] : " + req.body.media_type + " - " + user_id);
    // any error occurres send error response to client
    fs.writeFile(`${media_name}`, media_decoded.data, function (err) {
      if (err) {
        logger_all.info("[upload image failed response] : " + err);
        return { response_code: 1, response_status: 201, response_msg: 'Error occurred ' };
      }
    });
    // otherwise to send the success message and media_url
    logger_all.info("[upload image success response] : file written in - " + media_name);
    return { response_code: 0, response_status: 200, response_msg: 'Success', media_path: media_url };

  }
  catch (e) { // any error occurres send error response to client
    logger_all.info("[upload image failed response] : " + e)
    return { response_code: 1, response_status: 201, response_msg: 'Error occurred' };

  }
}
// uplaodmedia - end

// using for module exporting
module.exports = {
  uplaodmedia
};
