/*
It is used to one of which is user input validation.
ReadStatus function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare ReadStatus object
const ReadStatus = Joi.object().keys({
  // Object Properties are define   
  sender_no: Joi.string().required().label("Sender Number"),
  receiver_no: Joi.string().required().label("Receiver Number"),
}).options({ abortEarly: false });
// To exports the ReadStatus module
module.exports = ReadStatus
