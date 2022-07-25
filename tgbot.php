<?php 
include "config.php";

$data = file_get_contents('php://input');
$data = json_decode($data, true);
 
if (empty($data['message']['chat']['id']) AND empty($data['callback_query']['message']['chat']['id']))
{
	#exit();
}

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

#################################

if (isset($data['message']['chat']['id']))
{
	$chat_id = $data['message']['chat']['id'];
}
elseif (isset($data['callback_query']['message']['chat']['id']))
{
	$chat_id = $data['callback_query']['message']['chat']['id'];
}
elseif(isset($data['inline_query']['from']['id']))
{
	$chat_id = $data['inline_query']['from']['id'];
}

// Register new user in DB
if(isset($data['callback_query']['message']['chat']['username']) && $data['callback_query']['message']['chat']['username'] != ''){
	$fname = $data['callback_query']['message']['chat']['first_name'];
	$lname = $data['callback_query']['message']['chat']['last_name'];
	$uname = $data['callback_query']['message']['chat']['username'];
} else{
	$fname = $data['message']['from']['first_name'];
	$lname = $data['message']['from']['last_name'];
	$uname = $data['message']['from']['username'];	
}
$time = time();
if($chat_id != ''){
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result) == 0){
		$str2ins = "INSERT INTO `users` (`chatid`,`fname`,`lname`,`username`) VALUES ('$chat_id','".addslashes($fname)."','".addslashes($lname)."','$uname')";
		mysqli_query($link, $str2ins);	
		$result = mysqli_query($link, $str2select);
	}
	$row = @mysqli_fetch_object($result);	
}
// Register new user in DB

