/*
It is used to one of which is user input validation.
Replymsg function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare Replymsg object
const Replymsg = Joi.object().keys({
  // Object Properties are define    
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),
  sender_id: Joi.string().required().label("Sender Mobile"),
  // receiver_mobile: Joi.string().required().label("Receiver Mobile"),
  reply_msg: Joi.string().required().label("Reply Message"),
}).options({ abortEarly: false });
// To exports the Replymsg module
module.exports = Replymsg
