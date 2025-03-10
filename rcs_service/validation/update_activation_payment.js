
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
  payment_id: Joi.string().required().label("payment Id"),
  payment_status: Joi.string().required().label("Payment Status"),
  active_status: Joi.string().required().label("Active status"),
  payment_comments: Joi.string().required().label("Plan comments"),
}).options({ abortEarly: false });

// To exports the Activation Payment module
module.exports = Activation_Payment


