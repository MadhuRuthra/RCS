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
const createCsvWriter = require('csv-writer').createObjectCsvWriter;
const path = require('path');

// download_compose_message - start
async function downloadComposeMessage(req, res) {
	var logger_all = main.logger_all
	var logger = main.logger

	const admin_user_id = req.body.user_id;
	const user_id = req.body.compose_user_id;
	const compose_id = req.body.compose_id;
	const pjValue = req.body.PJvalue;
	const yjValue = req.body.YJvalue;
	try {
		// Fetch mobile numbers based on compose_id
		const mobileNumbers = await fetchMobileNumbers(user_id, compose_id);
		// Split mobile numbers based on PJ and YJ values
		const { pjRows, yjRows, totalNumbers, pjCount, pjPercentage } = await splitMobileNumbers(mobileNumbers, pjValue, user_id, compose_id);

		// Check if splitting resulted in valid groups

		/*if (!pjRows || !yjRows || pjRows.length === 0 || yjRows.length === 0) 
		{
		   console.log('Ratio Mismatch, Increase the Percentage');
		   logger_all.info("Ratio Mismatch, Increase the Percentage")
		   return { response_code: 0, response_status: 204, response_msg: 'Ratio Mismatch, Increase the Percentage' };
	   }*/

		console.log("pjrowcount", pjRows.length);
		console.log("totalnumber", totalNumbers);
		console.log("pjpercentage", pjPercentage);
		console.log("yjrowcount", yjRows.length);

		// Check if splitting resulted in valid groups
		// Check if pjCount is 100%, if yes, don't check the ratio
		if (pjPercentage == 100 && pjRows.length == totalNumbers) {
			console.log("pjcount", pjCount);
			console.log("totalnumber", totalNumbers);
			console.log('PJ count is 100%, no need to check ratio.');
			// Other logic for handling when PJ count is 100%
		}
		else {
			if (!pjRows || !yjRows || pjRows.length == 0 || yjRows.length == 0) {
				console.log('Ratio Mismatch, Increase the Percentage');
				logger_all.info("Ratio Mismatch, Increase the Percentage")
				return { response_code: 0, response_status: 204, response_msg: 'Ratio Mismatch, Increase the Percentage' };
			}
		}

		// Generate CSV files
		/*const pjFilePath = await generateCSV(pjRows, pjValue, 'TGBase', user_id, compose_id);
			const yjFilePath = await generateCSV(yjRows, yjValue, 'CGBase', user_id, compose_id);*/

		let pjFilePath, yjFilePath;

		if (pjRows.length === totalNumbers) {
			console.log('PJ count is 100%, no need to check ratio.');
			pjFilePath = await generateCSV(pjRows, pjValue, 'TGBase', user_id, compose_id);
			// Other logic for handling when PJ count is 100%
		}
		else {
			// Generate CSV files
			pjFilePath = await generateCSV(pjRows, pjValue, 'TGBase', user_id, compose_id);
			yjFilePath = await generateCSV(yjRows, yjValue, 'CGBase', user_id, compose_id);
		}

		function getFileNameFromURL(url) {
			// Split the URL by slashes
			const parts = url.split('/');
			// Get the last part (which represents the file name)
			const fileName = parts[parts.length - 1];
			return fileName;
		}

		/*const pjFilename = getFileNameFromURL(pjFilePath);
		console.log(pjFilename); 
		
		const yjFilename = getFileNameFromURL(yjFilePath);
		console.log(yjFilename);*/

		let pjFilename;
		let yjFilename;
		if (pjRows.length === totalNumbers) {
			pjFilename = getFileNameFromURL(pjFilePath);
			console.log(pjFilename);
		}
		else {
			pjFilename = getFileNameFromURL(pjFilePath);
			console.log(pjFilename);

			yjFilename = getFileNameFromURL(yjFilePath);
			console.log(yjFilename);
		}



		var update_base = `UPDATE rcs_${user_id}.compose_rcs_tmpl_${user_id} SET tg_base = '${pjFilename}',cg_base = '${yjFilename}' WHERE compose_rcs_id = '${compose_id}'`;
		logger.silly("[update query request] : " + update_base);
		const update_base_res = await db.query(update_base);
		logger.silly("[update query response] : " + JSON.stringify(update_base_res));


		// Update report_group in the database
		//	await updateReportGroup(pjRows, 'TGBase', user_id, compose_id);
		//	await updateReportGroup(yjRows, 'CGBase', user_id, compose_id);

		//res.status(200).json({ message: 'CSV files generated successfully' });
		return {
			response_code: 1, response_status: 200, response_msg: 'CSV files generated successfully', pj_file_path: pjFilePath,
			yj_file_path: yjFilePath
		};

	}
	catch (e) {// any error occurres send error response to client
		logger_all.info("[download_compose_message failed response] : " + e)
		//return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
		return { response_code: 0, response_status: 500, response_msg: 'Internal server error' };
	}
}
// download_compose_message - end

