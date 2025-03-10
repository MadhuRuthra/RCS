/*
It is used to one of which is user input validation.
FilterCampName function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare check_available_msg object 
const rppayment_usrsmscrd_id = Joi.object().keys({
  // Object Properties are define  
  usrsmscrd_id: Joi.string().required().label("User Sms Credit Id"),

}).options({ abortEarly: false });
// To exports the check_available_msg module
module.exports = rppayment_usrsmscrd_id


