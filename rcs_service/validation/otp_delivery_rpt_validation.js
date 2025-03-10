/*
It is used to one of which is user input validation.
OTPDeliveryValidation function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare OTPDeliveryValidation object 
const OTPDeliveryValidation = Joi.object().keys({
  // Object Properties are define    
  response_date_filter: Joi.string().optional().label("Response Date Filter"),
  delivery_date_filter: Joi.string().optional().label("Delivery Date Filter"),
  read_date_filter: Joi.string().optional().label("Read Date Filter"),
  user_id: Joi.string().optional().label("User Id"),
  store_id_filter: Joi.string().optional().label("Store Id Filter"),
  sender_filter: Joi.string().optional().label("Sender Filter"),
  receiver_filter: Joi.string().optional().label("Receiver Filter"),
  status_filter: Joi.string().optional().label("Status Filter"),
  delivery_filter: Joi.string().optional().label("Delivery Filter"),
  read_filter: Joi.string().optional().label("Read Filter"),
  filter_department: Joi.string().optional().label("Filter Department"),
  filter_user: Joi.string().optional().label("Filter User"),
}).options({ abortEarly: false });
// To exports the OTPDeliveryValidation module
module.exports = OTPDeliveryValidation
