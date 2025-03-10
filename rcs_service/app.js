/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This is a main page for starting API the process.This page to routing the subpages page and then process are executed.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const https = require("http");
const express = require("express");
const dotenv = require('dotenv');
dotenv.config();
// const router = express.Router();
var cors = require("cors");
var axios = require('axios');
// const csv = require("csv-stringify");
const fse = require('fs-extra');
const csv = require('csv-parser');
const nodemailer = require('nodemailer');

const mime = require('mime');
// Database Connections
const app = express();
const port = 10056;
const db = require("./db_connect/connect");
const dynamic_db = require("./db_connect/dynamic_connect");
const moment = require('moment');
const validator = require('./validation/middleware')

const bodyParser = require('body-parser');
const fs = require('fs');
const log_file = require('./logger')
const logger = log_file.logger;
const logger_all = log_file.logger_all;


const httpServer = https.createServer(app);
const io = require('socket.io')(httpServer, {
	cors: {
		origin: "*",
	},
});

// Process Validations
const bigqueryValidation = require("./validation/big_query_validation");
const composeMsgValidation = require("./validation/send_message_validation");
const createTemplateValidation = require("./validation/template_approval_validation");
const UserApprovalValidation = require("./validation/user_approval_validation");
const CreateCsvValidation = require("./validation/create_csv_validation");
const UpdateReportValidation = require("./validation/update_report_validation")
const ApproveTemplate = require('./validation/approvetemplate_validation')
const Logout = require("./logout/route");
const Login = require("./login/route");
const Template = require("./api_request/template/template_route");
const Message = require("./api_request/send_messages/send_message_route");
const Report = require("./api_request/report/report_route");
const List = require("./api_request/list/list_route");
const SenderId = require("./api_request/sender_id/sender_id_route");
const Chat = require("./api_request/chat/chat_route");
const DeviceId = require("./api_request/update_device_token/route_devicetoken");
const valid_user = require("./validation/valid_user_middleware");
const Upload = require("./api_request/upload/upload_route");
const dashboard = require("./api_request/dashboard/dashboard_route");
const getHeaderFile = require('./api_request/template/getHeader');

const testing = require('./api_request/testing/testing_route');
const env = process.env

const api_url = env.API_URL;
const media_bearer = env.MEDIA_BEARER;
const media_storage = env.MEDIA_STORAGE;

const DB_NAME = env.DB_NAME;
// Current Date and Time
// var today = new Date().toLocaleString("en-IN", {timeZone: "Asia/Kolkata"});
var day = new Date();

// Log file Generation based on the current date
var util = require('util');
const { get } = require("http");
const { date } = require("@hapi/joi");
var exec = require('child_process').exec;

app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(
	express.urlencoded({
		extended: true,
		limit: '50mb'
	})
);

// Allows you to send emits from express
app.use(function (request, response, next) {
	request.io = io;
	next();
});

app.get("/", async (req, res) => {
	logger_all.info(day)
	res.json({ message: "okkkk" });
});

app.post("/webhook", async function (request, response) {
	var logger_all = log_file.logger_all;
        logger_all.info(' Incoming webhook: ' + JSON.stringify(request.body));
	try {

		var message = request.body;
		switch (message.eventType) {
			case 'READ':

				break;

			case 'USER_REPLY':
				let rec_message = btoa(message.text)
				const insert_data = `INSERT INTO messenger_response VALUES(NULL,'0','${message.agentId}','${message.recipient}','-','${message.rcsId}','${message.communicationType}','${JSON.stringify(message)}','${rec_message}',NULL,NULL,NULL,NULL,NULL,NULL,'Y','Y',CURRENT_TIMESTAMP,'0000-00-00 00:00:00')`;

				logger_all.info(" [insert query request] : " + insert_data)

				const insert_unsupport = await db.query(insert_data);

				logger_all.info(" [insert query response] : " + JSON.stringify(insert_unsupport))
				request.io.emit("messenger_response",message.text);
				break;
			default:
		}
	}
	catch (e) {
		logger_all.info(" [receive message failed response] : " + e)
	}

	response.sendStatus(200);
});

function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}

// parse application/x-www-form-urlencoded
app.use(bodyParser.urlencoded({ extended: false }));

// parse application/json
app.use(bodyParser.json());

// API initialzation
app.use("/login", Login);
app.use("/template", Template);
app.use("/message", Message);
app.use("/report", Report);
app.use("/list", List);
app.use("/sender_id", SenderId);
app.use("/chat", Chat);
app.use("/devicetoken", DeviceId);
app.use("/logout", Logout);
app.use("/upload_media", Upload);
app.use("/dashboard", dashboard);

app.use("/test", testing);

