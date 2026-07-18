<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BuyCourseApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $classRoom;
    private $course;

    /**
     * Create a new message instance.
     *
     * @param $course
     * @param $classroom
     */
    public function __construct($course, $classroom)
    {

        $this->classRoom = $classroom;
        $this->course = $course;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.classroom.course.approved')
            ->subject('Rainichi - Xác nhận mua khóa học thành công')
            ->with(['classroom' => $this->classRoom, 'course' => $this->course]);

    }
}
