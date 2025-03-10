
/*
It is used to one of which is user input validation.
Activation Payment function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 28-Jul-2023
*/

// Import the required packages and libraries
const Joi = require("@hapi/joi");

// To declare Activation Payment object
const Activation_Payment = Joi.object().keys({
  // Object Properties are define
  user_mobile: Joi.string().optional().label("User Mobile"),
  user_email: Joi.string().optional().label("User Email"),
}).options({ abortEarly: false });

// To exports the Activation Payment module
module.exports = Activation_Payment