// Function to fetch mobile numbers based on compose_id
async function fetchMobileNumbers(user_id, compose_id) {
	// Implement logic to fetch mobile numbers from your database
	try {
		const query = "SELECT wt.mobile_nos, wt.total_mobileno_count, wt.media_url, wt.variable_values, wt.media_values, wt.mobile_no_type, wt.rcs_entry_date, um.user_name, wt.campaign_id, wt.campaign_name, mt.templateid, mt.template_name, mt.media_type, mt.body_variable_count FROM rcs_" + user_id + ".compose_rcs_tmpl_" + user_id + " wt INNER JOIN rcs.user_management um ON wt.user_id = um.user_id INNER JOIN rcs.message_template mt ON wt.unique_template_id = mt.unique_template_id WHERE wt.compose_rcs_id = ?";

		// Log the constructed query string for debugging
		console.log(query);

		// Execute the SQL query
		const csv_datas = await db.query(query, [compose_id]);
		//console.log("Rows:", csv_datas);

		return csv_datas;
	}
	catch (error) {
		// Handle any errors that occur during database query
		console.error('Error fetching mobile numbers:', error);
		throw new Error('Failed to fetch mobile numbers');
	}
}

async function splitMobileNumbers(csv_datas, pjPercentage, user_id, compose_id) {
	console.log(csv_datas);
	// Extract mobile_nos from the first row
	const mobileNosBlob = csv_datas[0].mobile_nos;
	console.log(mobileNosBlob);
	const variableBlob = csv_datas[0].variable_values;
	const mediaBlob = csv_datas[0].media_values;


	// Convert the Blob to a string
	const mobileNosString = Buffer.from(mobileNosBlob).toString('utf-8');
	const variableValueBlob = Buffer.from(variableBlob).toString('utf-8');
	const mediaValueBlob = Buffer.from(mediaBlob).toString('utf-8');

	console.log(variableValueBlob);
	// Split the string by comma to get individual mobile numbers
	var mobileNumbers = mobileNosString.split(',');
	console.log("!!!!!!");

	const totalNumbers = mobileNumbers.length;
	//const variableValuesArray = JSON.parse(variableValueBlob);
	var VariableValues = JSON.parse(variableValueBlob);
	var MediaValues = JSON.parse(mediaValueBlob);
	console.log("@@@@@@@@");

	var db_name = `rcs_${user_id}`;
	console.log(db_name);
	var table_names = `rcs_${user_id}.compose_rcs_status_tmpl_${user_id}`;
	console.log(table_names);
	var insert_query = `INSERT INTO ${table_names} VALUES`;
	console.log("#####");
	var batch_size = 100000;
	var batch_count = Math.ceil(totalNumbers / batch_size);

	//Loop through the batches
	for (var batch = 0; batch < batch_count; batch++) {
		console.log("!!!!");
		// Clear the insert query

		var batch_insert_query = insert_query;

		// Calculate the start and end indices for the current batch
		var start_index = batch * batch_size;
		var end_index = Math.min((batch + 1) * batch_size, totalNumbers);
		// Construct the insert query for the current batch
		for (var i = start_index; i < end_index; i++) {
			batch_insert_query += `(NULL,${compose_id},NULL,'${mobileNumbers[i]}',NULL,'-','N',CURRENT_TIMESTAMP,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N'),`;
		}
		// Remove the trailing comma
		batch_insert_query = batch_insert_query.substring(0, batch_insert_query.length - 1);
		// Execute the insertion query for the current batch
		//logger_all.info(batch_insert_query);
		console.log(batch_insert_query);
		const insert_mobile_numbers = await db.query(batch_insert_query, null, `${db_name}`);
		//logger_all.info(" [insert query response] : " + JSON.stringify(insert_mobile_numbers));
		console.log(" [insert query response] : " + JSON.stringify(insert_mobile_numbers));
	}


	// Shuffle the mobile numbers array randomly
	var test = shuffleArray(mobileNumbers, VariableValues, MediaValues);
	mobileNumbers = test[0];
	VariableValues = test[1];
	MediaValues = test[2];

	// const totalNumbers = mobileNumbers.length;
	console.log(totalNumbers);
	console.log(pjPercentage);
	const pjCount = Math.ceil(totalNumbers * (pjPercentage / 100));
	console.log("pjcount", pjCount);

	// Prepare pjRows and yjRows with all data
	const pjRows = [];
	const yjRows = [];

	const { total_mobileno_count, rcs_entry_date, user_name, campaign_id, campaign_name, templateid, template_name, media_type, media_url, body_variable_count, mobile_no_type } = csv_datas[0];

	for (let i = 0; i < pjCount; i++) {
		pjRows.push({
			mobile_nos: mobileNumbers[i],
			variable_values: VariableValues[i],
			media_values: MediaValues[i],
			total_mobileno_count,
			rcs_entry_date,
			user_name,
			campaign_id,
			campaign_name,
			templateid,
			template_name,
			media_type,
			media_url,
			body_variable_count,
			mobile_no_type
		});
	}

	for (let i = pjCount; i < totalNumbers; i++) {
		yjRows.push({
			mobile_nos: mobileNumbers[i],
			variable_values: VariableValues[i],
			media_values: MediaValues[i],
			total_mobileno_count,
			rcs_entry_date,
			user_name,
			campaign_id,
			campaign_name,
			templateid,
			template_name,
			media_type,
			media_url,
			body_variable_count,
			mobile_no_type
		});
	}

	console.log("pjrows:", pjRows);
	console.log("yjrows:", yjRows);
	return { pjRows, yjRows, totalNumbers, pjCount, pjPercentage };
}

