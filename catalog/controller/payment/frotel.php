<?php
/**
 * User: ReZa ZaRe <Rz.ZaRe@Gmail.com>
 * Date: 5/29/15
 * Time: 12:31 AM
 */

class ControllerPaymentFrotel extends Controller
{
    private $errors = '';

    protected function index()
    {
        $this->language->load('payment/frotel');
        $this->data['error_response'] = $this->language->get('error_response');
        $this->data['button_confirm'] = $this->language->get('button_confirm');

        $this->data['continue'] = $this->url->link('checkout/success','','SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/frotel.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/frotel.tpl';
        } else {
            $this->template = 'default/template/payment/frotel.tpl';
        }

        $this->render();
    }

    /**
     * ثبت سفارش
     */
    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->language->load('payment/frotel');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        if (!isset($order['shipping_code'])) {
            echo (json_encode(array(
                'error'=>1,
                'message'=>'<div class="warning">'.$this->language->get('order_not_found').'</div>'
            )));
            exit;
        }

        $shipping = str_replace('frotel_shipping.','',$order['shipping_code']);
        $shipping = explode('_',$shipping);

        $send_type = 2;
        if ($shipping[0] == 'express')
            $send_type = 1;

        $buy_type = 1;

        if ($shipping[1] == 'online')
            $buy_type = 2;

        $url = $this->url->link('checkout/success','','SSL');
        $frotel_factor = '';
        $desc = '';
        try {
            $name = $order['shipping_firstname'];
            $family = $order['shipping_lastname'];
            $address = $order['shipping_address_1'] . ' ' . ($order['shipping_address_2'] ? '('.$order['shipping_address_2'].')' : '');

            $postCode = $order['shipping_postcode'];
            $email = $order['email'];

            $mobile = $order['telephone'];
            $phone = '';
            $gender = 1;
            $province = $this->session->data['province_id'];
            $city = $this->session->data['city_id'];
            $pm = $order['comment'];
            $basket = array();
            $products = $this->cart->getProducts();

            $pro_code_key = $this->config->get('frotel_pro_code');

            $default_weight = ceil($this->config->get('frotel_default_weight'));
            foreach ($products as $item) {
                $product_name = array();
                foreach ($item['option'] as $option) {
                    $product_name[] = $option['name'] . ' : ' . $option['option_value'];
                }
                if (empty($product_name))
                    $product_name = '';
                else
                    $product_name = '(' . implode(',', $product_name) . ')';

                $weight = ceil($this->weight->convert($item['weight'], $this->config->get('config_weight_class_id'), 2));  # convert to gram
                if ($weight<=0)
                    $weight = $default_weight;

                $basket[] = array(
                    'pro_code' => $item[$pro_code_key],
                    'name' => $item['name'] . $product_name,
                    'price' => ceil($this->currency->convert($item['price'], $this->config->get('config_currency'), 'RLS')),    # convert to RLS
                    'count' => ceil($item['quantity']),
                    'weight' => $weight,
                    'porsant' => 0,
                );
            }

            $this->load->library('frotel/helper');
            $helper = new helper($this->config->get('frotel_url'), $this->config->get('frotel_api'));
            $result = $helper->registerOrder($name, $family, $gender, $mobile, $phone, $email, $province, $city, $address, $postCode, $buy_type, $send_type, $pm, $basket);
            $frotel_factor = $result['factor']['id'];

            $result['factor']['total'] = $this->currency->convert($result['factor']['total'],'RLS',$this->currency->getCode());

            $sql = 'UPDATE `'.DB_PREFIX.'order`
                    SET `total` = '.$result['factor']['total'].'
                    WHERE `order_id` = '.$this->session->data['order_id'];

            $this->db->query($sql);

            if (isset($result['factor']['banks']))
                $url = $this->url->link('payment/frotel/pay','','SSL');

            $result['pay'] = 0;
            $this->session->data['frotel_data'] = $result;

        }catch (FrotelWebserviceException $e){
            echo (json_encode(array(
                'error'=>1,
                'message'=>$e->getMessage()
            )));
            exit;
        }catch (FrotelResponseException $e) {
            if (isset($this->session->data['frotel_shipping_default'])) {
                $desc = "مبلغ ".number_format($this->session->data['frotel_shipping_default'])." ریال به عنوان علی الحساب هزینه ارسال دریافت شد.\n";
            }
        }

