/*
It is used to one of which is user input validation.
UplaodMedia function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare UplaodMedia object
const UplaodMedia = Joi.object().keys({
  // Object Properties are define   
  user_id: Joi.string().optional().label("User Id"),
  media_data: Joi.string().required().label("Media Data"),
  media_type: Joi.string().required().label("Media Type"),
}).options({ abortEarly: false });
// To exports the UplaodMedia module
module.exports = UplaodMedia

