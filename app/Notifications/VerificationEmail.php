<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerificationEmail extends Notification
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    private $password;

    /**
     * Create a new notification instance.
     *
     * @param $password
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::getFromJson('Xác nhận địa chỉ Email'))
            ->line(Lang::getFromJson('Chúng tôi nhận được yêu cầu tạo tài khoản cho email này từ Admin của bạn. Vui lòng bấm vào link bên dưới để đăng nhập.'))
            ->line(Lang::getFromJson('Mật khẩu của bạn là: ' . $this->password))
            ->action(
                Lang::getFromJson('Đăng nhập ngay'),
                $this->verificationUrl()
            )
            ->line(Lang::getFromJson('Nếu bạn không tạo muốn tạo tài khoản, vui lòng bỏ qua email này.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl()
    {
        return url('https://rainichi.vn/login');
    }

}
