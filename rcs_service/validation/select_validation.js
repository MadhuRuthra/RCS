/*
It is used to one of which is user input validation.
SelectSchema function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare SelectSchema object
const SelectSchema = Joi.object().keys({
  // Object Properties are define   
  table_name: Joi.string().required().label("Table Name"),
  parameters: Joi.string().required().label("Parameters"),
  where_condition: Joi.string().optional().label("Where Condition"),
  group_by: Joi.string().optional().label("Group By"),
  order_by: Joi.string().optional().label("Order By"),
  sort_by: Joi.string().optional().label("Sort By"),
  limit: Joi.string().optional().label("Limit"),
}).options({ abortEarly: false });
// To exports the SelectSchema module
module.exports = SelectSchema
