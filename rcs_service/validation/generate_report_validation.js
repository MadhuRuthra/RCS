/*
It is used to one of which is user input validation.
ChangePassword function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare ChangePassword object
const GenerateReport = Joi.object().keys({
  // Object Properties are define
  compose_user_id: Joi.string().required().label("User Id"),
  compose_id: Joi.string().required().label("Compose Id"),
  user_id: Joi.string().optional().label("User Id")

}).options({ abortEarly: false });
// To exports the ChangePassword module
module.exports = GenerateReport
