<?php

namespace App\Http\Controllers\API\Contact;

use App\Models\Contact;
use App\Service\BaseResponse;
use App\Transformers\ContactTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var $fractal
     */
    private $fractal;

    /**
     * @var ContactTransformer
     */
    private $contactTransformer;

    /**
     * @param Manager $fractal
     * @param ContactTransformer $contactTransformer
     */
    function __construct(Manager $fractal, ContactTransformer $contactTransformer)
    {
        $this->fractal = $fractal;
        $this->contactTransformer = $contactTransformer;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListAdmin(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $is_active = $request->get('is_active');
        $search_string = $request->query('search_string');
        $page_id = $request->query('page_id');
        $contact_list = Contact::orderByCustom($column, $order_by_type)
            ->search($search_string)
            ->getPage($page_id)
            ->isActive($is_active)
            ->paginate($paging);

        $contacts = new Collection($contact_list->items(), $this->contactTransformer);
        $contacts->setPaginator(new IlluminatePaginatorAdapter($contact_list));
        $contacts = $this->fractal->createData($contacts);
        return BaseResponse::customResponse(
            'Get list successfully',
            $contacts->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $contacts->toArray()['meta']
        );
    }

    /**
     * @return JsonResponse
     */
    public function getContactList()
    {
        $typeList = Contact::getContactList();
        return BaseResponse::customResponse(
            'Get list successfully',
            $typeList,
            true,
            200,
            200,
            'Success'
        );
    }
}
