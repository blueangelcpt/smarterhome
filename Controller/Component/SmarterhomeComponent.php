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
		$request = array(
			'header' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Connection' => 'keep-alive',
				'Cache-Control' => 'no-cache'
			)
		);
		$payload = array(
			'token' => $this->settings['token'],
			'meterNo' => $meterNumber,
			'invoiceNo' => $transactionId,
			'amount' => $amount / 100
		);
		$result = $this->socket->post($this->settings['url'] . '/api/v1/prepaid/credit', $payload, $request);
		$this->log('Smarterhomes API request: ' . $this->socket->request['raw'], $this->tag);
		$this->log('Smarterhomes API response (N$' . ($amount / 100) . ' for ' . $meterNumber . '): ' . $result, $this->tag);
		return json_decode($result->body, true);
	}

	public function validate($meterNumber) {
		$request = array(
			'header' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Connection' => 'keep-alive',
				'Cache-Control' => 'no-cache'
			)
		);
		$payload = array(
			'token' => $this->settings['token'],
			'meterSerial' => $meterNumber
		);
		$result = $this->socket->post($this->settings['url'] . '/api/meter-validation', $payload, $request);
		$this->log('Smarterhomes validation request: ' . $this->socket->request['raw'], $this->tag);
		$this->log('Smarterhomes validation response (' . $meterNumber . '): ' . $result, $this->tag);
		$response = json_decode($result->body, true);
		return (!$response['error']);
	}
}