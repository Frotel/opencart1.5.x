<?php
/**
 * User: ReZa ZaRe <Rz.ZaRe@Gmail.com>
 * Date: 5/29/15
 * Time: 12:04 AM
 */

class ControllerShippingFrotelShipping extends Controller
{
    private $error = array();

    public function index()
    {
        $this->language->load('shipping/frotel_shipping');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('frotel_shipping', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
        }
        /* entry ,text */
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort'] = $this->language->get('entry_sort');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');

        /* button */
        $this->data['action'] = $this->url->link('shipping/frotel_shipping','token='.$this->session->data['token'],'SSL');
        $this->data['cancel'] = $this->url->link('extension/shipping','token='.$this->session->data['token'],'SSL');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        /* breadcrumbs , error message*/
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_shipping'),
            'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('shipping/frotel_shipping', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        /* data */
        if (isset($this->request->post['frotel_shipping_status'])) {
            $this->data['frotel_shipping_status'] = $this->request->post['frotel_shipping_status'];
        } else {
            $this->data['frotel_shipping_status'] = $this->config->get('frotel_shipping_status');
        }

        if (isset($this->request->post['frotel_shipping_sort'])) {
            $this->data['frotel_shipping_sort'] = $this->request->post['frotel_shipping_sort'];
        } else {
            $this->data['frotel_shipping_sort'] = $this->config->get('frotel_shipping_sort');
        }


        /* template */
        $this->template = 'shipping/frotel.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'shipping/frotel_shipping')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}