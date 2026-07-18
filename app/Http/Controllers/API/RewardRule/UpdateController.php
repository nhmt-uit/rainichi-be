<?php

namespace App\Http\Controllers\API\RewardRule;

use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Models\RewardRule;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\RewardRuleTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var RewardRuleTransformer
     */
    private $rewardRuleTransformer;

    function __construct(Manager $fractal, RewardRuleTransformer $rewardRuleTransformer)
    {
        $this->fractal = $fractal;
        $this->rewardRuleTransformer = $rewardRuleTransformer;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $credit_id = $request->route('id');
        $data_change = $request->except('_method');
        $credit = RewardRule::find($credit_id);
        if ($credit) {
            $credit->update($data_change);
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $credit->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                    $credit->translateOrNew($language)->method = $data_change['translations'][$language]['method'];
                }
            }
            if ($credit->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $credit,
                    true,
                    200,
                    200,
                    'Success'
                );
            }

        } else {
            return BaseResponse::customResponse(
                'Credit not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }

    }

    public function detail($id)
    {
        $credit = RewardRule::query()->find($id);
        if ($credit) {
            $credit = new Item($credit, $this->rewardRuleTransformer);
            $credit = $this->fractal->createData($credit);
            return BaseResponse::customResponse(
                'Update successfully',
                $credit->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Vocabulary not found',
                [],
                false,
                Config('error_constant.credit.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
