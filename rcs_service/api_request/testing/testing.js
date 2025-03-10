
const main = require('../../logger')
var util = require('util');
require('dotenv').config()

const env = process.env
const api_url = env.API_URL;

var axios = require('axios');

async function testing(req) {
    try {
        var logger_all = main.logger_all

        // get current Date and time
        var day = new Date();
        var today_date = day.getFullYear() + '-' + (day.getMonth() + 1) + '-' + day.getDate();
        var today_time = day.getHours() + ":" + day.getMinutes() + ":" + day.getSeconds();
        var current_date = today_date + ' ' + today_time;
        // get all the req data
        // var api_key = req.body.api_key;
        // var user_id;

        logger_all.info("[query parameters] : " + JSON.stringify(req.query));

        if (req.query.data) {
            query_data = req.query.data;
        }
	
       else {
            return { response_code: 1, response_status: 204, response_msg: 'No data available' };
        }
        if(query_data != ""){
            return { response_code: 1, response_status: 200, response_msg: 'Success', data: query_data };
        }

    }
        catch (e) {
        logger_all.info("[messenger response failed response] : " + e)
        return { response_code: 0, response_status: 201, response_msg: 'Error occured' };
    }
}


module.exports = {
    testing,
};