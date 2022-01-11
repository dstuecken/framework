<?php

namespace DS\Component\Slack;

use Maknz\Slack\Client;
use Maknz\Slack\Message;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class Slack
{
    /**
     * @var Client
     */
    private $errorsApi = null;

    /**
     * @var Client
     */
    private $signupsApi = null;

    /**
     * @var null
     */
    private $config = null;

    /**
     * @return Client
     */
    public function getErrorsApi(): Client
    {
        if (!$this->errorsApi)
        {
            $this->errorsApi = new Client(
                $this->config['errors']['webhook'], [
                    'username' => 'Dennis Stücken',
                    'channel' => $this->config['errors']['channel'],
                    'link_names' => true,
                ]
            );
        }

        return $this->errorsApi;
    }

    /**
     * @return Client
     */
    public function getSignupsApi(): Client
    {
        if (!$this->signupsApi)
        {
            $this->signupsApi = new Client(
                $this->config['signups']['webhook'], [
                    'username' => 'Dennis Stücken',
                    'channel' => $this->config['signups']['channel'],
                    'link_names' => true,
                ]
            );
        }

        return $this->signupsApi;
    }

    /**
     * @return Client
     */
    public function getNotificationsApi(): Client
    {
        if (!$this->signupsApi)
        {
            $this->signupsApi = new Client(
                $this->config['notifications']['webhook'], [
                    'username' => 'Dennis Stücken',
                    'channel' => $this->config['notifications']['channel'],
                    'link_names' => true,
                ]
            );
        }

        return $this->signupsApi;
    }

    /**
     *
     * Available colors: good (green), warning (yellow), danger (red), or any hex color code (eg. #439FE0)
     *
     * @param string      $text
     * @param string      $header
     * @param SlackStat[] $stats
     * @param string      $color
     *
     * @return Slack
     */
    public function sendStatsNotificationMessage(string $text, string $header = '', array $stats = [], string $color = 'good'): self
    {
        $message = new Message($this->getNotificationsApi());

        $fields = [];

        foreach ($stats as $stat)
        {
            if ($stat === null)
            {
                continue;
            }

            $fields[] = [
                'title' => $stat->text,
                'value' => $stat->value,
                'short' => $stat->short,
            ];
        }

        $message->attach(
            [
                'fallback' => $header,
                'text' => $header,
                'color' => $color,
                'fields' => $fields,
                'mrkdwn_in' => ['pretext', 'text'],
            ]
        );
        $message->send($text);

        return $this;
    }

    /**
     * @param string $text
     * @param string $header
     * @param bool   $result
     *
     * @return Slack
     */
    public function sendResultNotificationMessage(string $text, string $header = 'Server health: good', bool $result = true): self
    {
        $message = new Message($this->getNotificationsApi());

        $message->attach(
            [
                'fallback' => $header,
                'text' => $header,
                'color' => $result ? 'good' : 'danger',
            ]
        );
        $message->send($text);

        return $this;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function sendNotificationMessage(string $text): self
    {
        $message = new Message($this->getNotificationsApi());
        $message->send($text);

        return $this;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function sendSignupMessage(string $text): self
    {
        $message = new Message($this->getSignupsApi());
        $message->send($text);

        return $this;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function sendErrorMessage(string $text): self
    {
        $message = new Message($this->getErrorsApi());
        $message->send($text);

        return $this;
    }

    /**
     * Slack constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }
}
