<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CompanyActive extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $status;

    /**
     * Create a new notification instance.
     *
     * @param $status
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $textStatus = $this->status ? 'Chào mừng bạn đến với Rainichi.'
            : 'Doanh nghiệp của bạn dừng hoạt động. Vì thế tất cả các dịch vụ liên quan sẽ ngừng truy cập';
        return $this->subject('Rainichi-Thông báo')
            ->view('mail.company.active')
            ->with(['textStatus' => $textStatus]);
    }
}