// api for create a template
app.post("/create_template", validator.body(createTemplateValidation),
	valid_user, async (req, res) => {

		try {
			const logger_all = log_file.logger_all;

			var header_json = req.headers;
			let ip_address = header_json['x-forwarded-for'];

			// get current_year to generate a template name
			var current_year = day.getFullYear().toString();

			// get today's julian date to generate template name
			Date.prototype.julianDate = function () {
				var j = parseInt((this.getTime() - new Date('Dec 30,' + (this.getFullYear() - 1) + ' 23:00:00').getTime()) / 86400000).toString(),
					i = 3 - j.length;
				while (i-- > 0) j = 0 + j;
				return j
			};

			// get all the data from the api body and headers
			let api_bearer = req.headers.authorization;

			let communication_type = req.body.communicationType;
			let message_content = req.body.messageContent;
			let temp_label = req.body.templatelabel;
			let camp_type = req.body.campaigntype;
                         let media_type = req.body.media_type;
			var user_id;
			var user_short_name;
			var full_short_name;
			var user_master;
			var unique_id;
			var js1;
			const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
			logger_all.info("[insert query request] : " + insert_api_log);
			const insert_api_log_result = await db.query(insert_api_log);
			logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))

			const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
			logger_all.info("[select query request] : " + check_req_id);
			const check_req_id_result = await db.query(check_req_id);
			logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));

			if (check_req_id_result.length != 0) {

				logger_all.info("[failed response] : Request already processed");
				logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id }))

				var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);
				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result))

				return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });

			}

			// if req.body contains user_id we are checking both user_id and bearer token are valid and store some information like short_name for generate a template name
			if (req.body.user_id) {
				user_id = req.body.user_id;
				logger_all.info("[select query request] : " + `SELECT * FROM user_management WHERE bearer_token = '${api_bearer}' AND user_id = '${user_id}'`)
				const get_user_id = await db.query(`SELECT * FROM user_management WHERE bearer_token = '${api_bearer}' AND user_id = '${user_id}'`);
				logger_all.info("[select query response] : " + JSON.stringify(get_user_id))
				user_short_name = get_user_id[0].user_short_name;
				user_master = get_user_id[0].parent_id;

			}
			else {
				// if user_id not received in req.body we will get if using bearer token.
				logger_all.info("[select query request] : " + `SELECT * FROM user_management WHERE bearer_token = '${api_bearer}' AND usr_mgt_status = 'Y'`)
				const get_user_id = await db.query(`SELECT * FROM user_management WHERE bearer_token = '${api_bearer}' AND usr_mgt_status = 'Y'`);
				logger_all.info("[select query response] : " + JSON.stringify(get_user_id))

				user_id = get_user_id[0].user_id;
				user_short_name = get_user_id[0].user_short_name;
				user_master = get_user_id[0].parent_id;
			}

			// get the given user's master short name 
			logger_all.info("[select query request] : " + `SELECT usr1.user_short_name FROM user_management usr
			LEFT JOIN user_management usr1 on usr.parent_id = usr1.user_id
			WHERE usr.user_short_name = '${user_short_name}'`)
			const get_user_short_name = await db.query(`SELECT usr1.user_short_name FROM user_management usr
			LEFT JOIN user_management usr1 on usr.parent_id = usr1.user_id
			WHERE usr.user_short_name = '${user_short_name}'`);
			logger_all.info("[select query response] : " + JSON.stringify(get_user_short_name))

			// if nothing returns set given user's short_name as full_short_name
			if (get_user_short_name.length == 0) {
				full_short_name = user_short_name;
			}
			else {
				// if the given user is primary admin then no master shouldn't be there. so set given user's short_name as full_short_name
				if (user_master == 1 || user_master == '1') {
					full_short_name = user_short_name;
				}
				// concat the given user's master short_name in given user's short_name
				else {
					full_short_name = `${get_user_short_name[0].user_short_name}_${user_short_name}`;
				}
			}

			// get the unique_serial_number to generate unique template name
			logger_all.info("[select query request] : " + `SELECT unique_template_id FROM message_template ORDER BY template_id DESC limit 1`)
			const get_unique_id = await db.query(`SELECT unique_template_id FROM message_template ORDER BY template_id DESC limit 1`);
			logger_all.info("[select query response] : " + JSON.stringify(get_unique_id))

			// if nothing returns this is going to be a first template so make it as 001
			if (get_unique_id.length == 0) {
				unique_id = '001'
			}
			else {
				// get the serial_number of the latest template
				var serial_id = get_unique_id[0].unique_template_id.substr(get_unique_id[0].unique_template_id.length - 3)
				var temp_id = parseInt(serial_id) + 1;

				// add 0 as per our need
				if (temp_id.toString().length == 1) {
					unique_id = '00' + temp_id;
				}
				if (temp_id.toString().length == 2) {
					unique_id = '0' + temp_id;
				}
				if (temp_id.toString().length == 3) {
					unique_id = temp_id;
				}
			}

			const usedCampaignIds = new Set();

			function generateCampaignId() {
				const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
				let campaignId;

				do {
					campaignId = '';
					for (let i = 0; i < 10; i++) {
						const randomIndex = Math.floor(Math.random() * characters.length);
						campaignId += characters.charAt(randomIndex);
					}
				} while (usedCampaignIds.has(campaignId));

				usedCampaignIds.add(campaignId);

				// Add any additional formatting if needed
				return campaignId;
			}

			// Example usage:
			const template_id = generateCampaignId();

			// generate unique template name 
			let temp_name = `te_${full_short_name}_${current_year.substring(2)}${day.getMonth() + 1}${day.getDate()}_${unique_id}`;
			let unique_template_id = `tmplt_${full_short_name}_${new Date().julianDate()}_${unique_id}`;


			// check if the language is in our db 
			/* logger_all.info("[select query request] : " + `SELECT * from master_language WHERE language_code = '${language}' AND language_status = 'Y'`)
			const select_lang = await db.query(`SELECT * from master_language WHERE language_code = '${language}' AND language_status = 'Y'`);
			logger_all.info("[select query response] : " + JSON.stringify(select_lang))
 */
			// if (select_lang.length != 0) {
			// let messageObject = message_content[0]; 
			// let fullSquareBracketCount;
			// if (messageObject && messageObject.text) {
			// // Convert text to a string if necessary
			// let fullText = typeof messageObject.text === 'string' 
			// 	? messageObject.text 
			// 	: JSON.stringify(messageObject.text);

			// // Use regex to count both empty and non-empty square bracket pairs
			// fullSquareBracketCount = (fullText.match(/\[[^\]]*\]/g) || []).length;

			// logger_all.info('Number of square bracket pairs:', fullSquareBracketCount);
			// } else {
			// 	logger_all.info('messageObject.text is undefined or not available');
			// }


			// var message_text = message_content[0].text

			// const regex = /\[[^\]]*\]/g;

			// const matches = message_text.match(regex);
			// let body_count = matches ? matches.length : 0;

			// logger_all.info(body_count);

			let template_message = JSON.stringify(message_content);
			logger_all.info("________________", communication_type)
			if (communication_type === 'CAROUSEL') {
				// Parse the template message if it's in JSON string format
				// const js = JSON.parse(template_message); // Assuming template_message is a JSON string
				// logger_all.info(js);

				// // Initialize textArray to store all text values in CAROUSEL
				// const textArray = [];

				// // Loop through the outer array, check if each element is an array
				// js.forEach(innerArray => {
				// 	if (Array.isArray(innerArray)) {
				// 		innerArray.forEach(item => {
				// 			if (item.text) {
				// 				textArray.push(item.text);
				// 			}
				// 		});
				// 	} else {
				// 		// If it's not an array, check directly for a text property
				// 		if (innerArray.text) {
				// 			textArray.push(innerArray.text);
				// 		}
				// 	}
				// });

				// // Join the array of text values into a single string for CAROUSEL
				// js1 = textArray.join(", ");
				// logger_all.info("Extracted content for CAROUSEL as a string:", js1);
				js1= "";
			}

			else {
				if (!message_content[0] || !message_content[0].text) {

					// if error occurred send error response to the client
					logger_all.info("[template approval failed response] : text message missing")
					logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'text message missing ', request_id: req.body.request_id }))
					var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'text message missing' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
					logger.silly("[update query request] : " + log_update);
					const log_update_result = await db.query(log_update);
					logger.silly("[update query response] : " + JSON.stringify(log_update_result))

					return res.json({ response_code: 0, response_status: 201, response_msg: 'text message missing', request_id: req.body.request_id });
				}
				// Handle normal case where it's not CAROUSEL
				const js = JSON.parse(template_message); // Assuming template_message is a JSON string
				logger_all.info(js);

				// Get the text value for the normal case
				js1 = js[0]?.text || "";
				logger_all.info("Extracted content for normal case:", js1);
			}

			// Continue processing js1 regardless of template_category
			const regex = /\[[^\]]*\]/g;
			const matches = js1.match(regex);
			let body_count = matches ? matches.length : 0;

			logger_all.info(body_count);

			const extractedContent = js1.match(/\[([^\]]*)\]/g)?.map(match => match.slice(1, -1)) || [];
			logger_all.info("***********", extractedContent);

			const extractedContentStr = extractedContent.join(", ");
			logger_all.info("Extracted content as a string:", extractedContentStr);

			/*var msg_tmp = `INSERT INTO message_template VALUES(NULL,'-','${unique_template_id}','${temp_name}',0,'${communication_type}','${template_message}','-','${user_id}','N',CURRENT_TIMESTAMP,'0000-00-00 00:00:00','${body_count}',NULL, '-' ,'NULL' , '-', '${temp_label}','${camp_type}')`;
			logger_all.info(msg_tmp);
			// get the whatsapp business id, bearer token for the sender number from db
			logger_all.info("[insert query request] : " + msg_tmp);*/
                        let msg_tmp;
			if (media_type) {
				// Insert query when media_type is present
				msg_tmp = `INSERT INTO message_template VALUES(NULL, '-', '${unique_template_id}', '${temp_name}', 0, '${communication_type}', '${template_message}', '-', '${user_id}', 'N', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '${body_count}', NULL, '-', '${media_type}', '-', 
    '${temp_label}', '${camp_type}'
  				)`;
			} else {
				// Insert query when media_type is null
				msg_tmp = `INSERT INTO message_template VALUES(NULL, '-', '${unique_template_id}', '${temp_name}', 0, '${communication_type}', 
    '${template_message}', '-', '${user_id}', 'N', CURRENT_TIMESTAMP, '0000-00-00 00:00:00', '${body_count}', NULL, '-', NULL, '-', '${temp_label}', '${camp_type}')`;
			}
                         // get the whatsapp busilogger_all.infoness id, bearer token for the sender number from db
			logger_all.info("[insert query request] : " + msg_tmp);
			const insert_template = await db.query(msg_tmp);


			// Get the last insert ID
			const last_template_id = insert_template.insertId;
			logger_all.info("Last insert ID: " + last_template_id);
			var user_name_get = await dynamic_db.query(`SELECT user_name from rcs.user_management WHERE user_id = ${user_id}`);
			var user_name = user_name_get[0].user_name;

			logger_all.info("[insert query response] : " + JSON.stringify(insert_template));
			var log_update = `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
			logger.silly("[update query request] : " + log_update);
			const log_update_result = await db.query(log_update);
			logger.silly("[update query response] : " + JSON.stringify(log_update_result))
			res.json({ response_code: 1, response_status: 200, response_msg: 'Success ', request_id: req.body.request_id });

			let transporter = nodemailer.createTransport({
				// Configure your email service here (SMTP, Gmail, etc.)
				service: 'gmail',
				auth: {
					user: 'shanthini.m@yeejai.com', // Your email address
					pass: 'wsxnkyzsrkadpioy' // Your email password or app-specific password
				}
			});

			// Define email options
			let mailOptions = {
				from: 'shanthini.m@yeejai.com', // Sender's email address and name
				to: 'tech@yeejai.com', // Recipient's email addresses separated by commas
				subject: 'Alert: RCS - Template Created by User', // Email subject
				text: `Below Template Details:\n\nUser: ${user_name}\nTemplate ID: ${last_template_id}\nTemplate Name: ${temp_name}`
				// html: '<p>This is the HTML version of the email.</p>' // HTML body
			};

			// Send email
			transporter.sendMail(mailOptions, (error, info) => {
				if (error) {
					return logger_all.info('Error occurred:', error);
				}
				logger_all.info('Email sent:', info.response);
			});
			// }
			/* else {
				logger_all.info("[template approval failed number] :   language not available in DB")
				error_array.push({ reason: 'Language not available' })
				res.json({ response_code: 0, response_status: 201, response_msg: 'Language not available ',  request_id: req.body.request_id });

			} */
		}
		catch (e) {
			logger_all.info(e);
			// if error occurred send error response to the client
			logger_all.info("[template approval failed response] : " + e)
			logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Error occurred ', request_id: req.body.request_id }))

			var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Error occurred' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
			logger.silly("[update query request] : " + log_update);
			const log_update_result = await db.query(log_update);
			logger.silly("[update query response] : " + JSON.stringify(log_update_result))

			res.json({ response_code: 0, response_status: 201, response_msg: 'Error occurred ', request_id: req.body.request_id });

		}
	});

// api for to send compose_whatsapp_message
app.post("/compose_rcs_message", validator.body(composeMsgValidation),
	valid_user, async (req, res) => {
		try {

			var header_json = req.headers;
			let ip_address = header_json['x-forwarded-for'];

			var api_bearer = req.headers.authorization;
			var file_location = req.body.file_location;
			var template_id = req.body.template_id;
			var request_id = req.body.request_id;
			var total_mobileno_count = req.body.total_mobileno_count;
			var media_url = req.body.media_url;
			var user_id = req.body.user_id;

			var tmpl_name, whtsap_send, tmpl_lang;


			// declare and initialize all the required variables and array
			var user_id, store_id, full_short_name, user_master, template_variable_count, template_category, template_message, extractedContentStr;

			const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
			logger_all.info("[insert query request] : " + insert_api_log);
			const insert_api_log_result = await db.query(insert_api_log);
			logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))

			const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
			logger_all.info("[select query request] : " + check_req_id);
			const check_req_id_result = await db.query(check_req_id);
			logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));

			if (check_req_id_result.length != 0) {

				logger_all.info("[failed response] : Request already processed");
				logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id }))

				var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);
				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result))

				return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });

			}

			// get the available creidts of the user
			logger_all.info("[select query request] : " + `SELECT lim.available_messages,usr.user_id,usr.user_short_name,usr.parent_id,usr.user_name FROM user_management usr
		LEFT JOIN message_limit lim ON lim.user_id = usr.user_id
		WHERE usr.bearer_token = '${api_bearer}' AND usr.usr_mgt_status = 'Y'`)

			const check_available_credits = await db.query(`SELECT usr.user_master_id,lim.available_messages,usr.user_id,usr.user_short_name,usr.parent_id,usr.user_name FROM user_management usr
		LEFT JOIN message_limit lim ON lim.user_id = usr.user_id
		WHERE usr.bearer_token = '${api_bearer}' AND usr.usr_mgt_status = 'Y'`);
			logger_all.info("[select query response] : " + JSON.stringify(check_available_credits))

			// if credits is less than numbers of message to send then process will continued otherwise send a error response to the client
			if (check_available_credits[0].available_messages < total_mobileno_count) {
				logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Available credit not enough.', request_id: req.body.request_id }))

				var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Available credit not enough' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);
				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result))

				return res.json({ response_code: 0, response_status: 201, response_msg: 'Available credit not enough.', request_id: req.body.request_id });
			}


			// get the user_id, user's parent id and user shortname to generate campaign name
			user_id = check_available_credits[0].user_id;
			user_master = check_available_credits[0].parent_id;
			var user_master_id = check_available_credits[0].user_master_id;
			var user_short_name = check_available_credits[0].user_short_name;
			var user_name = check_available_credits[0].user_name
			var jsonArray = []
			// get the given user's master short name
			logger_all.info("[select query request] : " + `SELECT usr1.user_short_name FROM user_management usr
		LEFT JOIN user_management usr1 on usr.parent_id = usr1.user_id
		WHERE usr.user_short_name = '${user_short_name}'`)
			const get_user_short_name = await db.query(`SELECT usr1.user_short_name FROM user_management usr
		LEFT JOIN user_management usr1 on usr.parent_id = usr1.user_id
		WHERE usr.user_short_name = '${user_short_name}'`);
			logger_all.info("[select query response] : " + JSON.stringify(get_user_short_name))

			// if nothing returns set given user's short_name as full_short_name
			if (get_user_short_name.length == 0) {
				full_short_name = user_short_name;
			}
			else {
				// if the given user is primary admin then no master shouldn't be there. so set given user's short_name as full_short_name
				if (user_master == 1 || user_master == '1') {
					full_short_name = user_short_name;
				}
				// concat the given user's master short_name in given user's short_name
				else {
					full_short_name = `${get_user_short_name[0].user_short_name}_${user_short_name}`;
				}
			}

			// check if the template is available
			logger_all.info("[select query request] : " + `SELECT * FROM message_template 
		WHERE template_id = '${template_id}' AND template_status = 'Y'`)
			const check_variable_count = await db.query(`SELECT * FROM message_template 
		WHERE template_id = '${template_id}' AND template_status = 'Y'`);
			logger_all.info("[select query response] : " + JSON.stringify(check_variable_count))

			// if template not available send error response to the client
			if (check_variable_count.length == 0) {
				logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'template not available', request_id: req.body.request_id }))

				var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Template not available' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);
				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result))

				return res.json({ response_code: 0, response_status: 201, response_msg: 'template not available', request_id: req.body.request_id });
			}
			// if template available process will be continued
			else {
				whtsap_send = check_variable_count[0].template_message;
				unique_template_id = check_variable_count[0].unique_template_id;
				template_variable_count = check_variable_count[0].body_variable_count;
				template_category = check_variable_count[0].template_category;
				template_message = check_variable_count[0].template_message;
				// temp_id = check_variable_count[0].templateid;
				// get the template name and language from template id
				tmpl_name = check_variable_count[0].template_name;
				tem_id = check_variable_count[0].templateid;
				logger_all.info("______________________________")
				logger_all.info(template_message, template_category)
				// if request have store_id, store id will be received store id value
				if (req.body.store_id) {
					store_id = req.body.store_id;
				}
				// otherwise store id will be 0
				else {
					store_id = 0;
				}

				// get today's julian date to generate compose_unique_name
				Date.prototype.julianDate = function () {
					var j = parseInt((this.getTime() - new Date('Dec 30,' + (this.getFullYear() - 1) + ' 23:00:00').getTime()) / 86400000).toString(),
						i = 3 - j.length;
					while (i-- > 0) j = 0 + j;
					return j
				};

				// declare db name and tables_name
				var db_name = `rcs_${user_id}`;
				var table_names = [`compose_rcs_tmp_${user_id}`, `compose_rcs_status_tmpl_${user_id}`];

				var compose_unique_name;

				logger_all.info("[select query request] : " + `SELECT compose_rcs_id from ${table_names[0]} ORDER BY compose_rcs_id desc limit 1`)
				const select_compose_id = await dynamic_db.query(`SELECT compose_rcs_id from ${table_names[0]} ORDER BY compose_rcs_id desc limit 1`, null, `${db_name}`);
				logger_all.info("[select query response] : " + JSON.stringify(select_compose_id))
				// To select the select_compose_id length is '0' to create the compose unique name 
				if (select_compose_id.length == 0) {
					compose_unique_name = `ca_${full_short_name}_${new Date().julianDate()}_1`;
				}

				else { // Otherwise to get the select_compose_id using
					compose_unique_name = `ca_${full_short_name}_${new Date().julianDate()}_${select_compose_id[0].compose_rcs_id + 1}`;
				}

				const usedCampaignIds = new Set();

				function generateCampaignId() {
					const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
					let campaignId;

					do {
						campaignId = '';
						for (let i = 0; i < 10; i++) {
							const randomIndex = Math.floor(Math.random() * characters.length);
							campaignId += characters.charAt(randomIndex);
						}
					} while (usedCampaignIds.has(campaignId));

					usedCampaignIds.add(campaignId);

					// Add any additional formatting if needed
					return campaignId;
				}

				// Example usage:
				const campaign_id = generateCampaignId();
				logger_all.info(campaign_id); // Output: P9C2M4T5F6


				var insert_msg;

				var insert_msg = `INSERT INTO ${table_names[0]} VALUES(NULL,'${user_id}','${store_id}','1','-','-','[]','[]', '${tmpl_name}','TEXT','${total_mobileno_count}','1','${total_mobileno_count}','${compose_unique_name}','${campaign_id}',NULL,'${unique_template_id}', NULL ,'W',CURRENT_TIMESTAMP,'NULL',NULL,'${file_location}')`;

				logger_all.info("[insert query request] : " + insert_msg);
				const insert_compose = await dynamic_db.query(insert_msg, null, `${db_name}`);
				logger_all.info("[insert query response] : " + JSON.stringify(insert_compose))


				// Get the last insert ID
				const last_compose_id = insert_compose.insertId;
				logger_all.info("Last insert ID: " + last_compose_id);

				var insert_main_com = `INSERT INTO master_compose_rcs VALUES(NULL,'${last_compose_id}','${user_id}','${total_mobileno_count}','${compose_unique_name}','${campaign_id}','${tmpl_name}', '${tem_id}',0,'W',CURRENT_TIMESTAMP,NULL,NULL)`;
				logger_all.info("[insert query request] : " + insert_main_com);
				const insert_main_com_result = await db.query(insert_main_com)
				logger_all.info("[insert_main_com response] : " + JSON.stringify(insert_main_com_result))

				// Execute the update query outside the loop
				var message_update = `UPDATE message_limit SET available_messages = available_messages - ${total_mobileno_count} WHERE user_id = '${user_id}'`;
				logger_all.info(" [update query request] : " + message_update);
				const increase_limit = await db.query(message_update);
				logger_all.info(" [update query response] : " + JSON.stringify(increase_limit));
				logger_all.info("******************************");
				logger_all.info(user_name)
				let transporter = nodemailer.createTransport({
					// Configure your email service here (SMTP, Gmail, etc.)
					service: 'gmail',
					auth: {
						user: 'shanthini.m@yeejai.com', // Your email address
						pass: 'wsxnkyzsrkadpioy' // Your email password or app-specific password
					}
				});

				// Define email options
				let mailOptions = {
					from: 'shanthini.m@yeejai.com', // Sender's email address and name
					to: 'tech@yeejai.com', // Recipient's email addresses separated by commas
					subject: 'Alert: RCS - Campaign Created by User', // Email subject
					text: `Below Campaign Details:\n\nUser: ${user_name}\nCampaign ID: ${last_compose_id}\nCampaign Name: ${compose_unique_name}\nTemplate ID: ${tem_id}\nTemplate Name: ${tmpl_name}\nTotal Count: ${total_mobileno_count}` // Plain text body
					// html: '<p>This is the HTML version of the email.</p>' // HTML body
				};

				// Send email
				transporter.sendMail(mailOptions, (error, info) => {
					if (error) {
						return logger_all.info('Error occurred:', error);
					}
					logger_all.info('Email sent:', info.response);
				});

				var insert_summary = `INSERT INTO user_summary_report VALUES(NULL,'${user_id}','${last_compose_id}','${compose_unique_name}','${campaign_id}','${total_mobileno_count}','${total_mobileno_count}',0, 0, 0, 0, 0, "N", "N", CURRENT_TIMESTAMP, NULL, NULL)`;

				logger_all.info("[insert query request] : " + insert_summary);
				const insert_summary1 = await db.query(insert_summary);
				logger_all.info("[insert query response] : " + JSON.stringify(insert_summary1))


				var log_update = `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);

				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result));

				logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 1, response_status: 200, response_msg: 'Initiated', compose_id: compose_unique_name, request_id: req.body.request_id }))
				res.json({ response_code: 1, response_status: 200, response_msg: 'Initiated', compose_id: compose_unique_name, request_id: req.body.request_id });
			}
		}
		catch (e) {// any error occurres send error response to client
			logger_all.info("[Send msg failed response] : " + e)
			logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Something went wrong', request_id: req.body.request_id }))
			res.json({ response_code: 0, response_status: 201, response_msg: 'Something went wrong', request_id: req.body.request_id });
		}
	});

