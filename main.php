<?php
/**
* Telegram Bot Osm Wiki Bot
* @author Francesco Piero Paolicelli @piersoft
*/

include("Telegram.php");
include("settings_t.php");

class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");

	if ($text == "/start" || $text == "Informazioni") {
		$img = curl_file_create('osm_logo.png','image/png');
		$contentp = array('chat_id' => $chat_id, 'photo' => $img);
		$telegram->sendPhoto($contentp);
		$reply = "Benvenuto. Questo è un servizio automatico (bot da Robot) di ".NAME.". Puoi ricercare gli argomenti del Wiki di OpenStreeMap per parola chiave descrittiva anteponendo il carattere ? e quindi fare una ricerca per numero corrispondente. In qualsiasi momento scrivendo /start ti ripeterò questo messaggio di benvenuto.\nQuesto bot è stato realizzato da @piersoft insieme a ".NAME.". Il progetto e il codice sorgente sono liberamente riutilizzabili con licenza MIT.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ";new chat started;" .$chat_id. "\n";
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;
	}
			elseif ($text == "Ricerca") {
				$reply = "Scrivi la parola da cercare anteponendo il carattere ?\nAd esempio: <b>?confine</b>";
				$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true,'parse_mode'=>"HTML");
				$telegram->sendMessage($content);
				$log=$today. ";new chat started;" .$chat_id. "\n";
	//			$this->create_keyboard_temp($telegram,$chat_id);
exit;

}elseif($location!=null)
		{

		//	$this->location_manager($telegram,$user_id,$chat_id,$location);
		//	exit;

		}
//elseif($text !=null)

		elseif(strpos($text,'/') === false){

			if(strpos($text,'?') !== false){
				$text=str_replace("?","",$text);
				$location="Sto cercando argomenti con parola chiave: ".$text;
				$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
				$telegram->sendMessage($content);
				$text=str_replace(" ","%20",$text);
				$text=strtoupper($text);
				$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20WHERE%20upper(C)%20contains%20%27";
				$urlgd .=$text;
				$urlgd .="%27%20OR%20upper(F)%20contains%20%27".$text."%27&key=".GDRIVEKEY."&gid=".GDRIVEGID1;
				$inizio=1;
				$homepage ="";
				//$comune="Lecce";

				//echo $urlgd;
				$csv = array_map('str_getcsv',file($urlgd));
				//var_dump($csv[1][0]);
				$count = 0;
				foreach($csv as $data=>$csv1){
					$count = $count+1;
				}
				if ($count ==1){
						$location="Nessun risultato trovato";
						$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
						$telegram->sendMessage($content);
					}
					if ($count >40){
							$location="Troppe risposte per il criterio scelto. Ti preghiamo di fare una ricerca più circoscritta";
							$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
							$telegram->sendMessage($content);
							exit;
						}
					//	$content = array('chat_id' => $chat_id, 'text' => $urlgd,'disable_web_page_preview'=>true);
					//	$telegram->sendMessage($content);
				foreach($csv as $i=>$csv1){

if ($i>0){
					$homepage .="\n";
					//$homepage .="ID N°: ".$csv[$i][6]."\n";
					$homepage .="key:value -> ".$csv[$i][0].":".$csv[$i][1]."\n";
					$homepage .="Per i dettagli digita il numero: <b>".$csv[$i][6]."</b>\n";
					$homepage .="***********";

}
				}
				$chunks = str_split($homepage, self::MAX_LENGTH);
				foreach($chunks as $chunk) {
					$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true,'parse_mode'=>"HTML");
					$telegram->sendMessage($content);
						}
		}elseif (strpos($text,'1') !== false || strpos($text,'2') !== false || strpos($text,'3') !== false || strpos($text,'4') !== false || strpos($text,'5') !== false || strpos($text,'6') !== false || strpos($text,'7') !== false || strpos($text,'8') !== false || strpos($text,'9') !== false || strpos($text,'0') !== false ){
			$location="Sto elaborando la risposta ..";
			$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
			//$text=str_replace(" ","%20",$text);
			//$text=strtoupper($text);
			$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20WHERE%20G%20%3D%20";
			$urlgd .=$text;
			$urlgd .="%20&key=".GDRIVEKEY."&gid=".GDRIVEGID1;
			$inizio=1;
			$homepage ="";
			//$comune="Lecce";

		//echo $urlgd;
			$csv = array_map('str_getcsv',file($urlgd));
		//var_dump($csv[1][0]);
			$count = 0;
			foreach($csv as $data=>$csv1){
				$count = $count+1;
			}
		if ($count ==0 || $count ==1){
					$location="Nessun risultato trovato";
					$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);
				}

			for ($i=$inizio;$i<$count;$i++){
				$csv[$i][3]=str_replace("  ",", ",$csv[$i][3]);
				$homepage .="\nVai al Wiki: ";
				$homepage .="<a href='".$csv[$i][4]."'>".$csv[$i][0].":".$csv[$i][1]."</a>\n";
				$homepage .=$csv[$i][2]."\n";
			if ($csv[$i][3]	!=null) $homepage .="Used (node,way,relation): ".$csv[$i][3]."\n";
				$homepage .="____________\n";
		}
		$chunks = str_split($homepage, self::MAX_LENGTH);
		foreach($chunks as $chunk) {
			$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true,'parse_mode'=>"HTML");
			$telegram->sendMessage($content);
				}

		}
		else{
			$content = array('chat_id' => $chat_id, 'text' => "Non ho capito il tuo comando",'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
		}

		$this->create_keyboard_temp($telegram,$chat_id);
		exit;
}

	}

	function create_keyboard_temp($telegram, $chat_id)
	 {
			 $option = array(["Ricerca","Informazioni"]);
			 $keyb = $telegram->buildKeyBoard($option, $onetime=false);
			 $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Fai la tua ricerca con ?]");
			 $telegram->sendMessage($content);
	 }

}

?>
