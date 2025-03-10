/*
It is used to one of which is user input validation.
MessengerViewResponse function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare MessengerViewResponse object 
const MessengerViewResponse = Joi.object().keys({
  // Object Properties are define     
  user_id: Joi.string().optional().label("User Id"),
  message_from: Joi.string().required().label("message from"),
  message_to: Joi.string().required().label("Message To"),
}).options({ abortEarly: false });
// To exports the MessengerViewResponse module
module.exports = MessengerViewResponse


