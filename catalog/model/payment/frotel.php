<?php
/**
 * User: ReZa ZaRe <Rz.ZaRe@Gmail.com>
 * Date: 6/1/15
 * Time: 12:02 AM
 */

class ModelPaymentFrotel extends Model
{
    public function getMethod($address,$total)
    {
        if(!isset($this->session->data['shipping_method'])) {
            if ($this->config->get('frotel_default_payment') == null)
                return false;

            $shipping_method = $this->config->get('frotel_default_payment') . '_' . $this->config->get('frotel_default_delivery');
        } else {
            $shipping_method = explode('.',$this->session->data['shipping_method']['code']);
            if (!isset($shipping_method[1]))
                $shipping_method[1] = 'registered_online';
        }

        $this->load->language('payment/frotel');

        $shipping_method = explode('_',$shipping_method[1]);
        $delivery_type = $shipping_method[0];
        if (!isset($shipping_method[1]))
            $shipping_method[1] = 'online';


        if (isset($this->session->data['shipping_method']['default']) && $this->session->data['shipping_method']['default'] == 1) {
            $this->session->data['frotel_shipping_default'] = $this->session->data['shipping_method']['cost'];
        }

        return array(
            'code'=>'frotel',
            'title'=>$this->language->get('buy_'.$shipping_method[1]),
            'buy_type'=>$shipping_method[1] == 'online' ?1:0,
            'delivery_type'=>$delivery_type == 'express'?1:2,
            'sort_order'=>$this->config->get('frotel_sort_order')
        );
    }
}