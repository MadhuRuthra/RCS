/*
This page is used to connect the database for all api process.

Version : 1.0
Author : Madhubala (YJ0009)
Date : 05-Jul-2023
*/
const env = process.env;

const config = {
  db: { 
     host: env.DB_HOST || 'localhost',
     user: env.DB_USER || 'rcs_messenger',
      password: env.DB_PASSWORD || 'RCS-Ms@YJ_626101',
      database: env.DB_NAME || 'rcs',
  },
  listPerPage: env.LIST_PER_PAGE || 10,
};
  
module.exports = config;
