/*
It is used to one of which is user input validation.
BigQuerySchema function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare BigQuerySchema object 
const BigQuerySchema = Joi.object().keys({
  // Object Properties are define
  query: Joi.string().required().label("query"),

}).options({ abortEarly: false });
// To exports the BigQuerySchema module
module.exports = BigQuerySchema
