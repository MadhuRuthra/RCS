/*
Routes are used in direct incoming API requests to backend resources.
It defines how our application should handle all the HTTP requests by the client.
This page is used to routing the reports.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
// Import the required packages and libraries
const express = require("express");
const router = express.Router();
// Import the report functions page
const CampaignReport = require("./campaign_report");
const Summary_Report = require("./summary_report");
const Filter_campaign_name = require("./campaign_name_filter");
// Import the validation page
const MobileReportValidation = require("../../validation/mobile_report_validation");
const CampaignReportValidation = require("../../validation/campaign_report_validation");
const MessengerResponseListValidation = require("../../validation/messenger_response_list");
const ReportFilterUserValidation = require("../../validation/report_filter_user");
const ReportFilterDepartmentValidation = require("../../validation/report_filter_department");
const OtpsummaryreportValidation = require("../../validation/opt_summary_report");
const OtpdeliveryrptValidation = require("../../validation/otp_delivery_rpt_validation");
const campaign_nameValidation = require("../../validation/campaign_namevalidation");
const filtercampnameValidation = require("../../validation/filter_camp_name_validation");
// Import the default validation middleware
const validator = require('../../validation/middleware')
const valid_user = require("../../validation/valid_user_middleware");
const main = require('../../logger');

const ReportGeneration = require("./report_generation");
const ReportGenerationValidation = require("../../validation/report_generation_validation");

router.get(
    "/report_generation",
    validator.body(ReportGenerationValidation),
    // valid_user,
    async function (req, res, next) {
        try {
            var logger = main.logger
            var logger_all = main.logger_all

            var result = await ReportGeneration.report_update(req);

            logger.info("[API RESPONSE] " + JSON.stringify(result))

            res.json(result);

        } catch (err) {
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);

// mobile_number_report - start
router.get(
    "/mobile_number_report",
    validator.body(MobileReportValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the MobileReport function
            var logger = main.logger
            var result = await CampaignReport.MobileReport(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// mobile_number_report - end
// campaign_report - start
router.get(
    "/campaign_report",
    validator.body(CampaignReportValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the CampaignReport function
            var logger = main.logger
            var result = await CampaignReport.CampaignReport(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// campaign_report - end
// campaign_status - start
router.get(
    "/campaign_status",
    validator.body(CampaignReportValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the CampaignSummaryReport function
            var logger = main.logger
            var result = await CampaignReport.CampaignSummaryReport(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// campaign_status - end
// messenger_response_list - start
router.post(
    "/messenger_response_list",
    validator.body(MessengerResponseListValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the MessengerResponseList function
            var logger = main.logger
            var result = await CampaignReport.MessengerResponseList(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// messenger_response_list - end
// report_filter_user - start
router.post(
    "/report_filter_user",
    validator.body(ReportFilterUserValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the MessengerResponseList function
            var logger = main.logger
            var result = await CampaignReport.ReportFilterUser(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// report_filter_user - end
// report_filter_department - start
router.post(
    "/report_filter_department",
    validator.body(ReportFilterDepartmentValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the ReportFilterDepartment function
            var logger = main.logger
            var result = await CampaignReport.ReportFilterDepartment(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// report_filter_department - end
// summary_report - start
router.post(
    "/summary_report",
    validator.body(OtpsummaryreportValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the OtpSummaryReport function
            var logger = main.logger
            var result = await Summary_Report.SummaryReport(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// summary_report - end
// detailed_report - start
router.post(
    "/detailed_report",
    validator.body(OtpdeliveryrptValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the OtpDeliveryReport function
            var logger = main.logger
            var result = await CampaignReport.OtpDeliveryReport(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result));
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// detailed_report - end
// report_campaign_name - start
router.post(
    "/report_campaign_name",
    validator.body(campaign_nameValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the ReportCampaignName function
            var logger = main.logger
            var result = await Filter_campaign_name.ReportCampaignName(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// report_campaign_name - end
// filter_campaign_name - start
router.post(
    "/filter_campaign_name",
    validator.body(filtercampnameValidation),
    valid_user,
    async function (req, res, next) {
        try {// access the Filter_Campaign_Name function
            var logger = main.logger
            var result = await Filter_campaign_name.Filter_Campaign_Name(req);
            logger.info("[API RESPONSE] " + JSON.stringify(result))
            res.json(result);
        } catch (err) {// any error occurres send error response to client
            console.error(`Error while getting data`, err.message);
            next(err);
        }
    }
);
// filter_campaign_name - end

module.exports = router;