/*app.post('/approve_rcs', validator.body(ApproveTemplate),
	valid_user, async function (req, res, next) {
		try {
			var request_id = req.body.request_id;
			//var user_id = req.body.user_id;
			var campaign_id = req.body.campaign_id;
			var user_id = req.body.selected_user_id;

			var rec_count = 0;
			var total_count = 0;
			var insert_count = 1;
			var sender_numbers_active = [];
			var sender_numbers_inactive = [];
			var sender_id_active = [];
			var sender_devicetoken_active = [];
			var variable_value = [];
			let isFirstRow = true;
			is_check = false;
			const variable_values = [];
			const valid_mobile_numbers = [];
			const push_name = [];
			const push_name_and_values = [];
			const invalid_mobile_numbers = [];
			var media_url = [];
			const duplicateMobileNumbers = new Set();
			user_id_check = req.body.user_id;
			var body_variable_count, unique_template_id, rcs_template_id, template_category, template_message, insertId, media_type;
			var transactionIdString = [];
			var corelationId = [];
			//Check if user is admin
		//	if (user_id_check == 1) {
				//Query to get data based on compose id
				var get_campaign = `SELECT * FROM ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} where user_id = '${user_id}' AND compose_rcs_id = '${campaign_id}' AND rcs_status = 'W'`;
				logger_all.info("[select query request] : " + get_campaign);
				const get_user_det = await db.query(get_campaign);
				logger_all.info("[select query response] : " + JSON.stringify(get_user_det));

				// var get_compose_data = `SELECT * FROM ${DB_NAME}_${user_id}.compose_msg_media_${user_id} where compose_message_id = '${compose_message_id}'`;
				// logger_all.info("[select query request] : " + get_compose_data);
				// const get_compose_data_result = await db.query(get_compose_data);
				// logger_all.info("[select query response] : " + JSON.stringify(get_compose_data_result));

				//Check if selected data is equal to zero, send failuer response 'Compose ID Not Available'
				if (get_user_det.length == 0) {
					const update_api = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP,response_comments = 'Compose ID Not Available.' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
					logger_all.info("[update query request - compose ID not available] : " + update_api);
					const update_api_log = await db.query(update_api);
					logger_all.info("[update query response - compose ID not available] : " + JSON.stringify(update_api_log))
					const composeid_msg = { response_code: 0, response_status: 201, response_msg: 'Compose ID Not Available', request_id: req.body.request_id }
					logger.info("[API RESPONSE] " + JSON.stringify(composeid_msg))
					logger_all.info("[compose ID not available] : " + JSON.stringify(composeid_msg))

					return res.json(composeid_msg);
				}

				unique_template_id = get_user_det[0].unique_template_id;
				receiver_nos_path = get_user_det[0].receiver_nos_path;
				mobile_no_cnt = get_user_det[0].total_mobileno_count;
				message_type = get_user_det[0].message_type;
				//media_url = get_user_det[0].media_url;
				template_category = get_user_det[0].template_category
				logger_all.info(unique_template_id, receiver_nos_path, mobile_no_cnt, message_type);
				const get_count = `SELECT * FROM message_template where unique_template_id = '${unique_template_id}'`;
				const get_user_count = await db.query(get_count);
				logger_all.info("[select query response] : " + JSON.stringify(get_user_count));
				rcs_template_id = get_user_count[0].templateid;
				body_variable_count = get_user_count[0].body_variable_count;
				template_category = get_user_count[0].template_category;
				template_message = get_user_count[0].template_message
				media_url = get_user_count[0].media_url;
				media_type = get_user_count[0].media_type
				logger_all.info("***********************");
				logger_all.info(rcs_template_id, template_category, template_message, media_url, media_type);


				if (receiver_nos_path != '-') {

					// Fetch the CSV file
					await fs.createReadStream(receiver_nos_path)

						// Read the CSV file from the stream
						.pipe(csv({
							headers: false
						}))

						// Set headers to false since there are no column headers
						.on('data', (row) => {
							if (Object.values(row).every(value => value === '')) {
								return;
							}

							const firstColumnValue = row[0].trim();
							const name = row[1] ? row[1].trim() : '';
							valid_mobile_numbers.push(firstColumnValue)
							push_name.push(name)
							const otherValues = [];
							for (let i = 2; i < Object.keys(row).length; i++) { // Start from 2 to skip mobile number and name
								if (row[i]) {
									otherValues.push(row[i].trim());
								}
							}
							push_name_and_values.push([name, ...otherValues]);

						})
						.on('error', (error) => {
							console.error('Error:', error.message);
						})
						.on('end', async () => {
							logger_all.info(valid_mobile_numbers)
							logger_all.info(push_name_and_values)
							logger_all.info("**********************_______________________________")
							logger_all.info(body_variable_count, unique_template_id, media_url)


							// const insert_compose_rcs_status_tmpl= `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES (NULL,0,'-','number',NULL,'-','N',CURRENT_TIMESTAMP,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N')`;


							// logger_all.info(insert_compose_rcs_status_tmpl) ;
							// const get_insert_compose = await db.query(insert_compose_rcs_status_tmpl);
							// logger_all.info("[select query response] : " + JSON.stringify(get_insert_compose));
							//Insert compose details to compose_msg_status table
							var insert_numbers = `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES`;
							var insertId;
							var insertIdString = ""
							var numdate;
							var numDateArray = []
							var batchSize = 15000;
							//Loop for receiver numbers
							if (valid_mobile_numbers.length === 0) {
								logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Failed', request_id: req.body.request_id }))
								return res.json({ response_code: 0, response_status: 201, response_msg: 'Failed', request_id: req.body.request_id });


							}

							const update_status = `UPDATE master_compose_rcs SET rcs_status = "P" WHERE compose_rcs_id = '${campaign_id}' AND user_id = '${user_id}'AND rcs_status = "W"`;
							logger_all.info(update_status)
							const get_update_status = await db.query(update_status);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status));


							const update_status_master = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "P" WHERE compose_rcs_id = '${campaign_id}' AND rcs_status = "W" `;
							logger_all.info(update_status_master)
							const get_update_status_master = await db.query(update_status_master);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_master));
							for (var k = 0; k < valid_mobile_numbers.length; k) {

								if (k == valid_mobile_numbers.length) {
									break;
								}
								function getCurrentFormattedDate() {
									const now = new Date();
									const year = now.getFullYear();
									const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
									const day = String(now.getDate()).padStart(2, '0');
									const hours = String(now.getHours()).padStart(2, '0');
									const minutes = String(now.getMinutes()).padStart(2, '0');
									const seconds = String(now.getSeconds()).padStart(2, '0');
									return `${year}${month}${day}${hours}${minutes}${seconds}`;
								}
								const date = getCurrentFormattedDate();
								numdate = valid_mobile_numbers[k] + '_' + date
								numDateArray.push(numdate)
								//Insert compose data
								insert_numbers = insert_numbers + "" + `(NULL,'${campaign_id}',NULL,'${valid_mobile_numbers[k]}',NULL,'-','N',CURRENT_TIMESTAMP,NULL,NULL,NULL,'${numdate}',NULL,NULL,NULL,NULL,NULL,'N'),`;

								//check if insert_count is 1000, insert 1000 splits data
								if (insert_count == 1000) {
									insert_numbers = insert_numbers.substring(0, insert_numbers.length - 1)
									logger_all.info("[insert query request - insert numbers] : " + insert_numbers);
									var insert_numbers_result = await db.query(insert_numbers);
									logger_all.info("[insert query response - insert numbers] : " + JSON.stringify(insert_numbers_result))
									insert_count = 0;
									insert_numbers = `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES`;
								}
								insert_count = insert_count + 1;


								k++;


							}
							//After the loops complete, this if statement checks if any pending insert queries are left to be executed. If so, it executes
							if (insert_numbers !== `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES`) {
								insert_numbers = insert_numbers.substring(0, insert_numbers.length - 1); // Remove the trailing comma
								logger_all.info("[insert query request - insert numbers] : " + insert_numbers);
								var insert_numbers_result = await db.query(insert_numbers);
								logger_all.info("[insert query response - insert numbers] : " + JSON.stringify(insert_numbers_result));
								insertId = insert_numbers_result.insertId;
							}




							logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 1, response_status: 200, response_msg: 'Initiated', request_id: req.body.request_id }))
							res.json({ response_code: 1, response_status: 200, response_msg: 'Initiated', request_id: req.body.request_id });




							logger_all.info("****************___________****************")
							logger_all.info(insertId)

							const get_user_summary_report = `UPDATE user_summary_report SET total_waiting = '0' ,total_process = '${valid_mobile_numbers.length}' where user_id = '${user_id}' AND com_msg_id = '${campaign_id}'`;
							logger.silly("[select query response - get_user_summary_report ] : " + get_user_summary_report);
							const get_user_summary_report_log = await db.query(get_user_summary_report);
							logger.silly("[select query response - get_user_summary_report ] : " + JSON.stringify(get_user_summary_report_log))
							logger_all.info(get_user_summary_report)


							if (template_category === 'CAROUSEL') {
								js1 = "";
								// Parse the template message if it's in JSON string format
								// const js = JSON.parse(template_message); // Assuming template_message is a JSON string
								// logger_all.info(js);

								// // Initialize textArray to store all text values in CAROUSEL
								// const textArray = [];

								// // Loop through the outer array, check if each element is an array
								// js.forEach(innerArray => {
								// 	if (Array.isArray(innerArray)) {
								// 		innerArray.forEach(item => {
								// 			if (item.text) {
								// 				textArray.push(item.text);
								// 			}
								// 		});
								// 	} else {
								// 		// If it's not an array, check directly for a text property
								// 		if (innerArray.text) {
								// 			textArray.push(innerArray.text);
								// 		}
								// 	}
								// });

								// // Join the array of text values into a single string for CAROUSEL
								// js1 = textArray.join(", ");
								// logger_all.info("Extracted content for CAROUSEL as a string:", js1);

							} else {
								// Handle normal case where it's not CAROUSEL
								const js = JSON.parse(template_message); // Assuming template_message is a JSON string
								logger_all.info(js);

								// Get the text value for the normal case
								js1 = js[0]?.text || "";
								logger_all.info("Extracted content for normal case:", js1);
							}

							// Continue processing js1 regardless of template_category
							const regex = /\[[^\]]*\]/g;
							const matches = js1.match(regex);
							const extractedContent = js1.match(/\[([^\]]*)\]/g)?.map(match => match.slice(1, -1)) || [];
							logger_all.info("***********", extractedContent);

							const extractedContentStr = extractedContent.join(", ");
							logger_all.info("Extracted content as a string:", extractedContentStr);


							var msg_json = {
								"mode": "rcs",
								"rcsTemplateId": rcs_template_id,
								"campId": campaign_id,
								"unicode": false,
								"shortMessages": []
							};

							for (var k = 0; k < valid_mobile_numbers.length; k) {
								var contextjson = {}

								if (template_category === 'RICH CARD') {
									contextjson[`rcs_${media_type}`] = media_url
								}
								for (var i = 0; i < extractedContent.length; i++) {
									contextjson[`rcs_${extractedContent[i]}`] = push_name_and_values[k][i]
								}
								//logger_all.info(contextjson)

								msg_json.shortMessages.push({
									"recipient": valid_mobile_numbers[k],
									"corelationId": numDateArray[k],
									"context": contextjson
								});
								insertIdString = insertIdString + "','" + (insertId + (k))

								if (k % batchSize == 0 && k != 0) {

									logger_all.info(JSON.stringify(msg_json))
									let config = {
										method: 'post',
										maxBodyLength: Infinity,
										url: 'https://kapi.omni-channel.in/fe/api/v1/iPMessage/One2Many',
										headers: {
											'Content-Type': 'application/json',
											'Authorization': 'Basic eWVlamFpZGVtby50cmFuczpEZW1vQDEyMyQ='
										},
										data: msg_json
									};

									await axios.request(config)
										.then(async (response) => {
											var test = response.data
											for (var i = 0; i < test.submitResponses.length; i++) {
												transactionIdString.push(test.submitResponses[i].transactionId.toString());
												corelationId.push(test.submitResponses[i].corelationId.toString());
											}
											logger_all.info("Transaction IDs-----------------------------:", transactionIdString);
											logger_all.info("Corelation IDs------------------------------:", corelationId);

											insertIdString = insertIdString.substring(3);

											var update_rcs_tmp = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='S' WHERE comrcs_status_id IN ('${insertIdString}')`;
											logger.silly("[update query request] : " + update_rcs_tmp);
											const log_update_rcs_tmp = await db.query(update_rcs_tmp);
											logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp));
										})
										.catch(async (error) => {
											var update_rcs_tmp_err = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='F' , response_status = 'Y' , response_message = 'Failure' ,response_date = CURRENT_TIMESTAMP WHERE comrcs_status_id IN ('${insertIdString}')`;
											logger.silly("[update query request] : " + update_rcs_tmp_err);
											const log_update_rcs_tmp_err = await db.query(update_rcs_tmp_err);
											logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp_err));
											logger_all.info(error);
										});
									msg_json = {
										"mode": "rcs",
										"rcsTemplateId": rcs_template_id,
										"campId": campaign_id,
										"unicode": false,
										"shortMessages": []
									};
									insertIdString = ""
								}
								// Loop through the responses and extract transactionId and corelationId

								k++;
							}
							logger_all.info(JSON.stringify(msg_json))
							let config = {
								method: 'post',
								maxBodyLength: Infinity,
								url: 'https://kapi.omni-channel.in/fe/api/v1/iPMessage/One2Many',
								headers: {
									'Content-Type': 'application/json',
									'Authorization': 'Basic eWVlamFpZGVtby50cmFuczpEZW1vQDEyMyQ='
								},
								data: msg_json
							};

							await axios.request(config)
								.then(async (response) => {
									var test = response.data
									for (var i = 0; i < test.submitResponses.length; i++) {
										transactionIdString.push(test.submitResponses[i].transactionId.toString());
										corelationId.push(test.submitResponses[i].corelationId.toString());
									}
									logger_all.info("Transaction IDs-----------------------------:", transactionIdString);
									logger_all.info("Corelation IDs------------------------------:", corelationId);

									insertIdString = insertIdString.substring(3);

									var update_rcs_tmp = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='S' WHERE comrcs_status_id IN ('${insertIdString}')`;
									logger.silly("[update query request] : " + update_rcs_tmp);
									const log_update_rcs_tmp = await db.query(update_rcs_tmp);
									logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp));
								})
								.catch(async (error) => {
									var update_rcs_tmp_err = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='F' , response_status = 'Y' , response_message = 'Failure' ,response_date = CURRENT_TIMESTAMP WHERE comrcs_status_id IN ('${insertIdString}')`;
									logger.silly("[update query request] : " + update_rcs_tmp_err);
									const log_update_rcs_tmp_err = await db.query(update_rcs_tmp_err);
									logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp_err));
									logger_all.info(error);
								});


							async function updateMultipleRowsInBatches(transactionIdString, corelationId) {
								let totalRows = transactionIdString.length;
								let batchStartIndex = 0;

								while (batchStartIndex < totalRows) {
									// Determine the end index for the current batch
									let batchEndIndex = Math.min(batchStartIndex + batchSize, totalRows);

									// Extract the batch data for the current iteration
									const transactionBatch = transactionIdString.slice(batchStartIndex, batchEndIndex);
									const correlationBatch = corelationId.slice(batchStartIndex, batchEndIndex);

									// Construct the query for the current batch
									let caseStatements = '';
									let idsCondition = '';

									for (let i = 0; i < transactionBatch.length; i++) {
										const transactionId = transactionBatch[i];
										const correlationId = correlationBatch[i];

										// Construct the case statement for each row in the batch
										caseStatements += `WHEN corelation_id = '${correlationId}' THEN '${transactionId}' `;
										idsCondition += `'${correlationId}',`;
									}

									// Remove the trailing comma from idsCondition
									idsCondition = idsCondition.slice(0, -1);

									// Skip the query execution if caseStatements or idsCondition are empty
									if (caseStatements && idsCondition) {
										const updateQuery = `
                UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id}
                SET response_id = CASE ${caseStatements} END
                WHERE corelation_id IN (${idsCondition});
            `;

										logger.silly("[update batch query request] : " + updateQuery);
										logger_all.info(updateQuery);

										try {
											const result = await db.query(updateQuery);
											logger.silly("[update batch query response] : " + JSON.stringify(result));
										} catch (error) {
											logger.error("[update batch query error] : " + error.message);
										}
									} else {
										logger.warn("[batch processing skipped] : Empty caseStatements or idsCondition.");
									}

									// Move to the next batch
									batchStartIndex = batchEndIndex;
								}
							}

							// Usage
							await updateMultipleRowsInBatches(transactionIdString, corelationId);


							const update_status_final = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "C" WHERE compose_rcs_id = '${campaign_id}' AND rcs_status = "P"`;
							logger_all.info(update_status_final)
							const get_update_status_final = await db.query(update_status_final);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final));

							const update_status_sts = `UPDATE master_compose_rcs SET rcs_status = "C" WHERE compose_rcs_id = '${campaign_id}' AND user_id = '${user_id}'AND rcs_status = "P"`;
							logger_all.info(update_status_sts)
							const get_update_status_sts = await db.query(update_status_sts);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_sts));

							const update_status_summary = `UPDATE user_summary_report SET report_status = "Y" WHERE com_msg_id = '${campaign_id}' AND user_id = '${user_id}'AND report_status = "N"`;
							logger_all.info(update_status_summary)
							const get_update_status_summary = await db.query(update_status_summary);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_summary));


							var log_update = `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
							logger.silly("[update query request] : " + log_update);

							const log_update_result = await db.query(log_update);
							logger.silly("[update query response] : " + JSON.stringify(log_update_result));
						});
				}


			//}
		}
		catch (e) {// any error occurres send error response to client
			logger_all.info("[create csv failed response] : " + e)
			logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Error Occurred', request_id: req.body.request_id }))

			var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Error occurred' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
			logger.silly("[update query request] : " + log_update);
			const log_update_result = await db.query(log_update);
			logger.silly("[update query response] : " + JSON.stringify(log_update_result))

			res.json({ response_code: 0, response_status: 201, response_msg: 'Error Occurred', request_id: req.body.request_id });
		}
	});*/

