<?php
/**
 * Firebase server key
 * Using in push notification services
 */
return [
    'endpoint' => env('FIREBASE_PUSH_ENDPOINT','https://fcm.googleapis.com/fcm/send'),
    'server_key' => env('FIREBASE_SERVER_KEY', 'AAAA_EHYGKQ:APA91bF92Pl7IUshv_h2fzSjd1CLFeuNTXkRDIfU3endfQu6WOGNAoBU_YC664yF-tDZGPgufoe88Po1FeirUI4XzeVdyMEMa4SCrTUbIir0AAJf_LVPnCoZU4U5N0ppizCTyP2PlUOV')
];
