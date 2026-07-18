<?php

namespace App\Http\Controllers\API\Promotion;

use App\Models\Promotion;

use App\Service\BaseResponse;
use App\Transformers\PromotionTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $promotion = Promotion::create($data);

        if($request->has('translations')) {
            $language_keys = array_keys($data['translations']);
            foreach ($language_keys as $language) {
                $promotion->translateOrNew($language)->name = $data['translations'][$language]['name'];
                $promotion->translateOrNew($language)->description = $data['translations'][$language]['description'];
            }
            if ($promotion->save()) {
                return BaseResponse::customResponse(
                    'Create successfully',
                    (new PromotionTransformer)->transform($promotion),
                    true,
                    200,
                    201,
                    'Created'
                );
            } else {
                return BaseResponse::customResponse(
                    'Fail to insert',
                    [],
                    false,
                    Config('error_constant.normal.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        }
    }
}
