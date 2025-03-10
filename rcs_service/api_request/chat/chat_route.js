/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the chat.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the functions page
const ChatApi = require("./chat_api");
// Import the validation page
const GetNumbersValidation = require("../../validation/get_numbers_validation");
const GetChatValidation = require("../../validation/get_chat_validation");
// Import the default validation middleware
const validator = require('../../validation/middleware')
const valid_user = require("../../validation/valid_user_middleware");
const main = require('../../logger');

// get numbers api -start
router.post(
    "/get_numbers",
    validator.body(GetNumbersValidation),
    valid_user,
    async function (req, res, next) {
        try {
            // access the getNumbers function
            var logger = main.logger

            var result = await ChatApi.getNumbers(req)

            logger.info("[API RESPONSE] " + JSON.stringify(result))

            res.json(result);
        } catch (err) { // any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// get numbers api - End
//  get_chat api - start
router.post(
    "/get_chat",
    validator.body(GetChatValidation),
    valid_user,
    async function (req, res, next) {
        try {
            // access the getChat function
            var logger = main.logger

            var result = await ChatApi.getChat(req);

            logger.info("[API RESPONSE] " + JSON.stringify(result))

            res.json(result);
        } catch (err) { // any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
//  get_chat api - End
module.exports = router;
