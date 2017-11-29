<?php

class FrotelPaymentReturnBankModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	private function checkCurrency()
	{
		// Get cart currency and enabled currencies for this module
		$currency_order = new Currency($this->context->cart->id_currency);
		$currencies_module = $this->module->getCurrency($this->context->cart->id_currency);

		// Check if cart currency is one of the enabled currencies
		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;

		// Return false otherwise
		return false;
	}

	public function initContent()
	{
		$newCookie = new Cookie('frotelcod');
		
		/*if(intval($newCookie->__get("factor")) == 0)
			Tools::redirect('index.php');*/

		// Disable left and right column
		$this->display_column_left = false;
		$this->display_column_right = false;

		// Call parent init content method
		parent::initContent();
		
		/*$factor = Db::getInstance()->executeS('
		SELECT factor FROM `'._DB_PREFIX_.'frotelfactors`
		WHERE `id_cart` = '.(int)$newCookie->__get("orderfotel"));*/
		
		

		$fields = array(
			'api' => Configuration::get('API'),
			'paymentId' => $_GET["_i"],
			'ref' => $_GET["sb"],
			'factor' => $newCookie->__get("factor")
			);
		$checkPay = $this->frotelcheckpayfrotel($fields);
		$chp = json_decode($checkPay);

	
		//$newCookie->__unset('banksfrotel');
		if($chp->code == 0){
			//$newCookie->__unset("factor");
			if($chp->verify == 0)
			{
					$this->context->smarty->assign(array(
						'message' => $chp->result->message,
						'code' => 1
					));
			}else{
					$order = new Order($newCookie->__get("orderF"));
					$order->setCurrentState(Configuration::get("PS_PAY_FROTEL_PAYMENT"));
					$this->context->smarty->assign(array(
						'resultcode' => $chp->result->code,
						'resultmessage' => $chp->result->message,
						'code' => $chp->code
					));
			}
		} else
		{
			$this->context->smarty->assign(array(
				'message' => $chp->message,
				'code' => $chp->code
			));
		}
	
		$this->setTemplate('returnbank.tpl');
	}

	public function frotelcheckpayfrotel($fields)
	{
		$webservice = Configuration::get('WSF');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."payment/checkPay.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}

}