app.post('/approve_rcs', validator.body(ApproveTemplate),
	valid_user, async function (req, res, next) {
		try {
			var request_id = req.body.request_id;
			//var user_id = req.body.user_id;
			var campaign_id = req.body.campaign_id;
			var user_id = req.body.selected_user_id;

			var rec_count = 0;
			var total_count = 0;
			var insert_count = 1;
			var sender_numbers_active = [];
			var sender_numbers_inactive = [];
			var sender_id_active = [];
			var sender_devicetoken_active = [];
			var variable_value = [];
			let isFirstRow = true;
			is_check = false;
			const variable_values = [];
			const valid_mobile_numbers = [];
			const push_name = [];
			const push_name_and_values = [];
			const invalid_mobile_numbers = [];
			var media_url = [];
			const duplicateMobileNumbers = new Set();
			user_id_check = req.body.user_id;
			var body_variable_count, unique_template_id, rcs_template_id, template_category, template_message, insertId, media_type;
			var transactionIdString = [];
			var corelationId = [];
			//Check if user is admin
			//	if (user_id_check == 1) {
			//Query to get data based on compose id
			var get_campaign = `SELECT * FROM ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} where user_id = '${user_id}' AND compose_rcs_id = '${campaign_id}' AND rcs_status = 'W'`;
			logger_all.info("[select query request] : " + get_campaign);
			const get_user_det = await db.query(get_campaign);
			logger_all.info("[select query response] : " + JSON.stringify(get_user_det));

			// var get_compose_data = `SELECT * FROM ${DB_NAME}_${user_id}.compose_msg_media_${user_id} where compose_message_id = '${compose_message_id}'`;
			// logger_all.info("[select query request] : " + get_compose_data);
			// const get_compose_data_result = await db.query(get_compose_data);
			// logger_all.info("[select query response] : " + JSON.stringify(get_compose_data_result));

			//Check if selected data is equal to zero, send failuer response 'Compose ID Not Available'
			if (get_user_det.length == 0) {
				const update_api = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP,response_comments = 'Compose ID Not Available.' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger_all.info("[update query request - compose ID not available] : " + update_api);
				const update_api_log = await db.query(update_api);
				logger_all.info("[update query response - compose ID not available] : " + JSON.stringify(update_api_log))
				const composeid_msg = { response_code: 0, response_status: 201, response_msg: 'Compose ID Not Available', request_id: req.body.request_id }
				logger.info("[API RESPONSE] " + JSON.stringify(composeid_msg))
				logger_all.info("[compose ID not available] : " + JSON.stringify(composeid_msg))

				return res.json(composeid_msg);
			}

			unique_template_id = get_user_det[0].unique_template_id;
			receiver_nos_path = get_user_det[0].receiver_nos_path;
			mobile_no_cnt = get_user_det[0].total_mobileno_count;
			campaign_name = get_user_det[0].campaign_name;
			message_type = get_user_det[0].message_type;
			//media_url = get_user_det[0].media_url;
			template_category = get_user_det[0].template_category
			logger_all.info(unique_template_id, receiver_nos_path, mobile_no_cnt, message_type);
			const get_count = `SELECT * FROM message_template where unique_template_id = '${unique_template_id}'`;
			const get_user_count = await db.query(get_count);
			logger_all.info("[select query response] : " + JSON.stringify(get_user_count));
			rcs_template_id = get_user_count[0].templateid;
			body_variable_count = get_user_count[0].body_variable_count;
			template_category = get_user_count[0].template_category;
			template_message = get_user_count[0].template_message
			media_url = get_user_count[0].media_url;
			media_type = get_user_count[0].media_type
			logger_all.info("***********************");
			logger_all.info(rcs_template_id, template_category, template_message, media_url, media_type);


			if (receiver_nos_path != '-') {

				// Fetch the CSV file
				await fs.createReadStream(receiver_nos_path)

					// Read the CSV file from the stream
					.pipe(csv({
						headers: false
					}))

					// Set headers to false since there are no column headers
					.on('data', (row) => {
						if (Object.values(row).every(value => value === '')) {
							return;
						}

						const firstColumnValue = row[0].trim();
						const name = row[1] ? row[1].trim() : '';
						valid_mobile_numbers.push(firstColumnValue)
						push_name.push(name)
						const otherValues = [];
						for (let i = 2; i < Object.keys(row).length; i++) { // Start from 2 to skip mobile number and name
							if (row[i]) {
								otherValues.push(row[i].trim());
							}
						}
						push_name_and_values.push([name, ...otherValues]);

					})
					.on('error', (error) => {
						console.error('Error:', error.message);
					})
					.on('end', async () => {
						logger_all.info(valid_mobile_numbers)
						logger_all.info(push_name_and_values)
						logger_all.info("**********************_______________________________")
						logger_all.info(body_variable_count, unique_template_id, media_url)
						//Insert compose details to compose_msg_status table
						var insert_numbers = `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES`;
						var insertId;
						var insertIdString = ""
						var numdate;
						var numDateArray = []
						var batchSize = 15000;
						//Loop for receiver numbers
						if (valid_mobile_numbers.length === 0) {
							logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Failed', request_id: req.body.request_id }))
							return res.json({ response_code: 0, response_status: 201, response_msg: 'Failed', request_id: req.body.request_id });
						}

						const update_status = `UPDATE master_compose_rcs SET rcs_status = "P" WHERE compose_rcs_id = '${campaign_id}' AND user_id = '${user_id}'AND rcs_status = "W"`;
						logger_all.info(update_status)
						const get_update_status = await db.query(update_status);
						logger_all.info("[select query response] : " + JSON.stringify(get_update_status));

						const update_status_master = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "P" WHERE compose_rcs_id = '${campaign_id}' AND rcs_status = "W" `;
						logger_all.info(update_status_master)
						const get_update_status_master = await db.query(update_status_master);
						logger_all.info("[select query response] : " + JSON.stringify(get_update_status_master));
						for (var k = 0; k < valid_mobile_numbers.length; k) {

							if (k == valid_mobile_numbers.length) {
								break;
							}
							function getCurrentFormattedDate() {
								const now = new Date();
								const year = now.getFullYear();
								const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
								const day = String(now.getDate()).padStart(2, '0');
								const hours = String(now.getHours()).padStart(2, '0');
								const minutes = String(now.getMinutes()).padStart(2, '0');
								const seconds = String(now.getSeconds()).padStart(2, '0');
								return `${year}${month}${day}${hours}${minutes}${seconds}`;
							}
							const date = getCurrentFormattedDate();
							numdate = valid_mobile_numbers[k] + '_' + date
							numDateArray.push(numdate)
							//Insert compose data
							insert_numbers = insert_numbers + "" + `(NULL,'${campaign_id}',NULL,'${valid_mobile_numbers[k]}',NULL,'-','N',CURRENT_TIMESTAMP,NULL,NULL,NULL,'${numdate}',NULL,NULL,NULL,NULL,NULL,'N'),`;

							//check if insert_count is 1000, insert 1000 splits data
							if (insert_count == 1000) {
								insert_numbers = insert_numbers.substring(0, insert_numbers.length - 1)
								logger_all.info("[insert query request - insert numbers] : " + insert_numbers);
								var insert_numbers_result = await db.query(insert_numbers);
								logger_all.info("[insert query response - insert numbers] : " + JSON.stringify(insert_numbers_result))
								insert_count = 0;
								insert_numbers = `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES`;
							}
							insert_count = insert_count + 1;


							k++;


						}
						//After the loops complete, this if statement checks if any pending insert queries are left to be executed. If so, it executes
						if (insert_numbers !== `INSERT INTO ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} VALUES`) {
							insert_numbers = insert_numbers.substring(0, insert_numbers.length - 1); // Remove the trailing comma
							logger_all.info("[insert query request - insert numbers] : " + insert_numbers);
							var insert_numbers_result = await db.query(insert_numbers);
							logger_all.info("[insert query response - insert numbers] : " + JSON.stringify(insert_numbers_result));
							insertId = insert_numbers_result.insertId;
						}

						logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 1, response_status: 200, response_msg: 'Initiated', request_id: req.body.request_id }))
						res.json({ response_code: 1, response_status: 200, response_msg: 'Initiated', request_id: req.body.request_id });

						logger_all.info("****************___________****************")
						logger_all.info(insertId)

						const get_user_summary_report = `UPDATE user_summary_report SET total_waiting = '0' ,total_process = '${valid_mobile_numbers.length}' where user_id = '${user_id}' AND com_msg_id = '${campaign_id}'`;
						logger.silly("[select query response - get_user_summary_report ] : " + get_user_summary_report);
						const get_user_summary_report_log = await db.query(get_user_summary_report);
						logger.silly("[select query response - get_user_summary_report ] : " + JSON.stringify(get_user_summary_report_log))
						logger_all.info(get_user_summary_report)


						if (template_category === 'CAROUSEL') {
							js1 = "";
							// Parse the template message if it's in JSON string format
							// const js = JSON.parse(template_message); // Assuming template_message is a JSON string
							// logger_all.info(js);

							// // Initialize textArray to store all text values in CAROUSEL
							// const textArray = [];

							// // Loop through the outer array, check if each element is an array
							// js.forEach(innerArray => {
							// 	if (Array.isArray(innerArray)) {
							// 		innerArray.forEach(item => {
							// 			if (item.text) {
							// 				textArray.push(item.text);
							// 			}
							// 		});
							// 	} else {
							// 		// If it's not an array, check directly for a text property
							// 		if (innerArray.text) {
							// 			textArray.push(innerArray.text);
							// 		}
							// 	}
							// });

							// // Join the array of text values into a single string for CAROUSEL
							// js1 = textArray.join(", ");
							// logger_all.info("Extracted content for CAROUSEL as a string:", js1);

						} else {
							logger_all.info("&&&&&&&&&&&&&&&&");
							logger_all.info(template_message)
							var js = "";
							// Handle normal case where it's not CAROUSEL
							try{
							 js = JSON.parse(template_message); // Assuming template_message is a JSON string
							}
							catch(e){
								logger_all.info("^^^^^^^^^^^^^^errorrr");
								logger_all.info(e)
							}

							logger_all.info(js);
							logger_all.info("^^^^^^^^^^^^^^6");
							// Get the text value for the normal case
							js1 = js[0]?.text || "";
							logger_all.info("Extracted content for normal case:", js1);
						}

						// Continue processing js1 regardless of template_category
						const regex = /\[[^\]]*\]/g;
						const matches = js1.match(regex);
						const extractedContent = js1.match(/\[([^\]]*)\]/g)?.map(match => match.slice(1, -1)) || [];
						logger_all.info("***********", extractedContent);

						const extractedContentStr = extractedContent.join(", ");
						logger_all.info("Extracted content as a string...................:", extractedContentStr);


						var msg_json = {
							"mode": "rcs",
							"rcsTemplateId": rcs_template_id,
							"campId": campaign_id,
							"unicode": false,
							"shortMessages": []
						};

						for (var k = 0; k < valid_mobile_numbers.length; k) {
							var contextjson = {}

							if (template_category === 'RICH CARD' && media_url != "-") {
								contextjson[`rcs_${media_type}`] = media_url
							}
							for (var i = 0; i < extractedContent.length; i++) {
								contextjson[`rcs_${extractedContent[i]}`] = push_name_and_values[k][i]
							}
							logger_all.info("!!!!!!!!!!!!!!!!!!!")
							logger_all.info(contextjson)

							msg_json.shortMessages.push({
								"recipient": valid_mobile_numbers[k],
								"corelationId": numDateArray[k],
								"context": contextjson
							});
							insertIdString = insertIdString + "','" + (insertId + (k))

							if (k % batchSize == 0 && k != 0) {

								logger_all.info(JSON.stringify(msg_json))
								let config = {
									method: 'post',
									maxBodyLength: Infinity,
									url: 'https://kapi.omni-channel.in/fe/api/v1/iPMessage/One2Many',
									headers: {
										'Content-Type': 'application/json',
										'Authorization': 'Basic eWVlamFpZGVtby5wcm86RGVtb0AxMjMk'
									},
									data: msg_json
								};

								await axios.request(config)
									.then(async (response) => {
										var test = response.data
										for (var i = 0; i < test.submitResponses.length; i++) {
											transactionIdString.push(test.submitResponses[i].transactionId.toString());
											corelationId.push(test.submitResponses[i].corelationId.toString());
										}
										logger_all.info("Transaction IDs-----------------------------:", transactionIdString);
										logger_all.info("Corelation IDs------------------------------:", corelationId);

										insertIdString = insertIdString.substring(3);

										var update_rcs_tmp = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='S' WHERE comrcs_status_id IN ('${insertIdString}')`;
										logger.silly("[update query request] : " + update_rcs_tmp);
										const log_update_rcs_tmp = await db.query(update_rcs_tmp);
										logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp));
									})
									.catch(async (error) => {
										var update_rcs_tmp_err = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='F' , response_status = 'Y' , response_message = 'Failure' ,response_date = CURRENT_TIMESTAMP WHERE comrcs_status_id IN ('${insertIdString}')`;
										logger.silly("[update query request] : " + update_rcs_tmp_err);
										const log_update_rcs_tmp_err = await db.query(update_rcs_tmp_err);
										logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp_err));
										logger_all.info(error);
									});
								msg_json = {
									"mode": "rcs",
									"rcsTemplateId": rcs_template_id,
									"campId": campaign_id,
									"unicode": false,
									"shortMessages": []
								};
								insertIdString = ""
							}
							// Loop through the responses and extract transactionId and corelationId

							k++;
						}
						logger_all.info("loop ended.........")
						logger_all.info(JSON.stringify(msg_json))
						let config = {
							method: 'post',
							maxBodyLength: Infinity,
							url: 'https://kapi.omni-channel.in/fe/api/v1/iPMessage/One2Many',
							headers: {
								'Content-Type': 'application/json',
								'Authorization': 'Basic eWVlamFpZGVtby5wcm86RGVtb0AxMjMk'
							},
							data: msg_json
						};
						await axios.request(config)
							.then(async (response) => {
								var test = response.data
								for (var i = 0; i < test.submitResponses.length; i++) {
									transactionIdString.push(test.submitResponses[i].transactionId.toString());
									corelationId.push(test.submitResponses[i].corelationId.toString());
								}
								logger_all.info("Transaction IDs-----------------------------:", transactionIdString);
								logger_all.info("Corelation IDs------------------------------:", corelationId);

								insertIdString = insertIdString.substring(3);

								var update_rcs_tmp = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='S' WHERE comrcs_status_id IN ('${insertIdString}')`;
								logger.silly("[update query request] : " + update_rcs_tmp);
								const log_update_rcs_tmp = await db.query(update_rcs_tmp);
								logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp));
							})
							.catch(async (error) => {
								var update_rcs_tmp_err = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET comrcs_status='F' , response_status = 'Y' , response_message = 'Failure' ,response_date = CURRENT_TIMESTAMP WHERE comrcs_status_id IN ('${insertIdString}')`;
								logger.silly("[update query request] : " + update_rcs_tmp_err);
								const log_update_rcs_tmp_err = await db.query(update_rcs_tmp_err);
								logger.silly("[update query response] : " + JSON.stringify(log_update_rcs_tmp_err));
								logger_all.info(error);
							});


						async function updateMultipleRowsInBatches(transactionIdString, corelationId) {
							let totalRows = transactionIdString.length;
							let batchStartIndex = 0;

							while (batchStartIndex < totalRows) {
								// Determine the end index for the current batch
								let batchEndIndex = Math.min(batchStartIndex + batchSize, totalRows);

								// Extract the batch data for the current iteration
								const transactionBatch = transactionIdString.slice(batchStartIndex, batchEndIndex);
								const correlationBatch = corelationId.slice(batchStartIndex, batchEndIndex);

								// Construct the query for the current batch
								let caseStatements = '';
								let idsCondition = '';

								for (let i = 0; i < transactionBatch.length; i++) {
									const transactionId = transactionBatch[i];
									const correlationId = correlationBatch[i];

									// Construct the case statement for each row in the batch
									caseStatements += `WHEN corelation_id = '${correlationId}' THEN '${transactionId}' `;
									idsCondition += `'${correlationId}',`;
								}

								// Remove the trailing comma from idsCondition
								idsCondition = idsCondition.slice(0, -1);

								// Skip the query execution if caseStatements or idsCondition are empty
								if (caseStatements && idsCondition) {
									const updateQuery = `
                UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id}
                SET response_id = CASE ${caseStatements} END
                WHERE corelation_id IN (${idsCondition});
            `;

									logger.silly("[update batch query request] : " + updateQuery);
									logger_all.info(updateQuery);

									try {
										const result = await db.query(updateQuery);
										logger.silly("[update batch query response] : " + JSON.stringify(result));
									} catch (error) {
										logger.error("[update batch query error] : " + error.message);
									}
								} else {
									logger.warn("[batch processing skipped] : Empty caseStatements or idsCondition.");
								}

								// Move to the next batch
								batchStartIndex = batchEndIndex;
							}
						}

						// Usage
						await updateMultipleRowsInBatches(transactionIdString, corelationId);

						const get_campaign_sts = `SELECT * FROM ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} where compose_rcs_id = '${campaign_id}' and response_status is NULL`;
						const get_status_res = await db.query(get_campaign_sts);
						//   if check response_status is NULL length is zero.This condition will be executed.
						if (get_status_res.length == 0) {

							const update_status_final = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "O" WHERE compose_rcs_id = '${campaign_id}' AND rcs_status = "P"`;
							logger_all.info(update_status_final)
							const get_update_status_final = await db.query(update_status_final);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final));

							const update_status_sts = `UPDATE master_compose_rcs SET rcs_status = "O" WHERE compose_rcs_id = '${campaign_id}' AND user_id = '${user_id}'AND rcs_status = "P"`;
							logger_all.info(update_status_sts)
							const get_update_status_sts = await db.query(update_status_sts);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_sts));

							const select_Credit = `UPDATE message_limit SET available_messages = available_messages+${get_status_res.length} where user_id = ${user_id}`;
							logger_all.info(select_Credit)
							const get_select_credit = await db.query(select_Credit);
							logger_all.info("[select query response] : " + JSON.stringify(get_select_credit));


						} else {  //response_status is NULL length is coming update the "C" status and send the mail

							const update_status_final = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "C" WHERE compose_rcs_id = '${campaign_id}' AND rcs_status = "P"`;
							logger_all.info(update_status_final)
							const get_update_status_final = await db.query(update_status_final);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final));

							const update_status_sts = `UPDATE master_compose_rcs SET rcs_status = "C" WHERE compose_rcs_id = '${campaign_id}' AND user_id = '${user_id}'AND rcs_status = "P"`;
							logger_all.info(update_status_sts)
							const get_update_status_sts = await db.query(update_status_sts);
							logger_all.info("[select query response] : " + JSON.stringify(get_update_status_sts));
						   //   get username
							const get_username = `select user_name from user_management WHERE user_id = '${user_id}' AND usr_mgt_status = "Y"`;
							logger_all.info(get_username)
							const get_username_res = await db.query(get_username);
							logger_all.info("[select query response] : " + JSON.stringify(get_username_res));

							//  Send Mail For 
							let transporter = nodemailer.createTransport({
								// Configure your email service here (SMTP, Gmail, etc.)
								service: 'gmail',
								auth: {
									user: 'shanthini.m@yeejai.com', // Your email address
									pass: 'wsxnkyzsrkadpioy' // Your email password or app-specific password
								}
							});

							// Define email options
							let mailOptions = {
								from: 'shanthini.m@yeejai.com', // Sender's email address and name
								to: 'tech@yeejai.com,muthukamatchi170167@gmail.com', // Recipient's email addresses separated by commas
								subject: `Alert: RCS - Campaign completed - ${get_username_res[0].user_name}`, // Email subject
								text: `Below Campaign Details:\n\nUser: ${get_username_res[0].user_name}\nTotal Mobile nos: ${mobile_no_cnt}\nCampaign Id: ${campaign_id}\nCampaign Name: ${campaign_name}`
							};

							// Send email
							transporter.sendMail(mailOptions, (error, info) => {
								if (error) {
									return logger_all.info('Error occurred:', error);
								}
								logger_all.info('Email sent:', info.response);
							});

						}

						const update_status_summary = `UPDATE user_summary_report SET report_status = "Y" WHERE com_msg_id = '${campaign_id}' AND user_id = '${user_id}'AND report_status = "N"`;
						logger_all.info(update_status_summary)
						const get_update_status_summary = await db.query(update_status_summary);
						logger_all.info("[select query response] : " + JSON.stringify(get_update_status_summary));


						var log_update = `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
						logger.silly("[update query request] : " + log_update);

						const log_update_result = await db.query(log_update);
						logger.silly("[update query response] : " + JSON.stringify(log_update_result));
					});
			}
		}
		catch (e) {// any error occurres send error response to client
			logger_all.info("[approve failed response] : " + e)
			logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Error Occurred', request_id: req.body.request_id }))

			var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Error occurred' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
			logger.silly("[update query request] : " + log_update);
			const log_update_result = await db.query(log_update);
			logger.silly("[update query response] : " + JSON.stringify(log_update_result))

			res.json({ response_code: 0, response_status: 201, response_msg: 'Error Occurred', request_id: req.body.request_id });
		}
	});

