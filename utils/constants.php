<?php

namespace utils;

class Constants
{
    const STORAGE_PATH_NAME = 'storage';
    const HASH_KEY = '123456789';
    const HASH_ALGORITHM = 'sha256';
    const AUTHORIZATION_HEADER_NAME = 'www-token-auth';
    const HEADER_CONTENT_TYPE = 'application/json';
    const HEADER_XCTO = 'nosniff';
    const RETRY_AFTER = 60;
    const RATE_LIMIT = 100;
    const RATE_LIMIT_REMAINING = 50;
}
