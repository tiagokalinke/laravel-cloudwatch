<?php

namespace TiagoKalinke\Laravel\CloudWatchLogs\Providers;

use Aws\Laravel\AwsFacade;
use DateTime;
use DateTimeZone;
use Illuminate\Support\ServiceProvider;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CloudWatchLogsServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../../config/cloudwatch.php' => config_path('cloudwatch.php'),
        ]);
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../../config/cloudwatch.php', 'cloudwatch.php'
        );

        app()->singleton(LoggerInterface::class, function () {
            return new Logger('laravel', $this->getMonologHandler());
        });
    }

    /**
     * Extends the default logging implementation with additional handlers if configured in .env
     *
     * @return array of type \Monolog\Handler\AbstractHandler
     */
    protected function getMonologHandler()
    {

        $clientConfig = config('cloudwatch');
        $awsCredentials = $clientConfig['aws'];

        $currentTzDate = new DateTime("now", new DateTimeZone(env("APP_TIMEZONE", "UTC")));
        $streamName = $clientConfig['cloudwatch']['streamName'];
        if (isset($clientConfig['cloudwatch']['streamDateFormat']) && !empty($clientConfig['cloudwatch']['streamDateFormat'])) {
            $streamName = $currentTzDate->format($clientConfig['cloudwatch']['streamDateFormat']);
        }

        $cwClient = AwsFacade::createClient('CloudWatchLogs');
        $cwRetentionDays = $clientConfig['cloudwatch']['retention'];
        $cwGroupName = $clientConfig['cloudwatch']['groupName'];
        $cwStreamNameInstance = $streamName;

        $handler = new CloudWatch($cwClient, $cwGroupName, $cwStreamNameInstance, $cwRetentionDays);
        $handler->setFormatter(new LineFormatter(null, null, false, true));

        $handlers = [
            $handler,
        ];

        return $handlers;
    }
}
