<?php


namespace App\Service;


class PushNotification
{

    protected $url;
    protected $headers;
    protected $ch;
    protected $all;
    protected $device_tokens;
    protected $title;
    protected $body;

    /**
     * PushNotification constructor.
     * @param $device_tokens
     * @param $title
     * @param $body
     * @param $all
     */
    public function __construct($device_tokens, $title, $body, $all = false)
    {
        $this->url = config('firebase.endpoint');
        $this->headers = [
            'Content-Type:application/json',
            'Authorization:key=' . config('firebase.server_key')
        ];
        $this->device_tokens = $device_tokens;
        $this->title = $title;
        $this->body = $body;
        $this->all = $all;
    }


    public function trigger()
    {
        $this->push();
    }

    /**
     * @return mixed
     */
    function push()
    {
        $fields = [
            $this->all ? 'to' : 'registration_ids' => $this->all ? '/topics/Rainichi_All_User' : $this->device_tokens,
            'priority' => 'high',
            'data' => [
                'title' => $this->title,
                'body' => $this->body,
                'sound' => 'default',
                'badge' => '1'
            ],
            'notification' => [
                'title' => $this->title,
                'body' => $this->body,
                'sound' => 'default',
                'badge' => '1'
            ]
        ];
        return $this->start($fields);
    }

    /**
     * @param $fields
     * @return array
     */
    function start($fields)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($this->ch);
        if ($result === FALSE) {
            return [
                'message' => 'FCM Send Error: ' . curl_error($this->ch),
                'error' => true
            ];
        }
        curl_close($this->ch);
        return [
            'message' => 'FCM push notify successfully',
            'error' => false
        ];
    }
}
