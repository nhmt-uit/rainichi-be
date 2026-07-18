<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Language;


use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Service\BaseResponse;
use App\Transformers\LanguageTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var LanguageTransformer
     */
    private $languageTransformer;

    function __construct(Manager $fractal, LanguageTransformer $languageTransformer)
    {
        $this->fractal = $fractal;
        $this->languageTransformer = $languageTransformer;
    }
    public function index(Request $request)
    {
        $language = Language::all();
        $language = new Collection($language, $this->languageTransformer);
        $language = $this->fractal->createData($language);
        return BaseResponse::customResponse(
            'Success',
            $language->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }
}
