/*
It is used to one of which is user input validation.
MobileReport function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare MobileReport object 
const MobileReport = Joi.object().keys({
  // Object Properties are define   
  user_id: Joi.string().optional().label("User Id"),
  mobile_number: Joi.number().required().label("Mobile Number"),

}).options({ abortEarly: false });
// To exports the MobileReport module
module.exports = MobileReport

