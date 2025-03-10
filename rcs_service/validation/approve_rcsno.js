/*
It is used to one of which is user input validation.
approve_rcsnoList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare approve_rcsnoList object 
const approve_rcsnoList = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  whatspp_config_status: Joi.string().required().label("Whatspp Config Status"),
  whatspp_config_id: Joi.string().required().label("rcs Config Id"),
}).options({ abortEarly: false });
// To exports the approve_rcsnoList module
module.exports = approve_rcsnoList


