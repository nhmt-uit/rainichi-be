<?php


namespace App\Http\Controllers\API\Files;


use App\Http\Controllers\Controller;
use App\Service\BaseResponse;
use Aws\S3\S3Client;
use Carbon\Carbon;

class ListController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $files = [];
        $s3 = new S3Client([
            'version' => 'latest',
            'endpoint' => env('AWS_URL'),
            'region' => 'us-east-1',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        $bucket = env('AWS_BUCKET');
        try {
            $objects = $s3->getIterator('ListObjects', array(
                "Bucket" => $bucket,
                "Prefix" => 'finders/' //must have the trailing forward slash "/"
            ))->toArray();
            usort($objects, function ($t1, $t2) {
                return strtotime($t2['LastModified']) - strtotime($t1['LastModified']);
            });
            foreach ($objects as $key => $object) {
                $folder = basename(dirname($object["Key"])) === 'finders' ? '.' : basename(dirname($object["Key"]));
                $name = explode(basename(dirname($object["Key"])), $object['Key']);
                $should_add = true;
                if (count($name) > 1) {
                    $name = str_replace('/', '', $name[1]);
                } else {
                    $should_add = false;
                }
                if (strripos($object['Key'], '/') === strlen($object['Key']) - 1) {
                    $type = 'FOLDER';
                } else {
                    $type = 'FILE';
                }
                if ($should_add) {
                    array_push($files, [
                        'image' => media_url_web($object['Key']),
                        'name' => $name,
                        'type' => $type,
                        'date' => Carbon::parse($object['LastModified'])->format('d/m/y'),
                        'size' => formatSizeUnits($object['Size']),
                        'folder' => $folder,
                        'key' => $object['Key']
                    ]);
                }
            }
            if (count($files) > 0) {
                return BaseResponse::customResponse(
                    'Success',
                    $files,
                    true,
                    200, 200,
                    'Success',
                    []
                );
            } else {
                return BaseResponse::customResponse(
                    'Success',
                    [],
                    true,
                    200, 200,
                    'Success',
                    []
                );
            }

        } catch (\Exception $e) {
            dd($e);
        }
    }
}
