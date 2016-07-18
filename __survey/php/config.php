<?php
define('BASE_URL', 'http://phsweb2119.partners.org/survey/');

/** You main websites URL */
define('SITE_URL', 'http://phsweb2119.partners.org/');

define('DB_NAME', 'maps');

define('DB_LOGIN_AUDIT', 'maps_login_audit');

/** MySQL login audit database password */
define('DB_FAILED_LOGINS_USER',  getenv('MAPS_FAILED_LOGINSUSER'));
define('DB_FAILED_LOGINS_PASSWORD',  getenv('MAPS_FAILED_LOGINSPW'));

/** MySQL database username */
define('DB_USER',  getenv('MAPS_DBUSR'));

/** MySQL database password */
define('DB_PASSWORD',  getenv('MAPS_DBPW'));

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('TABLES_PREFIX', 'survey_');

define('ROWS_PER_PAGE', 15);

/** Set to false when in production to suppress errors for users. */
define('DEVELOPMENT', true);

/** Cookies Name */
define('COOKIE_NAME', 'SurveyEngineApp');

/** Time till a cookie expires */
define('COOKIE_TIME', 10368000);

/** Webmaster's email address. Will be used in the from part of email. */
define('WEBMASTER_EMAIL', 'skylerdesign@gmail.com');

/** Allow users to view results when done */
define('ALLOW_PUBLIC_VIEW_RESULTS', false);

/** Your preferred timezone */
define('TIME_ZONE', "UTC");

/** Use SEO friendly url for the surveys */
define('SEO_FRIENDLY', FALSE);

/** LOCKOUT **/
define("ATTEMPTS_NUMBER", "3");
define("TIME_PERIOD", "5");
define("TBL_ATTEMPTS", "user_login_attempts");
