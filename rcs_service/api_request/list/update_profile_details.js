/*
This api has chat API functions which is used to connect the mobile chat.
This page is act as a Backend page which is connect with Node JS API and PHP Frontend.
It will collect the form details and send it to API.
After get the response from API, send it back to Frontend.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
require("dotenv").config();
const main = require('../../logger');
// UpdateProfileDetails- start
async function UpdateProfileDetails(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {
		// get all the req data
        var user_id = req.body.user_id;
        var clientname_txt = req.body.clientname_txt;
        var official_email = req.body.email_id_txt;
        var official_mobile_no = req.body.contact_no_cmpy;
        var reg_official_address_cpy = req.body.reg_add_business;
        var cont_person = req.body.contact_person_txt;
        var cont_designation = req.body.designation_txt;
        var cont_person_mobileno = req.body.mobile_no_txt;
        var cont_email = req.body.email_id_contact;
        var billing_address = req.body.bill_address;
        var company_name = req.body.business_name;
        var company_website = req.body.cpy_website_details;
        var parent_company_name = req.body.parent_company_name;
        var company_display_name = req.body.cpy_display_name;
        var description_business = req.body.desp_business_txt;
        var profile_image = req.body.profile_display_pic;
        var business_category = req.body.slt_service_category;
        var sender = req.body.sender_id_txt;
        let sender_id_2_txt = req.body.sender_id_2_txt;
        var sender1 = req.body.sender_id_txt_1_txt;
        var sender2 = req.body.sender_id_txt_2_txt;
        var message_type = req.body.type_of_message;
        var otp_process = req.body.otp_in_process;
        var enquiry_approval = req.body.enquiry_approve_txt;
        var privacy_terms = req.body.privacy_terms_txt;
        var terms_conditions = req.body.terms_condition_txt;
        var proof_doc_name = req.body.proof_document;
        var document_proof = req.body.proof_document_slt;
        var volume_day_expected = req.body.expected_volumes_day;
        var  update_query_value_1;
var update_query_values = '' ;
var update_query_value1 = '';

var update_profile_details1;
// query parameters

        if(clientname_txt){
            update_query_value1 += `user_name = '${clientname_txt}', login_id = '${clientname_txt}',`;
        }
        if(official_email){
            update_query_value1 += `user_email = '${official_email}',`;
        }
        if(official_mobile_no){
            update_query_value1 += `user_mobile = '${official_mobile_no}',`;
        }
	if(user_id != 1){
            update_query_value1 += `usr_mgt_status = 'N',`;
        }
        if(reg_official_address_cpy){
            update_query_value1 += `user_address = '${reg_official_address_cpy}',`;
        }
      logger_all.info("[UpdateProfileDetails query parameters] : " + JSON.stringify(req.body));

        if(update_query_value1){
            // UpdateProfileDetails to execute this query
          var update_user_man = `UPDATE user_management SET ${update_query_value1}`;
logger_all.info(update_user_man);
            update_query_value_1  = update_user_man.substring(0, update_user_man.length - 1); 
logger_all.info(update_query_value_1);
			logger_all.info("[Update query request - User details1] : " + `${update_query_value_1} WHERE user_id = ${user_id}`);
               update_profile_details1 = await db.query(`${update_query_value_1} WHERE user_id = ${user_id}`);
              logger_all.info("[Update query request - User details1] : " + JSON.stringify(update_profile_details1));
  // if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
        }
if(cont_person){
    update_query_values += `cont_person = '${cont_person}',`;
}
if(cont_designation){
    update_query_values += `cont_designation = '${cont_designation}',`;
}
if(cont_designation){
    update_query_values += `cont_designation = '${cont_designation}',`;
}
if(cont_person_mobileno){
    update_query_values += `cont_mobile_no = '${cont_person_mobileno}',`;
}
if(cont_email){
    update_query_values += `cont_email = '${cont_email}',`;
}
if(billing_address){
    update_query_values += `billing_address = '${billing_address}',`;
}
if(company_name){
    update_query_values += `company_name = '${company_name}',`;
}
if(company_website){
    update_query_values += `company_website = '${company_website}',`;
}
if(parent_company_name){
    update_query_values += `parent_company_name = '${parent_company_name}',`;
}
if(company_display_name){
    update_query_values += `company_display_name = '${company_display_name}',`;
}
if(description_business){
    update_query_values += `description_business = '${description_business}',`;
}
if(profile_image){
    update_query_values += `profile_image = '${profile_image}',`;
}
if(business_category){
    update_query_values += `business_category = '${business_category}',`;
}
if(sender){
    update_query_values += `sender = '${sender}',`;
}
if(sender_id_2_txt){
    update_query_values += `sender2 = '${sender_id_2_txt}',`;
}
if(sender1){
    update_query_values += `sender_1 = '${sender1}',`;
}
if(sender2){
    update_query_values += `sender_2 = '${sender2}',`;
}
if(message_type){
    update_query_values += `message_type = '${message_type}',`;
}
if(otp_process){
    update_query_values += `opt_process = '${otp_process}',`;
}
if(enquiry_approval){
    update_query_values += `enquiry_approval = '${enquiry_approval}',`;
}
if(privacy_terms){
    update_query_values += `privacy_terms = '${privacy_terms}',`;
}
if(terms_conditions){
    update_query_values += `terms_conditions = '${terms_conditions}',`;
}
if(document_proof){
    update_query_values += `document_proof = '${document_proof}',`;
}
if(proof_doc_name){
    update_query_values += `proof_doc_name = '${proof_doc_name}',`;
}
if(volume_day_expected){
    update_query_values += `volume_day_expected = '${volume_day_expected}',`;
}

var  update_user_details = `UPDATE user_details SET updated_date = CURRENT_TIMESTAMP,${update_query_values}`;
logger_all.info(update_user_details);
 
update_query_values_1 = update_user_details.substring(0, update_user_details.length - 1);
logger_all.info(update_query_values_1);
// var update_query_values_1 = update_query_values.replace(",", "");

// UpdateProfileDetails to execute this query
logger_all.info("[Update query request - User details1] : " + ` ${update_query_values_1}  WHERE user_id = ${user_id} and user_details_id = ${user_id}`);
			const update_profile_details = await db.query(`${update_query_values_1}  WHERE user_id = ${user_id} and user_details_id = ${user_id}`);
			logger_all.info("[Update query request - User details] : " + JSON.stringify(update_profile_details));

// if the get_available_message length is not available to send the no available data.otherwise it will be return the get_available_message details.
			if (update_profile_details || update_profile_details1) {
                return { response_code: 1, response_status: 200, num_of_rows: 1, response_msg: 'Success' };
				
			}			else {
				return { response_code: 1, response_status: 204, response_msg: 'No data available' };
			}
	
}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[UpdateProfileDetails failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// UpdateProfileDetails - end

// using for module exporting
module.exports = {
	UpdateProfileDetails,
}



