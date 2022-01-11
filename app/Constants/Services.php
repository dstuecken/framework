<?php
namespace DS\Constants;

/**
 * DS-Framework
 *
 * Router Initialization
 *
 * @package DS
 * @version $Version$
 */
class Services
{
    /**
     * Application
     *
     * @definition Application.php
     */
    const APPLICATION = 'application';

    /**
     * Config Service.
     *
     * @definition Config.php
     */
    const CONFIG = 'config';

    /**
     * Logger
     *
     * @definition bootstrap/Services/Logger.php
     */
    const LOGGER = 'logger';

    /**
     * Logger
     *
     * @definition bootstrap/Services/Logger.php
     */
    const CLILOGGER = 'cliLogger';

    /**
     * Logger
     *
     * @definition Application.php
     */
    const COOKIES = 'cookies';

    /**
     * Logger
     *
     * @definition bootstrap/Services/Logger.php
     */
    const ERRORLOGGER = 'errorLogger';

    /**
     * Debug Service.
     *
     * @definition Application.php
     */
    const DEBUG = 'debug';

    /**
     * Authorization Service.
     *
     * @definition bootstrap/Services/Auth.php
     */
    const AUTH = 'auth';

    /**
     * View Service.
     *
     * @definition bootstrap/Services/View.php
     */
    const VIEW = 'view';

    /**
     * Request service
     *
     * Provided by phalcon
     */
    const REQUEST = 'request';

    /**
     * Memcache caching service.
     *
     * @definition bootstrap/Services/Cache.php
     */
    const MEMCACHE = 'memcache';

    /**
     * Redis caching service.
     *
     * @definition bootstrap/Services/Cache.php
     */
    const REDIS = 'redis';

    /**
     * Predis caching service.
     *
     * @definition bootstrap/Services/Predis.php
     */
    const PREDIS = 'predis';

    /**
     * Models manager
     *
     * @definition bootstrap/Services/Cache.php
     */
    const MODELSMANAGER = 'modelsManager';

    /**
     * Models meta data cache
     *
     * @definition bootstrap/Services/Cache.php
     */
    const MODELSMETADATA = 'modelsMetadata';

    /**
     * Mailer service
     *
     * @definition bootstrap/Services/Mailer.php
     */
    const MAILER = 'mailer';

    /**
     * Raven Client service (sentry.io bug tracker)
     *
     * @definition bootstrap/Services/RavenClient.php
     */
    const RAVENCLIENT = 'ravenClient';
}
