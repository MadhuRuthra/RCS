/*
It is used to one of which is user input validation.
InsertSchema function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare InsertSchema object 
const InsertSchema = Joi.object().keys({
  // Object Properties are define      
  table_name: Joi.string().required().label("Table Name"),
  values: Joi.string().required().label("Values"),
  variables: Joi.string().optional().label("Variables"),
}).options({ abortEarly: false });
// To exports the InsertSchema module
module.exports = InsertSchema
