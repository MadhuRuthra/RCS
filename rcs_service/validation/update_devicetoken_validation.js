/*
It is used to one of which is user input validation.
update_deviceToken function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare update_deviceToken object
const update_deviceToken = Joi.object().keys({
  // Object Properties are define     
  user_id: Joi.string().optional().label("User Id"),
  device_token: Joi.string().required().label("Device Token"),
}).options({ abortEarly: false });
// To exports the update_deviceToken module
module.exports = update_deviceToken
