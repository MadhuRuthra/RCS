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
const manual_report_pj = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  compose_user_id: Joi.string().required().label("ComposeUser Id"),
  compose_id: Joi.string().required().label("Compose Id"),
  file: Joi.string().required().label("File"),
  reportType: Joi.string().required().label("reportType"),
  upload_info: Joi.string().required().label("Upload info"),
}).options({ abortEarly: false });
// To exports the approve_rcsnoList module
module.exports = manual_report_pj