// LANGUAGE
$str3select = "SELECT `lang` FROM `users` WHERE `chatid`='$chat_id'";
$result3 = mysqli_query($link, $str3select);
$row3 = @mysqli_fetch_object($result3);
if($row3->lang != ''){
	$langcode = $row3->lang;
}else{
	$langcode = 0;	
}
###################
$langcode = langCode($langcode);
###################
require "lang.php";
for ($i = 0; $i < count($text); $i++) {
	for ($k = 0; $k < count($text[$i]); $k++) {
		$text[$i][$k] = str_replace("&#13;&#10;", "
", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#9;", "", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#60;", "<", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#62;", ">", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#39;", "'", $text[$i][$k]);
		$text[$i][$k] = str_replace("", "", $text[$i][$k]);						
	} // end FOR
} // end FOR	
// LANGUAGE

checkInlineQuery();

############### START ###############
if( preg_match("/\/start/i", $data['message']['text'] )){

//register subscriber
$newrecord = $chat_id."|".addslashes($data['message']['from']['first_name'])." ".addslashes($data['message']['from']['last_name'])."|".addslashes($data['message']['from']['username']);
if(file_exists('subscribers.php')) include 'subscribers.php';
if(isset($user) && count($user) > 0){
	if(!in_array($newrecord, $user)){
		$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
		
	}
}else{
	$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
} // end IF-ELSE count($user) > 0

if(isset($towrite) && $towrite != ''){
	if($file = fopen("subscribers.php", "a+")){
		fputs($file,$towrite);
		fclose($file);
	} // end frite to file
}
//register subscriber

// record referral
$ref = trim(str_replace("/start", "", $data['message']['text']));
if($ref != ''){
	if($ref != $chat_id){
		$str2select = "SELECT `ref` FROM `users` WHERE `chatid`='$chat_id'";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);
		if($row->ref < 10){
			$str2upd = "UPDATE `users` SET `ref`='$ref' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			$reftxt = str_replace("%ref%", $ref, $text[$langcode][14]);
			
			$response = array(
					'chat_id' => $ref,
					'text' => hex2bin('F09F92B0').' '.$data['message']['from']['first_name'].' '.$data['message']['from']['last_name'].$reftxt);
			sendit($response, 'sendMessage');			
		}
	}
}
// record referral

mainMenu("");
#chooseLang();

}
elseif( preg_match("/8Seu8SwemYdn6SmdYdf/", $data['message']['text'] )){
	
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $chat_id,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');	
	
}
else{
	if(isset($data['callback_query']['data']) && $data['callback_query']['data'] != ''){

		if( preg_match("/chkp/", $data['callback_query']['data']) ){	

			// Check payment for NFT
			$senderid = str_replace("chkp", "", $data['callback_query']['data']);
			$parts = explode("|", $senderid);
			$senderid = $parts[0];
			$sum = $parts[1];

			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$XAPIKey));
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$str17select = "SELECT `nftcode` FROM `users` WHERE `chatid`='$chat_id'";
			$result17 = mysqli_query($link, $str17select);
			$row17 = @mysqli_fetch_object($result17);
			
			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $NFTwallet && $res["result"][$i]["out_msgs"][0]["message"] == $row17->nftcode){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
						$nftcodeORIG = $row17->nftcode;						
				}
			} // end FOR
			
			if($verified == 1){
				
				// sum validation
				$nc = explode(";", $row17->nftcode);
				switch ($nc[0]) {
					case "silver":
					$storedsum = 5;
					$case = 14;
					break;
					case "gold":
					$storedsum = 15;
					$case = 15;					
					break;
					case "platinum":
					$storedsum = 25;
					$case = 16;					
					break;
					case "diamond":
					$storedsum = 45;
					$case = 17;					
					break;						
				}	
				
				if($storedsum != (int)$paidSumForNFT){
				
					$response = array(
						'chat_id' => $chat_id, 
						'text' => $text[$langcode][15],
						'parse_mode' => 'HTML');	
					sendit($response, 'sendMessage');

				}else{
				
					#clean_temp_sess();
					delMessage("", $data['callback_query']['message']['message_id']);
					
					$nftcode = rand_string(20);
					$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
					mysqli_query($link, $str2upd);				
					
					$gotTON = (int)$paidSumForNFT;
					
					include "roulette.php";
					roulette($case, $gotTON, $senderid);
				}
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][16],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');	
			}
		}
		elseif( $data['callback_query']['data']  == 3){
		
		// Go to define sum
		#addSum();
		choosePayMethod();
			
		}
		elseif( $data['callback_query']['data']  == 4){			
		
			processWallet2();
			
		}
		elseif( $data['callback_query']['data'] > 99  && $data['callback_query']['data'] < 103){
			
			$langcode = $data['callback_query']['data'] - 100;
		
			$str2upd = "UPDATE `users` SET `lang`='".$langcode."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			###################
			$langcode = langCode($langcode);
			###################
			
			mainMenu();
		}
		elseif( $data['callback_query']['data'] == 150){

			$str22select = "SELECT `ccase` FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
			$result22 = mysqli_query($link, $str22select);
			$row22 = @mysqli_fetch_object($result22);			
			$case = $row22->ccase;	
			
			switch ($case) {
				case 14:
				$sum = 5;
				break;
				case 15:
				$sum = 15;
				break;
				case 16:
				$sum = 25;
				break;
				case 17:
				$sum = 45;
				break;						
			}

			$paylink = makelink($sum, $case);
			
			$tomessage = str_replace("%sumtopay%", $sum, " Оплатить %sumtopay% TON.");				
			$url = $paylink;
			$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3').$tomessage;
			$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
			send($chat_id, $text[$langcode][17], $arInfo);	
				
		}
		elseif( $data['callback_query']['data'] == 151){						

			messageIfPayByTON();					
		}
		elseif($data['callback_query']['data'] == 10){
			
			chooseCase();			

		}
		elseif($data['callback_query']['data'] == 11){
			
			$nfts = "";
			$str12select = "SELECT * FROM `raffles_history` WHERE `chatid`='$chat_id' ORDER BY `rowid`";
			$result12 = mysqli_query($link, $str12select);
			while($row12 = @mysqli_fetch_object($result12)){
				if($row12->nft == "nude") $nfts .= $row12->date_time.": 1 👩‍🎤 Custom Nude
";
				elseif($row12->nft == "anime") $nfts .= $row12->date_time.": 1 👸 Custom Anime
";
				elseif($row12->nft == "custom3d") $nfts .= $row12->date_time.": 1 👑 Custom 3D
";
			}  // end WHILE MySQL	
			if($nfts == "")$nfts = $text[$langcode][18];
			
			$tomessage = $text[$langcode][19]."
";
			
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $tomessage.$nfts,
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');
			
		}
		elseif($data['callback_query']['data'] == 12){
			
			$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
			$result12 = mysqli_query($link, $str12select);
			$numOfReferals = mysqli_num_rows($result12);
			
			$refbalance = ($row->refbalance > 0) ? $row->refbalance : "0.00";
			
			$tomessage = str_replace("%NFTRefPercent%", $WinNFTbotRefPercent, $text[$langcode][20]);
			$tomessage = str_replace("%numOfReferals%", $numOfReferals, $tomessage);
			$tomessage = str_replace("%refbalance%", $refbalance, $tomessage);		
			$tomessage = str_replace("%chat_id%", $chat_id, $tomessage);			
			
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');	
				
		send2('sendMessage',
			[
				'chat_id' => $chat_id,
				'text' => $text[$langcode][34],
				'reply_markup' =>
				[
					'inline_keyboard' =>
					[
						[
							[
								'text' => $text[$langcode][35],
								'switch_inline_query' => ''
							]
						]
					]
				]
			]);			
			
		}
		elseif($data['callback_query']['data'] == 13){
			
			if($langcode == 0)$langcode = 1;
			else $langcode = 0;
			
			$str2upd = "UPDATE `users` SET `lang`='".$langcode."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			###################
			$langcode = langCode($langcode);
			###################

			delMessage("", $data['callback_query']['message']['message_id']);
			mainMenu("");			
			
		}
		elseif($data['callback_query']['data'] == 250 || $data['callback_query']['data'] == 251){	
			
			delMessage("", $data['callback_query']['message']['message_id']);
			mainMenu("");				
			
		}
		elseif($data['callback_query']['data'] > 13 && $data['callback_query']['data'] < 18){
		
			if($data['callback_query']['data'] == 14){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][21],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}
			elseif($data['callback_query']['data'] == 15){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][22],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}
			elseif($data['callback_query']['data'] == 16){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][23],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}								
			elseif($data['callback_query']['data'] == 17){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][24],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}					
		
			processWallet($data['callback_query']['data']);
																								
		}
		elseif($data['callback_query']['data'] == 1){
			
			$channel_id1 = "@TonCustomNft";
			$channel_id2 = "@TonBloggerNft";
			$channel_id3 = "@FrontNFT";
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id1, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			$res = json_decode($res, true);
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id2, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res2 = curl_exec($ch);
			curl_close($ch);
			$res2 = json_decode($res2, true);		
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id3, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res3 = curl_exec($ch);
			curl_close($ch);
			$res3 = json_decode($res3, true);					
			
			if ($res['ok'] == true && $res['result']['status'] != "left" && $res2['ok'] == true && $res2['result']['status'] != "left" && $res3['ok'] == true && $res3['result']['status'] != "left") {
		
				$str2upd = "UPDATE `users` SET `verified`='1' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "👍 Порядок! Вы участвуете в розыгрыше. Приглашайте людей и выигрывайте главный приз!",
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');					
		
			}
			elseif($res['result']['status'] == "left"){
		
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => "Oops! Вы не подписаны на оба канала. Пожалуйста подпишитесь!", 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
		
			} else {
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => "Oops! Вы не подписаны на оба канала. Пожалуйста подпишитесь!", 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			
			}		
		
		}

	}else{
	
		if(!isset($data['inline_query']['from']['id'])){
	
		$str5select = "SELECT `action` FROM `temp_sess` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
		$result5 = mysqli_query($link, $str5select);
		$row5 = @mysqli_fetch_object($result5);
		
		if(preg_match("/walletfor_nft/", $row5->action)){	
			
			$walletno = trim($data['message']['text']);
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$XAPIKey));
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);
			
			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][25],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$nfttype = str_replace("walletfor_nft|", "", $row5->action);
				
				messageIfPayByTON();
				
			}
		}
		elseif(preg_match("/wait4wallet/", $row5->action)){

			if(strlen(trim($data['message']['text'])) < 20){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][26],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
				
			}else{
			
			//Wallet verify
			$walletno = trim($data['message']['text']);
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$XAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][27],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$str2upd = "UPDATE `users` SET `wallet`='$walletno' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);	
				
				#addSum();
				choosePayMethod();
			
			}
		
		}

		}
		elseif(preg_match("/wait4sum/", $row5->action)){
			
			$sum = trim($data['message']['text']);
			if(preg_match("/^[0-9]+$/", $sum)){
			
				choosePayMethod($sum);			
			
			}else{
				$response = array(
					'chat_id' => $chat_id,
					'text' => "");
				sendit($response, 'sendMessage');				
			}
			
		}
		elseif(preg_match("/tegro/", $row5->action)){
			if(preg_match("/^[0-9]+$/", trim($data['message']['text']))){
				$coin = str_replace("tegro|", "", $row5->action);	
				
				if($coin == "blogger"){
					$sum = trim($data['message']['text']) * $BloggerNFT;
				}
				elseif($coin == "custom"){
					$sum = trim($data['message']['text']) * $Blogger3D;	
				}
				elseif($coin == "nude"){
					$sum = trim($data['message']['text']) * $NFTNude;										
				}
				
				$paylink = makelink($sum, $coin);
				
				$tomessage = str_replace("%sumtopay%", $data['message']['text'], " Купить %sumtopay% TON.");				
				$url = $paylink;
				$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3').$tomessage;
				$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
				send($chat_id, "Перейдите по ссылке для пополнения:", $arInfo);	
				
			}else{
				$response = array(
					'chat_id' => $chat_id,
					'text' => "❌ Введена некорректная сумма. Пожалуйста используйте только целые числа:");
				sendit($response, 'sendMessage');			
			}
					
		}
		}		
	}

} // if-else /start
 
exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл

function sendit($response, $restype){
	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/'.$restype);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_exec($ch);
	curl_close($ch);	
}

function send($id, $message, $keyboard) {   
		
		//Удаление клавы
		if($keyboard == "DEL"){		
			$keyboard = array(
				'remove_keyboard' => true
			);
		}
		if($keyboard){
			//Отправка клавиатуры
			$encodedMarkup = json_encode($keyboard);
			
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'reply_markup' => $encodedMarkup,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True
			);
		}else{
			//Отправка сообщения
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True				
			);
		}
       
        $out = sendit($data, 'sendMessage');       
        return $out;
}     

function mainMenu($message){
	global $chat_id, $link, $langcode, $text;
	
	$str15select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result15 = mysqli_query($link, $str15select);
	if(mysqli_num_rows($result15) == 0){
		$nftbalance = 0;
		$anime = 0;
		$custom3d = 0;	
		$nude = 0;				
	}else{
		$row15 = @mysqli_fetch_object($result15);
		$nftbalance = $row15->nft_balance;
		$anime = $row15->anime;
		$custom3d = $row15->custom3d;	
		$nude = $row15->nude;									
	}	
	
	$toButton = str_replace("%anime%", $anime, $text[$langcode][0]);
	$toButton = str_replace("%custom3d%", $custom3d, $toButton);	
	$toButton = str_replace("%nude%", $nude, $toButton);	

	$arInfo["inline_keyboard"][0][0]["callback_data"] = 10;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][29];
	$arInfo["inline_keyboard"][1][0]["callback_data"] = 11;
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][30]; 
	$arInfo["inline_keyboard"][2][0]["callback_data"] = 12;
	$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][31]; 
	$arInfo["inline_keyboard"][2][1]["callback_data"] = 13;
	$arInfo["inline_keyboard"][2][1]["text"] = $text[$langcode][1]; 		
	send($chat_id, $message.$toButton.'👇', $arInfo); 
	
}

