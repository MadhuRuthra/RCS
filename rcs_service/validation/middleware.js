/*
It is used to one of which is user input validation.
middleware function to validate the user.
// check() is a middleware used to validate
// the incoming data as per the fields

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
// If any validation error are occured to send the response

const log_file = require('../logger')
const logger = log_file.logger;
const logger_all = log_file.logger_all;

exports.body = (schema) => (req, res, next) => {
    const {
      error
    } = schema.validate(req.body);
    if (error) {
      var error_array = []; // Array declare
      for(var i = 0; i<error.details.length;i++){
        error_array.push(error.details[i].message)
      }

logger_all.info(error_array)
      res.status(200)
      // To send the response message
        .send({response_code: 0, response_status: 201, response_msg:'Error occurred', data:error_array});
    } else {
      next();
    }
  };
