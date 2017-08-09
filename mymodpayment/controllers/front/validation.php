<?php

class MyModPaymentValidationModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		// Check if cart exists and all fields are set
		$cart = $this->context->cart;
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');
		
		// Check if module is enabled
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == $this->module->name)
				$authorized = true;
		if (!$authorized)
			die('This payment method is not available.');

		// Check if customer exists
		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		// Set datas
		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		$extra_vars = array(
			'{total_to_pay}' => Tools::displayPrice($total),
			'{cheque_order}' => Configuration::get('MYMOD_CH_ORDER'),
			'{cheque_address}' => Configuration::get('MYMOD_CH_ADDRESS'),
			'{bankwire_details}' => Configuration::get('MYMOD_BA_DETAILS'),
			'{bankwire_owner}' => Configuration::get('MYMOD_BA_OWNER'),
		);
		
	    $FrotelCookie = new Cookie('FrotelService');
		$FrotelCookie->__get("city_frotel");
		$newCookie = new Cookie('frotelcod');
		$carrier = $newCookie->__get("carriermod");
		if($carrier == 1){ $buytype = 1; $sendtype = 1; }
		elseif($carrier == 2){$buytype = 1; $sendtype = 2; }
		elseif($carrier == 3){$buytype = 2; $sendtype = 2; }
		elseif($carrier == 4){$buytype = 2; $sendtype = 1; }
		$Address = new Address((int)$cart->id_address_delivery);
		$products = $cart->getProducts();
		
		$basket = array();
		foreach ($products as $key ) {
			if($key['weight'] <= 0 )
				$weight = Configuration::get('WEIGHTUNIT');
			else
				$weight = $key['weight'];
			
			$basket[] = array(
				'pro_code' => $key['id_product'],
				'name' => $key['name'],
				'price' => $key['price'],
				'count' => $key['quantity'],
				'weight' => $weight,
				'porsant' => 0,
				'bazayab' => 0,
				'discount' => 0,
				'free_send' => 0,
				'tax' => 0,
				'option' => array(),
			) ;
			
		}
		//die();
		$frotel_cart = array(
			'api' => Configuration::get('API'),
			'name' => $customer->firstname,
			'family' => $customer->lastname,
			'phone' => $Address->phone,
			'mobile' => $Address->phone_mobile,
			'gender' => $customer->id_gender,
			'email' => $customer->email,
			'address' => $Address->address1,
			'code_posti' => $Address->postcode,
			'province' => $FrotelCookie->__get("state_frotel"),
			'city' => $FrotelCookie->__get("city_frotel"),
			'buy_type' => $buytype,
			'send_type' => $sendtype,
			'ip' => $_SERVER['REMOTE_ADDR'],
			//'pm' => ,
			'basket' =>  $basket,//$bascket,
			'free_send' => 0,
		);

		
		$order_frotel= $this->frotelsaveorder($frotel_cart);
		$ar = json_decode($order_frotel, JSON_UNESCAPED_UNICODE);
		
		if($ar["code"] == 0)
		{	

			$this->module->validateOrder($cart->id, Configuration::get('PS_OS_MYMOD_PAYMENT'), $total,
				$this->module->displayName, NULL, $extra_vars, (int)$currency->id, false, $customer->secure_key);

			$newCookie->__set("orderF",$this->module->currentOrder);

			$insert = array(
				'id_cart' => $this->module->currentOrder,
				'factor' => $ar["result"]["factor"]["id"],
				'id_city' => $FrotelCookie->__get("city_frotel"),
				'id_state' => $FrotelCookie->__get("state_frotel"),
				'buy_type' => $buytype,
				'buy_send' => $sendtype,
			);
			Db::getInstance()->insert('frotelfactors', $insert);

			$FrotelCookie->__unset("PriceFrotel");
			$FrotelCookie->__unset("city_frotel");
			$FrotelCookie->__unset("state_frotel");
			$newCookie->__unset("carriermod");
			$newCookie->__unset("frotelCarrier");

		}elseif (isset($ar["code"]) && $ar["code"] != 0) {
			$newCookie->__set("error_message",$ar["message"]);			
			Tools::redirect($this->context->link->getModuleLink('mymodpayment', 'payment'));
		}else
		{
			$this->module->validateOrder($cart->id, Configuration::get('PS_ERR_MYMOD_PAYMENT'), $total,
				$this->module->displayName, NULL, $extra_vars, (int)$currency->id, false, $customer->secure_key);

			$insert = array(
				'id_cart' => $this->module->currentOrder,
				'factor' => "",
				'id_city' => $FrotelCookie->__get("city_frotel"),
				'id_state' => $FrotelCookie->__get("state_frotel"),
				'buy_type' => $buytype,
				'buy_send' => $sendtype,
			);
			Db::getInstance()->insert('frotelfactors', $insert);

			Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.
				$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key
				.'&factor='.$ar["result"]["factor"]["id"]);	
			//Tools::redirect('index.php?controller=order&step=3');
		}

		//$this->context->smarty->assign('frotelbank',$ar["result"]["factor"]["banks"]);
		if(isset($ar["result"]["factor"]["banks"]))
		{
			$banks = $ar["result"]["factor"]["banks"];
			$newCookie->__set('banksfrotel',serialize($banks));
			Tools::redirect($this->context->link->getModuleLink('mymodpayment', 'banks'));
		}else{
			// Redirect on order confirmation page
			Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.
				$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key
				.'&factor='.$ar["result"]["factor"]["id"]);	
		}
	}

	public function frotelsaveorder($fields)
	{
		$webservice = Configuration::get('WSF');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."order/registerOrder.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		return $server_output;
	}

}
