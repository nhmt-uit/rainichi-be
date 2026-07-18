<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Lang;

class AddUserToClassroom extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $classroom;

    /**
     * Create a new message instance.
     *
     * @param $classroom
     */
    public function __construct($classroom)
    {
        $this->classroom = $classroom;
    }

    /**
     * Build the message.
     *
     * @return AddUserToClassroom
     */
    public function build()
    {
        return $this->view('mail.classroom.user.add')
            ->subject('Rainichi - Thông tin lớp học')
            ->with(['className' => $this->classroom->name,]);;
    }
}
