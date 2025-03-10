/*
It is used to one of which is user input validation.
AddMessageCredit function to validate the user

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare AddMessageCredit object 
const AddMessageCredit = Joi.object().keys({
  // Object Properties are define
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),
  parent_user: Joi.string().required().label("Parent User"),
  receiver_user: Joi.string().required().label("Receiver User"),
  message_count: Joi.string().required().label("Message Count"),
  credit_raise_id: Joi.string().optional().label("Credit Raised User Id"),
}).options({ abortEarly: false });
// To exports the AddMessageCredit module
module.exports = AddMessageCredit

