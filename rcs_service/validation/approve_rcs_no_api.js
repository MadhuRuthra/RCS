/*
It is used to one of which is user input validation.
approve_rcs_no_apiList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare approve_rcs_no_apiList object 
const approve_rcs_no_apiList = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  mobile_filter: Joi.string().optional().label("Mobile Filter"),
}).options({ abortEarly: false });
// To exports the approve_rcs_no_apiList module
module.exports = approve_rcs_no_apiList

