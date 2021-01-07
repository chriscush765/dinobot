<?php

$bot_id = ""; //your bot id

$json = file_get_contents('php://input');

$msg = json_decode($json);

if($msg->sender_type == "bot")
    die("is bot");

$msg_array = explode(" ", $msg->text); //make an array out of the message, seperated by space character

$num_dinos = 0;

for ($i = 1; $i < count($msg_array); $i++) { //skip the first word
    if(stripos($msg_array[$i], 'dino') !== false){ 
        $num_dinos = $msg_array[$i-1]; //the number of dinos is the word before "dino"
    }
}

if($num_dinos == 0) //accidental dino detection
    die("no dinos");

$reply_text = str_repeat("d", $num_dinos); //create the reply text, use d as placeholder for dino emoji

$emoji_array = str_repeat("[1,62],", $num_dinos); //create array of emoji indexes. 62 happens to be dino, but this can be changed
$emoji_array = rtrim($emoji_array , ", "); //remove the comma at the end of the list for valid JSON


$url = 'https://api.groupme.com/v3/bots/post';

$msg_json = '{
	"bot_id": "'. $bot_id .'",
	"text": "'. $reply_text .'",
	"attachments": [
		{
			"type": "emoji",
			"placeholder": "d",
			"charmap": [
				'.$emoji_array.'
			]
		},
		{
			"type": "reply",
			"user_id": "'. $msg->user_id .'",
			"reply_id": "'. $msg->id .'",
			"base_reply_id": "'. $msg->id .'"
		}
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
