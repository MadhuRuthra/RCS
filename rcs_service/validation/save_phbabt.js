/*
It is used to one of which is user input validation.
save_phbabt function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare save_phbabt object
const save_phbabt = Joi.object().keys({
  // Object Properties are define    
  user_id: Joi.string().optional().label("User Id"),
  whatspp_config_id: Joi.string().required().label("Whatspp Config Id"),
  phone_number_id: Joi.string().optional().label("Phone Number Id"),
  rcs_business_acc_id: Joi.string().optional().label("Whatspp Business Account Id"),
  bearer_token: Joi.string().optional().label("Bearer Token"),
}).options({ abortEarly: false });
// To exports the save_phbabt module
module.exports = save_phbabt


