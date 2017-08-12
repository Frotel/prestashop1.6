<?php   

if (!defined('_PS_VERSION_')) {
  exit;
}

class Frotel extends Module
{
	public function __construct()
   	{
	    $this->name = 'frotel';
	    $this->tab = 'administration';
	    $this->version = '1.0.0';
	    $this->author = 'peyman sheybani';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
	 
	    parent::__construct();
	 
	    $this->displayName = $this->l('frotel');
	    $this->description = $this->l('frotel module.');
	 	
	    $this->confirmUninstall = $this->l('آیا شما می خواهید این ماژول را پاک کنید؟');
	 
  	}

  	public function install()
	{
	  if (Shop::isFeatureActive()) {
	    Shop::setContext(Shop::CONTEXT_ALL);
	  }

		//-------------create carrier array------------------------
		$carrierConfig = array(0 => array(
                'name' => $this->l('ارسال به صورت پیشتاز و پرداخت در محل'),
                'id_tax_rules_group' => 0,
                'active' => true,
                'deleted' => 0,
                'shipping_handling' => false,
                'range_behavior' => 0,
                'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::
                        get('PS_LANG_DEFAULT')) => $this->l('ارسال به صورت پیشتاز و پرداخت در محل انجام شود')),
                'id_zone' => 3,
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => $this->name,
                'need_range' => true), 1 => array(
                'name' => $this->l('ارسال به صورت سفارشی و پرداخت در محل'),
                'id_tax_rules_group' => 0,
                'active' => true,
                'deleted' => 0,
                'shipping_handling' => false,
                'range_behavior' => 0,
                'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::
                        get('PS_LANG_DEFAULT')) => $this->l('ارسال به صورت سفارشی و پرداخت در محل انجام شود')),
                'id_zone' => 3,
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => $this->name,
                'need_range' => true),2 => array(
                'name' => $this->l('ارسال به صورت سفارشی و پرداخت آنلاین'),
                'id_tax_rules_group' => 0,
                'active' => true,
                'deleted' => 0,
                'shipping_handling' => false,
                'range_behavior' => 0,
                'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::
                        get('PS_LANG_DEFAULT')) => $this->l('ارسال به صورت سفارشی و پرداخت آنلاین انجام شود')),
                'id_zone' => 3,
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => $this->name,
                'need_range' => true),3 => array(
                'name' => $this->l('ارسال به صورت پیشتاز و پرداخت آنلاین'),
                'id_tax_rules_group' => 0,
                'active' => true,
                'deleted' => 0,
                'shipping_handling' => false,
                'range_behavior' => 0,
                'delay' => array('en' => 'Description 1', Language::getIsoById(Configuration::
                        get('PS_LANG_DEFAULT')) => $this->l('ارسال به صورت پیشتاز و پرداخت آنلاین انجام شود')),
                'id_zone' => 3,
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => $this->name,
                'need_range' => true), 
				);

		$id_pishtaz_cod = $this->installExternalCarrier($carrierConfig[0]);
		$id_sefareshi_cod = $this->installExternalCarrier($carrierConfig[1]);
		$id_sefareshi_online = $this->installExternalCarrier($carrierConfig[2]);
		$id_pishtaz_online = $this->installExternalCarrier($carrierConfig[3]);

		Configuration::updateValue('ID_PISHTAZ_COD', $id_pishtaz_cod);
		Configuration::updateValue('ID_SEFARESHI_COD', $id_sefareshi_cod);
		Configuration::updateValue('ID_SEFARESHI_ONLINE', $id_sefareshi_online);
		Configuration::updateValue('ID_PISHTAZ_ONLINE', $id_pishtaz_online);
		//-------------end  carrier array--------------------------
	  Configuration::updateValue('API', '');
	  Configuration::updateValue('WSF', '');
	  Configuration::updateValue('WEIGHTUNIT', '');
	  Configuration::updateValue('PAYMENT_FROTEL_COD', '');
	  Configuration::updateValue('PAYMENT_FROTEL_ONLINE', '');
	  Configuration::updateValue('SEFARESHI_FROTEL', '');
	  Configuration::updateValue('PISHTAZ_FROTEL', '');
      Configuration::updateValue('RahgiriOnTop', 0);
	  Configuration::updateValue('RahgiriOnFooter', 0);   

	require (_PS_MODULE_DIR_ . 'frotelpayment/frotelpayment.php');
	$payment = new FrotelPayment();
	  if (!parent::install() ||
	  	!$payment->install() ||
	    !$this->registerHook('displayShoppingCartFooter')||
		!$this->registerHook('displayBeforeCarrier') ||
		!$this->registerHook('adminOrder') ||
		!$this->registerHook('displayTop') ||
		!$this->registerHook('displayHeader') ||
		!$this->registerHook('displayFooter')
	  ) {
	    return false;
	  }

	  if(!$this->installFrotelDb())
	  		return false;
	 
	  return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('API');
		Configuration::deleteByName('WSF');
		Configuration::deleteByName('WEIGHTUNIT');
		Configuration::deleteByName('PAYMENT_FROTEL_COD');
		Configuration::deleteByName('PAYMENT_FROTEL_ONLINE');
		Configuration::deleteByName('SEFARESHI_FROTEL');
		Configuration::deleteByName('PISHTAZ_FROTEL');
		
		if(!$this->deletefrotelCarriers()) return false;

		Configuration::deleteByName('ID_PISHTAZ_COD');
		Configuration::deleteByName('ID_SEFARESHI_COD');
		Configuration::deleteByName('ID_SEFARESHI_ONLINE');
		Configuration::deleteByName('ID_PISHTAZ_ONLINE');

		if(!$this->uninstallFrotelDb()) return false;

		require (_PS_MODULE_DIR_ . 'mymodpayment/mymodpayment.php');
		$payment = new MyModPayment();

		if (!parent::uninstall() || $payment->uninstall()) 	return false;

		return true;
	}

	protected function deletefrotelCarriers()
    {
        
        $carrierfroteldel = array(
                Configuration::get('ID_PISHTAZ_COD'),
                Configuration::get('ID_SEFARESHI_COD'),
                Configuration::get('ID_SEFARESHI_ONLINE'),
                Configuration::get('ID_PISHTAZ_ONLINE')
            );
        foreach ($carrierfroteldel as $key) {
            $carrier = new Carrier($key);
            $carrier->delete();
        }
    
        return TRUE;
    }

	public function installFrotelDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'frotelfactors`(  
            `id_factor` INT(10) NOT NULL AUTO_INCREMENT,
            `id_cart` INT(10) NOT NULL,
            `factor` VARCHAR(30),
			`id_city` INT(10) NOT NULL,
			`id_state` INT(10) NOT NULL,
			`buy_type` INT(10) NOT NULL,
			`buy_send` INT(10) NOT NULL,
            PRIMARY KEY (`id_factor`)
            )ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        if (!Db::getInstance()->execute($sql))
            return false;

        return true;

    }

	public function uninstallFrotelDb()
	{
		$sql = 'DROP TABLE '._DB_PREFIX_.'frotelfactors';
		if (!Db::getInstance()->execute($sql))
            return false;

		return true;
	}

	public function getContent()
    {
        $output = '';
       
		if (Tools::getValue('submit'.$this->name))
        {
			$api = Tools::getValue('api_key');
			$address = Tools::getValue('webservice_address');
			$weightunit = Tools::getValue('weightunit');
			$CodPayment = Tools::getValue('paymentcod');
			$OnlinePayment = Tools::getValue('paymentonline');
			$SefareshiFrotel = Tools::getValue('sefareshifrotel');
			$PishtazFrotel = Tools::getValue('pishtazfrotel');
			$RahgiriOnTop = Tools::getValue('RahgiriOnTop');
			$RahgiriOnFooter = Tools::getValue('RahgiriOnFooter');

			Configuration::updateValue('API', $api);
			Configuration::updateValue('WSF', $address);
			Configuration::updateValue('WEIGHTUNIT', $weightunit);
			Configuration::updateValue('PAYMENT_FROTEL_COD', $CodPayment);
			Configuration::updateValue('PAYMENT_FROTEL_ONLINE', $OnlinePayment);
			Configuration::updateValue('SEFARESHI_FROTEL', $SefareshiFrotel);
			Configuration::updateValue('PISHTAZ_FROTEL', $PishtazFrotel);
			Configuration::updateValue('RahgiriOnTop', $RahgiriOnTop);
			Configuration::updateValue('RahgiriOnFooter', $RahgiriOnFooter);

			if($CodPayment){

				if($PishtazFrotel){
					Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'carrier 
					SET active = 1 WHERE id_carrier = '.
					Configuration::get("ID_PISHTAZ_COD"));
				}

				if($SefareshiFrotel){
					Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'carrier 
					SET active = 1 WHERE id_carrier = '.
					Configuration::get("ID_SEFARESHI_COD"));
				}

			}else{
				Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'carrier 
				SET active = 0 WHERE id_carrier IN ('.
				Configuration::get("ID_PISHTAZ_COD").','.Configuration::get("ID_SEFARESHI_COD").')');
			}
			
			if ($OnlinePayment) {	
				
				if($PishtazFrotel){
					Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'carrier 
					SET active = 1 WHERE id_carrier = '.
					Configuration::get("ID_PISHTAZ_ONLINE"));
				}

				if($SefareshiFrotel){
					Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'carrier 
					SET active = 1 WHERE id_carrier = '.
					Configuration::get("ID_SEFARESHI_ONLINE"));
				}

			}else{
				Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'carrier 
				SET active = 0 WHERE id_carrier IN ('.
				Configuration::get("ID_PISHTAZ_ONLINE").','.Configuration::get("ID_SEFARESHI_ONLINE").')');
			}
			
			$output .= $this->displayConfirmation($this->l('تنظیمات شما ذخیره شد.'));
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
	        // Get default language
	        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

	        // Init Fields form array
	        $fields_form[0]['form'] = array(
	            'legend' => array(
	                'title' => $this->l('تنظیمات'),
	            ),
	            'input' => array(
	                array(
	                    'type' => 'text',
	                    'label' => $this->l('کلید API'),
	                    'name' => 'api_key',
	                    'size' => 80,
	                    //'required' => true,
	                    'desc' => $this->l('کلید دسترسی به سرویس ها'),
	                    'class' => 'pull-right'
	                ),
					array(
						'type' => 'text',
						'label' => $this->l('آدرس وب سرویس ها'),
						'name' => 'webservice_address',
						'size' => 2,
						//'required' => true,
						'desc' => $this->l('آدرس وب سرویس های فروتل')
					),
					array(
	                    'type' => 'text',
	                    'label' => $this->l('وزن پایه'),
	                    'name' => 'weightunit',
	                    'size' => 80,
	                    //'required' => true,
	                    'desc' => $this->l('حداقل وزن '),
	                    'class' => 'pull-right'
	                ),
					array(
						'type' => 'switch',
						'label' => $this->l('پرداخت در محل'),
						'name' => 'paymentcod',
						'is_bool' => true,
						'desc' => $this->l('پرداخت در محل'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('فعال')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('غیر فعال')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('پرداخت آنلاین'),
						'name' => 'paymentonline',
						'is_bool' => true,
						'desc' => $this->l('پرداخت آنلاین'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('فعال')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('غیر فعال')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('ارسال سفارشی'),
						'name' => 'sefareshifrotel',
						'is_bool' => true,
						'desc' => $this->l('ارسال به صورت پست سفارشی'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('فعال')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('غیر فعال')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('ارسال پیشتاز'),
						'name' => 'pishtazfrotel',
						'is_bool' => true,
						'desc' => $this->l('ارسال به صورت پست پیشتاز'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('فعال'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('غیر فعال'),
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('نمایش لینک رهگیری خرید در بالای صفحه'),
						'name' => 'RahgiriOnTop',
						'is_bool' => true,
						'desc' => $this->l('در صورتی که می خواهید دکمه رهگیری خرید در بالای صفحه قالب شما قرار گیرد این آیتم را فعال کنید'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('فعال')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('غیر فعال')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('نمایش لینک رهگیری خرید در فوتر صفحه'),
						'name' => 'RahgiriOnFooter',
						'is_bool' => true,
						'desc' => $this->l('در صورتی که می خواهید دکمه رهگیری خرید در فوتر صفحه قالب شما قرار گیرد این آیتم را فعال کنید'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('فعال')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('غیر فعال')
							)
						),
					),
					array(
						'type' => 'text',
						'label' => $this->l('آدرس صفحه رهگیری'),
						'name' => 'LinkRahgiriPage',
						'size' => 2,
						'readonly' => 'readonly' ,
						'desc' => $this->l('این آدرس صفحه رهگیری  می باشد در صورتی که می خواهید شما میتوانید آن را در قالب خود به صورت اختصاصی  قرار دهید '),
					),
                ),
	            'submit' => array(
	                'title' => $this->l('Save'),
	                'class' => 'button'
	            )
	        );

	        $helper = new HelperForm();
			$helper->fields_value = array(
				'LinkRahgiriPage' => $this->context->link->getModuleLink('frotel', 'rahgiri'),
			);
	        // Module, token and currentIndex
	        $helper->module = $this;
	        $helper->name_controller = $this->name;
	        $helper->token = Tools::getAdminTokenLite('AdminModules');
	        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	        // Language
	        $helper->default_form_language = $default_lang;
	        $helper->allow_employee_form_lang = $default_lang;
	        // Title and toolbar
	        $helper->title = $this->displayName;
	        $helper->show_toolbar = true;      // false -> remove toolbar
	        $helper->toolbar_scroll = true;     // yes - > Toolbar is always visible on the top of the screen.
	        $helper->submit_action = 'submit'.$this->name;
	        $helper->toolbar_btn = array(
	            'save' =>
	            array(
	                'desc' => $this->l('Save'),
	                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	                '&token='.Tools::getAdminTokenLite('AdminModules'),
	            ),
	            'back' => array(
	                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	                'desc' => $this->l('Back to list')
	            )
	        );

	        // Load current value
	        $helper->fields_value['api_key'] = Configuration::get('API');
	        $helper->fields_value['webservice_address'] = Configuration::get('WSF');
			$helper->fields_value['weightunit'] = Configuration::get('WEIGHTUNIT');
			$helper->fields_value['paymentcod'] = Configuration::get('PAYMENT_FROTEL_COD');
			$helper->fields_value['paymentonline'] = Configuration::get('PAYMENT_FROTEL_ONLINE');
			$helper->fields_value['sefareshifrotel'] = Configuration::get('SEFARESHI_FROTEL');
			$helper->fields_value['pishtazfrotel'] = Configuration::get('PISHTAZ_FROTEL');
			$helper->fields_value['RahgiriOnTop'] = Configuration::get('RahgiriOnTop');
			$helper->fields_value['RahgiriOnFooter'] = Configuration::get('RahgiriOnFooter');

	        return $helper->generateForm($fields_form);
    }


    public function hookDisplayShoppingCartFooter(){
    	$this->smarty->assign(array(
			'tpldir' => _PS_MODULE_DIR_ . $this->name . '/views/templates/front',
			'imgdir' => _MODULE_DIR_.$this->name.'/views/img/loading.gif',
			'ajaxurl' => _MODULE_DIR_ . $this->name . '/views/ajax/ajaxprocessFrotel.php?secure=' . Tools::encrypt($this->
                name),
            ));
        return $this->display(__file__,'views/templates/front/citystate.tpl');
    }

	public function hookDisplayBeforeCarrier()
	{
		$newCookie = new CookieCore('frotelcod');
        if(!$newCookie->__get('payment_method'))
            $method =0;
        else
           $method = $newCookie->__get('payment_method');

        $id_pishtaz_cod = Configuration::get('ID_PISHTAZ_COD');
        $id_sefareshi_cod = Configuration::get('ID_SEFARESHI_COD');
		$id_sefareshi_online = Configuration::get('ID_SEFARESHI_ONLINE');
		$id_pishtaz_online = Configuration::get('ID_PISHTAZ_ONLINE');
		
        $this->smarty->assign(array(
            'frotel_carrier_rul' => $method,
            'ajaxurl' => _MODULE_DIR_ . $this->name . '/views/ajax/ajaxprocessFrotel.php?action=getCarrierPriceFrotel',
            'module' => $this->name,
           	//'frotel_pcid'=>$id_pishtaz_cod,
           	//'frotel_scid'=>$id_sefareshi_cod,
			//'frotel_soid'=>$id_sefareshi_online,
			//'frotel_poid'=>$id_pishtaz_online,
            'frotel_online'=>$this->getCarriers($id_pishtaz_cod,$id_sefareshi_cod,$id_sefareshi_online,$id_pishtaz_online),
            ));
		return $this->display(__file__,'views/templates/front/carrierfrotel.tpl');
	}

	public function getCarriers($id_pishtaz_cod,$id_sefareshi_cod,$id_sefareshi_online,$id_pishtaz_online)
    {
        $ids = array();
        $query = new DbQuery();
        $query->select('id_carrier');
        $query->from('carrier');
        $query->where('active=1');
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query->build());
        if(!$results)
            return false;
        foreach($results as $result)
        {
            if($id_pishtaz_cod != $result['id_carrier'] and $id_sefareshi_cod != $result['id_carrier'] and
				$id_sefareshi_online != $result['id_carrier'] and $id_pishtaz_online != $result['id_carrier'])
            $ids[] = $result['id_carrier'];
        }
        return $ids;
    }

	public function getOrderShippingCost($params, $shipping_cost)
	{
		$FrotelCookie = new Cookie('FrotelService');
		$f_carrier = $FrotelCookie->__get('PriceFrotel');
		$f_carrier = (array) json_decode($f_carrier,true);
		
		if(!empty($f_carrier) ){
			if($f_carrier['code']!=0)
				{
					$f_carrier = $FrotelCookie->__unset('PriceFrotel');
					return;
				}
				else{	
						if(isset($f_carrier['result']['naghdi'][1]))
								$sefareshionline = array_sum ($f_carrier['result']['naghdi'][1]);
					
						if(isset($f_carrier['result']['naghdi'][2]))
									$pishtazonline = array_sum ($f_carrier['result']['naghdi'][2]);

						if(isset($f_carrier['result']['posti'][1]))
									$sefareshicod = array_sum ($f_carrier['result']['posti'][1]);

						
						if(isset($f_carrier['result']['posti'][2]))
									$pishtazcod = array_sum ($f_carrier['result']['posti'][2]);

				}
				
		}
		$newCookie = new Cookie('frotelcod');
        $carrier = false;
        
        if (Tools::getValue('step') == 2)
        {
           $id = $params->id_carrier;
		    
            if (Configuration::get('ID_PISHTAZ_COD') == $id)
                {$carrier = $pishtazcod;
                            	$newCookie->__set('carriermod', 1);
							}
            elseif (Configuration::get('ID_SEFARESHI_COD') == $id)
                 {$carrier = $sefareshicod;
                              	 $newCookie->__set('carriermod',2);
								  }
			elseif (Configuration::get('ID_SEFARESHI_ONLINE') == $id)
                 {$carrier = $sefareshionline;
                              	 $newCookie->__set('carriermod',3);
								  }
			elseif (Configuration::get('ID_PISHTAZ_ONLINE') == $id)
                 {$carrier = $pishtazonline;
                              	 $newCookie->__set('carriermod',4);
								  }
        }
        
        elseif (Tools::getValue('step') == 3)
        {
            
            $id = $params->id_carrier;
			
            if (Configuration::get('ID_PISHTAZ_COD') == $id)
                {$carrier = $pishtazcod;
                            	$newCookie->__set('carriermod', 1);
								}
            elseif (Configuration::get('ID_SEFARESHI_COD') == $id)
                 {$carrier = $sefareshicod;
                              	$newCookie->__set('carriermod',2);
								   }
			elseif (Configuration::get('ID_SEFARESHI_ONLINE') == $id)
                 {$carrier = $sefareshionline;
                              	$newCookie->__set('carriermod',3);
								   }
			elseif (Configuration::get('ID_PISHTAZ_ONLINE') == $id)
                 {$carrier = $pishtazonline;
                              	$newCookie->__set('carriermod',4);
								   }

            $newCookie->__set('frotelCarrier', $carrier);

        }
        $iso_rial = Currency::getIdByIsoCode('IRR');
        $rial = new Currency($iso_rial);
        $current_currency = new Currency($this->context->cookie->id_currency);
		
        if ($carrier !=0)
        {
            if ($current_currency->id == $rial->id)
                return $carrier;
            else
                return Tools::convertPriceFull($carrier, $rial, $current_currency);
        }

        elseif ($newCookie->__isset('frotelCarrier'))
        {

            if ($current_currency->id == $rial->id)
                return $newCookie->__get('frotelCarrier');
            else
                return Tools::convertPriceFull($newCookie->__get('frotelCarrier'), $rial,
                    $current_currency);
        }
        /*else
            return 0;*/

	}

	public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params,0);
    }

	public function installExternalCarrier($config)
    {
        $carrier = new Carrier();
        $carrier->name = $config['name'];
        $carrier->id_tax_rules_group = $config['id_tax_rules_group'];
        $carrier->id_zone = $config['id_zone'];
        $carrier->active = $config['active'];
        $carrier->deleted = $config['deleted'];
        $carrier->delay = $config['delay'];
        $carrier->shipping_handling = $config['shipping_handling'];
        $carrier->range_behavior = $config['range_behavior'];
        $carrier->is_module = $config['is_module'];
        $carrier->shipping_external = $config['shipping_external'];
        $carrier->external_module_name = $config['external_module_name'];
        $carrier->need_range = $config['need_range'];

        $languages = Language::getLanguages(true);
        foreach ($languages as $language)
        {
            if ($language['iso_code'] == 'en')
                $carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
            if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
                $carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
        }

        if ($carrier->add())
        {
            $groups = Group::getGroups(true);
            foreach ($groups as $group)
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_group', array('id_carrier' =>
                        (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');
            // price range
            $rangePrice = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '10000000';
            $rangePrice->add();
            // weight range
            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '100000';
            $rangeWeight->add();

            $zones = Zone::getZones(true);
            foreach ($zones as $zone)
            {
                
                
                    Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_zone', array('id_carrier' =>
                            (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
                    Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_ . 'delivery', array(
                        'id_carrier' => (int)($carrier->id),
                        'id_range_price' => (int)($rangePrice->id),
                        'id_range_weight' => null,
                        'id_zone' => (int)($zone['id_zone']),
                        'price' => '0'), 'INSERT');
                    Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_ . 'delivery', array(
                        'id_carrier' => (int)($carrier->id),
                        'id_range_price' => null,
                        'id_range_weight' => (int)($rangeWeight->id),
                        'id_zone' => (int)($zone['id_zone']),
                        'price' => '0'), 'INSERT');
               
            }

            #if (!copy(dirname(__FILE__).'/views/img/'.$carrier->name.'.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
            #return false;

            return (int)($carrier->id);
        }

        return false;
    }

	public function hookUpdateCarrier($params)
    {
        if ((int)($params['id_carrier']) == (int)(Configuration::get('ID_PISHTAZ_COD')))
            Configuration::updateValue('ID_PISHTAZ_COD', (int)($params['carrier']->id));

        if ((int)($params['id_carrier']) == (int)(Configuration::get('ID_SEFARESHI_COD')))
            Configuration::updateValue('ID_SEFARESHI_COD', (int)($params['carrier']->
                id));

		if ((int)($params['id_carrier']) == (int)(Configuration::get('ID_SEFARESHI_ONLINE')))
            Configuration::updateValue('ID_SEFARESHI_ONLINE', (int)($params['carrier']->
                id));
		
		if ((int)($params['id_carrier']) == (int)(Configuration::get('ID_PISHTAZ_ONLINE')))
            Configuration::updateValue('ID_PISHTAZ_ONLINE', (int)($params['carrier']->
                id));

    }

	public function getCarrierPriceFrotel()
	{
		//$newCookie = new Cookie('frotel');
        $context = Context::getContext();

		$FrotelCookie = new Cookie('FrotelService');
		$f_carrier = $FrotelCookie->__get('PriceFrotel');

		$f_carrier = (array) json_decode($f_carrier,true);
		
		if(!empty($f_carrier) ){
			if($f_carrier['code']!=0)
			{
				$f_carrier = $FrotelCookie->__unset('PriceFrotel');
				return;
			}
			else{

					if(isset($f_carrier['result']['naghdi'][1]))
								$sefareshionline = array_sum ($f_carrier['result']['naghdi'][1]);
					
					if(isset($f_carrier['result']['naghdi'][2]))
								$pishtazonline = array_sum ($f_carrier['result']['naghdi'][2]);

					if(isset($f_carrier['result']['posti'][1]))
								$sefareshicod = array_sum ($f_carrier['result']['posti'][1]);

					
					if(isset($f_carrier['result']['posti'][2]))
								$pishtazcod = array_sum ($f_carrier['result']['posti'][2]);

			}
		}

        $iso_rial = Currency::getIdByIsoCode('IRR');
        $rial = new Currency($iso_rial);

        $current_currency = new Currency($context->cookie->id_currency);
        if ($current_currency->id != $rial->id)
        {

			$po = Tools::convertPriceFull($pishtazonline,$rial,$current_currency);
			$so = Tools::convertPriceFull($sefareshionline,$rial,$current_currency);
			$pc = Tools::convertPriceFull($pishtazcod,$rial,$current_currency);
			$ps = Tools::convertPriceFull($sefareshicod,$rial,$current_currency);
            
            $msg = array(
                Configuration::get('ID_PISHTAZ_ONLINE') => Tools::displayPrice($po,$current_currency),
				Configuration::get('ID_SEFARESHI_ONLINE') => Tools::displayPrice($so,$current_currency),
				Configuration::get('ID_PISHTAZ_COD') => Tools::displayPrice($pc,$current_currency),
				Configuration::get('ID_SEFARESHI_COD') => Tools::displayPrice($ps,$current_currency),
                );
        }
        else
            $msg = array(
                Configuration::get('ID_PISHTAZ_ONLINE') => Tools::displayPrice($pishtazonline,$current_currency),
				Configuration::get('ID_SEFARESHI_ONLINE') => Tools::displayPrice($sefareshionline,$current_currency),
				Configuration::get('ID_PISHTAZ_COD') => Tools::displayPrice($pishtazcod,$current_currency),
				Configuration::get('ID_SEFARESHI_COD') => Tools::displayPrice($sefareshicod,$current_currency),
                );
        return Tools::jsonEncode($msg);
	}

	public function hookAdminOrder()
	{	
		$factor = Db::getInstance()->executeS('
        SELECT * FROM `'._DB_PREFIX_.'frotelfactors`
        WHERE `id_cart` = '.(int)$_GET["id_order"]);

        if(!is_null($factor[0]["factor"]) && $factor[0]["factor"] != "")
        {
            return;
        }else{
			$this->smarty->assign(array(
				'orderid' => $_GET["id_order"],
				'ajaxurl' => _MODULE_DIR_ . $this->name . '/views/ajax/ajaxprocessFrotel.php?action=sendfrotel',
			)
			);
			return $this->display(__file__ ,'views/templates/admin/adminfrotel.tpl');
		}
	}


	public function hookdisplayTop()
	{
		if(Configuration::get('RahgiriOnTop')==1)
			return $this->display(__file__ ,'views/templates/hook/rahgiri.tpl');
	}

	public function hookdisplayHeader()
	{
		
	}

	public function hookdisplayFooter()
	{
		if (Configuration::updateValue('RahgiriOnFooter', $RahgiriOnFooter) == 1)
				return $this->display(__file__ ,'views/templates/hook/rahgiri.tpl');
	}

}