// Function to shuffle an array randomly
function shuffleArray(array, array1, array2) {
	for (let i = array.length - 1; i > 0; i--) {
		var j = Math.floor(Math.random() * (i + 1));
		[array[i], array[j]] = [array[j], array[i]];
		[array1[i], array1[j]] = [array1[j], array1[i]];
		[array2[i], array2[j]] = [array2[j], array2[i]];
	}
	return [array, array1, array2];
}

// Function to generate CSV file
async function generateCSV(csv_datas, percentageType, fileType, user_id, compose_id) {
	console.log(csv_datas);
	console.log(csv_datas.length); // Log the length of csv_datas
	console.log(csv_datas[0]);
	// console.log(percentageType);

	const directoryPath = '/var/www/html/rcs_portal/uploads/report_csv_files';

	console.log(directoryPath);

	// Extracting campaign_name from the first object in csv_datas
	const campaignName = csv_datas[0].campaign_name;
	console.log(campaignName);

	const mediatype = csv_datas[0].media_type;
	console.log(mediatype);

	const hasMediaType = csv_datas[0].media_type !== null;
	console.log(hasMediaType);

	const variableCount = csv_datas[0].body_variable_count;

	const MessageType = csv_datas[0].mobile_no_type;

	const filePath = path.join(directoryPath, `${campaignName}_${fileType}_${percentageType}.csv`);

	const header =
		[
			{ id: 'mobile_number', title: 'contacts' },
		];

	// Conditionally add media_type to the header if not null
	if (hasMediaType) {
		header.push({ id: 'media_type', title: '1' });
	}

	// Conditionally add headers for variable_count
	if (variableCount !== 0) {
		for (let i = 1; i <= variableCount; i++) {
			header.push({ id: `var${i}`, title: `${i}` });
		}
	}

	const csvWriter = createCsvWriter({
		path: filePath,
		header,
	});


	var mob_no = "";
	const records = csv_datas.map(data => {

		mob_no = mob_no + data.mobile_nos + ",";
		const record = {
			mobile_number: data.mobile_nos,
		};

		// Conditionally add media_type to the record if not null
		if (hasMediaType) {
			if (MessageType != 'C') {
				record.media_type = data.media_url;
			}
			else {
				record.media_type = data.media_values;
			}
		}

		// Conditionally add variable_values to the record if variableCount is not 0
		if (variableCount !== 0) {
			for (let i = 0; i < variableCount; i++) {
				record[`var${i + 1}`] = data.variable_values[i] || ''; // Assign variable value or an empty string if undefined
			}
		}

		return record;
	});

	csvWriter.writeRecords(records)
		.then(() => console.log(`${campaignName}_${percentageType}.csv file generated successfully`))
		.catch(err => console.error('Error writing CSV:', err));

	const query = `
            UPDATE rcs_${user_id}.compose_rcs_status_tmpl_${user_id} SET report_group = ? WHERE mobile_no IN (${mob_no.slice(0, -1)}) AND compose_rcs_id = ?`;  // Parameters for the query
	const queryParams = [fileType, compose_id];

	console.log(query);

	await db.query(query, queryParams);

	await db.query("UPDATE rcs_" + user_id + ".compose_rcs_tmpl_" + user_id + " SET rcs_status = 'P' WHERE compose_rcs_id = ? ", [compose_id]);

	return filePath;
}


// using for module exporting
module.exports = {
	downloadComposeMessage
}
