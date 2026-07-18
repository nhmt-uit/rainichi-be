<?php

namespace App\Service;

use App\Mail\CompanyActive;
use App\Models\Company;
use App\Models\CourseClass;
use App\Models\UserClass;
use App\Models\UserCompany;
use Illuminate\Support\Facades\Mail;

class CompanyService
{

    public static function updateRelateToCompany($company_id, $status)
    {
        $company = Company::query()->find($company_id);
        if ($company) {
            $classroom_ids = $company->classRoom()->pluck('id');
            $company->classRoom()->update(['is_active' => $status]);
            UserCompany::query()->where('company_id', $company_id)->update(['is_active' => $status]);
            if (!$status) {
                CourseClass::query()->where('classroom_id', $classroom_ids)->update(['is_active' => $status]);
                UserClass::query()->where('classroom_id', $classroom_ids)->update(['is_active' => $status]);
            }
            Mail::to($company->users)->queue(new CompanyActive($status));
        }

    }
}
