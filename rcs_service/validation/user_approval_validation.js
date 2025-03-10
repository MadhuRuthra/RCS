/*
It is used to one of which is user input validation.
UserApproval function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare UserApproval object
const UserApproval = Joi.object().keys({
  // Object Properties are define    
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),
  mobile_number: Joi.string().required().label("Mobile Number"),
  phone_number_id: Joi.string().length(15).required().label("Phone Number Id"),
  rcs_business_acc_id: Joi.string().length(15).required().label("rcs Business Acc Id"),
  bearer_token: Joi.string().required().label("Bearer Token"),
}).options({ abortEarly: false });
// To exports the UserApproval module
module.exports = UserApproval
