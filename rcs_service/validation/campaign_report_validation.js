/*
It is used to one of which is user input validation.
CampaignReport function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare CampaignReport object 
const CampaignReport = Joi.object().keys({
  user_id: Joi.string().optional().label("User Id"),
  campaign_name: Joi.string().required().label("Campaign Name"),
  mobile_number: Joi.array().optional().label("Mobile Number"),
}).options({ abortEarly: false });
// To exports the CampaignReport module
module.exports = CampaignReport

