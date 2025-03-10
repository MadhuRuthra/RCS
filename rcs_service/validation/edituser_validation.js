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
const EditUser = Joi.object().keys({
  // Object Properties are define   
  user_id: Joi.string().optional().label("User Id"),
  user_name: Joi.string().required().label("User Name"),
  user_password: Joi.string().required().label("User Password"),
  user_mobile: Joi.string().required().label("User Mobile"),

}).options({ abortEarly: false });
// To exports the EditUser module
module.exports = EditUser



