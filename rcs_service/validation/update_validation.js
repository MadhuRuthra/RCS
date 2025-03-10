/*
It is used to one of which is user input validation.
UpdateSchema function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare UpdateSchema object
const UpdateSchema = Joi.object().keys({
  // Object Properties are define    
  table_name: Joi.string().required().label("Table Name"),
  values: Joi.string().required().label("Values"),
  where_condition: Joi.string().required().label("Where Condition"),
}).options({ abortEarly: false });
// To exports the UpdateSchema module
module.exports = UpdateSchema
