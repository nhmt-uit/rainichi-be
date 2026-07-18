<?php

namespace App\Transformers;

use App\Models\Classroom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ClassroomAdminTransformer extends TransformerAbstract
{

    public function transform(Classroom $classroom)
    {
        return [
            'id' => $classroom->id,
            'company_id' => $classroom->company_id,
            'name' => $classroom->name,
            'image' => $classroom->image && $classroom->image != 'null' ? media_url_web($classroom->image) : null,
            'num_of_employee' => $classroom->num_of_employee,
            'level_id' => $classroom->level_id,
            'credits' => $classroom->credits,
            'is_active' => $classroom->is_active,
            'is_approved' => $classroom->is_approved,
            'admin' => $classroom->admin ? $classroom->admin->name : null,
            'teacher_name' => $classroom->teacher() ? $classroom->teacher()->name : null,
            'teacher_id' => $classroom->teacher() ? $classroom->teacher()->id : null,
            'company' => $classroom->company ? $classroom->company->name : '',
            'created_at' => Carbon::parse($classroom->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($classroom->updated_at)->format('d-m-Y'),
            'approved_by' => $classroom->approvedBy ? $classroom->approvedBy->name : null,
            'created_by' => $classroom->createdBy ? $classroom->createdBy->name : null,
            'is_edit' => Auth::user()->type == User::ADMIN || ($classroom->courses->count() <= 0 && Auth::user()->type <> User::ADMIN) ? true : false,
            'number_course' => $classroom->courses->count() ?? 0,
        ];
    }
}
