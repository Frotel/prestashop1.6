<?php
/**
 * User: ReZa ZaRe <Rz.ZaRe@Gmail.com>
 * Date: 2017/01/01
 * Time: 11:02
 */

class frotel_helper
{
    /**
     * آدرس وب سرویس
     *
     * @var string
     */
    private $url;
    /**
     * کلید API
     *
     * @var string
     */
    private $api_key;

    const METHOD_POST   = 'post';
    const METHOD_GET    = 'get';

    /**
     * روش پرداخت به صورت پرداخت در محل
     */
    const BUY_COD       = 1;
    /**
     * روش پرداخت به صورت آنلاین
     */
    const BUY_ONLINE    = 2;

    /**
     * روش ارسال به صورت پیشتاز
     */
    const DELIVERY_PISHTAZ      = 1;
    /**
     * روش ارسال به صورت سفارشی
     */
    const DELIVERY_SEFARESHI    = 2;
    /**
     * روش ارسال با هزینه ثابت
     */
    const DELIVERY_FIXED = 20;

    /**
     * list of errors
     *
     * @var array
     */
    private $errors = array();

    /**
     * @param string $webserviceUrl
     * @param string $apiKey
     */
    public function __construct($webserviceUrl,$apiKey)
    {
        $this->url = $webserviceUrl;
        $this->api_key = $apiKey;
    }

    /**
     * get frotel service price
     *
     * @return array|bool
     */
    public function frotelService()
    {
        return $this->call('order/frotelService.json',array());
    }

    /**
     * محاسبه هزینه ارسال یک سفارش با روش ارسال و پرداخت مشخص
     *
     * @param int $src_city         شناسه شهر مبدا
     * @param int $des_city         شناسه شهر مقصد
     * @param int $price            جمع کل هزینه سفارش
     * @param int $weight           وزن کل سفارش + وزن بسته بندی
     * @param int $send_type        روش ارسال
     * @param int $buy_type         روش پرداخت
     * @return array|bool
     */
    public function fPrice($src_city, $des_city, $price, $weight, $send_type, $buy_type)
    {
        $params = array(
            'src_city'  => $src_city,
            'des_city'  => $des_city,
            'price'     => $price,
            'weight'    => $weight,
            'send_type' => $send_type,
            'buy_type'  => $buy_type
        );

        return $this->call('order/fPrice.json',$params);
    }

    /**
     * محاسبه هزینه ارسال یک سفارش
     *
     * @param int $des_city             شناسه شهر مقصد
     * @param int $price                وزن کل سفارش
     * @param int $weight               وزن کل سفارش + وزن بسته بندی
     * @param array $buy_type           آرایه ای از روش های پرداخت
     * @param array $send_type          آرایه ای از روش های ارسال
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function getPrices($des_city,$price,$weight,$buy_type,$send_type)
    {
        $params = array(
            'des_city'  => $des_city,
            'price'     => $price,
            'weight'    => $weight,
            'buy_type'  => $buy_type,
            'send_type' => $send_type
        );

        return $this->call('order/getPrices.json',$params);
    }

    /**
     * تفکیک سبد خرید براساس فروشنده و نوع محصولات
     *
     * @param array $basket         اطلاعات محصولات سبد خرید
     *
     * $basket = [
     *  [       // product 1
     *      'product_id' => 123456, // شناسه محصول
     *      'count'      => 1,      // تعداد محصول خریداری شده
     *      'options'    => [       // ویژگی های محصول - اختیاری
     *          // option id => value_id or value
     *          12 => 4
     *          1  => 'ReZa'
     *      ]
     *  ],
     *  [       // product 2
     *      'product_id' => 987654, // شناسه محصول
     *      'count'      => 1,      // تعداد محصول خریداری شده
     *  ],
     *        // ....
     * ]
     * @return array|bool
     */
    public function separationCart($basket)
    {
        $params = array(
            'basket' => $basket
        );

        return $this->call('order/separationCart.json',$params);
    }

