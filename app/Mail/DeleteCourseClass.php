<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteCourseClass extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $course;
    private $classRoom;

    /**
     * Create a new message instance.
     *
     * @param $classroom
     * @param $course
     */
    public function __construct($classroom, $course)
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
        return $this->view('mail.classroom.course.delete')
            ->subject('Rainichi - Xác nhận xóa khóa học thành công')
            ->with(['classroom' => $this->classRoom, 'course' => $this->course]);

    }
}
