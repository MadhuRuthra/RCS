/*
It is used to one of which is user input validation.
TemplateList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare TemplateList object
const TemplateList = Joi.object().keys({
// Object Properties are define    
  user_id: Joi.string().optional().label("User Id"),
  template_name_filter: Joi.string().optional().label("Template Name Filter"),
  template_id_filter:Joi.string().optional().label("Template Id Filter"),
  template_category_filter: Joi.string().optional().label("Template Category Filter"),
  sender_id_filter: Joi.string().optional().label("Sender Id Filter"),
  status_filter: Joi.string().optional().label("Status Filter"),
  approve_date_filter: Joi.string().optional().label("Approve Date Filter"),
  entry_date_filter: Joi.string().optional().label("Entry Date Filter"),
}).options({abortEarly : false});
// To exports the TemplateList module
module.exports = TemplateList

