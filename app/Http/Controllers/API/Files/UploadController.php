<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/20/19
 * Time: 10:16
 */

namespace App\Http\Controllers\API\Files;


use App\Http\Controllers\Controller;
use App\Service\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException
     */
    public function uploadLargeVideoFile(Request $request)
    {
        $file = UploadService::upload($request, Config('uploadpath.grammar_folder'));
        return response()->json([
            'path' => $file
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException
     */
    public function uploadViaFinder(Request $request)
    {
        $folder = $request->get('folder');
        $file = UploadService::upload($request, Config('uploadpath.finder_folder') . $folder);
        return response()->json([
            'path' => media_url_web($file)
        ]);
    }

    /**
     * @param Request $request
     */
    public function removeViaFinder(Request $request)
    {
        $data = UploadService::handleRemoveFile($request->get('file'));
        return $data;
    }

    public function createFolder(Request $request)
    {
        $name = $request->get("name");
        $s3Path = Config('uploadpath.finder_folder') . $name . '/';
        $result = UploadService::createFolder($s3Path);
        return $result;
    }
}