function clean_temp_sess(){
	global $chat_id, $link;
	
	$str2del = "DELETE FROM `temp_sess` WHERE `chatid` = '$chat_id'";
	mysqli_query($link, $str2del);
}

function save2temp($field, $val){
	global $link, $chat_id;
	$curtime = time();
	
	$str2ins = "INSERT INTO `temp_sess` (`chatid`,`$field`) VALUES ('$chat_id','$val')";
	mysqli_query($link, $str2ins);	

}

function rand_string( $length ) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

}

function delMessage($mid, $cid){
	global $chat_id;
		if($mid != ''){
			$message_id = $mid-1;
		}
		elseif($cid != ''){
			$message_id = $cid;
		}

		$ch2 = curl_init('https://api.telegram.org/bot' . TOKEN . '/deleteMessage');  
		curl_setopt($ch2, CURLOPT_POST, 1);  
		curl_setopt($ch2, CURLOPT_POSTFIELDS, array('chat_id' => $chat_id, 'message_id' => $message_id));
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_HEADER, false);
		$res2 = curl_exec($ch2);
		curl_close($ch2);		
}

function langCode($langcode){
	if($langcode > 12) $langcode = 0;
	return $langcode;
}

function chooseLang(){
	global $chat_id, $link, $langcode, $text, $lang;
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 100;
	$arInfo["inline_keyboard"][0][0]["text"] = $lang[0];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 101;
	$arInfo["inline_keyboard"][0][1]["text"] = $lang[1]; 
	send($chat_id, hex2bin('F09F92AD')." Выберите язык:", $arInfo); 	
}

