<?php


namespace App\Service;
use App\Models\MailSetting;
use Illuminate\Mail\TransportManager;


class CustomTransportManager extends TransportManager
{
    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        if( $settings = MailSetting::all() ){

            $this->app['config']['mail'] = [
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
            ];
        }

    }
}
