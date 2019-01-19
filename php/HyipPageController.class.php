<?php
require_once($_SERVER['DOCUMENT_ROOT']."/php/CPanelCore.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/Data.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/TemplateEngine.class.php");

	class HyipPageController {
		private $TEngine;
		private $PathToTemplate;
		private $uid;
		private $Core;
		
			private $PAGE;
			private $TITLE;
			private $PAGE_TITLE;
			private $CONTENT;
			private $PAGE_CONTENT;
		
		public function __construct($Core) {
			$this->PathToTemplate = $_SERVER['DOCUMENT_ROOT'] . "/template/template-" . $Core->int_GetSiteTemplate();
			$this->Core = $Core;
			$this->TEngine = new TemplateEngine();
			
			$this->PAGE = (array_key_exists("uid", $_SESSION)) ?
						  file_get_contents($this->PathToTemplate . "/layout-signedin.html") :
						  file_get_contents($this->PathToTemplate . "/layout.html");
			
			$this->TITLE = "";
			$this->PAGE_TITLE = "";
			$this->CONTENT = "";
			$this->PAGE_CONTENT = "";
			
			self::_handleError();
		}
		
		private function _checkBan() {
			$user = $this->Core->GetAccountById($this->uid);
			if ($user["ban"] == 1) return true;
			else return false;
		}
		
			private function _handleError() {
				global $_ERRORS;
				if ($_SESSION["ERROR"] == 0) {
					$this->TEngine->error_visibility = "none";
					$this->TEngine->error_text = "";
				}
				else {
					$this->TEngine->error_visibility = "block";
					$this->TEngine->error_text = $_ERRORS[$_SESSION["ERROR"]];
				}
				
				$this->PAGE = $this->TEngine->generate($this->PAGE);
				$this->TEngine->clean();
			}

			private function _makeActiveTopMenu($page) {
				$this->TEngine->main_active = ($page == "main") ? "active" : "";
				$this->TEngine->faq_active = ($page == "faq") ? "active" : "";
				$this->TEngine->rules_active = ($page == "rules") ? "active" : "";
				$this->TEngine->reg_active = ($page == "reg") ? "active" : "";
				$this->TEngine->signin_active = ($page == "signin") ? "active" : "";
				$this->TEngine->cabinet_active = ($page == "cabinet") ? "active" : "";
				
				$this->PAGE = $this->TEngine->generate($this->PAGE);
				$this->TEngine->clean();
			}
			
			private function _addSideMenu($banned = false) {
				$side_menu = (!$banned) ? 
							 file_get_contents($this->PathToTemplate . "/page-parts/side-menu.html") : 
							 file_get_contents($this->PathToTemplate . "/page-parts/side-menu-banned.html");
				
				$this->TEngine->side_menu = $side_menu;
				$this->CONTENT = $this->TEngine->generate($this->CONTENT);
				$this->TEngine->clean();
			}
			
			private function _makeActiveSideMenu($page) {
				$this->TEngine->cabinet_active = ($page == "cabinet") ? "active" : "";
				$this->TEngine->deposit_active = ($page == "deposit") ? "active" : "";
				$this->TEngine->affilate_active = ($page == "affilate") ? "active" : "";
				$this->TEngine->withdrawal_active = ($page == "withdrawal") ? "active" : "";
				$this->TEngine->tickets_active = ($page == "tickets") ? "active" : "";
				
				$this->CONTENT = $this->TEngine->generate($this->CONTENT);
				$this->TEngine->clean();
			}
			
			private function _generatePage() {
				$this->TEngine->title = $this->TITLE;
				$this->TEngine->content = $this->CONTENT;
				$this->TEngine->page_title = $this->PAGE_TITLE;
				$this->TEngine->page_content = $this->PAGE_CONTENT;
				
				$res = $this->TEngine->generate($this->PAGE);
				$this->TEngine->clean();
				return $res;
			}
		
		
		public function GenerateAuth() {
			self::_makeActiveTopMenu("signin");
			
			$this->TITLE = "Авторизация";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/auth.html");
			
			return self::_generatePage();
		}
				
		public function GenerateReg($login = "", $email = "") {
			$this->TITLE = "Регистрация";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/reg.html");
			
			self::_makeActiveTopMenu("reg");
			
			$this->TEngine->login = $login;
			$this->TEngine->email = $email;
			
			$this->CONTENT = $this->TEngine->generate($this->CONTENT);
			$this->TEngine->clean();
			
			return self::_generatePage();
		}
		
		
		public function GenerateIndex() {
			$this->TITLE = "Главная";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/index.html");
			
				$main_currency = $this->Core->s_GetCurrencyNameById($this->Core->int_GetMainCurrencyId());
			
				$last_regs = $this->Core->GetLastAccs(5);
				$last_regs_html = "";
				foreach($last_regs as $value) {
					$last_regs_html .= "<tr> <td>".$value["login"]."</td> <td>".(date("d.m.Y", $value["reg_date"]))."</td> </tr>\n";
				}
			
				$last_refills = $this->Core->GetLastRefills(5);
				$last_refills_html = "";
				foreach($last_refills as $value) {
					$user = $this->Core->GetAccountById($value["user_id"]);
					$last_refills_html .= "<tr> <td>".($user["login"])."</td> <td>".$value["amount"]." ".$main_currency."</td> </tr>\n";
				}
			
				$last_withdrawals = $this->Core->GetLastWithdrawals(5);
				$last_withdrawals_html = "";
				foreach($last_withdrawals as $value) {
					$user = $this->Core->GetAccountById($value["user_id"]);
					$last_withdrawals_html .= "<tr> <td>".($user["login"])."</td> <td>".$value["amount"]." ".$main_currency."</td> </tr>\n";
				}
				
			$this->TEngine->total_accs = $this->Core->CountAllAccounts();
			$this->TEngine->total_deposits = intval($this->Core->CountSumOfRefills())." ".$main_currency;
			$this->TEngine->total_withdrawals = intval($this->Core->CountSumOfWithdrawals())." ".$main_currency;
			$this->TEngine->new_regs = $last_regs_html;
			$this->TEngine->new_refills = $last_refills_html;
			$this->TEngine->new_withdrawals = $last_withdrawals_html;
			
			$this->CONTENT = $this->TEngine->generate($this->CONTENT);
			$this->TEngine->clean();
			
			self::_makeActiveTopMenu("main");
			
			return self::_generatePage();
		}
		
		public function GenerateCabinet() {
			$this->TITLE = "Кабинет";
			$this->PAGE_TITLE = "Кабинет";
			
			self::_makeActiveTopMenu("cabinet");
			self::_makeActiveSideMenu("cabinet");
			
			if (self::_checkBan()) {
				$this->CONTENT = file_get_contents($this->PathToTemplate . "/page-parts/banned.html");
				
					$user = $this->Core->GetAccountById($_SESSION["uid"]);
					$this->TEngine->reason = $user["reason"];
	
					$this->CONTENT = $this->TEngine->generate($this->CONTENT);
					$this->TEngine->clean();
	
				self::_addSideMenu(true);
				
				return self::_generatePage();
			}
			
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/page-parts/page-content.html");
			
				$balance_table = file_get_contents($this->PathToTemplate . "/page-parts/balance_table.html");
				$wallets_table = file_get_contents($this->PathToTemplate . "/page-parts/wallets_table.html");
				
				$refill_awaiting = file_get_contents($this->PathToTemplate . "/page-parts/refill_awaiting.html");
				$refill_confirmed = file_get_contents($this->PathToTemplate . "/page-parts/refill_confirmed.html");
				$refill_notpassed = file_get_contents($this->PathToTemplate . "/page-parts/refill_notpassed.html");
				
				$uid_refills = $this->Core->arr_GetRefillsByUID($this->uid);
				$uid_balance = $this->Core->arr_GetBalanceByUID($this->uid);
				$uid_wallets = $this->Core->arr_GetWalletsByUID($this->uid);
				
				$html_refills = "";
				$html_balance = "";
				$html_wallets = "";
				
				$html_balance = "<tr> <td>".($this->Core->s_GetCurrencyNameById($this->Core->int_GetMainCurrencyId()))."</td> <td>".($uid_balance["sum"])."</td> </tr>\n"; 
				foreach($uid_wallets as $value) $html_wallets .= "<tr> <td>".($this->Core->s_GetPaymentSystemNameByID($value["payment_system_id"]))."</td> <td>".($value["wallet"])."</td> </tr>\n"; 
				
				foreach($uid_refills as $value){
					if ($value["viewed"] == 1) continue;
					
					switch ($value["status"]) {
						case 0:
							$this->TEngine->payment_system_name = $this->Core->s_GetPaymentSystemNameByID($value["payment_system_id"]);
							$this->TEngine->trans_id = $value["transaction_id"];
							
							$html_refills .= $this->TEngine->generate($refill_awaiting);
							$this->TEngine->clean();
						break;
						
						case 1:
							$this->TEngine->id = $value["id"];
							$this->TEngine->payment_system_name = $this->Core->s_GetPaymentSystemNameByID($value["payment_system_id"]);
							$this->TEngine->trans_id = $value["transaction_id"];
							
							$html_refills .= $this->TEngine->generate($refill_confirmed);
							$this->TEngine->clean();
						break;
						
						case 2:
							$this->TEngine->id = $value["id"];
							$this->TEngine->payment_system_name = $this->Core->s_GetPaymentSystemNameByID($value["payment_system_id"]);
							$this->TEngine->trans_id = $value["transaction_id"];
							
							$html_refills .= $this->TEngine->generate($refill_notpassed);
							$this->TEngine->clean();
						break;
					}
				}
				
				$this->TEngine->balances = $html_balance;
				$this->TEngine->comission = $this->Core->int_GetСommissionPercent();
				$balance_table .= $this->TEngine->generate($balance_table);
				$this->TEngine->clean();
				
				$this->TEngine->wallets = $html_wallets;
				$wallets_table .= $this->TEngine->generate($wallets_table);
				$this->TEngine->clean();
				
			$this->PAGE_CONTENT = $html_refills.$balance_table.$wallets_table;
			
			self::_addSideMenu();
			
			return self::_generatePage();
		}
		
		public function GenerateDeposits() {
			$this->TITLE = "Депозиты";
			$this->PAGE_TITLE = "Депозиты";
			
			if (self::_checkBan()) {
				header("Location: /cabinet");
				exit();
			}
			
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/page-parts/page-content.html");
				
				$deposits_table = file_get_contents($this->PathToTemplate . "/page-parts/deposits_table.html");
				
				$uid_deposits = $this->Core->arr_GetDepositsByUID($this->uid);
				
				$html_deposits = "";
				
				foreach($uid_deposits as $value) {
					$plan = $this->Core->arr_GetPlanByID($value["plan_id"]);
					
					$html_deposits .=   "</td> <td>".$plan["name"].
										"</td> <td>".$value["amount"].
										"</td> <td>".$value["return_amount"].
										"</td> <td>".(date("d.m.Y H:i", $value["start_date"])).
										"</td> <td>".(date("d.m.Y H:i", $value["end_date"]));
										
					if ($value["status"] == 1) $html_deposits .= "</td> <td>Выплачено</td> </tr>\n";
					else $html_deposits .= "</td> <td>".(date("d.m.Y H:i", $value["next_payment"]))."</td> </tr>\n";
				}
				
				$this->TEngine->deposits = $html_deposits;
				$deposits_table = $this->TEngine->generate($deposits_table);
				$this->TEngine->clean();
				
			$this->PAGE_CONTENT = $deposits_table;
			
			self::_addSideMenu();
			self::_makeActiveTopMenu("cabinet");
			self::_makeActiveSideMenu("deposit");
			
			return self::_generatePage();
		}
		
		
		public function GenerateWithdrawal() {
			$this->TITLE = "Вывод средств";
			$this->PAGE_TITLE = "Вывод средств";
			
			if (self::_checkBan()) {
				header("Location: /cabinet");
				exit();
			}
			
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/page-parts/page-content.html");
			
				$withdrawal_table = file_get_contents($this->PathToTemplate . "/page-parts/withdrawal_table.html");
				
				$uid_withdrawals = $this->Core->arr_GetWithdrawalsByUID($this->uid);
				
				$html_withdrawals = "";
				
				foreach($uid_withdrawals as $value) {
					$wallet = $this->Core->arr_GetWallet($value["wallet_id"]);
					$html_withdrawals .= "<tr> <td>".(date("d.m.Y H:i", $value["date"])).
										 "</td> <td>".$value["amount"].
										 "</td> <td>".($this->Core->s_GetPaymentSystemNameByID($wallet["payment_system_id"])).
										 "</td> <td>".$wallet["wallet"];
					if ($value["status"] == 1) $html_withdrawals .= "</td> <td>Обработано</td></tr>\n";
					else $html_withdrawals .= "</td> <td>В ожидании</td> </tr>\n";
				}
				
				$this->TEngine->withdrawal = $html_withdrawals;
				$withdrawal_table = $this->TEngine->generate($withdrawal_table);
				$this->TEngine->clean();
				
			$this->PAGE_CONTENT = $withdrawal_table;
			
			self::_addSideMenu();
			self::_makeActiveTopMenu("cabinet");
			self::_makeActiveSideMenu("withdrawal");
			
			return self::_generatePage();
		}
				
				public function GenerateTickets() {
					$this->TITLE = "Техподдержка";
					$this->PAGE_TITLE = "Техподдержка";
					
					$this->CONTENT = file_get_contents($this->PathToTemplate . "/page-parts/page-content.html");
					
						$tickets_table = file_get_contents($this->PathToTemplate . "/page-parts/tickets_table.html");
						
						$uid_tickets = $this->Core->arr_GetTicketsByUID($this->uid);
						
						$html_tickets = "";
						
						foreach($uid_tickets as $value) {
							$html_tickets .= "<tr> <td>".(date("d.m.Y H:i", $value["date"])).
												 "</td> <td> <a href='/ticket?tid=".$value["id"]."'> ".$value["title"];
							if ($value["status"] == 1) $html_tickets .= " </a> </td> <td>Обработано</td> </tr>\n";
							else $html_tickets .= " </a> </td> <td>В ожидании</td> </tr>\n";
						}
						
						$this->TEngine->tickets = $html_tickets;
						$tickets_table = $this->TEngine->generate($tickets_table);
						$this->TEngine->clean();
						
					$this->PAGE_CONTENT = $tickets_table;
					
					if (self::_checkBan()) self::_addSideMenu(true);
					else self::_addSideMenu();
					
					self::_makeActiveTopMenu("cabinet");
					self::_makeActiveSideMenu("tickets");
					
					return self::_generatePage();
				}
		
		
		
		public function GenerateFaq() {
			$this->TITLE = "Часто задаваемые вопросы";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/faq.html");
			
			self::_makeActiveTopMenu("faq");
			
			return self::_generatePage();
		}
		
		public function GenerateRules() {
			$this->TITLE = "Правила и соглашение";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/rules.html");
			
			self::_makeActiveTopMenu("rules");
			
			return self::_generatePage();
		}
		
		public function GenerateHelp() {
			$this->TITLE = "Поддержка";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/help.html");
			
			self::_makeActiveTopMenu("");
			
			return self::_generatePage();
		}
		
		public function GenerateError404() {
			$this->TITLE = "Ошибка!";
			$this->CONTENT = file_get_contents($this->PathToTemplate . "/pages/404.html");
			return self::_generatePage();
		}
	}
?>