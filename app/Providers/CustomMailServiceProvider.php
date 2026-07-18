<?php

namespace App\Providers;

use App\Models\MailSetting;
use App\Service\CustomTransportManager;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Mail\TransportManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class CustomMailServiceProvider extends MailServiceProvider
{
    public function overrideMailerConfig()
    {
        $settings = MailSetting::query()->first();
        if ($settings) //checking if table is not empty
        {
            $config = array(
                'driver'        => $settings->driver,
                'host'          => $settings->host,
                'port'          => $settings->port,
                'from'          => [
                    'address'   => $settings->email,
                    'name'      => $settings->name
                ],
                'encryption'    => $settings->encryption,
                'username'      => $settings->email,
                'password'      => $settings->password,
                'sendmail' => '/usr/sbin/sendmail -bs',
                'markdown' => [
                    'theme' => 'default',
                    'paths' => [
                        resource_path('views/vendor/mail')
                    ]
                ],
                'log_channel' => null
            );
            Config::set('mail', $config);
            $this->registerSwiftTransport($config);
            $app = App::getInstance();

            $app['swift.transport'] = $app->share(function ($app) {
                return new TransportManager($app);
            });

            $mailer = new \Swift_Mailer($app['swift.transport']->driver());
            Mail::setSwiftMailer($mailer);
        }



        // Once we have the transporter registered, we will register the actual Swift
        // mailer instance, passing in the transport instances, which allows us to
        // override this transporter instances during app start-up if necessary.

    }
}
