<?php

namespace SlackLogEngine\Log\Engine;

use Cake\Core\Configure;
use Cake\Log\Engine\BaseLog;
use Exception;
use Maknz\Slack\Client as SlackClient;

/**
 * Log engine to post to slack with hook url.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 */
class SlackLogEngine extends BaseLog
{

    /**
     * Instance of SlackClient class
     *
     * @var \Maknz\Slack\Client
     */
    protected $_SlackClient = null;

    /**
     * The default config used unless overridden by runtime configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'types'       => null,
        'levels'      => [],
        'scopes'      => [],
        'clientClass' => '\Maknz\Slack\Client',
    ];

    /**
     * Configuration is valid or not
     *
     * @var boolean
     */
    protected $_valid = true;

    /**
     * Sets protected properties based on config provided
     * Either `client` or `hookUrl` is required
     *
     * - `hookUrl` [string] Slack hook url.
     * - `client` [\Maknz\Slack\Client] Slack client instance for custom.
     * - `clientClass` [string(optional)] slack client class. This option is used only with `hookUrl` option.
     *
     * Other available settings can be seen at https://github.com/maknz/slack#settings
     *
     * @param array $config Configuration array
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $config = $this->_config;

        if (isset($config['client'])) {
            $this->_SlackClient = $config['client'];
            unset($config['client']);
        } else if (isset($config['hookUrl'])) {
            $hookUrl = $config['hookUrl'];
            $className = $config['clientClass'];
            unset($config['hookUrl'], $config['clientClass']);
            $this->_SlackClient = new $className($hookUrl, $config);
        } else {
            $this->_valid = false;
        }
    }

    /**
     * Get slack client
     *
     * @return \Maknz\Slack\Client slack client
     */
    public function getClient()
    {
        return $this->_SlackClient;
    }

    /**
     * Set slack client
     *
     * @param \Maknz\Slack\Client $client slack client
     *
     * @return \Maknz\Slack\Client slack client
     */
    public function setClient(SlackClient $client)
    {
        $this->_SlackClient = $client;
        $this->_valid = true;

        return $client;
    }

    /**
     * Implements post a log to slack.
     *
     * @param string $level The severity level of the message being written.
     *    See Cake\Log\Log::$_levels for list of possible levels.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message
     *
     * @return bool success
     * @throws \Exception
     */
    public function log($level, $message, array $context = [])
    {
        if (!$this->_valid) {
            return false;
        }

        $message = $this->_format($message, $context);
        $output = date('Y-m-d H:i:s') . ' ' . ucfirst($level) . ': ' . $message;
        try {
            $this->_SlackClient->send($output);
        } catch (Exception $e) {
            $this->_valid = false;
            // Rethrow exception when debug mode
            if (Configure::read('debug')) {
                throw $e;
            }

            // otherwise DO nothing
            return false;
        }

        return true;
    }
}
