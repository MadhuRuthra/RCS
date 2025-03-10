/*
It is used to one of which is user input validation.
createCsvValidation function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare createCsvValidation object 
const createCsvValidation = Joi.object().keys({
  // Object Properties are define
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),
  mobile_number: Joi.array().required().label("Mobile Number"),
}).options({ abortEarly: false });
// To exports the createCsvValidation module
module.exports = createCsvValidation