app.post('/update_report', validator.body(UpdateReportValidation),
	valid_user, async function (req, res) {
		try {
			let user_id = req.body.selected_user_id;
			// let selected_user_id = req.body.selected_user_id;
			let compose_id = req.body.compose_id;
			let csvFilePath = req.body.csvFilePath;

			let message_ids = [];
			let processed_dates = [];
			let done_dates = [];
			let descriptions = [];
			let dlr_states = [];
			let description_status = [];
			let combined_data = [];
			let res_status_msg = [];
			let batchSize = 15000;
			let rcsStatus = 'O';
			const update_status = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "V" WHERE compose_rcs_id = '${compose_id}' AND rcs_status = "C"`;
			logger_all.info(update_status)
			const get_update_status = await db.query(update_status);
			logger_all.info("[select query response] : " + JSON.stringify(get_update_status));

			const update_status_final_master = `UPDATE master_compose_rcs SET rcs_status = "V" WHERE compose_rcs_id = '${compose_id}' AND rcs_status = "C"`;
			logger_all.info(update_status_final_master)
			logger_all.info("[query resopnes] : " + JSON.stringify(update_status_final_master))
			const get_update_status_final_master = await db.query(update_status_final_master);
			logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final_master));

			csvFilePath = `/var/www/html/rcs/uploads/pj_report_file/${csvFilePath}`
			res.json({ response_code: 1, response_status: 200, response_msg: 'Update initiated and processed.' });

			// Read and parse the CSV file
			fs.createReadStream(csvFilePath)
				.pipe(csv())
				.on('data', (row) => {
					message_ids.push(row.message_id);

					// Reformat the dates using moment and check if they're valid
					let processed_date = moment(row.processed_date, 'DD-MM-YYYY HH:mm:ss', true);
					let done_date = moment(row.done_date, 'DD-MM-YYYY HH:mm:ss', true);

					// Check if processed_date is valid
					if (processed_date.isValid()) {
						processed_dates.push(processed_date.format('YYYY-MM-DD HH:mm:ss'));
					} else {
						processed_dates.push('0000-00-00 00:00:00');
					}

					let combined_description = `${row.description} ${row.dlr_state}`;
					combined_data.push(combined_description);

					// Check if done_date is valid
					if (done_date.isValid() && row.dlr_state.includes('DELIVERY_SUCCESS')) {
						done_dates.push(done_date.format('YYYY-MM-DD HH:mm:ss'));
						description_status.push('Y');
					} else {
						done_dates.push('0000-00-00 00:00:00'); // Change '0000-00-00 00:00:00' to null
						description_status.push('F'); // Change 'F' to null
					}

					if (row.description && row.description.trim() !== '') {
						if (row.description.includes('successfully')) {
							res_status_msg.push('Y');
						} else {
							res_status_msg.push('F'); // Change 'F' to null
						}
					}
					descriptions.push(row.description);
					dlr_states.push(row.dlr_state);
				})
				.on('end', async () => {

					logger_all.info(message_ids);
					logger_all.info(processed_dates);
					logger_all.info(done_dates);
					logger_all.info(descriptions);
					logger_all.info(dlr_states);
					logger_all.info(description_status);
					logger_all.info(combined_data);
					logger_all.info(res_status_msg)
					logger_all.info('CSV file successfully processed');

					// Call the batch update function
					await updateMultipleRowsInBatches();

					const select_update_status = `SELECT 
					COUNT(CASE WHEN response_status = 'Y' THEN 1 END) AS success_count,
					COUNT(CASE WHEN response_status = 'F' THEN 1 END) AS failure_count,
					COUNT(CASE WHEN delivery_status = 'Y' THEN 1 END) AS delivered_count,
					COUNT(CASE WHEN response_status IS NULL THEN 1 END) AS null_count
				FROM ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id}
				WHERE compose_rcs_id = '${compose_id}'`;
					logger_all.info(select_update_status);

					const get_select_update_status = await db.query(select_update_status);
					logger_all.info(get_select_update_status)
					logger_all.info("[select query response] : " + JSON.stringify(get_select_update_status))
					let nullCount = get_select_update_status[0].null_count;
					let success_count = get_select_update_status[0].success_count;
					let failure_count = get_select_update_status[0].failure_count;
					let delivered_count = get_select_update_status[0].delivered_count;
					if (nullCount != 0) {
						rcsStatus = 'C';
					}

					const select_Credit = `UPDATE message_limit SET available_messages = available_messages+${failure_count} where user_id = ${user_id}`;
					logger_all.info(select_Credit);
					logger_all.info(select_Credit)
					const get_select_credit = await db.query(select_Credit);
					logger_all.info("[select query response] : " + JSON.stringify(get_select_credit));

					const update_status_final = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id} SET delivery_date = NULL,delivery_status = NULL WHERE compose_rcs_id = '${compose_id}' and delivery_date='0000-00-00 00:00:00' and delivery_status = 'F'`;
					logger_all.info(update_status_final);
					const get_update_status_final = await db.query(update_status_final);
					logger_all.info(update_status_final)
					logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final));

					const update_status_final_date = `UPDATE ${DB_NAME}_${user_id}.compose_rcs_tmp_${user_id} SET rcs_status = "${rcsStatus}" WHERE compose_rcs_id = '${compose_id}' AND rcs_status = "V"`;
					logger_all.info(update_status_final_date);
					const get_update_status_final_date = await db.query(update_status_final_date);
					logger_all.info(update_status_final_date)
					logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final_date));

					const update_status_summaryReport = `UPDATE user_summary_report 
					SET total_process = '0', 
						total_success = '${success_count}', 
						total_failed = '${failure_count}', 
						total_delivered = '${delivered_count}' 
					WHERE com_msg_id = '${compose_id}' 
					  AND user_id = '${user_id}'`;

					logger_all.info(update_status_summaryReport)
					const get_update_status_summaryReport = await db.query(update_status_summaryReport);

					logger_all.info("[select query response] : " + JSON.stringify(get_update_status_summaryReport));

					const update_status_final_master = `UPDATE master_compose_rcs SET rcs_status = "${rcsStatus}" WHERE compose_rcs_id = '${compose_id}' AND rcs_status = "V"`;
					logger_all.info(update_status_final_master)
					logger_all.info("[query resopnes] : " + JSON.stringify(update_status_final_master))
					const get_update_status_final_master = await db.query(update_status_final_master);
					logger_all.info("[select query response] : " + JSON.stringify(get_update_status_final_master));


					// Return response after updates
				})
				.on('error', (err) => {
					console.error('Error processing CSV file:', err);
					res.json({ response_code: 0, response_status: 201, response_msg: 'Error occurred while processing the CSV file.' });
				});

			const escapeMySQLString = (str) => {
				return str.replace(/'/g, "\\'");
			};

			async function updateMultipleRowsInBatches() {
				let totalRows = message_ids.length;
				let batchStartIndex = 0;

				while (batchStartIndex < totalRows) {
					let batchEndIndex = Math.min(batchStartIndex + batchSize, totalRows);

					const messageBatch = message_ids.slice(batchStartIndex, batchEndIndex);
					const processedDateBatch = processed_dates.slice(batchStartIndex, batchEndIndex);
					const statusBatch = description_status.slice(batchStartIndex, batchEndIndex);
					const combinedDataBatch = combined_data.slice(batchStartIndex, batchEndIndex);
					const doneDateBatch = done_dates.slice(batchStartIndex, batchEndIndex);
					const dlrBatch = dlr_states.slice(batchStartIndex, batchEndIndex);
					const ressponsemsgBatch = res_status_msg.slice(batchStartIndex, batchEndIndex);
					let caseStatementsDate = '';
					let caseStatementsStatus = '';
					let caseStatementsComments = '';
					let caseStatementsDonedate = '';
					let caseStatementsDlrstate = '';
					let caseStatementsresponseMsg = '';
					let idsCondition = '';

					for (let i = 0; i < messageBatch.length; i++) {
						const messageId = messageBatch[i];
						const processedDate = processedDateBatch[i];
						const status = statusBatch[i];
						const combinedDescription = escapeMySQLString(combinedDataBatch[i]);
						const doneDate = doneDateBatch[i];
						const dlrState = dlrBatch[i];
						const resMsg = ressponsemsgBatch[i];
						caseStatementsDate += `WHEN response_id = '${messageId}' THEN '${processedDate}' `;
						caseStatementsStatus += `WHEN response_id = '${messageId}' THEN '${status}' `;
						caseStatementsComments += `WHEN response_id = '${messageId}' THEN '${combinedDescription}' `;
						caseStatementsDonedate += `WHEN response_id = '${messageId}' THEN '${doneDate}'`;
						caseStatementsDlrstate += `WHEN response_id = '${messageId}' THEN '${dlrState}'`;
						caseStatementsresponseMsg += `WHEN response_id = '${messageId}' THEN '${resMsg}'`;
						idsCondition += `'${messageId}',`;
					}

					idsCondition = idsCondition.slice(0, -1);

					const updateQuery = `
							UPDATE ${DB_NAME}_${user_id}.compose_rcs_status_tmpl_${user_id}
							SET 
								response_date = CASE ${caseStatementsDate} END,
								delivery_status = CASE ${caseStatementsStatus} END,
								comments = CASE ${caseStatementsComments} END,
								delivery_date = CASE ${caseStatementsDonedate} END,
								response_message = CASE ${caseStatementsDlrstate} END,
								response_status = CASE ${caseStatementsresponseMsg} END
							WHERE response_id IN (${idsCondition});
						`;

					logger.silly("[update batch query request] : " + updateQuery);
					logger_all.info(updateQuery);
					logger_all.info(updateQuery)

					try {
						const result = await db.query(updateQuery);

						logger.silly("[update batch query response] : " + JSON.stringify(result));
					} catch (error) {
						logger.error("[update batch query error] : " + error.message);
					}

					// Move to the next batch
					batchStartIndex = batchEndIndex;
				}
			}

		} catch (err) {
			logger.error("Error in the update report endpoint: " + err.message);
			res.json({ response_code: 0, response_status: 201, response_msg: 'Error occurred during the update process.' });
		}
	});
