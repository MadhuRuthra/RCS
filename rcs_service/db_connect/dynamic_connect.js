/*
This page is used to connect the database for all api process.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
const mysql = require('mysql2/promise');
const env = process.env;

async function query(sql, params, db_name) {
  var db =  { 
     host: env.DB_HOST || 'localhost',
     user: env.DB_USER || 'rcs_messenger',
     password: env.DB_PASSWORD || 'RCS-Ms@YJ_626101',
     
  }
  db['database'] = db_name;
  const pool = mysql.createPool(db);

  const [rows, fields] = await pool.execute(sql, params);
  pool.end()
  return rows;
}

module.exports = {
  query
}
