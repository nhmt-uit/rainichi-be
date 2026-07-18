<?php

namespace App\Transformers;

use App\Models\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyAdminTransformer extends TransformerAbstract
{

    public function transform(Company $company)
    {
        return [
            'id' => $company->id,
            'type' => $company->type,
            'name' => $company->name,
            'image' => $company->image && $company->image != 'null' ? media_url_web($company->image) : null,
            'phone' => $company->phone,
            'fax' => $company->fax,
            'email' => $company->email,
            'career' => $company->career,
            'content' => $company->content,
            'charter_capital' => $company->charter_capital,
            'num_of_employee' => $company->num_of_employee,
            'is_active' => $company->is_active,
            'sort_order' => $company->sort_order,
            'created_at' => Carbon::parse($company->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($company->updated_at)->format('d-m-Y'),
            'created_by' => $company->user ? $company->user->name : null,
            'updated_by' => $company->user ? $company->user->name : null,
            'translations' => $company->getTranslationsArray()
        ];
    }
}
