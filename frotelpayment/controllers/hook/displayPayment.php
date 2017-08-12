<?php

class FrotelPaymentDisplayPaymentController
{
	public function __construct($module, $file, $path)
	{
		$this->file = $file;
		$this->module = $module;
		$this->context = Context::getContext();
		$this->_path = $path;
	}

	public function run($params)
	{
		$this->context->controller->addCSS($this->_path.'views/css/frotelpayment.css', 'all');
		
		foreach ($_POST["delivery_option"] as $key => $value) {
			$mod_carrier = intval($value);
		}

		if(Configuration::get('ID_PISHTAZ_COD') == $mod_carrier) $mod = 1;
		if(Configuration::get('ID_SEFARESHI_COD') == $mod_carrier) $mod = 2;
		if(Configuration::get('ID_SEFARESHI_ONLINE') == $mod_carrier) $mod = 3;
		if(Configuration::get('ID_PISHTAZ_ONLINE') == $mod_carrier) $mod = 4;


		$this->context->smarty->assign('carriermod',$mod);
		
		return $this->module->display($this->file, 'displayPayment.tpl');
	}
}