function choosePayMethod(){
	global $chat_id, $link, $langcode, $text,$CryptoPayAPIToken;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$case = $row->ccase;
	
################# PREPARE FOR CRYPTO BOT #######################
	switch ($case) {
		case 14:
		$sum = 5;
		break;
		case 15:
		$sum = 15;
		break;
		case 16:
		$sum = 25;
		break;
		case 17:
		$sum = 45;
		break;						
	}
	
	$ctime = time();
	$payload = $chat_id.":".$case;
	$data = array("asset"=>"TON", "amount"=>$sum, "payload"=>$payload, "paid_btn_name"=>"callback", "paid_btn_url"=>"https://t.me/WinNFTbot");
	
	$prop = http_build_query($data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://pay.crypt.bot/api/createInvoice?".$prop);
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'Crypto-Pay-API-Token: '.$CryptoPayAPIToken));
	
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	$res = json_decode($server_output, true);		
################# PREPARE FOR CRYPTO BOT #######################
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 150;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][2];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 151;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][3]; 
	$url22 = $res['result']['pay_url'];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url22);	
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][4]; 	
	send($chat_id, $text[$langcode][5], $arInfo); 	
}

function makelink($sum, $case){
	global $link, $chat_id, $roskassa_publickey, $roskassa_secretkey;
	
	$curtime = time();
	$str2ins = "INSERT INTO `paylinks` (`chatid`,`times`,`status`,`sum`) VALUES ('$chat_id','$curtime','0','$sum')";
	mysqli_query($link, $str2ins);
	$last_id = mysqli_insert_id($link);
	
	$secret = $roskassa_secretkey;
	$data = array(
		'shop_id'=>$roskassa_publickey,
		'amount'=>$sum,
		'currency'=>'TON',
		'order_id'=>$chat_id."|".$case
		#'test'=>1
	);
	ksort($data);
	$str = http_build_query($data);
	$sign = md5($str . $secret);
	
	return 'https://tegro.money/pay/?'.$str.'&sign='.$sign;
	
}

