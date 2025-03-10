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
const update_profile = Joi.object().keys({
  // Object Properties are define  
 request_id: Joi.string().required().label("Request ID"),  
  user_id: Joi.string().required().label("user_id"),
  clientname_txt: Joi.string().optional().label("Client Name"),
  email_id_txt: Joi.string().optional().label("official_email"),
  contact_no_cmpy: Joi.string().optional().label("official_mobile_no"),
  reg_add_business: Joi.string().optional().label("reg_official_address_cpy"),
  contact_person_txt: Joi.string().optional().label("contact_person"),
  designation_txt: Joi.string().optional().label("contact_person_designation"),
  mobile_no_txt: Joi.string().optional().label("contact_person_mobileno"),
  email_id_contact: Joi.string().optional().label("contact_person_email_id"),
  bill_address: Joi.string().optional().label("billing_address"),
  business_name: Joi.string().optional().label("company_name"),
  cpy_website_details: Joi.string().optional().label("company_website"),
  parent_company_name: Joi.string().optional().label("parent_company_name"),
  cpy_display_name: Joi.string().optional().label("company_display_name"),
  desp_business_txt: Joi.string().optional().label("description_business"),
  profile_display_pic: Joi.string().optional().label("profile_image"),
  slt_service_category: Joi.string().optional().label("business_category"),
  sender_id_txt: Joi.string().optional().label("sender"),
  sender_id_2_txt: Joi.string().optional().label("sender_id_2_txt"),
  sender_id_txt_1_txt: Joi.string().optional().label("sender1"),
  sender_id_txt_2_txt: Joi.string().optional().label("sender2"),
  type_of_message: Joi.string().optional().label("message_type"),
  otp_in_process: Joi.string().optional().label("otp_process"),
  enquiry_approve_txt: Joi.string().optional().label("enquiry_approval"),
  privacy_terms_txt: Joi.string().optional().label("privacy_terms"),
  terms_condition_txt: Joi.string().optional().label("terms_conditions"),
  proof_document_slt: Joi.string().optional().label("doc_proof_name"),
  proof_document: Joi.string().optional().label("document_proof"),
  expected_volumes_day: Joi.string().optional().label("volume_day_expected"),


}).options({ abortEarly: false });
// To exports the Signup module
module.exports = update_profile



