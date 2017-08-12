<?php

class FrotelPaymentBankModuleFrontController extends ModuleFrontController
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

		if(intval($newCookie->__get("factor")) == 0)
			Tools::redirect('index.php');

		// Disable left and right column
		$this->display_column_left = false;
		$this->display_column_right = false;

		// Call parent init content method
		parent::initContent();

		if(isset($_GET["status"]) && $_GET["status"] == "continuepay")
		{
				$fields = array(
				'api' => Configuration::get('API'),
				'factor' => $newCookie->__get("factor"),
				'bankId' => $_GET["id"],
				'callback' => $this->context->link->getModuleLink('frotelpayment', 'returnbank')
				);
				$formbank = $this->frotelcountinupayfrotel($fields);
				$fm = json_decode($formbank);

				if($fm->code == 0)
				{
					$this->context->smarty->assign(array(
					'form' => $fm->result,
					));
				}else
				{
					Tools::redirect($this->context->link->getModuleLink('frotelpayment', 'banks')."?status=orderpay");
				}

		}else
		{
			$fields = array(
				'api' => Configuration::get('API'),
				'factor' => $newCookie->__get("factor"),
				'bank' => intval($_GET["id"]),
				'callback' => $this->context->link->getModuleLink('frotelpayment', 'returnbank') ,
				);
			$formbank = $this->frotelpayfrotel($fields);
			$fm = json_decode($formbank);

			if($fm->code == 0)
			{
				$this->context->smarty->assign(array(
				'form' => $fm->result,
				));
			}else
			{
				Tools::redirect($this->context->link->getModuleLink('frotel', 'banks'));
			}
		}

		$this->setTemplate('bank.tpl');
	}

	public function frotelpayfrotel($fields)
	{
		$webservice = Configuration::get('WSF');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."payment/pay.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}

	public function frotelcountinupayfrotel($fields)
	{
		
		$webservice = Configuration::get('WSF');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."payment/continuePay.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}


}
