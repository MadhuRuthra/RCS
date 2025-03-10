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

// TemplateList function - start
async function TemplateList(req) {
	var logger_all = main.logger_all
    var logger = main.logger
	try {

		const header_token = req.headers['authorization'];

		 // declare the variables
		var user_id, user_master_id;
		var filter;
		 // To initialize a variable with an empty string value
		var prntid = '';
		var get_template_list = '';
		var get_template_list1 = ``;
		filter = ``;
		// get all the req filter data
		var template_name_filter = req.body.template_name_filter;
		var template_category_filter = req.body.template_category_filter;
		var sender_id_filter = req.body.sender_id_filter;
		var status_filter = req.body.status_filter;
		var approve_date_filter = req.body.approve_date_filter;
		var template_id_filter = req.body.template_id_filter;
		var entry_date_filter = req.body.entry_date_filter;
		// query parameters
		logger_all.info("[Template List query parameters] : " + JSON.stringify(req.body));
		// To get the User_id
		var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
		if (req.body.user_id) {
			get_user = get_user + `and user_id = '${req.body.user_id}' `;
		}
		logger_all.info("[select query request] : " + get_user);
		const get_user_id = await db.query(get_user);
		logger_all.info("[select query response] : " + JSON.stringify(get_user_id));
               // If get_user not available send error response to client in ivalid token
		if (get_user_id.length == 0) {
			logger_all.info("Invalid Token")
			return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
		}
		else {// otherwise to get the user details
			user_id = get_user_id[0].user_id;
			user_master_id = get_user_id[0].user_master_id;
		}

		prntid = user_id;// admin - Dept Head are following this to get the parent id
		if (user_master_id == 3 || user_master_id == 2) {
			logger_all.info("[select query request] : " + `SELECT user_id FROM user_management where parent_id = '${user_id}' ORDER BY user_id ASC`)
			const get_parent_id = await db.query(`SELECT user_id FROM user_management where parent_id = '${user_id}' ORDER BY user_id ASC`);
			logger_all.info("[select query response] : " + JSON.stringify(get_parent_id))
			 // if number of get_parent_id length  is available then process the will be continued
                    // loop all the get the user ids to act as the parent ids.
			for (var i = 0; i < get_parent_id.length; i++) {
				prntid += ',' + get_parent_id[i].user_id;
			}
		}

		if (user_master_id == 4) {// user_master_id is '4' is following this to get the parent id
			if (template_name_filter) { // template_name_filter using

				filter = ` where tmp.template_name Like '%${template_name_filter}%' `;
			}

			if (template_id_filter) { // template_id_filter using

				filter = ` where and tmp.unique_template_id Like '%${template_id_filter}%' `;
			}
			if (template_category_filter) { //template_category_filter using

				filter = ` where tmp.template_category Like '%${template_category_filter}%' `;
			}

			if (sender_id_filter) { // sender_id_filter using

				filter = ` where concat(cnf.country_code,cnf.mobile_no) Like '%${sender_id_filter}%' `;
			}
			if (status_filter) { //status_filter using
				switch (status_filter != '') {
					case (status_filter.toLowerCase() == 'approved'):
						filter = ` where tmp.template_status = 'Y' `;
						break;

					case (status_filter.toLowerCase() == 'inactive'):

						filter = ` where tmp.template_status = 'N' `;
						break;

					case (status_filter.toLowerCase() == 'rejected'):

						filter = ` where tmp.template_status = 'R' `;
						break;

					case (status_filter.toLowerCase() == 'failed'):

						filter = ` where tmp.template_status = 'F' `;
						break;
					case (status_filter.toLowerCase() == 'waiting'):

						filter = ` where tmp.template_status = 'S' `;
						break;
					default:
						filter = ` where tmp.template_status = 'G' `;
						break;
				}
			}

			if (approve_date_filter) { //approve date filter using
                          // date function loop the date in one by one.
				filter_date_1 = approve_date_filter.split("-");
				filter_date_first = Date.parse(filter_date_1[0]);
				filter_date_second = Date.parse(filter_date_1[1]);
				function dateRange(startDate, endDate, steps = 1) {
					const dateArray = [];
					let currentDate = new Date(startDate);

					while (currentDate <= new Date(endDate)) {
						dateArray.push(new Date(currentDate));

						function convert(dates) {
							var date = new Date(dates),
								mnth = ("0" + (date.getMonth() + 1)).slice(-2),
								day = ("0" + date.getDate()).slice(-2);
							return [date.getFullYear(), mnth, day].join("-");
						}
						slt_date = convert(currentDate);
						get_template_list1 += ` SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates,tmp.approve_date FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}') and (date(tmp.approve_date) BETWEEN '${slt_date}' and '${slt_date}') UNION`;

						// Use UTC date to prevent problems with time zones and DST
						currentDate.setUTCDate(currentDate.getUTCDate() + steps);
					}
					return dateArray;

				}
				const dates = dateRange(filter_date_1[0], filter_date_1[1]);

				var lastIndex = get_template_list1.lastIndexOf(" ");
				query_select = get_template_list1.substring(0, lastIndex);
				logger_all.info("[select query request] : " + query_select);
				get_template_list = await db.query(query_select + ` order by approve_date desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))

			}
			else if (entry_date_filter) { // entry_date_filter using
				// date function using loop the date in one by one.
				filter_date_1 = entry_date_filter.split("-");
				filter_date_first = Date.parse(filter_date_1[0]);
				filter_date_second = Date.parse(filter_date_1[1]);
				function dateRange(startDate, endDate, steps = 1) {
					const dateArray = [];
					let currentDate = new Date(startDate);

					while (currentDate <= new Date(endDate)) {
						dateArray.push(new Date(currentDate));
						function convert(dates) {
							var date = new Date(dates),
								mnth = ("0" + (date.getMonth() + 1)).slice(-2),
								day = ("0" + date.getDate()).slice(-2);
							return [date.getFullYear(), mnth, day].join("-");
						}
						slt_date = convert(currentDate);
						get_template_list1 += `SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates,tmp.template_entdate, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}') (date(tmp.template_entdate) BETWEEN '${slt_date}' and '${slt_date}') UNION`;

						// Use UTC date to prevent problems with time zones and DST
						currentDate.setUTCDate(currentDate.getUTCDate() + steps);
					}
					return dateArray;

				}
				const dates = dateRange(filter_date_1[0], filter_date_1[1]);

				var lastIndex = get_template_list1.lastIndexOf(" ");
				query_select = get_template_list1.substring(0, lastIndex);

				get_template_list = await db.query(query_select + ` order by template_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))
			}
			else {
                               // otherwise it will be executed the get_template_list query
				logger_all.info("[select query request] : " + `SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}') ${filter} order by tmp.template_entdate desc`);

				get_template_list = await db.query(`SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}') ${filter} order by tmp.template_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))

			}
		} // admin - Dept Head are following this to get the parent id
		else if (user_master_id == 3 || user_master_id == 2) {

			if (template_name_filter) { // template_name_filter using

				filter = ` and tmp.template_name Like '%${template_name_filter}%' `;
			}

			if (template_id_filter) { //template_id_filter using

				filter = ` and tmp.unique_template_id Like '%${template_id_filter}%' `;
			}
			if (template_category_filter) { //template_category_filter using

				filter = ` and tmp.template_category Like '%${template_category_filter}%' `;
			}

			if (sender_id_filter) { //sender_id_filter using

				filter = ` and concat(cnf.country_code,cnf.mobile_no) Like '%${sender_id_filter}%' `;
			}
			if (status_filter) { //status_filter using

				switch (status_filter != '') {
					case (status_filter.toLowerCase() == 'approved'):
						filter = ` and tmp.template_status = 'Y' `;
						break;

					case (status_filter.toLowerCase() == 'inactive'):

						filter = ` and tmp.template_status = 'N' `;
						break;

					case (status_filter.toLowerCase() == 'rejected'):

						filter = ` and tmp.template_status = 'R' `;
						break;

					case (status_filter.toLowerCase() == 'failed'):

						filter = ` and tmp.template_status = 'F' `;
						break;
					case (status_filter.toLowerCase() == 'waiting'):

						filter = ` and tmp.template_status = 'S' `;
						break;
					default:
						filter = ` and tmp.template_status = 'G' `;
						break;
				}

			}

			if (approve_date_filter) { //approve_date_filter using
                         // date function loop the date in one by one.
				filter_date_1 = approve_date_filter.split("-");
				filter_date_first = Date.parse(filter_date_1[0]);
				filter_date_second = Date.parse(filter_date_1[1]);
				function dateRange(startDate, endDate, steps = 1) {
					const dateArray = [];
					let currentDate = new Date(startDate);

					while (currentDate <= new Date(endDate)) {
						dateArray.push(new Date(currentDate));

						function convert(dates) {
							var date = new Date(dates),
								mnth = ("0" + (date.getMonth() + 1)).slice(-2),
								day = ("0" + date.getDate()).slice(-2);
							return [date.getFullYear(), mnth, day].join("-");
						}
						slt_date = convert(currentDate);
						get_template_list1 += ` SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates,tmp.approve_date FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}' or crev.parent_id in ('${prntid}') ) and (date(tmp.approve_date) BETWEEN '${slt_date}' and '${slt_date}') UNION`;

						// Use UTC date to prevent problems with time zones and DST
						currentDate.setUTCDate(currentDate.getUTCDate() + steps);
					}
					return dateArray;

				}
				const dates = dateRange(filter_date_1[0], filter_date_1[1]);

				var lastIndex = get_template_list1.lastIndexOf(" ");
				query_select = get_template_list1.substring(0, lastIndex);
				logger_all.info("[select query request] : " + query_select);
				get_template_list = await db.query(query_select + ` order by approve_date desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))

			}
			else if (entry_date_filter) { //entry_date_filter using
				// date function using loop the date in one by one.
				filter_date_1 = entry_date_filter.split("-");
				filter_date_first = Date.parse(filter_date_1[0]);
				filter_date_second = Date.parse(filter_date_1[1]);
				function dateRange(startDate, endDate, steps = 1) {
					const dateArray = [];
					let currentDate = new Date(startDate);

					while (currentDate <= new Date(endDate)) {
						dateArray.push(new Date(currentDate));
						function convert(dates) {
							var date = new Date(dates),
								mnth = ("0" + (date.getMonth() + 1)).slice(-2),
								day = ("0" + date.getDate()).slice(-2);
							return [date.getFullYear(), mnth, day].join("-");
						}
						slt_date = convert(currentDate);
						get_template_list1 += ` SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates,tmp.template_entdate, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}' or crev.parent_id in ('${prntid}') ) and (date(tmp.template_entdate) BETWEEN '${slt_date}' and '${slt_date}') UNION`;

						// Use UTC date to prevent problems with time zones and DST
						currentDate.setUTCDate(currentDate.getUTCDate() + steps);
					}
					return dateArray;

				}
				const dates = dateRange(filter_date_1[0], filter_date_1[1]);

				var lastIndex = get_template_list1.lastIndexOf(" ");
				query_select = get_template_list1.substring(0, lastIndex);

				get_template_list = await db.query(query_select + ` order by template_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))
			}
			else { // otherwise it will be executed

				logger_all.info("[select query request] : " + `SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}' or crev.parent_id in ('${prntid}') ) ${filter} order by tmp.template_entdate desc`);

				get_template_list = await db.query(`SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (crev.user_id = '${user_id}' or crev.parent_id in ('${prntid}') ) ${filter} order by tmp.template_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))

			}
		} else {
			if (template_name_filter) { //template_name_filter using

				filter = ` where tmp.template_name Like '%${template_name_filter}%' `;
			}

			if (template_id_filter) { //template_id_filter using

				filter = ` where tmp.unique_template_id Like '%${template_id_filter}%' `;
			}
			if (template_category_filter) { // template_category_filter using

				filter = ` where tmp.template_category Like '%${template_category_filter}%' `;
			}

			if (sender_id_filter) { //sender_id_filter using

				filter = ` where concat(cnf.country_code,cnf.mobile_no) Like '%${sender_id_filter}%' `;
			}
			if (status_filter) { //status_filter using
				switch (status_filter != '') {
					case (status_filter.toLowerCase() == 'approved'):
						filter = ` where tmp.template_status = 'Y' `;
						break;

					case (status_filter.toLowerCase() == 'inactive'):

						filter = ` where tmp.template_status = 'N' `;
						break;

					case (status_filter.toLowerCase() == 'rejected'):

						filter = ` where tmp.template_status = 'R' `;
						break;

					case (status_filter.toLowerCase() == 'failed'):

						filter = ` where tmp.template_status = 'F' `;
						break;
					case (status_filter.toLowerCase() == 'waiting'):

						filter = ` where tmp.template_status = 'S' `;
						break;
					default:
						filter = ` where tmp.template_status = 'G' `;
						break;
				}

			}

			if (approve_date_filter) { //approve_date_filter using
              // date function loop the date in one by one.
				filter_date_1 = approve_date_filter.split("-");
				filter_date_first = Date.parse(filter_date_1[0]);
				filter_date_second = Date.parse(filter_date_1[1]);
				function dateRange(startDate, endDate, steps = 1) {
					const dateArray = [];
					let currentDate = new Date(startDate);

					while (currentDate <= new Date(endDate)) {
						dateArray.push(new Date(currentDate));

						function convert(dates) {
							var date = new Date(dates),
								mnth = ("0" + (date.getMonth() + 1)).slice(-2),
								day = ("0" + date.getDate()).slice(-2);
							return [date.getFullYear(), mnth, day].join("-");
						}
						slt_date = convert(currentDate);
						get_template_list1 += ` SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates,tmp.approve_date FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (date(tmp.approve_date) BETWEEN '${slt_date}' and '${slt_date}') UNION`;

						// Use UTC date to prevent problems with time zones and DST
						currentDate.setUTCDate(currentDate.getUTCDate() + steps);
					}
					return dateArray;

				}
				const dates = dateRange(filter_date_1[0], filter_date_1[1]);

				var lastIndex = get_template_list1.lastIndexOf(" ");
				query_select = get_template_list1.substring(0, lastIndex);
				logger_all.info("[select query request] : " + query_select);
				get_template_list = await db.query(query_select + ` order by approve_date desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))

			}
			else if (entry_date_filter) { //entry_date_filter using
				// date function using loop the date in one by one.
				filter_date_1 = entry_date_filter.split("-");
				filter_date_first = Date.parse(filter_date_1[0]);
				filter_date_second = Date.parse(filter_date_1[1]);
				function dateRange(startDate, endDate, steps = 1) {
					const dateArray = [];
					let currentDate = new Date(startDate);

					while (currentDate <= new Date(endDate)) {
						dateArray.push(new Date(currentDate));
						function convert(dates) {
							var date = new Date(dates),
								mnth = ("0" + (date.getMonth() + 1)).slice(-2),
								day = ("0" + date.getDate()).slice(-2);
							return [date.getFullYear(), mnth, day].join("-");
						}
						slt_date = convert(currentDate);
						get_template_list1 += ` SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates,tmp.template_entdate, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id where (date(tmp.template_entdate) BETWEEN '${slt_date}' and '${slt_date}') UNION`;

						// Use UTC date to prevent problems with time zones and DST
						currentDate.setUTCDate(currentDate.getUTCDate() + steps);
					}
					return dateArray;

				}
				const dates = dateRange(filter_date_1[0], filter_date_1[1]);

				var lastIndex = get_template_list1.lastIndexOf(" ");
				query_select = get_template_list1.substring(0, lastIndex);

				get_template_list = await db.query(query_select + ` order by template_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))
			}
			else {
// otherwise to the get_template_list it will be executed.
				logger_all.info("[select query request] : " + `SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id ${filter} order by tmp.template_entdate desc`);

				get_template_list = await db.query(`SELECT tmp.unique_template_id template_id,concat(cnf.country_code,cnf.mobile_no) mobile_no,tmp.template_name, tmp.template_category,lng.language_code, tmp.template_message, tmp.template_status, DATE_FORMAT(tmp.template_entdate,'%d-%m-%Y %h:%i:%s %p') template_entdates, DATE_FORMAT(tmp.approve_date,'%d-%m-%Y %h:%i:%s %p') approve_dates FROM message_template tmp left join user_management crt on tmp.created_user = crt.user_id left join master_language lng on lng.language_id = tmp.language_id left join rcs_config cnf on cnf.whatspp_config_id = tmp.rcs_config_id left join user_management crev on crev.user_id = cnf.user_id left join user_master ums on ums.user_master_id = crev.user_master_id left join master_countries ctr on ctr.id = cnf.country_id ${filter} order by tmp.template_entdate desc`);
				logger_all.info("[select query response] : " + JSON.stringify(get_template_list))
			}
		}
  // if theget_template_list length is '0' to send the no available data.otherwise it will be return the get_template_list details.
		if (get_template_list.length == 0) {
			return { response_code: 1, response_status: 204, response_msg: 'No data available' };
		}
		else {
		return { response_code: 1, response_status: 200, response_msg: 'Success', num_of_rows: get_template_list.length, templates: get_template_list };
		}

	}
	catch (e) { // any error occurres send error response to client
		logger_all.info("[Template List failed response] : " + e)
		return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
	}
}
// TemplateList - end

// using for module exporting
module.exports = {
	TemplateList
}
