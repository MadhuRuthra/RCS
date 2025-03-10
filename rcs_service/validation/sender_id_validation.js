/*
It is used to one of which is user input validation.
ManageSenderIdValidation function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare ManageSenderIdValidation object
const ManageSenderIdValidation = Joi.object().keys({
  // Object Properties are define    
  request_id: Joi.string().required().label("Request ID"),
  user_id: Joi.string().optional().label("User Id"),
  mobile_filter: Joi.string().optional().label("Mobile Filter"),
  status_filter: Joi.string().optional().label("Status Filter"),
  entry_date_filter: Joi.string().optional().label("Entry Date Filter"),
  approve_date_filter: Joi.string().optional().label("User Id"),
  country_code: Joi.string().required().label("Country Code"),
  mobile_no: Joi.string().required().label("Mobile No"),
  profile_name: Joi.string().required().label("Profile Name"),
  profile_image: Joi.string().required().label("Profile Image"),
  service_category: Joi.string().required().label("Service Category"),
}).options({ abortEarly: false });
// To exports the ManageSenderIdValidation module
module.exports = ManageSenderIdValidation
