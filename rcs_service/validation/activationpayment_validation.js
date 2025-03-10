
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

  user_name: Joi.string().optional().label("User Name"),
  user_id: Joi.string().optional().label("User Id"),
  user_mobile: Joi.string().optional().label("User Mobile"),
  user_email: Joi.string().optional().label("User Email"),
  product_name: Joi.string().optional().label("Product Name"),
  price: Joi.string().optional().label("Price"),
}).options({ abortEarly: false });

// To exports the Activation Payment module
module.exports = Activation_Payment


