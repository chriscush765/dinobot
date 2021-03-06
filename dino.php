<?php

$bot_id = "";

$json = file_get_contents('php://input');

$chance = 100; //1 in x chance, in this case 1 in 100 to Trigger AI

$msg = json_decode($json);

$random_reply = false;

if($msg->sender_type == "bot")
    die("is bot");

$msg_array = explode(" ", $msg->text);

$num_dinos = 0;

for ($i = 1; $i < count($msg_array); $i++) {
    if(stripos($msg_array[$i], 'dino') !== false){
        $num_dinos = $msg_array[$i-1];
    }
}

if(!is_numeric($num_dinos) || $num_dinos == 0)
    if((rand(1,$chance)<=1)){
        $num_dinos = 1; // 1 in 200 chance of triggering AI
        $ai = true;
    }
    else
        die("no dinos");

$reply_text = str_repeat("�", $num_dinos);

$emoji_array = str_repeat("[1,62],", $num_dinos);
$emoji_array = rtrim($emoji_array , ", ");


$url = 'https://api.groupme.com/v3/bots/post';

$attachment_text = ',
		{
			"type": "reply",
			"user_id": "'. $msg->user_id .'",
			"reply_id": "'. $msg->id .'",
			"base_reply_id": "'. $msg->id .'"
		}';


$msg_json = '{
	"bot_id": "'. $bot_id .'",
	"text": "'. $reply_text .'",
	"attachments": [
		{
			"type": "emoji",
			"placeholder": "�",
			"charmap": [
				'.$emoji_array.'
			]
		}'. ($ai  ? $attachment_text : '').'
	]
}';


$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => $msg_json
    )
);
$context  = stream_context_create($options);

$result = file_get_contents($url, false, $context);
