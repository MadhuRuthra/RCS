/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the dashoard.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the functions page
const dashboard_list = require("./dashboard_list");
// Import the validation page
const DashBoardotpValidation = require("../../validation/dashboard_otp");
// Import the default validation middleware
const validator = require('../../validation/middleware');
const valid_user = require("../../validation/valid_user_middleware");
const main = require('../../logger');
// dashboard api -start
router.post(
    "/dashboard",
    //validator.body(DashBoardotpValidation),
    //valid_user,
    async function (req, res, next) {
        try { // access the getNumbers function
            var logger = main.logger

            var result = await dashboard_list.otp_Dash_Board(req);

            logger.info("[API RESPONSE] " + JSON.stringify(result))

            res.json(result);
        } catch (err) { // any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// dashboard api - End
module.exports = router;
