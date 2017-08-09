<?php

require_once (dirname(__file__) . '/../../../../config/config.inc.php');
require_once (dirname(__file__) . '/../../Frotel.php');
require_once (dirname(__file__) . '/../../frotel_helper.php');
$class = new Frotel();

//if (Tools::getValue('secure') != Tools::encrypt($class->name))
//    return false;
if (Tools::getValue('action') == 'getPriceFrotel')
{
    $FrotelCookie = new Cookie('FrotelService');

    $api = Configuration::get('API');
    $webservice = Configuration::get('WSF');
    $weight = Configuration::get('WEIGHTUNIT');
    $city = Tools::getValue('id_city');
    $state = Tools::getValue('id_state');
    $cod = Configuration::get('PAYMENT_FROTEL_COD');
    $online = Configuration::get('PAYMENT_FROTEL_ONLINE');
    $sefareshi = Configuration::get('SEFARESHI_FROTEL');
    $pishtaz = Configuration::get('PISHTAZ_FROTEL');
    $send_type = array();
    $buy_type = array();

    $id_cart = Context::getContext()->cookie->id_cart;
    $ClassCart = new Cart($id_cart);
    $TotalPrice = $ClassCart->getOrderTotal(true,Cart::BOTH_WITHOUT_SHIPPING);
    
    if($cod)  array_push($buy_type,1);
    if($online)  array_push($buy_type,2);
    if($sefareshi)  array_push($send_type,1);
    if($pishtaz)  array_push($send_type,2);

    $fields = array(
        'api' => $api,
        'price' => intval($TotalPrice),
        'weight' => $weight,
        'des_city' => $city,
        'send_type' => $send_type,
        'buy_type' => $buy_type
         );
/*$frotel = new frotel_helper($webservice,$api);

try {
    $result = $frotel->getPrices($city,intval($TotalPrice),$weight,$buy_type,$send_type);
    print_r($result);
    $FrotelCookie->__set("PriceFrotel",$result);
    $FrotelCookie->__set("city_frotel" , $city);
    $FrotelCookie->__set("state_frotel" , $state);
    echo 1;
} catch (FrotelWebserviceException $e) {
    // در این قسمت خطایی که وب سرویس برگردانده قابل دسترسی است
    echo 'Error ';
    echo $e->getMessage();
    // var_dump($frotel->getErrors());
} catch (FrotelResponseException $e) {
    // زمانی که وب سرویس قطع باشد و یا پاسخ مناسبی به درخواست ندهد این قسمت اجرا می شود
    echo 'Fatal Error ';
    echo $e->getMessage();
}*/

   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$webservice."order/getPrices.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);
    $PRICES = (array) json_decode($server_output,true);
    
    if(isset($PRICES) && $PRICES['code'] === 0)
    {
        $FrotelCookie->__set("PriceFrotel",$server_output);
        $FrotelCookie->__set("city_frotel" , $city);
        $FrotelCookie->__set("state_frotel" , $state);
        echo 1;
    }else{
        echo 0;
    }


}
elseif (Tools::getValue('action') == 'getCarrierPriceFrotel')
    echo $class->getCarrierPriceFrotel();
elseif (Tools::getValue('action') == 'sendfrotel')
{

       $ProductDetailObject = new OrderDetail;
       $product_detail = $ProductDetailObject->getList($_POST["orderid"]);
$order = new Order($_POST["orderid"]); //create order object
        $customer = new Customer((int)($order->id_customer)); //create customer object
        
$Address = new Address((int)$order->id_address_delivery);
         
        $products = $product_detail;
        $basket = array();
        foreach ($products as $key ) {
            
			if($key['product_weight'] <= 0 )
				$weight = Configuration::get('WEIGHTUNIT');
			else
				$weight = $key['product_weight'];
			
			$basket[] = array(
				'pro_code' => $key['product_id'],
				'name' => $key['product_name'],
				'price' => $key['total_price_tax_excl'],
				'count' => $key['product_quantity_in_stock'],
				'weight' => $weight,
				'porsant' => 0,
				'bazayab' => 0,
				'discount' => 0,
				'free_send' => 0,
				'tax' => 0,
				'option' => array(),
			) ;
			
		}
		
        $factor = Db::getInstance()->executeS('
        SELECT * FROM `'._DB_PREFIX_.'frotelfactors`
        WHERE `id_cart` = '.(int)$_POST["orderid"]);

        if(!is_null($factor[0]["factor"]) && $factor[0]["factor"] != "")
        {
            echo "این سفارش در فروتل ثبت شده است./";
            echo "<br>";
            echo "شماره فاکتور این سفارش :".$factor[0]["factor"];
            return;
        }
        
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
			'province' => $factor[0]["id_state"],
			'city' => $factor[0]["id_city"],
			'buy_type' => $factor[0]["buy_type"],
			'send_type' => $factor[0]["buy_send"],
			'ip' => $_SERVER['REMOTE_ADDR'],
			//'pm' => ,
			'basket' =>  $basket,//$bascket,
			'free_send' => 0,
		);


        $webservice = Configuration::get('WSF');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."order/registerOrder.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($frotel_cart));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
        
		$order_frotel = $server_output;
        $ar = json_decode($order_frotel, JSON_UNESCAPED_UNICODE);

        if($ar["code"] == 0)
        {
					/*Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'frotelfactors 
					SET active = '..'1 WHERE id_cart = '.
					Configuration::get("ID_PISHTAZ_COD"));*/

            $update = array(
				'factor' => $ar["result"]["factor"]["id"],
			);
			Db::getInstance()->update
            ('frotelfactors', $update,"id_cart=".$_POST["orderid"]);

            $order->setCurrentState(Configuration::get("PS_OS_MYMOD_PAYMENT"));

            echo "سفارش شما در فروتل ثبت شد.";
        }
        else{
            echo "مشکل در اتصال به سرور";
        }

}
elseif (Tools::getValue('action') == 'get_tracking'){
    
    $factor  =  $_POST["factor"];

    $tracking = array(
			'api'    => Configuration::get('API'),
            'factor' => $factor
    );
    $webservice = Configuration::get('WSF');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$webservice."order/tracking.json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($tracking));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec ($ch);
    curl_close ($ch);

    $result = json_decode($server_output , true);

    if($result["code"]==0)
    {
?>
<div class="row">
    <div class="col-lg-3">
        شماره فاکتور :
    </div>
    <div class="col-lg-3">
        <?= $result["result"]["order"]["factor"] ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        نام و نام خانوادگی :
    </div>
    <div class="col-lg-3">
        <?= $result["result"]["customer"] ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        وضعیت :
    </div>
    <div class="col-lg-3">
        <?= $result["result"]["order"]["status"] ?>
    </div>
</div>
<?php
if($result["result"]["order"]["buy_type"] == 0)
{
?>
<div class="row">
    <div class="col-lg-3">
        توضیحات :
    </div>
    <div class="col-lg-3">
        <?= $result["result"]["order"]["desc"] ?>
    </div>
</div>
<?php
}else{
?>
<div class="row">
    <div class="col-lg-3">
        <?= $result["result"]["payment"]["message"] ?>
    </div>
    <div class="col-lg-3">
        <a type="button" class="btn btn-primary btn-lg" 
         href="<?= $result['result']['payment']['pay_link'] ?>" >
            پرداخت سفارش
        </a>
    </div>
</div>
<?php
}

    }else{

    }
}