/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the upload routes.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the report functions page
const UploadApi = require("./upload_api");
// Import the validation page
const uploadMediaValidation = require("../../validation/upload_media_validation");
// Import the default validation middleware
const validator = require('../../validation/middleware')
const valid_user = require("../../validation/valid_user_middleware");
const main = require('../../logger');
// uplaodmedia function - start
router.post(
    "/",
    validator.body(uploadMediaValidation),
    valid_user,
    async function (req, res, next) {
        try { // access the uplaodmedia function
            var logger = main.logger
            var result = await UploadApi.uplaodmedia(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) { // any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// uplaodmedia function - end
module.exports = router;