    /**
     *  روش های ارسال و پرداخت یک سبد خرید
     *
     * @param array $basket         اطلاعات محصولات سبد خرید
     *
     * $basket = [
     *  [       // product 1
     *      'product_id' => 123456, // شناسه محصول
     *      'count'      => 1,      // تعداد محصول خریداری شده
     *      'options'    => [       // ویژگی های محصول - اختیاری
     *          // option id => value_id or value
     *          12 => 4
     *          1  => 'ReZa'
     *      ]
     *  ],
     *  [       // product 2
     *      'product_id' => 987654, // شناسه محصول
     *      'count'      => 1,      // تعداد محصول خریداری شده
     *  ],
     *        // ....
     * ]
     * @param int $province         شناسه استان مقصد
     * @param int $city             شناسه شهر مقصد
     * @param int $seller           شناسه فروشنده
     * @param string $coupon        کد تخفیف (کوپن یا کارت هدیه)
     * @return array|bool
     */
    public function costCalculation($basket, $province, $city, $seller, $coupon = '')
    {
        $params = array(
            'basket'    => $basket,
            'province'  => $province,
            'city'      => $city,
            'seller'    => $seller,
            'coupon'    => $coupon
        );

        return $this->call('order/costCalculation.json',$params);
    }

    /**
     * ثبت سفارش محصولات فیزیکی
     *
     * @param string $name          نام خریدار
     * @param string $family        نام خانوادگی خریدار
     * @param int $gender           جنسیت خریدار
     * @param string $mobile        شماره موبایل
     * @param string $phone         شماره تلفن ثابت
     * @param string $email         ایمیل خریدار
     * @param int $province         شناسه استان مقصد
     * @param int $city             شناسه شهر مقصد
     * @param string $address       آدرس خریدار
     * @param string $postCode      کد پستی
     * @param int $buy_type         روش پرداخت
     * @param int $send_type        روش ارسال
     * @param array $basket         اطلاعات محصولات سبد خرید
     * @param int $postPrice        هزینه ارسال - زمانی که نوع ارسال به صورت هزینه ثابت انتخاب شده باشد باید هزینه ارسال به وب سرویس ارسال شود - مختص فروشنده
     * @param bool $free_send       ارسال رایگان - مختص فروشنده
     * @param string $pm            پیام خریدار
     * @param array $fields         اطلاعات درخواستی فروشنده (اختیاری)
     * @param string $coupon        کد تخفیف (کوپن یا کارت هدیه)
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function registerOrder($name, $family, $gender, $mobile, $phone, $email, $province, $city, $address, $postCode, $buy_type, $send_type, $basket, $postPrice = 0, $free_send = false, $pm = '', $fields = array(), $coupon = '')
    {
        $params = array(
            'name'      => $name,
            'family'    => $family,
            'gender'    => $gender,
            'mobile'    => $mobile,
            'phone'     => $phone,
            'email'     => $email,
            'province'  => $province,
            'city'      => $city,
            'address'   => $address,
            'code_posti'=> $postCode,
            'buy_type'  => $buy_type,
            'send_type' => $send_type,
            'pm'        => $pm,
            'basket'    => $basket,
            'fields'    => $fields,
            'post_price'=> $postPrice,
            'free_send' => $free_send,
            'coupon'    => $coupon
        );
        return $this->call('order/registerOrder.json',$params);
    }

    /**
     * ثبت سفارش محصولات فیزیکی
     *
     * @param string $name          نام خریدار
     * @param string $family        نام خانوادگی خریدار
     * @param int $gender           جنسیت
     * @param string $mobile        شماره موبایل
     * @param string $phone         شماره تلفن ثابت
     * @param string $email         ایمیل - لینک های دانلود به ایمیل خریدار ارسال می شود
     * @param array $basket         اطلاعات محصولات سبد خرید
     * @param string $pm            پیام خریدار
     * @param array $fields         اطلاعات درخواستی فروشنده (اختیاری)
     * @param string $coupon        کد تخفیف (کوپن یا کارت هدیه)
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function registerOrderVirtual($name, $family, $gender, $mobile, $phone, $email, $basket, $pm = '', $fields = array(), $coupon = '')
    {
        $params = array(
            'name'      => $name,
            'family'    => $family,
            'gender'    => $gender,
            'mobile'    => $mobile,
            'phone'     => $phone,
            'email'     => $email,
            'pm'        => $pm,
            'basket'    => $basket,
            'fields'    => $fields,
            'coupon'    => $coupon
        );
        return $this->call('order/registerOrderVirtual.json',$params);
    }

    /**
     * ثبت سفارش محصولات خدماتی
     *
     * @param string $name          نام خریدار
     * @param string $family        نام خانوادگی خریدار
     * @param int $gender           جنسیت
     * @param string $mobile        شماره موبایل
     * @param string $phone         شماره تلفن ثابت
     * @param string $email         ایمیل
     * @param int $province         شناسه استان مقدص
     * @param int $city             شناسه شهر مقصد
     * @param string $address       آدرس خریدار
     * @param string $postCode      کد پستی
     * @param array $basket         اطلاعات محصولات سبد خرید
     * @param string $pm            پیام خریدار
     * @param array $fields         اطلاعات درخواستی فروشنده (اختیاری)
     * @param string $coupon        کد تخفیف (کوپن یا کارت هدیه)
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function registerOrderService($name, $family, $gender, $mobile, $phone, $email, $province, $city, $address, $postCode, $basket, $pm = '', $fields = array(), $coupon = '')
    {
        $params = array(
            'name'      => $name,
            'family'    => $family,
            'gender'    => $gender,
            'mobile'    => $mobile,
            'phone'     => $phone,
            'email'     => $email,
            'province'  => $province,
            'city'      => $city,
            'address'   => $address,
            'code_posti'=> $postCode,
            'pm'        => $pm,
            'basket'    => $basket,
            'fields'    => $fields,
            'coupon'    => $coupon
        );
        return $this->call('order/registerOrderService.json',$params);
    }

    /**
     * رهگیری سفارش
     *
     * @param string $factor            شماره فاکتور سفارش - فاکتور فروتل
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function tracking($factor)
    {
        $params = array(
            'factor'   => $factor
        );
        return $this->call('order/tracking.json',$params);
    }

    /**
     * دریافت فرم ارجاع به بانک
     *
     * @param string $factor                شماره فاکتور سفارش - فاکتور فروتل
     * @param int $bankId                   شناسه درگاه بانکی انتخاب شده توسط خریدار
     * @param string $callback              آدرس بازگشت از بانک
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function pay($factor,$bankId,$callback)
    {
        $params = array(
            'factor'    => $factor,
            'bank'      => $bankId,
            'callback'  => $callback
        );
        return $this->call('payment/pay.json',$params);
    }

    /**
     * بررسی صحت پرداخت
     *
     * @param string $factor                شماره فاکتور سفارش - فاکتور فروتل
     * @param int $paymentId                شناسه پرداخت - پارامتر _i در زمان بازگشت از بانک
     * @param string $ref                   شناسه ارجاع - پارامتر sb در زمان بازگشت از بانک
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function checkPay($factor,$paymentId,$ref)
    {
        $params = array(
            'factor'    => $factor,
            'paymentId' => $paymentId,
            'ref'       => $ref
        );
        return $this->call('payment/checkPay.json',$params);
    }

    /**
     * بررسی کد کوپن
     * 
     * @param string $coupon                کد تخفیف (کوپن یا کارت هدیه)
     * @return array|bool
     * @throws FrotelResponseException
     */
    public function checkCoupon($coupon)
    {
        $params = array(
            'code'      => $coupon
        );

        return $this->call('order/checkCoupon.json',$params);
    }

