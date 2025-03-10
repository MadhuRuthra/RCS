/*
It is used to one of which is user input validation.
ManageUsersList function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare ManageUsersList object 
const ManageUsersList = Joi.object().keys({
    // Object Properties are define   
    date_filter: Joi.string().optional().label("Date Filter"),
    user_id: Joi.string().optional().label("User Id"),
    status_filter: Joi.string().optional().label("Status Filter"),
}).options({ abortEarly: false });
// To exports the ManageUsersList module
module.exports = ManageUsersList

