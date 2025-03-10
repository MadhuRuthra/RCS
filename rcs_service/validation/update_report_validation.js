/*
It is used to one of which is user input validation.
update_report_validation function to validate the user

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare update_report_validation object 
const update_report_validation = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  compose_id: Joi.string().required().label("Compose Id"),
  selected_user_id: Joi.string().required().label("Selected User Id"),
  csvFilePath: Joi.string().required().label("CSX File Path"),
  
}).options({ abortEarly: false });
// To exports the update_report_validation module
module.exports = update_report_validation

