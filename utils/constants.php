<?php

namespace utils;

class Constants
{
    const APP_NAME = 'YosyPics_Api';

    const STORAGE_PATH_NAME = 'storage';
    const HASH_KEY = '123456789';
    const HASH_ALGORITHM = 'sha256';
    const AUTHORIZATION_HEADER_NAME = 'www-token-auth';
    const HEADER_CONTENT_TYPE = 'application/json';
    const HEADER_XCTO = 'nosniff';
    const RETRY_AFTER = 60;
    const RATE_LIMIT = 100;
    const RATE_LIMIT_REMAINING = 50;

    const MAIL_HOST = "sandbox.smtp.mailtrap.io";
    const MAIL_PORT = 2525;
    const MAIL_USERNAME = "1a5079b6023672";
    const MAIL_PASSWORD = "10ef83a14b0304";
    const MAIL_ENCRYPTION = "tls";
    const MAIL_FROM_ADDRESS = "admin@yosypics.com";
    const MAIL_FROM_NAME = "YosyPics Admin";
}