function processWallet($case){
	global $chat_id, $link, $langcode, $text;
	
	if($case != ""){
		$str2del = "DELETE FROM `temp_coin` WHERE `chatid`='$chat_id'";
		mysqli_query($link, $str2del);
		$str2ins = "INSERT INTO `temp_coin` (`chatid`,`ccase`) VALUES ('$chat_id','$case')";
		mysqli_query($link, $str2ins);
	}
	
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if(strlen($row->wallet) > 10){
		$toButton = str_replace("%walletno%", $row->wallet, $text[$langcode][6]);	
		
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 3;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][7];
		$arInfo["inline_keyboard"][0][1]["callback_data"] = 4;
		$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][8];				
		send($chat_id, $toButton, $arInfo);
		 		
	}else{
		
		processWallet2();
		
	}
	
}

function processWallet2(){
	global $chat_id, $link, $langcode, $text;

	clean_temp_sess();
	save2temp("action", "wait4wallet");
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][9],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	

}

function addSum(){
	global $chat_id, $link, $langcode, $text, $BloggerNFT, $Blogger3D;
	
	clean_temp_sess();
	save2temp("action", "wait4sum");
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if($row->coin == "blogger"){$rate = $BloggerNFT;}
	elseif($row->coin == "custom"){$rate = $Blogger3D;}	
	elseif($row->coin == "nude"){$rate = $NFTNude;}	
	
	$tomsg = str_replace("%coin%", $row->coin, "Введите количество приобретаемых %coin% NFT:");	
	$tomsg = str_replace("%coinrate%", $rate, $tomsg);		
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomsg,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	
	
}

function messageIfPayByTON(){
	global $chat_id, $link, $langcode, $text, $NFTwallet;
	
	$str2select = "SELECT * FROM `temp_coin` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	$case = $row->ccase;
	
	$str20select = "SELECT `wallet` FROM `users` WHERE `chatid`='$chat_id'";
	$result20 = mysqli_query($link, $str20select);
	$row20 = @mysqli_fetch_object($result20);
	$walletno = $row20->wallet;
	
/*	$str15select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result15 = mysqli_query($link, $str15select);
	if(mysqli_num_rows($result15) == 0){
		$nftbalance = 0;
		$nftcat = 0;
		$nftdog = 0;
		$nftnude = 0;
	}else{
		$row15 = @mysqli_fetch_object($result15);
		$nftbalance = $row15->nft_balance;
		$nftcat = $row15->blogger;
		$nftdog = $row15->custom;					
		$nftnude = $row15->nude;							
	}*/
	
	switch ($case) {
		case 14:
		$casetext = "silver";
		break;
		case 15:
		$casetext = "gold";
		break;
		case 16:
		$casetext = "platinum";
		break;
		case 17:
		$casetext = "diamond";
		break;						
	}	
	
	$nftcode = $casetext.";".rand_string(20);
	$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);				

	switch ($case) {
		case 14:
		$sum = 5;
		$casename = "🪙 Silver";
		break;
		case 15:
		$sum = 15;
		$casename = "⚜️ Gold";
		break;
		case 16:
		$sum = 25;
		$casename = "💍 Platinum";		
		break;
		case 17:
		$sum = 45;
		$casename = "💠 Diamond";		
		break;						
	}			
	$suminnanoton = $sum * 1000000000;
	$suminton = $sum;
	
