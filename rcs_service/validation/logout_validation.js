/*
It is used to one of which is user input validation.
LogoutSchema function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare LogoutSchema object 
const LogoutSchema = Joi.object().keys({
    // Object Properties are define 
    request_id: Joi.string().required().label("Request ID"),
    user_id: Joi.string().optional().label("User Id"),
}).options({ abortEarly: false });
// To exports the LogoutSchema module
module.exports = LogoutSchema
