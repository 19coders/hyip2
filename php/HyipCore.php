<?php
		/*
		 *	Пользователи
		 *	Сайт
		 *	Кошельки
		 *	Пополнения
		 *	Балансы
		 *	Планы
		 *	Платежные системы
		 *	Депозиты
		 *	Валюты
		 *	Партнерка
		 *	Выплаты
		 *	Тикеты
		 */


require_once($_SERVER['DOCUMENT_ROOT']."/php/DB.class.php");

	class HyipCore {
		private $db;
		
		public function __construct(){
			$this->db = new DB();
		}
		
		/*
		 *		Пользователи
		 */
		public function CheckAccount($login, $password) {
			return $this->db->checkRow
			(
				"accounts", 
				array (
					array("login", "=", $login),
					array("passhash", "=", md5($password . "salt salt salt"))
				)
			);
		}
		
		public function CheckLogin($login) {
			return $this->db->checkRow
			(
				"accounts", 
				array (
					array("login", "=", $login)
				)
			);
		}
		
		public function CheckEmail($email) {
			return $this->db->checkRow
			(
				"accounts", 
				array (
					array("email", "=", $email)
				)
			);
		}
		
		public function GetAccount($login) {
			$rows = $this->db->getRows(
				"accounts",
				array (
					array("login", "=", $login)
				)
			);
			return $rows[0];
		}
		
		public function GetAccountById($id) {
			$rows = $this->db->getRows(
				"accounts",
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0];
		}
		
		public function CountAllAccounts() {
			return $this->db->countRows(
				"accounts",
				array (
					array("id", ">", 0)
				)
			);
		}
		
		public function GetLastAccs($number) {
			return $this->db->getListRows("accounts", false, "id", false, true, 0, $number);
		}
		
		public function AddAccount($login, $password, $email, $refer = NULL) {
			$this->db->addRow(
				"accounts",
				array (
					array("login", "=", $login),
					array("passhash", "=", md5($password . "salt salt salt")),
					array("email", "=", $email),
					array("reg_date", "=", time()),
					array("ban", "=", 0),
					array("ref_views", "=", 0),
					array("refer_id", "=", $refer)
				)
			);
		}
		
		public function UpdateAccount($id, $login, $password, $email, $ban, $refviews) {
			$this->db->updateRow(
				"accounts", 
				array (
					array("id", "=", $id)
				),
				array (
					array("login", "=", $login),
					array("passhash", "=", md5($password . "salt salt salt")),
					array("email", "=", $email),
					array("ban", "=", $ban),
					array("ref_views", "=", $refviews)
				)
			);
		}
		
		public function int_GetRefViewsByID($id){
			$rows = $this->db->getRows(
				"accounts", 
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0]["ref_views"];
		}
		
		public function void_IncrementRefViews($id){
			$user = self::GetAccountById($id);
			
			$this->db->updateRow(
				"accounts", 
				array (
					array("id", "=", $id)
				),
				array (
					array("ref_views", "=", $user["ref_views"]+1)
				)
			);
		}
		/*
		 *		/Пользователи
		 */
		 
		
		/*
		 *		Сайт
		 */
		public function int_GetSiteTemplate(){
			$rows = $this->db->getRows(
				"config", 
				array (
					array("param_name", "=", "template")
				)
			);
			return $rows[0]["param_value"];
		}
		public function int_GetRoundValue(){
			$rows = $this->db->getRows(
				"config", 
				array (
					array("param_name", "=", "round_value")
				)
			);
			return $rows[0]["param_value"];
		}
		public function int_GetСommissionPercent(){
			$rows = $this->db->getRows(
				"config", 
				array (
					array("param_name", "=", "commission")
				)
			);
			return $rows[0]["param_value"];
		}
		/*
		 *		/Сайт
		 */
		
		/*
		 *		Кошельки
		 */
		public function void_addWallet($uid, $psid, $wallet) {
			$this->db->addRow(
				"wallets", 
				array (
					array("user_id", "=", $uid),
					array("payment_system_id", "=", $psid),
					array("wallet", "=", $wallet)
				)
			);
		}
		public function arr_GetWalletsByUID($uid){
			$rows = $this->db->getRows(
				"wallets", 
				array (
					array("user_id", "=", $uid)
				)
			);
			return $rows;
		}
		public function arr_GetWallet($wid){
			$rows = $this->db->getRows(
				"wallets", 
				array (
					array("id", "=", $wid)
				)
			);
			return $rows[0];
		}
		public function void_deleteWallet($wid) {
			$this->db->deleteRow(
				"wallets", 
				array (
					array("id", "=", $wid)
				)
			);
		}
		public function void_editWallet($wid, $wallet) {
			$this->db->updateRow(
				"wallets",
				array (
					array("id", "=", $wid)
				),
				array (
					array("wallet", "=", $wallet)
				)
			);
		}
		/*
		 *		/Кошельки
		 */
		 
		/*
		 *		Пополнения
		 */
		public function void_addRefill($uid, $psid, $transaction_id) {
			$this->db->addRow(
				"refills", 
				array (
					array("user_id", "=", $uid),
					array("payment_system_id", "=", $psid),
					array("transaction_id", "=", $transaction_id)
				)
			);
		}
		
		public function int_checkTransId($psid, $transaction_id) {
			if(
				$this->db->checkRow(
					"refills", 
					array (
						array("payment_system_id", "=", $psid),
						array("transaction_id", "=", $transaction_id)
					)
				)
			) return 9;
			else return 0;
		}
		
		public function void_setRefillAmount($id, $amount) {
			$this->db->updateRow(
				"refills", 
				array (
					array("id", "=", $id)
				),
				array (
					array("amount", "=", $amount)
				)
			);
		}
		
		public function void_setRefillStatus($id, $status) {
			$this->db->updateRow(
				"refills", 
				array (
					array("id", "=", $id)
				),
				array (
					array("status", "=", $status)
				)
			);
		}
		
		public function CountSumOfRefills() {
			return $this->db->getFieldSum("refills", "amount");
		}
		
		public function arr_GetRefillsByUID($uid){
			$rows = $this->db->getRows(
				"refills", 
				array (
					array("user_id", "=", $uid)
				)
			);
			return $rows;
		}
		
		public function GetLastRefills($number) {
			return $this->db->getListRows(
				"refills",
				false,
				"id", 
				array(
					array("status", "=", 1)
				),
				true,
				0,
				$number
			);
		}
		
		public function GetAwaitingRefills() {
			return $this->db->getListRows(
				"refills",
				true,
				"id", 
				array(
					array("status", "=", 0)
				),
				false
			);
		}
		
		public function arr_ViewRefill($id){
			$this->db->updateRow(
				"refills",
				array (
					array("id", "=", $id)
				),
				array (
					array("viewed", "=", 1)
				)
			);
		}
		/*
		 *		/Пополнения
		 */
		 
		/*
		 *		Балансы
		 */
		public function arr_GetBalanceByUID($uid){
			$rows = $this->db->getRows(
				"balances", 
				array (
					array("user_id", "=", $uid)
				)
			);
			return $rows[0];
		}
		public function void_addBalance($uid) {
			$this->db->addRow(
				"balances",
				array (
					array("user_id", "=", $uid)
				)
			);
		}
		public function void_AddToBalance($uid, $sum) {
			$balance = self::arr_GetBalanceByUID($uid);
			
			$this->db->updateRow(
				"balances",
				array (
					array("user_id", "=", $uid)
				),
				array (
					array("sum", "=", $balance["sum"] + $sum)
				)
			);
		}
		public function void_TakeFromBalance($uid, $sum) {
			$balance = self::arr_GetBalanceByUID($uid);
			
			$this->db->updateRow(
				"balances",
				array (
					array("user_id", "=", $uid)
				),
				array (
					array("sum", "=", $balance["sum"] - $sum)
				)
			);
		}
		/*
		 *		/Балансы
		 */
		
		/*
		 *		Планы
		 */
		public function s_GetPlanNameByID($id){
			$rows = $this->db->getRows(
				"plans", 
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0]["name"];
		}
		
		public function arr_GetPlanByID($id){
			$rows = $this->db->getRows(
				"plans", 
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0];
		}
		
		public function arr_GetPlans(){
			return $this->db->getListRows("plans");
		}
		/*
		 *		/Планы
		 */
		
		/*
		 *		Платежные системы
		 */
		public function s_GetPaymentSystemNameByID($id){
			$rows = $this->db->getRows(
				"payment_systems", 
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0]["name"];
		}
		public function arr_GetPaymentSystemsByCurrency($currency){
			$rows = $this->db->getRows(
				"payment_systems", 
				array (
					array("currency", "=", $currency)
				)
			);
			return $rows;
		}
		public function arr_GetPaymentSystems(){
			$rows = $this->db->getListRows(
				"payment_systems"
			);
			return $rows;
		}
		/*
		 *		/Платежные системы
		 */
		
		/*
		 *		Депозиты
		 */
		public function arr_GetDepositsByUID($uid){
			$rows = $this->db->getRows(
				"deposits", 
				array (
					array("user_id", "=", $uid)
				)
			);
			return $rows;
		}
		public function void_CreateDeposit($uid, $pid, $amount){
			$balance = self::arr_GetBalanceByUID($uid);
			
			$plan = self::arr_GetPlanByID($pid);
			
			if ($amount < $plan["min"]) return 7;
			if ($amount > $plan["max"]) return 8;
			if ($balance["sum"] < $amount) return 6;
			else self::void_TakeFromBalance($uid, $amount);
			
			$return_amount = $amount + ($amount * ($plan["percent"] / 100));
			$start_date = time();
			$end_date = $start_date + ($plan["delay_between_payments"] * $plan["payments"]);
			$full_payments = $plan["payments"];
			$next_payment = time() + $plan["delay_between_payments"];
			
			$this->db->addRow(
				"deposits", 
				array (
					array("user_id", "=", $uid),
					array("plan_id", "=", $pid),
					array("amount", "=", $amount),
					// paid_out = 0
					array("return_amount", "=", $return_amount),
					array("start_date", "=", $start_date),
					array("end_date", "=", $end_date),
					array("full_payments", "=", $full_payments),
					// payments = 0
					array("next_payment", "=", $next_payment)
				)
			);
			return 0;
		}
		public function arr_GetActiveDeposits() {
			return $this->db->getRows(
				"deposits", 
				array (
					array("next_payment", "<=", time()),
					array("status", "=", 0)
				)
			);
		}
		public function void_SetStatusOfDeposit($did, $status) {
			return $this->db->updateRow(
				"deposits", 
				array (
					array("id", "=", $did)
				),
				array (
					array("status", "=", $status)
				)
			);
		}
		public function void_SetNextPaymentOfDeposit($did, $nextPayment) {
			return $this->db->updateRow(
				"deposits", 
				array (
					array("id", "=", $did)
				),
				array (
					array("next_payment", "=", $nextPayment)
				)
			);
		}
		public function void_SetPaymentsOfDeposit($did, $payments) {
			return $this->db->updateRow(
				"deposits", 
				array (
					array("id", "=", $did)
				),
				array (
					array("payments", "=", $payments)
				)
			);
		}
		public function void_SetPaidOutOfDeposit($did, $paidOut) {
			return $this->db->updateRow(
				"deposits", 
				array (
					array("id", "=", $did)
				),
				array (
					array("paid_out", "=", $paidOut)
				)
			);
		}
		public function void_UpdateAllDeposits() {
			$deposits = self::arr_GetActiveDeposits();
			
			foreach($deposits as $key => $deposit) {
				$plan = self::arr_GetPlanByID($deposit['plan_id']);
				
				$sum = 0;
				
				if (($deposit['payments']+1) >= $deposit['full_payments']){
					$sum = round($deposit['return_amount'] - $deposit['paid_out'], self::int_GetRoundValue());
					self::void_SetStatusOfDeposit($deposit['id'], 1);
				}
				else {
					$sum = round($deposit['return_amount'] / $deposit['full_payments'], self::int_GetRoundValue());
					$nextPayment = $deposit['next_payment'] + $plan["delay_between_payments"];
					self::void_SetNextPaymentOfDeposit($deposit['id'], $nextPayment);
				}
				
				self::void_SetPaidOutOfDeposit($deposit['id'], $deposit['paid_out'] + $sum);
				self::void_SetPaymentsOfDeposit($deposit['id'], $deposit['payments'] + 1);
				self::void_AddToBalance($deposit['user_id'], $sum);
				
				echo "";
			}
		}
		/*
		 *		/Депозиты
		 */
		
		/*
		 *		Валюты
		 */
		public function arr_GetCurrencies(){
			return $this->db->getListRows("currencies");
		}
		public function arr_GetCurrencyById($id){
			$rows = $this->db->getRows(
				"currencies", 
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0];
		}
		public function s_GetCurrencyNameById($id){
			$rows = $this->db->getRows(
				"currencies", 
				array (
					array("id", "=", $id)
				)
			);
			return $rows[0]["name"];
		}
		public function int_GetMainCurrencyId(){
			$rows = $this->db->getRows(
				"config", 
				array (
					array("param_name", "=", "main_currency_id")
				)
			);
			return $rows[0]["param_value"];
		}
		/*
		 *		/Валюты
		 */
		
		/*
		 *		Партнерка
		 */
		public function arr_GetRefLevels(){
			return $this->db->getListRows("ref_levels");
		}
		public function int_GetMaxRefLevel(){
			return $this->db->getMaxValue(
				"ref_levels",
				"level", 
				array (
					array("id", ">", 0)
				)
			);
		}
		public function arr_FindOneLevelRefs($uid) {
			return $this->db->getRows(
				"accounts", 
				array (
					array("refer_id", "=", $uid)
				)
			);
		}
		public function _changeLevels($refs) {
			$levels = self::arr_GetRefLevels();
			
			$levels = array_reverse($levels);
			array_unshift($levels, array());
			
			foreach($refs as $k => $ref) {
				$refs[$k]["level"] = $levels[$refs[$k]["level"]]["level"];
			}
			
			return $refs;
		}
		public function FindRefsRecursive($refs, $level){
			if ($level == 0) return array();
			
			$new_refs = array();
			
			foreach($refs as $ref) {
				$new_refs = array_merge($new_refs, self::arr_FindOneLevelRefs($ref["id"]));
			}
			
			foreach($refs as $k => $v) {
				$refs[$k]["level"] = $level;
			}
			
			return array_merge($refs, self::FindRefsRecursive($new_refs, $level-1));
		}
		/*
		 *		/Партнерка
		 */
		
		/*
		 *		Выплаты
		 */
		public function arr_GetWithdrawalsByUID($uid){
			$rows = $this->db->getRows(
				"withdrawals", 
				array (
					array("user_id", "=", $uid)
				)
			);
			return $rows;
		}
		
		public function CountSumOfWithdrawals() {
			return $this->db->getFieldSum("withdrawals", "amount");
		}
		
		public function GetLastWithdrawals($number) {
			return $this->db->getListRows(
				"withdrawals",
				false,
				"id", 
				false,
				true,
				0,
				$number
			);
		}
		
		public function int_addWithdrawal($uid, $wid, $amount){
			$balance = self::arr_GetBalanceByUID($uid);
			if ($balance["sum"] < $amount) return 6;
			
			$this->db->addRow(
				"withdrawals", 
				array (
					array("user_id", "=", $uid),
					array("wallet_id", "=", $wid),
					array("date", "=", time()),
					array("amount", "=", $amount)
				)
			);
			
			return 0;
		}
		/*
		 *		/Выплаты
		 */
		
		/*
		 *		Тикеты
		 */
		public function arr_GetTicketsByUID($uid){
			$rows = $this->db->getRows(
				"tickets", 
				array (
					array("user_id", "=", $uid)
				)
			);
			return $rows;
		}
		public function arr_GetTicketByTID($ticketId){
			$rows = $this->db->getRows(
				"tickets", 
				array (
					array("id", "=", $ticketId)
				)
			);
			return $rows[0];
		}
		public function SetTicketStatus($tid) {
			$this->db->updateRow(
				"tickets",
				array (
					array("id", "=", $tid)
				),
				array (
					array("status", "=", 0)
				)
			);
		}
		public function arr_GetTicketMsgsByTID($ticketId){
			$rows = $this->db->getListRows(
				"ticket_messages", 
				false,
				"date",
				array (
					array("ticket_id", "=", $ticketId)
				)
			);
			return $rows;
		}
		public function int_addTicket($uid, $title, $email = null) {
			return $this->db->addRow(
				"tickets",
				array (
					array("user_id", "=", $uid),
					array("email", "=", $email),
					array("title", "=", $title),
					array("date", "=", time())
				)
			);
		}
		public function void_addTicketMessage($owner, $tid, $txt) {
			$this->db->addRow(
				"ticket_messages",
				array (
					array("owner", "=", $owner),
					array("ticket_id", "=", $tid),
					array("text", "=", $txt),
					array("date", "=", time())
				)
			);
		}
		/*
		 *		/Тикеты
		 */
	}
?>