<?php
return [
	'driver'      => env('FCM_PROTOCOL', 'http'),
	'log_enabled' => true,

	'http' => [
		'server_key'       => env('FCM_SERVER_KEY', 'AAAA5Febnms:APA91bFiS-C-vPaJbftbLN1axaluUM6jYn9v-gvDV_SxMbIrCfSyL5UhM7bvGl3yJWiGDyc41wSkGD9Lql3k_rXW5trc5Ka_uGOm5T1QMnJhB04uTqubLtaauGfvtYzWHX5zYwMHcgM9'),
		'sender_id'        => env('FCM_SENDER_ID', '980722359915'),
            	'server_send_url'  => 'https://fcm.googleapis.com/fcm/send',
		'server_group_url' => 'https://android.googleapis.com/gcm/notification',
		'timeout'          => 30.0, // in second
            
	]
];