        $sql = "INSERT INTO `".DB_PREFIX."frotel_factor`(`oc_order_id`,`frotel_factor`,`last_change_status`,`province`,`city`,`desc`)
                        VALUES ({$this->session->data['order_id']},'{$frotel_factor}',".time().",{$province},{$city},'{$desc}')
                        ON DUPLICATE KEY UPDATE `frotel_factor` = '{$frotel_factor}',`last_change_status` = ".time().",`desc` = CONCAT(`desc`,'{$desc}')";

        $this->db->query($sql);

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('frotel_order_status'), '', true);
        $this->session->data['frotel_shipping_default'] = null;
        echo (json_encode(array(
            'error'=>0,
            'url'=>$url
        )));
    }

    /**
     * فرم انتخاب درگاه برای پرداخت آنلاین
     */
    public function pay()
    {
        if (!isset($this->session->data['frotel_data']))
            $this->response->redirect($this->url->link('checkout/cart','','SSL'));

        $this->language->load('payment/frotel');
        $this->document->setTitle($this->language->get('text_title'));
        $this->data['text_title'] = $this->language->get('text_title');
        $this->data['text_error_response'] = $this->language->get('error_response');
        $this->data['text_start_transaction'] = $this->language->get('start_transaction');
        $this->data['choose_bank'] = $this->language->get('choose_bank');
        $this->data['banks'] = $this->session->data['frotel_data']['factor']['banks'];
        $this->data['url'] = $this->url->link('payment/frotel/gateway','','SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/frotel_pay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/frotel_pay.tpl';
        } else {
            $this->template = 'default/template/payment/frotel_pay.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    /**
     * دریافت فرم ارجاع به بانک از وب سرویس
     */
    public function gateway()
    {
        $this->language->load('payment/frotel');

        if (!isset($this->request->get['bank'])) {
            echo json_encode(array(
                'error'=>1,
                'message'=>$this->language->get('choose_bank')
            ));
            exit;
        }

        $bank = intval($this->request->get['bank']);
        $orderId = $this->session->data['order_id'];
        $frotel_factor = $this->session->data['frotel_data']['factor']['id'];
        $callback = $this->url->link('payment/frotel/callback','id='.$orderId.'&factor='.$frotel_factor,'SSL');

        $this->load->library('frotel/helper');

        $helper = new helper($this->config->get('frotel_url'),$this->config->get('frotel_api'));

        try {
            $result = $helper->pay($frotel_factor,$bank,$callback);
            $output = array(
                'error' => 0,
                'message' => $result
            );
        }catch (FrotelWebserviceException $e){
            $output = array(
                'error' => 1,
                'message' => $e->getMessage()
            );
        }catch (FrotelResponseException $e){
            $output = array(
                'error' => 1,
                'message' => $this->language->get('error_webservice')
            );
        }

        echo json_encode($output);
        exit;
    }

    /**
     * برگشت خریدار از بانک
     * بررسی صحت پرداخت
     *
     */
    public function callback()
    {
        if (!isset($this->request->get['id'],$this->request->get['factor'],$this->request->get['_i'],$this->request->get['sb']))
            $this->response->redirect($this->url->link('checkout/cart','','SSL'));

        # اگر پرداخت قبلا تایید شده بود
        if ($this->session->data['frotel_data']['pay'] == 1)
            $this->response->redirect($this->url->link('checkout/success','','SSL'));

        $orderId = intval($this->request->get['id']);
        $factor = $this->request->get['factor'];
        $_i = intval($this->request->get['_i']);
        $sb = $this->request->get['sb'];

        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($orderId);

        if (!isset($order['order_id']) || $order['frotel_factor'] != $factor)
            $this->response->redirect($this->url->link('checkout/cart','','SSL'));


        $this->load->library('frotel/helper');
        $helper = new helper($this->config->get('frotel_url'),$this->config->get('frotel_api'));

        $this->language->load('payment/frotel');
        try {
            $result = $helper->checkPay($factor,$_i,$sb);
            if ($result['verify'] == 1) {
                $comment = "پرداخت نقدی سفارش تایید شده است.\nکد رهگیری سفارش : {$result['code']}\n";
                $this->db->query('
                    UPDATE `'.DB_PREFIX.'order` SET `comment` = CONCAT(`comment`,"\n","'.$comment.'"),`order_status_id` = '.$this->config->get('frotel_verify_status').'
                    WHERE `order_id` = '.$orderId.'
                ');
                $this->session->data['frotel_data']['pay'] = 1;
                $this->session->data['pay_verify'] = sprintf($this->language->get('verify_success'),$result['code']);
            } else {
                $this->session->data['pay_error'] = $result['message'];
            }


        } catch (FrotelWebserviceException $e) {
            $this->session->data['pay_error'] = $e->getMessage();
        } catch (FrotelResponseException $e) {
            $this->session->data['pay_error'] = $this->language->get('error_webservice');
        }

        $this->response->redirect($this->url->link('checkout/success','','SSL'));
    }

    /**
     * رهگیری سفارش
     */
    public function tracking()
    {
        $url = $this->config->get('frotel_url');
        $api = $this->config->get('frotel_api');

        $this->load->library('frotel/helper');
        $factor = isset($this->request->get['factor'])?$this->request->get['factor']:false;

        $h = new helper($url,$api);

        try{
            $result = $h->tracking($factor);
            $output = array(
                'error'=>0,
                'message'=>array(
                    'barcode'=>$result['order']['barcode'],
                    'status'=>$result['order']['status'],
                )
            );
        } catch (FrotelWebserviceException $e) {
            $output = array(
                'error'=>1,
                'message'=>$e->getMessage()
            );
        } catch (FrotelResponseException $e) {
            $output = array(
                'error'=>1,
                'message'=>$e->getMessage()
            );
        }

        $this->response->setOutput(json_encode($output));
    }


    /**
     * فرم انتخاب شهر و استان
     */
    public function city()
    {
        $this->language->load('payment/frotel');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (empty($this->request->post['province_id']) || $this->request->post['province_id']<=0) {
                $this->errors['province_id'] = $this->language->get('error_province_empty');
            }
            if (empty($this->request->post['city_id']) || $this->request->post['province_id']<=0)  {
                $this->errors['city_id'] = $this->language->get('error_city_empty');
            }

            if (!$this->errors) {
                $this->session->data['province_id'] = $this->request->post['province_id'];
                $this->session->data['city_id']  = $this->request->post['city_id'];

                $this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
            }
        }

        $this->document->setTitle($this->language->get('text_title_select_city'));
        $this->data['text_title_select_city'] = $this->language->get('text_title_select_city');
        $this->data['text_select_city_desc'] = $this->language->get('text_select_city_desc');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['entry_province'] = $this->language->get('entry_province');
        $this->data['entry_city'] = $this->language->get('entry_city');
        $this->data['error'] = empty($this->errors)?'':implode('<br />',$this->errors);

        $this->document->addScript('http://pc.fpanel.ir/ostan.js');
        $this->document->addScript('http://pc.fpanel.ir/city.js');

        $this->data['url'] = $this->url->link('payment/frotel/city','','SSL');

        if (isset($this->session->data['province_id']))
            $this->data['province_id'] = $this->session->data['province_id'];

        if (isset($this->session->data['city_id']))
            $this->data['city_id'] = $this->session->data['city_id'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/frotel_city.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/frotel_city.tpl';
        } else {
            $this->template = 'default/template/payment/frotel_city.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
}