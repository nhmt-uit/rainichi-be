<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 29/11/2018
 * Time: 15:03
 */

namespace App\Service;


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadService
{


    /**
     * Upload service, using when need upload file.
     * @param $file
     * @param $physic_path1
     * @return string |null
     */
    public static function handleUploadFile($file, $physic_path)
    {
        $path = '';
        if (!is_null($file)) {
            $path = self::saveFileToS3($file, $physic_path);
        }
        return $path;
    }

    /**
     * Remove file
     * @param $file_link
     */
    public static function handleRemoveFileOld($file_link)
    {
        if (file_exists(storage_path('app/public/' . $file_link))) {
            Storage::disk('public')->delete($file_link);
        }
    }

    /**
     * Remove file s3
     * @param $file_link
     */
    public static function handleRemoveFile($file_link)
    {
        $s3 = self::makeS3Client();
        try {
            $s3->deleteObject(array(
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $file_link,
            ));
            return [
                'success' => true,
                'message' => 'success'
            ];
        } catch (S3Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Handles the file upload
     *
     * @param Request $request
     * @param $key
     * @param $path
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     */
    public static function upload(Request $request, $path)
    {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return self::saveFileToS3($save->getFile(), $path);
        }
        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file to S3 server
     *
     * @param UploadedFile $file
     * @param $path
     *
     * @return string
     */
    static function saveFileToS3($file, $path)
    {
        $s3 = self::makeS3Client();
        $filePath = $file;
        $filename = self::createFilename($file);
        $bucket = env('AWS_BUCKET');

        $result = $s3->createMultipartUpload(array(
            'Bucket' => $bucket,
            'Key' => $path . $filename,
            'ACL' => 'public-read',
        ));
        $uploadId = $result['UploadId'];
        try {
            $file = fopen($filePath, 'r');
            $parts = array();
            $partNumber = 1;
            while (!feof($file)) {
                $result = $s3->uploadPart(array(
                    'Bucket' => $bucket,
                    'Key' => $path . $filename,
                    'UploadId' => $uploadId,
                    'PartNumber' => $partNumber,
                    'Body' => fread($file, 25 * 1024 * 1024),
                ));
                $parts[] = array(
                    'PartNumber' => $partNumber++,
                    'ETag' => $result['ETag'],
                );
            }
            fclose($file);
        } catch (S3Exception $e) {
            $s3->abortMultipartUpload(array(
                'Bucket' => $bucket,
                'Key' => $path . $filename,
                'UploadId' => $uploadId,
            ));
        }
        $s3->completeMultipartUpload(array(
            'Bucket' => $bucket,
            'Key' => $path . $filename,
            'UploadId' => $uploadId,
            'Parts' => $parts,
        ));

        return $path . $filename;

    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    static function saveFile(UploadedFile $file, $filePath)
    {
        $fileName = self::createFilename($file);

        $file->move(public_path('uploads/' . $filePath), $fileName);
        return response()->json([
            'path' => $filePath . '/' . $fileName,
        ]);
    }

    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    static function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        // Add timestamp hash to name of the file
        $filename = "rainichi-" . md5(time()) . str_random(4) . "." . $extension;
        return $filename;
    }

    static function createFolder($name)
    {
        $bucket = env('AWS_BUCKET');
        $s3 = self::makeS3Client();
        $response = $s3->doesObjectExist($bucket, $name);
        if ($response) {
            return [
              'success' => false,
              'message' => 'Folder already exist!'
            ];
        } else {
            $s3->putObject(array(
                'Bucket' => $bucket = env('AWS_BUCKET'),
                'Key' => $name . "/",
                'Body' => '',
                'ACL' => 'public-read-write',
            ));
            return [
                'success' => true,
                'message' => 'Success'
            ];
        }

    }

    /**
     * Build an S3 client for the custom (non-AWS) S3-compatible endpoint this app uses.
     * @return S3Client
     */
    private static function makeS3Client()
    {
        return new S3Client([
            'version' => 'latest',
            'endpoint' => env('AWS_URL'),
            'region' => 'us-east-1',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
    }
}
