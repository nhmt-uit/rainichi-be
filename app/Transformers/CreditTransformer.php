<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\MoneyToCredit;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CreditTransformer extends TransformerAbstract
{

    /**
     * @param MoneyToCredit $credit
     * @return array
     */
    public function transform(MoneyToCredit $credit)
    {
        return [
            'id' => $credit->id,
            'credit' => $credit->credit,
            'price' => $credit->price,
            'discount' => $credit->discount,
            'image' => $credit->image ? media_url_web($credit->image) : null,
            'is_active' => $credit->is_active,
            'created_at' => Carbon::parse($credit->created_at)->format('d-m-Y'),
            'created_by' => $credit->user ? $credit->user->name : null,
            'packageId' => $credit->package_id
        ];
    }
}
