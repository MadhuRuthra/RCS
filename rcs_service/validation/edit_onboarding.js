/*
It is used to one of which is user input validation.
EditOnboarding function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 28-Jul-2023
*/

// Import the required packages and libraries
const Joi = require("@hapi/joi");

// To declare EditOnboarding object
const EditOnboarding = Joi.object().keys({
  // Object Properties are define
  user_id: Joi.string().optional().label("User Id"),
  clientname_txt: Joi.string().required().label("Client Name"),
  contact_person_txt: Joi.string().required().label("Contact person"),
  designation_txt: Joi.string().required().label("Designation"),
  mobile_no_txt: Joi.string().required().label("Contact no"),
  email_id_contact: Joi.string().required().label("Contact Email ID"),

  bill_address: Joi.string().required().label("Billing Address & GST"),
  business_name: Joi.string().required().label("Name of the business"),
  cpy_website_details: Joi.string().required().label("Company website details"),
  parent_company_name: Joi.string().required().label("Parents company name"),
  cpy_display_name: Joi.string().required().label("Company display name (To be configured)"),

  desp_business_txt: Joi.string().required().label("Description of the business"),
  reg_add_business: Joi.string().required().label("Registered Address of the business"),
  email_id_txt: Joi.string().required().label("Email ID"),
  contact_no_cmpy: Joi.string().required().label("Contact no"),
  profile_display_pic: Joi.string().required().label("Profile/ Display picture of the business"),

  slt_service_category: Joi.string().required().label("Select business category"),
  sender_id_txt: Joi.string().required().label("Sender ID"),
  sender_id_txt_1_txt: Joi.string().required().label("Sender ID- 1 (Communication)"),
  sender_id_txt_2_txt: Joi.string().required().label("Sender ID 2 (Communication)"),
  type_of_message: Joi.string().required().label("Type of message"),

  otp_in_process: Joi.string().required().label("Opt-in process"),
  enquiry_approve_txt: Joi.string().required().label("Enquiry/ Approval page"),
  privacy_terms_txt: Joi.string().required().label("Privacy & terms of service page"),
  terms_condition_txt: Joi.string().required().label("Terms & Condition page"),
  proof_document_slt: Joi.string().required().label("Proof of document"),

  proof_document: Joi.string().required().label("Proof Upload PDF"),
  expected_volumes_day: Joi.string().required().label("Expected volumes for day")
}).options({ abortEarly: false });

// To exports the EditOnboarding module
module.exports = EditOnboarding

