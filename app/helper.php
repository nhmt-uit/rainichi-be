<?php
/**
 * Created by Thang le.
 * User: Zick
 * Date: 6/10/19
 * Time: 22:49
 */

use Carbon\Carbon;

/**
 * media_url_web function, return path of file in cloud storage
 * @param $file_path
 * @return string
 */
function media_url($file_path)
{
    return '' . env('AWS_URL', 'https://sin1.contabostorage.com') . '/4fe8f9114a184c3084122e583d0bec06:' . env('AWS_BUCKET', 'files') . '/' . $file_path;
//    return 'http://' . env('AWS_ACCESS_KEY_ID', 's3user10122') . '.cloudstorage.com.vn/' . env('AWS_BUCKET', 'files') . '/' . $file_path;
}

/**
 * Base url
 * @return string
 */
function media_base_url()
{
//    return 'http://' . env('AWS_ACCESS_KEY_ID', 's3user10122') . '.cloudstorage.com.vn/' . env('AWS_BUCKET', 'files') . '/';
    return '' . env('AWS_URL', 'https://sin1.contabostorage.com') . '/4fe8f9114a184c3084122e583d0bec06:' . env('AWS_BUCKET', 'files') . '/';

}


/**
 * @param $file_path
 * @return string
 */
function media_url_web($file_path)
{
//    return 'https://' . env('AWS_ACCESS_KEY_ID', 's3user10122') . '.storebox.vn/' . env('AWS_BUCKET', 'files') . '/' . $file_path;
    return '' . env('AWS_URL', 'https://sin1.contabostorage.com') . '/4fe8f9114a184c3084122e583d0bec06:' . env('AWS_BUCKET', 'files') . '/' . $file_path;
}

/**
 * Days left
 * @param $input_day
 * @return int
 */
function day_left($input_day)
{
    $due_date = Carbon::createFromFormat('Y-m-d H:i:s', $input_day)->startOfDay();
    $from_now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->endOfDay();
    $diff_in_days = $due_date->diffInDays($from_now);
    \Illuminate\Support\Facades\Log::info('day_left', [
        'due_days' => $due_date,
        'from_now' => $from_now,
        'diff' => $diff_in_days
    ]);
    return $diff_in_days;
}

/**
 * formatSizeUnits
 * @param string $from
 * @return int|null
 */
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * @param $utcDate
 * @return string
 */
function convertAsiaDate($utcDate) {
    return Carbon::createFromFormat('Y-m-d H:i:s', $utcDate, 'UTC')->setTimezone('Asia/Ho_Chi_Minh')->format('d-m-Y H:i');
}
