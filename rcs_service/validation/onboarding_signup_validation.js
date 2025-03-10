/*
It is used to one of which is user input validation.
Signup function to validate the user.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const Joi = require("@hapi/joi");
// To declare Signup object
const Signup = Joi.object().keys({
  // Object Properties are define    
  request_id: Joi.string().required().label("Request ID"),
  slt_super_admin: Joi.string().required().label("Slt Super Admin"),
  slt_dept_admin: Joi.string().required().label("Slt Dept Admin"),
  login_shortname: Joi.string().required().label("Login ShortName"),
  user_password: Joi.string().required().label("User Password"),
  user_permission: Joi.string().required().label("User Permission"),

  client_name: Joi.string().required().label("Client Name"),
  official_email: Joi.string().required().label("official_email"),
  official_mobile_no: Joi.string().required().label("official_mobile_no"),
  reg_official_address_cpy: Joi.string().required().label("reg_official_address_cpy"),
  
  contact_person: Joi.string().required().label("contact_person"),
  contact_person_designation: Joi.string().required().label("contact_person_designation"),
  contact_person_mobileno: Joi.string().required().label("contact_person_mobileno"),
  contact_person_email_id: Joi.string().required().label("contact_person_email_id"),
  billing_address: Joi.string().required().label("billing_address"),
  company_name: Joi.string().required().label("company_name"),
  company_website: Joi.string().required().label("company_website"),
  parent_company_name: Joi.string().required().label("parent_company_name"),
  company_display_name: Joi.string().required().label("company_display_name"),
  description_business: Joi.string().required().label("description_business"),

  profile_image: Joi.string().required().label("profile_image"),
  business_category: Joi.string().required().label("business_category"),
  sender: Joi.string().required().label("sender"),
  sender_txt_2 :Joi.string().optional().label("sender_txt_2"),
  sender1: Joi.string().required().label("sender1"),
  sender2: Joi.string().required().label("sender2"),
  message_type: Joi.string().required().label("message_type"),
  otp_process: Joi.string().required().label("otp_process"),
  enquiry_approval: Joi.string().required().label("enquiry_approval"),
  privacy_terms: Joi.string().required().label("privacy_terms"),
  terms_conditions: Joi.string().required().label("terms_conditions"),
  proof_doc_name: Joi.string().required().label("doc_proof_name"),
  document_proof: Joi.string().required().label("document_proof"),
  volume_day_expected: Joi.string().required().label("volume_day_expected"),
  user_type: Joi.string().optional().label("User Type")

}).options({ abortEarly: false });
// To exports the Signup module
module.exports = Signup


