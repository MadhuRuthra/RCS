/*
It is used to one of which is user input validation.
Signup function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare Signup object
const Signup = Joi.object().keys({
  // Object Properties are define 
  request_id: Joi.string().required().label("Request ID"),   
  user_id: Joi.string().optional().label("User Id"),
  user_type: Joi.string().required().label("User Type"),
  user_name: Joi.string().required().label("User Name"),
  user_email: Joi.string().required().label("User Email"),
  user_mobile: Joi.string().required().label("User Mobile"),
  login_shortname: Joi.string().required().label("Login ShortName"),
  user_password: Joi.string().required().label("User Password"),
  user_permission: Joi.string().required().label("User Permission"),

}).options({ abortEarly: false });
// To exports the Signup module
module.exports = Signup


