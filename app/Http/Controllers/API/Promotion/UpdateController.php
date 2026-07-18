<?php

namespace App\Http\Controllers\API\Promotion;

use App\Models\Promotion;
use App\Service\BaseResponse;
use App\Transformers\PromotionTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $promotion_id = $request->route('id');
        $data_change = $request->except('_method');
        $promotion = Promotion::find($promotion_id);
        if ($promotion) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $promotion->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    $promotion->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                }
            }

            $data_change['updated_by'] = Auth::user()->id;
            $promotion->update($data_change);

            if ($promotion->save()) {
                return BaseResponse::customResponse(
                    'Promotions updated successfully',
                    (new PromotionTransformer)->transform($promotion),
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Data not found',
                [],
                false,
                Config('error_constant.normal.not_found'),
                404,
                'NotFound'
            );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $promotion_id = $request->route('id');
        $promotion = Promotion::find($promotion_id);
        if ($promotion) {
            return BaseResponse::customResponse(
                'Get data successfully',
                (new PromotionTransformer)->transform($promotion),
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Data not found',
                [],
                false,
                Config('error_constant.normal.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
