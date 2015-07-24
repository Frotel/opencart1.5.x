<?php  
class ControllerModuleFrotelTracking extends Controller {

	public function index()
	{
		$this->load->language('module/frotel_tracking');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['frotel_tracking_id'] = uniqid();

		$this->data['button_tracking'] = $this->language->get('button_tracking');

		$this->data['text_tracking_factor'] = $this->language->get('text_tracking_factor');
		$this->data['text_order_code'] = $this->language->get('text_order_code');
		$this->data['text_order_status'] = $this->language->get('text_order_status');

      	$this->data['heading_title'] = $this->language->get('heading_title');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/frotel_tracking.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/frotel_tracking.tpl';
		} else {
			$this->template = 'default/template/module/frotel_tracking.tpl';
		}

        $this->document->addScript('catalog/view/javascript/frotel_tracking/tracking.js');
		$this->render();
	}
}