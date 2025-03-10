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
const TemplateApproval = Joi.object().keys({
  // Object Properties are define     
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),

  /* language: Joi.string().optional().label("Language"),
  category: Joi.string().optional().label("Category"),
  media_url: Joi.string().optional().label("Media Url"),
  components: Joi.array().optional().label("Components"),
  mediatype: Joi.string().optional().label("MediaType"), */
  //code: Joi.string().optional().min(9).max(9).label("Code"),
  
  communicationType: Joi.string().required().label('CommunicationType'),
  messageContent: Joi.array().required().label("MessageContent"),
  templatelabel : Joi.string().required().label("Template Label"),
  campaigntype: Joi.string().required().label("Campaign Type"),
    media_type: Joi.string().optional().label("Media Type"),
}).options({ abortEarly: false });
// To exports the TemplateApproval module
module.exports = TemplateApproval

