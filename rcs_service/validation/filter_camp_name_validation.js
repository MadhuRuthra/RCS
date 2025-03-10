/*
It is used to one of which is user input validation.
FilterCampName function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare FilterCampName object 
const FilterCampName = Joi.object().keys({
  // Object Properties are define
filter_user : Joi.string().optional().label("Filter User"),
   filter_department : Joi.string().optional().label("Filter Department"),  
  user_id: Joi.string().optional().label("User Id"),
  filter_date: Joi.string().required().label("Filter Date")
}).options({ abortEarly: false });
// To exports the FilterCampName module
module.exports = FilterCampName

