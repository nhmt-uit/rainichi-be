<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BuyingDefaultClassroom extends Notification implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    private $classRoom;
    private $is_course;

    /**
     * Create a new notification instance.
     *
     * @param $classRoom
     */
    public function __construct($classRoom, $is_course)
    {
        $this->classRoom = $classRoom;
        $this->is_course = $is_course;
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
        $subject = $this->is_course ? 'Lớp học' : 'Đề thi';
        $is_course = $this->is_course;
        $courseClass = $this->classRoom->courseClass()->where('is_approved', false)
            ->where(function ($q) use ($is_course) {
                if ($is_course) {
                    $q->whereNotNull('course_id');
                } else {
                    $q->whereNotNull('test_id');
                }
            })->get();
        return (new MailMessage)
            ->from(Config('mail.from.address'), Config('mail.from.name'))
            ->subject('Xác nhận Thông tin ' . $subject)
            ->view('mail.classroom.default', ['classroom' => $this->classRoom,
                'subject' => $subject, 'courseClass' => $courseClass]);
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
}
