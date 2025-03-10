/*
It is used to one of which is user input validation.
manage_rcsno_list function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare manage_rcsno_list object 
const manage_rcsno_list = Joi.object().keys({
  // Object Properties are define   
  user_id: Joi.string().optional().label("User Id"),
  status_filter: Joi.string().optional().label("Status Filter"),
  entry_date_filter: Joi.string().optional().label("Entry Date Filter"),
  approve_date_filter: Joi.string().optional().label("Approve Date Filter"),
}).options({ abortEarly: false });
// To exports the manage_rcsno_list module
module.exports = manage_rcsno_list
