/*
API that allows your frontend to communicate with your backend server (Node.js) for processing and retrieving data.
To access a MySQL database with Node.js and can be use it.
This page is used in report functions which is used to get the report details.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const db = require("../../db_connect/connect");
const dynamic_db = require("../../db_connect/dynamic_connect");
const main = require('../../logger');
// Filter_Campaign_Name function - start
async function Filter_Campaign_Name(req) {
  try {
    var logger_all = main.logger_all
    //  Get all the req header data
    const header_token = req.headers['authorization'];

    // get all the req data
    var filter_date = req.body.filter_date;
    var user_id = req.body.user_id;
    var filter_user = req.body.filter_user;
    var filter_department = req.body.filter_department;
    // declare the variables
    var filter_date_first;
    var filter_date_second;
    var filter_date_1;
    // declare the array
    var array_list_user_id = [];
    // To initialize a variable with an empty string value
    var get_campaign_name = '';
    var list_user_id = '';
    var query_1 ;
    // Query parameters 
    logger_all.info(" [Filter_Campaign_Name response query parameters] : " + JSON.stringify(req.body));
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
    else {
      // otherwise to get the user details
      user_id = get_user_id[0].user_id;
    }

    if (filter_user && filter_department) {
      user_id = filter_department;
      logger_all.info(`SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `);
      query_1 = ` SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `;
      logger_all.info(query_1);
    } else if (filter_user){
      user_id = filter_user;
      logger_all.info(`SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `);
      query_1 = ` SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `;
      logger_all.info(query_1);
    }
   else{
  if(filter_department){
      user_id = filter_department;
    }
    // to get the user ids to act as the parent ids.
    logger_all.info(`SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `);
     query_1 = ` SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `;
    logger_all.info(query_1);
    }
    // to get the user ids to act as the parent ids.
    const query = await db.query(query_1 )
    logger_all.info(" [select query response] : " + JSON.stringify(query))

    if (query.length > 0) { // if number of query length  is available then process the will be continued
      // loop all the get the user ids to act as the array_list_user_id.
      for (var i = 0; i < query.length; i++) {
        list_user_id += ", " + query[i].user_id;
        array_list_user_id.push(query[i].user_id);
      }
    }
    // date function
    filter_date_1 = filter_date.split("-");
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
        // if number of array_list_user_id length is available then process the will be continued
        // loop all the array_list_user_id.

        for (i = 0; i < array_list_user_id.length; i++) {
          get_campaign_name += ` SELECT  wht.compose_rcs_id, wht.store_id, wht.campaign_name, wht.message_type, wht.total_mobileno_count,DATE_FORMAT(wht.rcs_entry_date,'%d-%m-%Y %h:%i:%s %p')  FROM rcs_${array_list_user_id[i]}.compose_rcs_tmpl_${array_list_user_id[i]} wht where (date(wht.rcs_entry_date) BETWEEN '${slt_date}' and '${slt_date}') UNION`;
        }

        currentDate.setUTCDate(currentDate.getUTCDate() + steps);
      }
      return dateArray;
    }
    const dates = dateRange(filter_date_1[0], filter_date_1[1]);

    var lastIndex = get_campaign_name.lastIndexOf(" ");
    var get_campaign_name1 = get_campaign_name.substring(0, lastIndex);
    logger_all.info('[select query request] : ' + get_campaign_name1);
    var get_campaign_name_1 = await dynamic_db.query(get_campaign_name1, null, `rcs_${user_id}`);
    logger_all.info(" [select query response] : " + JSON.stringify(get_campaign_name_1));
    // if the get_campaign_name_1 length is '0' to send the no available data.otherwise it will be return the get_campaign_name_1 details.
    if (get_campaign_name_1.length > 0) {
      return {
        response_code: 1,
        response_status: 200,
        num_of_rows: get_campaign_name_1.length,
        response_msg: 'Success',
        report: get_campaign_name_1
      };

    }
    else {
      return {
        response_code: 1,
        response_status: 204,
        response_msg: 'No data available'
      };
    }
  }
  catch (e) { // any error occurres send error response to client
    logger_all.info(" [Filter_Campaign_Name response failed response] : " + e)
    return {
      response_code: 0,
      response_status: 201,
      response_msg: 'Error occured'
    };
  }
}
// Filter_Campaign_Name function - End

// ReportCampaignName - Start
async function ReportCampaignName(req) {
  try {
    console.log("ReportCampaignName");
    var logger_all = main.logger_all
    //  Get all the req header data
    const header_token = req.headers['authorization'];

    // get all the req data
    var filter_date = req.body.filter_date;
    var campaign_name = req.body.campaign_name;
    var filter_user = req.body.filter_user;
    var filter_department = req.body.filter_department;
    // declare the variables
    var user_id,filter_date_1;
    var campaign_name_filer;  
    // declare the array
    var array_list_user_id = [];
    var comp_what_id_arr = [];  
    // To initialize a variable with an empty string value
    var list_user_id = '';
    var comp_wastp_id = '';
    var get_campaign_report = '';
    var get_campaign_name = '';
    // query parameters
    logger_all.info(" [ReportFilterCampaign response query parameters] : " + JSON.stringify(req.body));
    // To get the User_id
    var get_user = `SELECT * FROM user_management where bearer_token = '${header_token}' AND usr_mgt_status = 'Y' `;
    if (req.body.user_id) {
      get_user = get_user + `and user_id = '${req.body.user_id}' `;
    }
    logger_all.info("[select query request] : " + get_user);
    const get_user_id = await db.query(get_user);
    logger_all.info("[select query response] : " + JSON.stringify(get_user_id));

    if (get_user_id.length == 0) { // If get_user not available send error response to client in ivalid token
      logger_all.info("Invalid Token")
      return { response_code: 0, response_status: 201, response_msg: 'Invalid Token' };
    }
    else {// otherwise to get the user details
      user_id = get_user_id[0].user_id;
    }

    if (filter_user) { //filter user using
      user_id = filter_user;
    }
    if (filter_department) { //filter_department using
      user_id = filter_department;
    }
    // to get the user ids to act as the parent ids.
    logger_all.info(`SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `);
    const query = await db.query(` SELECT user_id FROM user_management where (user_id = '${user_id}' or parent_id = '${user_id}') `);
    logger_all.info(" [select query response] : " + JSON.stringify(query))
    // if number of query length  is available then process the will be continued
    // loop all the get the user ids to push the array in  list_user_id,array_list_user_id.
    if (query.length > 0) {
      for (var i = 0; i < query.length; i++) {
        list_user_id += ", " + query[i].user_id;
        array_list_user_id.push(query[i].user_id);
      }
      // logger_all.info(array_list_user_id);
    }
    // if number of array_list_user_id length  is available then process the will be continued
    // loop all the array_list_user_id.
  
    for (i = 0; i < campaign_name.length; i++) {
     campaign_name_filer = campaign_name[i].replaceAll(",", "','");
     logger_all.info(campaign_name[i]+"+++"+campaign_name[i].replaceAll(",", "','")+"+++"+campaign_name_filer);
    }
    logger_all.info(campaign_name_filer);
  
    for (i = 0; i < array_list_user_id.length; i++) {
        get_campaign_name += ` SELECT * FROM rcs_${array_list_user_id[i]}.compose_rcs_tmpl_${array_list_user_id[i]} wht where wht.campaign_name in ('${campaign_name_filer}') UNION`;
    }

    var lastIndex = get_campaign_name.lastIndexOf(" ");
    var get_campaign_name1 = get_campaign_name.substring(0, lastIndex);
    logger_all.info('[select query request] : ' + get_campaign_name1);
    var get_campaign_name_1 = await dynamic_db.query(get_campaign_name1, null, `rcs_${user_id}`);
    logger_all.info(" [select query response] : " + JSON.stringify(get_campaign_name_1));


   
    // if the getcampaign name length is greater than the process will be continued.otherwise to send the error message.
    if (get_campaign_name_1.length > 0) {
      for (var k = 0; k < get_campaign_name_1.length; k++) {
       comp_what_id_arr.push(get_campaign_name_1[k].compose_rcs_id);
      }
      comp_wastp_id = comp_what_id_arr.toString().replaceAll(",", "','");
      logger_all.info(comp_wastp_id);
      
      
      // loop all the array_list_user_id in length
        // date function
    filter_date_1 = filter_date.split("-");
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
        // if number of array_list_user_id length is available then process the will be continued
        // loop all the array_list_user_id.
        for (i = 0; i < array_list_user_id.length; i++) {
          get_campaign_report += ` SELECT wht.user_id,wht.campaign_name,wht.store_id, usr.user_name,ussr.user_type,ussr.user_type,ml.available_messages,DATE_FORMAT(wht.rcs_entry_date,'%d-%m-%Y') entry_date, count(stt.comwtap_status_id) total_msg,(select count(distinct comwtap_status_id) from rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} where response_status = 'S' and compose_rcs_id in ('${comp_wastp_id}') ) total_success,(select count(distinct comwtap_status_id) from rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} where  response_status = 'S' and delivery_status = 'Y' and compose_rcs_id in ('${comp_wastp_id}') ) total_delivered,(select count(distinct comwtap_status_id) from rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} where response_status = 'S' and read_status = 'Y' and compose_rcs_id in ('${comp_wastp_id}')) total_read,(select count(distinct comwtap_status_id) from rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} where response_status = 'F' and compose_rcs_id in ('${comp_wastp_id}')) total_failed,(select count(distinct comwtap_status_id) from rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} where response_status = 'I' and compose_rcs_id in ('${comp_wastp_id}')) total_invalid,(select count(distinct comwtap_status_id) from rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} where (response_status not in ('I', 'F', 'S') or response_status is null) and compose_rcs_id in ('${comp_wastp_id}')) total_waiting FROM rcs_${array_list_user_id[i]}.compose_rcs_tmpl_${array_list_user_id[i]} wht left join rcs_${array_list_user_id[i]}.compose_rcs_status_tmpl_${array_list_user_id[i]} stt on wht.compose_rcs_id = stt.compose_rcs_id left join rcs.user_management usr on wht.user_id = usr.user_id left join rcs.message_limit ml on wht.user_id = ml.user_id left join rcs.user_master ussr on usr.user_master_id = ussr.user_master_id where usr.user_id = '${array_list_user_id[i]}' and wht.compose_rcs_id in ('${comp_wastp_id}') and (date(wht.rcs_entry_date) BETWEEN '${slt_date}' and '${slt_date}')   UNION`;
        }
        currentDate.setUTCDate(currentDate.getUTCDate() + steps);
      }
      return dateArray;
    }
    const dates = dateRange(filter_date_1[0], filter_date_1[1]);
      var lastIndex = get_campaign_report.lastIndexOf(" ");
      var get_campaign_report_1 = get_campaign_report.substring(0, lastIndex);
      logger_all.info('[select query request] : ' + get_campaign_report_1);
      var get_campaign_name_1 = await dynamic_db.query(get_campaign_report_1, null, `rcs_${user_id}`);

    }
 
    //  if the get_campaign_name_1 length is '0'.to send the no data available.
    if (get_campaign_name_1.length == 0) {
      return {
        response_code: 1,
        response_status: 204,
        response_msg: 'No data available'
      };
    } else { //otherwise to send the get_campaign_name_1 details
      return {
        response_code: 1,
        response_status: 200,
        num_of_rows: get_campaign_name_1.length,
        response_msg: 'Success',
        report: get_campaign_name_1
      };
    }
    
  }
  catch (e) { // any error occurres send error response to client
    logger_all.info(" [ReportFilterCampaign response failed response] : " + e)
    return {
      response_code: 0,
      response_status: 201,
      response_msg: 'Error occured'
    };
  }
}
// ReportCampaignName - End

// using for module exporting
module.exports = {
  Filter_Campaign_Name,
  ReportCampaignName
};


