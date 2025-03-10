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
  var logger_all = main.logger_all;
    var logger = main.logger;

    try {
        // Declare the variables
        const { user_id,user_master_id } = req.body;
        let template_query = '';
        
        console.log("User ID:", user_id);

        // Construct the SQL query based on user_id
        if (user_master_id.toString() === '1') {
            template_query = `
                SELECT mt.*, um.user_name 
                FROM message_template mt
                JOIN user_management um ON mt.created_user = um.user_id WHERE template_status = "Y"
                ORDER BY mt.template_entdate DESC`;
        } else {
            template_query = `
                SELECT mt.*, um.user_name 
                FROM message_template mt
                JOIN user_management um ON mt.created_user = um.user_id
                WHERE mt.created_user = ${user_id} AND template_status = "Y"
                ORDER BY mt.template_entdate DESC`;
        }

        // Execute the query
        const template_result = await db.query(template_query);

        // Check if any templates are returned
        // if (template_result.length === 0) {
        //     return { response_code: 1, response_status: 204, response_msg: 'No data available' };
        // } else {
            return { 
                response_code: 1, 
                response_status: 200, 
                response_msg: 'Success', 
                num_of_rows: template_result.length, 
                templates: template_result 
            };
        // }

    } catch (e) { 
        // Log the error and send an error response to the client
        logger_all.info("[Template List failed response] : " + e);
        return { response_code: 0, response_status: 500, response_msg: 'An error occurred' };
    }

}
// getTemplate - end

// using for module exporting
module.exports = {
  getTemplate,
};

