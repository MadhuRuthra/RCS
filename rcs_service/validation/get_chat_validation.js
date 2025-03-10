/*
It is used to one of which is user input validation.
getChatValidation function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare getChatValidation object 
const getChatValidation = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  sender_id: Joi.string().required().label("Sender Id"),
  mobile_number: Joi.string().required().label("Mobile Number"),
}).options({ abortEarly: false });
// To exports the getChatValidation module
module.exports = getChatValidation