/*	$tomessage = str_replace("%nftCatRate%", $BloggerNFT, "💎 На вашем балансе %nftcat% NFT Custom Blogger.&#13;&#10;&#13;&#10;Цена 1 Custom Blogger = %nftCatRate% Toncoin");
	$tomessage = str_replace("%nftcat%", $nftcat, $tomessage);
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	

	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');*/

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][10]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%casename%", $casename, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwallet, $text[$langcode][11]);
	$tomessage = str_replace("%nftcode%", $nftcode, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminnanoton, $tomessage);				
	
	unset($arInfo);
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "chkp".$walletno."|".$sum;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][12];
	send($chat_id, $tomessage, $arInfo); 									
				
}
/*function submenu(){
	global $chat_id, $langcode, $text;	
	
	$arInfo["keyboard"][0][0]["text"] = "🏵 Преимущества";
	$arInfo["keyboard"][0][1]["text"] = "📝 О проекте";	
	$arInfo["keyboard"][1][0]["text"] = "❗️ Команды";	
	$arInfo["keyboard"][1][1]["text"] = "❓ FAQ";
	$arInfo["keyboard"][2][0]["text"] = "↩️ Назад в главное меню";			
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $text[$langcode][43].'👇', $arInfo); 		
}
*/
function chooseCase(){
	global $chat_id, $langcode, $text;	
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 14;
	$arInfo["inline_keyboard"][0][0]["text"] = "🪙 Silver (5 TON)";
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 16;
	$arInfo["inline_keyboard"][0][1]["text"] = "💍 Platinum (25 TON)"; 
	$arInfo["inline_keyboard"][1][0]["callback_data"] = 15;
	$arInfo["inline_keyboard"][1][0]["text"] = "⚜️ Gold (15 TON)"; 
	$arInfo["inline_keyboard"][1][1]["callback_data"] = 17;
	$arInfo["inline_keyboard"][1][1]["text"] = "💠 Diamond (45 TON)"; 	
	$arInfo["inline_keyboard"][2][0]["callback_data"] = 251;
	$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][32]; 			
	send($chat_id, $text[$langcode][13], $arInfo); 	
}

function send2($method, $request)
{

	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);
	curl_setopt_array($ch,
		[
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($request),
			CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			CURLOPT_SSL_VERIFYPEER => false,
		]
	);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}
	
function uuid()
{
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}	

function checkInlineQuery()
{
	global $langcode, $text;	
	$request = json_decode(file_get_contents('php://input'));

	if (isset($request->inline_query))
	{
		
		$chatid = $request->inline_query->from->id;
		
		#file_put_contents('debug', print_r($request, true) . PHP_EOL . json_encode($request) . PHP_EOL . $result . PHP_EOL, FILE_APPEND);
		
		// https://core.telegram.org/bots/api#answerinlinequery
		send2('answerInlineQuery',
			[
				'inline_query_id' => $request->inline_query->id,

				// InlineQueryResult https://core.telegram.org/bots/api#inlinequeryresult
				'results' =>
				[
					[
						// InlineQueryResultArticle https://core.telegram.org/bots/api#inlinequeryresultarticle
						'type' => 'article',
						'id' => uuid(),
						// 'id' => 0,
						'title' => $text[$langcode][36],
						'description' => $text[$langcode][39],
						'thumb_url' => 'https://winnftbot.com/WinNFTbot/avatar100.jpg',

						// InputMessageContent https://core.telegram.org/bots/api#inputmessagecontent
						'input_message_content' =>
						[
							// InputTextMessageContent https://core.telegram.org/bots/api#inputtextmessagecontent
							'message_text' => $text[$langcode][37],
						],

						// InlineKeyboardMarkup https://core.telegram.org/bots/api#inlinekeyboardmarkup
						'reply_markup' =>
						[
							'inline_keyboard' =>
							[
								// InlineKeyboardButton https://core.telegram.org/bots/api#inlinekeyboardbutton
								[
									[
										'text' => $text[$langcode][38],
										'url' => 'https://t.me/WinNFTbot?start='.$chatid,
									],
								],
							],
						],
					],
				],
			]
		);
	}
}