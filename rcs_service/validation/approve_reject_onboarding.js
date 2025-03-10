/*
It is used to one of which is user input validation.
Signup function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare Signup object
const approve_reject_onboarding = Joi.object().keys({
  // Object Properties are define  
  request_id: Joi.string().required().label("Request ID"),  
  user_id: Joi.string().required().label("user_id"),
  change_user_id: Joi.string().required().label("Change User ID"),
  aprj_status: Joi.string().optional().label("Status"),
  txt_remarks: Joi.string().optional().label("Remarks"),
}).options({ abortEarly: false });

// To exports the Signup module
module.exports = approve_reject_onboarding



