/*
It is used to one of which is user input validation.
TemplatercsList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare TemplatercsList object
const TemplatercsList = Joi.object().keys({
  // Object Properties are define     
  user_id: Joi.string().optional().label("User Id"),
  read_status_filter: Joi.string().optional().label("Read Status Filter"),
  delivery_status_filter: Joi.string().optional().label("Delivery Status Filter"),
  response_status_filter: Joi.string().optional().label("Response Status Filter"),
  sender_filter: Joi.string().optional().label("Sender Filter"),
  receiver_filter: Joi.string().optional().label("Receiver Filter"),
}).options({ abortEarly: false });
// To exports the TemplatercsList module
module.exports = TemplatercsList

