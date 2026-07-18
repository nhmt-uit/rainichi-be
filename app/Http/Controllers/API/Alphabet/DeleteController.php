<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Alphabet;


use App\Http\Controllers\Controller;
use App\Models\Alphabet;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{

    /**
     * @param $type
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete($type, $id)
    {
        $alphabet = Alphabet::query()
            ->where('id', $id)
            ->where('type', Alphabet::TYPES[$type])
            ->first();

        if ($alphabet && $alphabet->delete()) {
            if ($alphabet->image != null) {
                UploadService::handleRemoveFile($alphabet->image);
            }
            if ($alphabet->audio != null) {
                UploadService::handleRemoveFile($alphabet->audio);
            }
        }

        return BaseResponse::customResponse(
            'Deleted',
            [],
            true,
            200,
            202,
            'Accepted'
        );
    }
}
