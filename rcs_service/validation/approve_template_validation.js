/*
It is used to one of which is user input validation.
approve_rcsnoList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare approve_rcsnoList object 
const template_approve = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  unique_template_id: Joi.string().required().label("Unique templateid"),
  template_status: Joi.string().required().label("Template status"),
}).options({ abortEarly: false });
// To exports the approve_rcsnoList module
module.exports = template_approve


