/*
This page is used to connect the database for all api process.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
const mysql = require('mysql2/promise');
const config = require('./config');
const main = require('../logger')
const { logger, logger_all } = main;
const pool = mysql.createPool(config.db);
pool.query("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
async function query(sql, params) {

  logger_all.info("[API Query] " + sql)
  const [rows, fields] = await pool.execute(sql, params);
  logger_all.info("[API Response] " + rows)
  return rows;
}

module.exports = {
  query
}
