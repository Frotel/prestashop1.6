<?php

		$fields = array(
			'api' => $_POST["api"],
			'factor' => $_POST["factor"],
			'bank' => $_POST["bank"],
			'callback' => $_POST["callback"],
			);

		
		$webservice = $_POST["webservice"];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$webservice."payment/pay.json");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		
		$formbank = $server_output;
		$fm = json_decode($formbank);
		
		//print_r($_POST["factor"]);
		if($fm->code == 0)
		{
			echo $fm->result;
		}else{
			echo 1;
		}



	
