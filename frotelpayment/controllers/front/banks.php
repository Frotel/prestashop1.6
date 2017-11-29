<?php

class FrotelPaymentBanksModuleFrontController extends ModuleFrontController
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

		if(intval($newCookie->__get("orderF")) == 0)
			Tools::redirect('index.php');

		// Disable left and right column
		$this->display_column_left = false;
		$this->display_column_right = false;

		// Call parent init content method
		parent::initContent();

		if(isset($_GET["status"]) && $_GET["status"] == "orderpay")
		{
			$fields = array(
			'api' => Configuration::get('API'),
			'factor' => $newCookie->__get("factor"),
			);

			$banks = $this->frotelorderpayfrotel($fields);
			$listbanks = json_decode($banks,true);

			if($listbanks["code"] == 0)
			{
				$this->context->smarty->assign(array(
					'banksfrotel' => $listbanks["result"]["banks"],
					'continuepay' => "continuepay",
					'api' => Configuration::get('API'),
					'factor' => $newCookie->__get("factor"),
					//'bankId' => $_GET["id"],
					'callback' => $this->context->link->getModuleLink('frotelpayment', 'returnbank'),
					'ajaxurl' => _MODULE_DIR_ . $this->name . 'frotelpayment/views/ajax/ajaxpaymentfrotel.php?secure=' . Tools::encrypt($this->name),
					'webservice' => Configuration::get('WSF'),
					'imgdir' => _MODULE_DIR_.$this->name.'frotelpayment/views/img/loading.gif',
				));
			}

		}else{
			
			$factor = Db::getInstance()->executeS('
			SELECT factor FROM `'._DB_PREFIX_.'frotelfactors`
			WHERE `id_cart` = '.(int)$newCookie->__get("orderF"));
			
			$newCookie->__set("factor",$factor[0]["factor"]);
			// Assign data to Smarty
			$this->context->smarty->assign(array(
				'banksfrotel' => unserialize($newCookie->__get('banksfrotel')),
				'continuepay' => "pay",
				'api' => Configuration::get('API'),
				'factor' => $newCookie->__get("factor"),
				//'bankId' => $_GET["id"],
				'callback' => $this->context->link->getModuleLink('frotelpayment', 'returnbank'),
				'ajaxurl' => _MODULE_DIR_ . $this->name . 'frotelpayment/views/ajax/ajaxpaymentfrotel.php?secure=' . Tools::encrypt($this->name),
				'webservice' => Configuration::get('WSF'),
				'imgdir' => _MODULE_DIR_.$this->name.'frotelpayment/views/img/loading.gif',
			));

		}

		// Set template
		$this->setTemplate('banks.tpl');
	}

	public function frotelorderpayfrotel($fields)
	{
		$webservice = Configuration::get('WSF');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."order/orderpay.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}


}
