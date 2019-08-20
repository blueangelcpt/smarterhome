<?php
App::uses('HttpSocket', 'Network/Http');
class SmarterhomeComponent extends Component {
	public $settings = array(
		'url' => '',
		'token' => ''
	);
	private $socket = '';
	private $tag = 'smarterhome';

	public function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
		$this->settings = array_merge($this->settings, $settings);
		$this->socket = new HttpSocket();
	}

	public function purchase($transactionId, $meterNumber, $amount) {
		$payload = array(
			'token' => $this->settings['token'],
			'meterNo' => $meterNumber,
			'invoiceNo' => $transactionId,
			'amount' => $amount / 100
		);
		$this->log('Smarterhomes API request: ' . json_encode($payload), $this->tag);
		$result = $this->socket->post($this->settings['url'] . '/api/v1/prepaid/credit', $payload);
		$this->log('Smarterhomes API response (N$' . $amount . ' for ' . $meterNumber . '): ' . $result, $this->tag);
		return json_decode($result->body, true);
	}
}