    /**
     * call method in webservice
     *
     * @param string $url
     * @param array $params
     * @param string $methodType
     * @return array|bool
     * @throws FrotelResponseException
     * @throws FrotelWebserviceException
     */
    private function call($url,$params,$methodType = self::METHOD_POST)
    {
        // flush error list
        $this->errors = array();
        if (stripos($url, 'http://') === false)
            $url = $this->url . $url;
        $params['api'] = $this->api_key;
        $data = http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, $methodType === self::METHOD_POST);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        if ($this->isJson($result)) {
            $result = json_decode($result,true);
            return $this->parseResponse($result);
        }
        throw new FrotelResponseException('Failed to Parse Response');
    }

    /**
     * check valid json
     *
     * @param $string
     * @return bool
     */
    private function isJson($string) {
        return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;//PHP Version 5.2.17 server
    }

    /**
     * parse webservice response
     *
     * @param array $response
     * @return bool
     * @throws FrotelResponseException
     * @throws FrotelWebserviceException
     */
    private function parseResponse($response)
    {
        if (!isset($response['code'],$response['message'],$response['result']))
            throw new FrotelResponseException('پاسخ دریافتی از سرور معتبر نیست.');
        if ($response['code'] == 0)
            return $response['result'];
        $this->errors[] = $response['message'];
        throw new FrotelWebserviceException($response['message']);
    }

    /**
     * get list of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
class FrotelResponseException extends Exception{}
class FrotelWebserviceException extends Exception{}