## What is this?

This is CakePHP plugin to provide a log engine that post to slack using incoming webhooks.

Please see detail [how to configure webhooks on slack](https://api.slack.com/incoming-webhooks).

The engine uses [Slack for PHP](https://github.com/maknz/slack) and is just thin wrapper for the library.

## Installation

```
composer require jeffersonsimaogoncalves/cakephp-slack-log-engine
```

## Requirements

* PHP 7
* CakePHP 3.x

## Usage

### Configure log

In your app.php, you can configure like:

    'Log' => [
        'error' => [
            'className' => 'SlackLogEngine\Log\Engine\SlackLogEngine',
            // Your slack hook URL here
            'hookUrl' => 'https://hooks.slack.com/services/xxxxx/xxxxx/xxxxxxxxxx',
            // Send logs of following levels to slack
            'levels' => ['error', 'critical', 'alert', 'emergency'],
        ],
    ],

### Log options

Either `client` or `hookUrl` is required.

- `hookUrl` [string] Slack hook url.
- `client` [\Maknz\Slack\Client] Slack client instance for custom.
- `clientClass` [string(optional)] slack client class. This option is used only with `hookUrl` option.

Other available settings can be seen at [Slack for PHP Official Docs](https://github.com/maknz/slack#settings)
## Credits

This work is based on the [code by Hiroki Shimizu](https://github.com/hiromi2424/cakephp-slack-log-engine).