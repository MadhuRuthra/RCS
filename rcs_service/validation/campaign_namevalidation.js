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
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  filter_user: Joi.string().optional().label("Filter User"),
  filter_date: Joi.string().optional().label("Filter Date"),
  campaign_name: Joi.array().required().label("Campaign Name"),
  filter_department: Joi.string().optional().label("Filter Department"),
}).options({ abortEarly: false });
// To exports the CampaignReport module
module.exports = CampaignReport

