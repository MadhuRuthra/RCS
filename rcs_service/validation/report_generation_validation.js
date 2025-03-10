const Joi = require("@hapi/joi");

const ReportGener = Joi.object().keys({
  database: Joi.string().required().label("Database"),
  table_name: Joi.string().required().label("Table Name"),
  compose_rcs_id: Joi.string().required().label("Compose rcs Id"),
  report_group: Joi.string().required().label("Report Group"),
  compose_user: Joi.string().required().label("User ID"),
}).options({abortEarly : false});

module.exports = ReportGener