// to api for create_csv 
app.post('/create_csv', validator.body(CreateCsvValidation),
	valid_user, async function (req, res) {

		try {
			var header_json = req.headers;
			let ip_address = header_json['x-forwarded-for'];

			// to get date and time
			var day = new Date();
			var today_date = day.getFullYear() + '' + (day.getMonth() + 1) + '' + day.getDate();
			var today_time = day.getHours() + "" + day.getMinutes() + "" + day.getSeconds();
			var current_date = today_date + '_' + today_time;
			// get all the req data
			let sender_number = req.body.mobile_number;

			logger.info(" [create csv query parameters] : " + sender_number)

			const insert_api_log = `INSERT INTO api_log VALUES(NULL,'${req.originalUrl}','${ip_address}','${req.body.request_id}','N','-','0000-00-00 00:00:00','Y',CURRENT_TIMESTAMP)`
			logger_all.info("[insert query request] : " + insert_api_log);
			const insert_api_log_result = await db.query(insert_api_log);
			logger_all.info("[insert query response] : " + JSON.stringify(insert_api_log_result))

			const check_req_id = `SELECT * FROM api_log WHERE request_id = '${req.body.request_id}' AND response_status != 'N' AND log_status='Y'`
			logger_all.info("[select query request] : " + check_req_id);
			const check_req_id_result = await db.query(check_req_id);
			logger_all.info("[select query response] : " + JSON.stringify(check_req_id_result));

			if (check_req_id_result.length != 0) {

				logger_all.info("[failed response] : Request already processed");
				logger.info("[API RESPONSE] " + JSON.stringify({ request_id: req.body.request_id, response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id }))

				var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Request already processed' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);
				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result))

				return res.json({ response_code: 0, response_status: 201, response_msg: 'Request already processed', request_id: req.body.request_id });

			}

			// to get the data in the array 
			var data = [
				['Name', 'Given Name', 'Group Membership', 'Phone 1 - Type', 'Phone 1 - Value']
			];
			// looping condition is true .to continue the process
			for (var i = 0; i < sender_number.length; i++) {
				data.push([`yjtec${day.getDate()}_${sender_number[i]}`, `yjtec${day.getDate()}_${sender_number[i]}`, '* myContacts', '', `${sender_number[i]}`])
			}

			// (C) CREATE CSV FILE to send the response in success message
			csv.stringify(data, async (err, output) => {
				fs.writeFileSync(`${media_storage}/uploads/whatsapp_docs/contacts_${current_date}.csv`, output);
				logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 1, response_status: 200, response_msg: 'Success ', file_location: `uploads/whatsapp_docs/contacts_${current_date}.csv`, request_id: req.body.request_id }))

				var log_update = `UPDATE api_log SET response_status = 'S',response_date = CURRENT_TIMESTAMP, response_comments = 'Success' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
				logger.silly("[update query request] : " + log_update);
				const log_update_result = await db.query(log_update);
				logger.silly("[update query response] : " + JSON.stringify(log_update_result))

				res.json({ response_code: 1, response_status: 200, response_msg: 'Success ', file_location: `uploads/whatsapp_docs/contacts_${current_date}.csv`, request_id: req.body.request_id });
			});

		}
		catch (e) {// any error occurres send error response to client
			logger_all.info("[create csv failed response] : " + e)
			logger.info("[API RESPONSE] " + JSON.stringify({ response_code: 0, response_status: 201, response_msg: 'Error Occurred', request_id: req.body.request_id }))

			var log_update = `UPDATE api_log SET response_status = 'F',response_date = CURRENT_TIMESTAMP, response_comments = 'Error occurred' WHERE request_id = '${req.body.request_id}' AND response_status = 'N'`
			logger.silly("[update query request] : " + log_update);
			const log_update_result = await db.query(log_update);
			logger.silly("[update query response] : " + JSON.stringify(log_update_result))

			res.json({ response_code: 0, response_status: 201, response_msg: 'Error Occurred', request_id: req.body.request_id });
		}
	});
// to listen the port in using the localhost
/*app.listen(port, () => {
	logger_all.info(`App started listening at http://localhost:${port}`);
});*/

// module.exports.logger = logger;

//  to listen the port in using the server
httpServer.listen(port, function (req, res) {
	logger_all.info("Server started at port " + port);
 });

