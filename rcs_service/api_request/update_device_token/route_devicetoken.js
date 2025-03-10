/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the device token page.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the report functions page
const update_devicetoken = require("./update_devicetoken");
// Import the validation page
const DeviceTokenValidation = require("../../validation/update_devicetoken_validation");
// Import the default validation middleware
const validator = require('../../validation/middleware');
const valid_user = require("../../validation/valid_user_middleware");
const main = require('../../logger');
// update_deviceToken - start
router.post(
    "/update_deviceToken",
    validator.body(DeviceTokenValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the getTemplate function
            var logger = main.logger
            var result = await update_devicetoken.devicetokenupdate(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result));
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// update_deviceToken - end
module.exports = router;
