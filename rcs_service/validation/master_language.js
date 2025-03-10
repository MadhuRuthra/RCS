/*
It is used to one of which is user input validation.
MasterLanguage function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare MasterLanguage object 
const MasterLanguage = Joi.object().keys({
// Object Properties are define     
  user_id: Joi.string().optional().label("User Id"),
}).options({abortEarly : false});
// To exports the MasterLanguage module
module.exports = MasterLanguage

