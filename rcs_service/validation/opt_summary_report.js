/*
It is used to one of which is user input validation.
OTPSummaryValidation function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare OTPSummaryValidation object 
const OTPSummaryValidation = Joi.object().keys({
  // Object Properties are define    
  user_id: Joi.string().optional().label("User Id"),
  filter_date: Joi.string().optional("Filter Date"),
  store_id_filter: Joi.string().optional("Store Id Filter"),
  filter_user: Joi.string().optional("Filter User"),
  filter_department: Joi.string().optional("Filter Department"),
}).options({ abortEarly: false });
// To exports the OTPSummaryValidation module
module.exports = OTPSummaryValidation
