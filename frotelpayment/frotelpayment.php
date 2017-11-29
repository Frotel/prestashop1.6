<?php

class FrotelPayment extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'frotelpayment';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
		$this->author = 'peyman sheybani';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Frotel payment');
		$this->description = $this->l('frotel payment module');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('displayPayment') || !$this->registerHook('displayPaymentReturn'))
			return false;

		if (!$this->installOrderState())
			return false;

		return true;
	}

	public function uninstall()
	{

		Configuration::deleteByName('PS_OS_FROTEL_PAYMENT');
		Configuration::deleteByName('PS_PAY_FROTEL_PAYMENT');
		Configuration::deleteByName('PS_ERR_FROTEL_PAYMENT');


		if (!parent::uninstall())
			return false;
		
		return true;
	}

	public function installOrderState()
	{
		if (Configuration::get('PS_OS_FROTEL_PAYMENT') < 1)
		{
			$order_state = new OrderState();
			$order_state->send_email = true;
			$order_state->module_name = $this->name;
			$order_state->invoice = false;
			$order_state->color = '#98c3ff';
			$order_state->logable = true;
			$order_state->shipped = false;
			$order_state->unremovable = false;
			$order_state->delivery = false;
			$order_state->hidden = false;
			$order_state->paid = false;
			$order_state->deleted = false;
			$order_state->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('پرداخت با فروتل - در انتظار پرداخت')));
			$order_state->template = array();
			foreach (LanguageCore::getLanguages() as $l)
				$order_state->template[$l['id_lang']] = 'mymodpayment';

			if ($order_state->add())
			{
				// We save the order State ID in Configuration database
				Configuration::updateValue('PS_OS_FROTEL_PAYMENT', $order_state->id);

			}
			else
				return false;
		}

		if (Configuration::get('PS_PAY_FROTEL_PAYMENT') < 1)
		{
			$order_state = new OrderState();
			$order_state->send_email = true;
			$order_state->module_name = $this->name;
			$order_state->invoice = false;
			$order_state->color = '#c3ff98';
			$order_state->logable = true;
			$order_state->shipped = false;
			$order_state->unremovable = false;
			$order_state->delivery = false;
			$order_state->hidden = false;
			$order_state->paid = false;
			$order_state->deleted = false;
			$order_state->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('پرداخت با فروتل - پرداخت شد')));
			$order_state->template = array();
			foreach (LanguageCore::getLanguages() as $l)
				$order_state->template[$l['id_lang']] = 'mymodpayment';

			if ($order_state->add())
			{
				// We save the order State ID in Configuration database
				Configuration::updateValue('PS_PAY_FROTEL_PAYMENT', $order_state->id);

			}
			else
				return false;
		}

		if (Configuration::get('PS_ERR_FROTEL_PAYMENT') < 1)
		{
			$order_state = new OrderState();
			$order_state->send_email = true;
			$order_state->module_name = $this->name;
			$order_state->invoice = false;
			$order_state->color = '#ff1111';
			$order_state->logable = true;
			$order_state->shipped = false;
			$order_state->unremovable = false;
			$order_state->delivery = false;
			$order_state->hidden = false;
			$order_state->paid = false;
			$order_state->deleted = false;
			$order_state->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('پرداخت با فروتل - سفارش در فروتل ثبت نشد')));
			$order_state->template = array();
			foreach (LanguageCore::getLanguages() as $l)
				$order_state->template[$l['id_lang']] = 'mymodpayment';

			if ($order_state->add())
			{
				// We save the order State ID in Configuration database
				Configuration::updateValue('PS_ERR_FROTEL_PAYMENT', $order_state->id);

			}
			else
				return false;
		}

		return true;
	}


	public function getHookController($hook_name)
	{
		// Include the controller file
		require_once(dirname(__FILE__).'/controllers/hook/'. $hook_name.'.php');

		// Build dynamically the controller name
		$controller_name = $this->name.$hook_name.'Controller';

		// Instantiate controller
		$controller = new $controller_name($this, __FILE__, $this->_path);

		// Return the controller
		return $controller;
	}

	public function hookDisplayPayment($params)
	{
		$newCookie = new Cookie('frotelcod');
		
		$controller = $this->getHookController('displayPayment');
		return $controller->run($params);
	}

	public function hookDisplayPaymentReturn($params)
	{
		$controller = $this->getHookController('displayPaymentReturn');
		return $controller->run($params);
	}


}

