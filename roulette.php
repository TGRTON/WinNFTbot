<?php 
function roulette($case, $gotTON, $senderid){
	global $chat_id, $link, $langcode, $text, $WinNFTbotRefPercent;

	########### ROULETTE #############
	$randresult = mt_rand(1, 100);
	$chanses[0] = array(50, 35, 15, 0);
	$chanses[1] = array(30, 50, 15, 5);
	$chanses[2] = array(10, 60, 20, 10);								
	$chanses[3] = array(5, 35, 45, 15);	
	
	if($case == 14)$cha = $chanses[0];
	elseif($case == 15)$cha = $chanses[1];
	elseif($case == 16)$cha = $chanses[2];
	elseif($case == 17)$cha = $chanses[3];								
		
	if($randresult <= $cha[0]) $won = "zero";
	elseif($randresult <= ($cha[0] + $cha[1])) $won = "nude";
	elseif($randresult <= ($cha[0] + $cha[1] + $cha[2])) $won = "anime";	
	else $won = "custom3d";								
	########### ROULETTE #############				
	
	if($won != "zero"){
	$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result16 = mysqli_query($link, $str16select);
		if(mysqli_num_rows($result16) == 0 ){
			$str2ins = "INSERT INTO `nft` (`chatid`,`$won`) VALUES ('$chat_id','1')";
			mysqli_query($link, $str2ins);
/*$response = array(
	'chat_id' => $chat_id, 
	'text' => "DEBUG: ".$str2ins,
	'parse_mode' => 'HTML');	
sendit($response, 'sendMessage');*/			
		}else{
			$row16 = @mysqli_fetch_object($result16);
			if($won == "nude"){
				$oldsum = $row16->nude;
			}elseif($won == "anime"){
				$oldsum = $row16->anime;
			}elseif($won == "custom3d"){
				$oldsum = $row16->custom3d;								
			}
			$newsum = $oldsum + 1;					
			$str11upd = "UPDATE `nft` SET `".$won."`='".$newsum."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str11upd);
							
		}

	}

	if($won == "nude"){
		$wondetails = "👩‍🎤 Custom Nude!";
	}elseif($won == "anime"){
		$wondetails = "👸 Custom Anime!";
	}elseif($won == "custom3d"){
		$wondetails = "👑 Custom 3D!";
	}else{
		$wondetails = "ничего.";				
	}
	
	########## REF FEE ##########
	$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$row12 = @mysqli_fetch_object($result12);	
	
	$earnRefNFT = $gotTON / 100 * $WinNFTbotRefPercent;
	
	if($row12->ref > 1){
		$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earnRefNFT WHERE `chatid`='".$row12->ref."'";
		mysqli_query($link, $str10upd);	
	}
	########## REF FEE ##########		
	
	$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result16 = mysqli_query($link, $str16select);
	$row16 = @mysqli_fetch_object($result16);
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => "🔐");	
	sendit($response, 'sendMessage');
	
	$tomessage = str_replace("%wondetails%", $wondetails, $text[$langcode][28]);
	$tomessage = str_replace("%nft_balance%", $row16->nft_balance, $tomessage);
	#$tomessage = str_replace("%nfttype%", $nfttype, $tomessage);				
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 250;
	$arInfo["inline_keyboard"][0][0]["text"] = "Назад";
	send($chat_id, $tomessage, $arInfo); 
	
/*	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');*/
	
	######## SAVE TRANSACTION ###########
	$date_time = date("j-m-Y G:i");
	$str2ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`ton`) VALUES ('$chat_id','$senderid','$date_time','$gotTON')";
	mysqli_query($link, $str2ins);
	if($won != "zero"){
		if($won == "nude"){
			$nude = 1;
			$anime = 0;
			$custom3d = 0;
		}elseif($won == "anime"){
			$nude = 0;
			$anime = 1;
			$custom3d = 0;
		}elseif($won == "custom3d"){
			$nude = 0;
			$anime = 0;
			$custom3d = 1;
		}
		$str3ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`nude`,`anime`,`custom3d`) VALUES ('$chat_id','case win','$date_time','$nude','$anime','$custom3d')";
		mysqli_query($link, $str3ins);
		
		$str4ins = "INSERT INTO `raffles_history` (`chatid`,`date_time`,`nft`,`sum`) VALUES ('$chat_id','$date_time','$won','1')";
		mysqli_query($link, $str4ins);		
/*$response = array(
	'chat_id' => $chat_id, 
	'text' => "DEBUG2: ".$str3ins,
	'parse_mode' => 'HTML');	
sendit($response, 'sendMessage');	*/		
	}
	######## SAVE TRANSACTION ###########											
	
}
?>