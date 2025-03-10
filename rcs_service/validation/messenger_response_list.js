/*
It is used to one of which is user input validation.
MessengerResponseList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare MessengerResponseList object 
const MessengerResponseList = Joi.object().keys({
  // Object Properties are define   
  user_id: Joi.string().optional().label("User Id"),
  sender: Joi.string().optional().label("Sender"),
  receiver: Joi.string().optional().label("Receiver"),
  message_type: Joi.string().optional().label("Message Type"),
  date_filter: Joi.string().optional().label("Date Filter"),
}).options({ abortEarly: false });
// To exports the MessengerResponseList module
module.exports = MessengerResponseList

