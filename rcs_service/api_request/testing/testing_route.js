const express = require("express");
const router = express.Router();
const testing_1 = require("./testing");
const main = require('../../logger');
const db = require("../../db_connect/connect");

router.post(
  "/",
  async function (req, res, next) {
    try {
      var logger = main.logger
      var result = await testing_1.testing(req);
       

      logger.info("[API RESPONSE] " + JSON.stringify(result))

      res.json(result);
    } catch (err) {
      console.error(`Error while getting data`, err.message);
      next(err);
    }
  }
);

module.exports = router;

