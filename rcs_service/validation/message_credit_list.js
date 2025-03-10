/*
It is used to one of which is user input validation.
MessageCreditList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare MessageCreditList object 
const MessageCreditList = Joi.object().keys({
  // Object Properties are define    
  user_id: Joi.string().optional().label("User Id"),
  message_count: Joi.string().optional().label("Message Count"),
  date_filter: Joi.string().optional().label("Date Filter"),
}).options({ abortEarly: false });
// To exports the MessageCreditList module
module.exports = MessageCreditList

