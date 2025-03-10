/*
It is used to one of which is user input validation.
UsersUserType function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare UsernameGenerate object
const UsernameGenerate = Joi.object().keys({
  // Object Properties are define  
  user_id: Joi.string().optional().label("User Id"),
  user_type: Joi.string().required().label("User Type"),
  super_admin: Joi.string().optional().label("Super Admin"),
  dept_admin: Joi.string().optional().label("Dept Admin"),
}).options({ abortEarly: false });
// To exports the UsernameGenerate module
module.exports = UsernameGenerate

