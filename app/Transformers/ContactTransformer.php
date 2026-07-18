<?php

namespace App\Transformers;

use App\Models\Contact;
use App\Models\Course;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{

    /**
     * @param Contact $contact
     * @return array
     */
    public function transform(Contact $contact)
    {
        $constList = Contact::getContactType();
        return [
            'id' => $contact->id,
            'type' => $constList[$contact->type] ?? $contact->type,
            'name' => $contact->name,
            'email' => $contact->email,
            'title' => $contact->title,
            'phone' => $contact->phone,
            'address' => $contact->address,
            'course_id' => $contact->course_id,
            'course_name' => [
                'vi' => $contact->course_id ? ['name' => self::generateName($contact->course_id, 'vi', 'NAME')] : null,
                'en' => $contact->course_id ? ['name' => self::generateName($contact->course_id, 'en', 'NAME')] : null
            ],
            'ip_address' => $contact->ip_address,
            'user_agent' => $contact->user_agent,
            'num_of_employee' => $contact->num_of_employee,
            'content' => $contact->content,
            'is_active' => $contact->is_active,
            'created_at' => Carbon::parse($contact->created_at)->format('d-m-Y'),
        ];
    }


    /**
     * Generate name or image from data
     * @param $course_ids
     * @param $lang
     * @return string|null
     */
    public static function generateName($course_ids, $lang)
    {
        $courseName = '';

        $course_ids = json_decode($course_ids);
        $courses = Course::query()
            ->join('course_translations as ct', function ($q) use ($lang) {
                $q->on('course.id', '=', 'ct.course_id')->where('ct.locale', '=', $lang);
            })->whereIn('course_id', is_array($course_ids) ? $course_ids : [$course_ids])
            ->pluck('ct.name');
        if ($courses) {
            foreach ($courses as $key => $name) {
                if ($key == 0) {
                    $courseName .= '- '. $name;
                } else {
                    $courseName .= '<br> - ' . $name;
                }
            }
        }
        return $courseName;
    }
}
