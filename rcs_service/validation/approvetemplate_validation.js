/*
It is used to one of which is user input validation.
TemplateApproval function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
const { template } = require("@hapi/joi/lib/errors");
// To declare TemplateApproval object
const ApproveTemplate = Joi.object().keys({
  // Object Properties are define     
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),
  campaign_id : Joi.string().required().label('Camoaign Id'),
  selected_user_id : Joi.string().required().label('Unique User Id'),
  
}).options({ abortEarly: false });
// To exports the TemplateApproval module
module.exports = ApproveTemplate

