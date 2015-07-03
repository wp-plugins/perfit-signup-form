<?php namespace PerfitSDK;

/**
 * Perfit class wrapper for Perfit UI communication
 *
 * @package PerfitSDK
 * @author Perfit
 */
class Perfit {

	/**
	 * @version 1.0.0
	 */
	const VERSION  = "1.0.0";

	/**
	 * @var array Default settings
	 */
	private $defaultSettings = [
		'url'				=> "http://api.nicolabs.com.ar",
		'version'			=> null,
	];

	/**
	 * @var $curl_opts Curl options
	 */
	public $curl_opts = array(
		CURLOPT_USERAGENT => "PERFIT-PHP-SDK-1.0.0", 
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_CONNECTTIMEOUT => 10, 
		CURLOPT_RETURNTRANSFER => 1, 
		CURLOPT_TIMEOUT => 60
	);

	/**
	 * @var array Permitted http methods
	 */
	private $httpMethods = ['GET', 'POST', 'PUT', 'DELETE'];

	/**
	 * @var $namespace Namespace to make the request to
	 */
	protected $namespace = null;

	/**
	 * @var $token API version that will be included in the request url
	 */
	protected $token = null;

	/**
	 * @var $params Stores params for next request
	 */
	protected $params = array();

	/**
	 * @var $id Stores id for next request
	 */
	protected $id = null;

	/**
	 * @var $action Action to execute for a resource
	 */
	protected $action = null;

	/**
	 * Constructor method. Set all variables to connect in Meli
	 *
	 * @param array $settings Settings to override
	 * @return object
	 */
	public function __construct($settings = null) {
		// Store settings
		$this->settings($settings);
	}

	/**
	 * Override default settings
	 *
	 * @param array $settings Settings to override
	 * @return array Current stored settigns
	 */
	public function settings($settings=null) {

		if ($settings) {
			foreach ($this->defaultSettings as $keySetting => $valSetting) {
				if (isset($settings[$keySetting])) {
					$this->defaultSettings[$keySetting] = $settings[$keySetting];
				}
			}
		}
		return $this->defaultSettings;
	}

	/**
	 * Login method
	 *
	 * @param string $user
	 * @param string $password
	 * @param string $account Optional account
	 * @return boolean
	 */
	public function login($user, $password, $account=null) {

		$params = ['email' => $user, 'password' => $password];
		if ($account) {
			$params['account'] = $account;
		}
		$response = $this->execute("POST", '/login', $params);

		// Successful login, store token
		if ($response->success) {
			$this->token($response->data->token);
			$this->account();
		}
		return $response;

		return true;
	}

	/**
	 * Token setter/getter
	 *
	 * @param string $token
	 * @return string Token
	 */
	public function token($token = null) {
		if ($token) {
			$this->token = $token;
		}
		return $this->token;
	}

	/**
	 * Account setter/getter
	 *
	 * @param string $account
	 * @return string Account name
	 */
	public function account($account = null) {
		if ($account) {
			$this->account = $account;
		}
		return $account;
	}

	/**
	 * Set id for next request
	 *
	 * @param integer $id
	 * @return object
	 */
	public function id($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * Set limit for next request
	 *
	 * @param integer $limit
	 * @return object
	 */
	public function limit($limit) {
		$this->params['limit'] = $limit;
		return $this;
	}

	/**
	 * Set offset for next request
	 *
	 * @param integer $offset
	 * @return object
	 */
	public function offset($offset) {
		$this->params['offset'] = $offset;
		return $this;
	}

	/**
	 * Set sort for next request
	 *
	 * @param integer $sortBy Column to sort by
	 * @param integer $sortDir Sorting direction
	 * @return object
	 */
	public function sort($sortBy, $sortDir=null) {
		$this->params['sortBy'] = $sortBy;
		if ($sortDir) {
			$this->params['sortDir'] = $sortDir;
		}
		return $this;
	}

	/**
	 * Makes request to server and retrieves response object
	 *
	 * @param $params
	 * @return object
	 */
	public function params($params) {
		$this->params = array_merge($this->params, $params);
		return $this;
	}

	/**
	 * Capture non existing variables and turn it into namespace
	 *
	 * @param $name
	 * @return object
	 */
	public function __get($namespace) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * Capture non existing functions and turn it into action or http verb
	 *
	 * @param $name
	 * @param $arguments
	 * @return object
	 */
	public function __call($name, $arguments=array()) {

		$url = null;
		$params = array();

		if (in_array(strtoupper($name), $this->httpMethods)) {
			$type = $name;

			if (isset($arguments[0])) {
				$url = $arguments[0];
			}

			if (isset($arguments[1])) {
				$params = $arguments[1];

			}
		}
		else {
			$type = 'POST';
			$this->action = $name;
		}

		if ($params) {
			$this->params($params);
		}

		return $this->execute($type, $url, $this->params);
	}

	/**
	 * Makes request to server and retrieves response object
	 *
	 * @param string $type HTTP verb (GET, POST, PUT, DELETE supported)
	 * @param string $url Request url
	 * @param array $params Parameters array if needed
	 * @return object
	 */
	private function execute($type='get', $url=null, $params=array()) {

		$type = strtoupper($type);
		$urlParams = '';

		// Add default params
		$params['token'] = $this->token();
		$params['_method'] = $type;

		// Add params
		if ($params) {
			$this->params($params);
		}

		// Build curl opts
		$opts = $this->curl_opts;
		// if ($type == 'GET') {
			$urlParams = http_build_query($this->params);
/*
		}
		else {
			$opts['CURLOPT_POST'] = true;
			$opts['CURLOPT_POSTFIELDS'] = $this->params;
		}
*/

		if (!$url) {
			$url = $this->buildRequestUrl();
		}

		// Execute curl request
		$url = $this->defaultSettings['url'].$url.'?'.$urlParams;
// print_r($this->params);
// return $type.' '.$url;
		$ch = curl_init($url);
		if (!empty($opts)) {
			curl_setopt_array($ch, $opts);
		}

		$response = json_decode(curl_exec($ch));
		$response->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

// $response->request = $url;
// $response->method = $type;
// $response->params = $this->params;

		$this->reset();

		return $response;
	}

	/**
	 * Build request url with information provided
	 *
	 * @return string Request url
	 */
	private function buildRequestUrl() {

		// Build request url
		$request = '';

		if ($this->defaultSettings['version']) {
			$request .= '/v'.$this->defaultSettings['version'];
		}

		if ($this->namespace) {
			if ($this->account()) {
				$request .= '/'.$this->account();
			}
			$request .= '/'.$this->namespace;
		}

		// Add id if set
		if ($this->id) {
			$request .= '/'.$this->id;
		}

		// Add action if set
		if ($this->action) {
			$request .= '/'.$this->action;
		}

		return $request;
	}

	/**
	 * Creates error object to return
	 *
	 * @param $message
	 * @param $code HTTP error code
	 * @return object
	 */
	private function error($message='Formato de solicitud invalido', $code=400) {
		return [
			'success' => false,
			'error' => [
				'status' => $code,
				'type' => 'Bad Request',
				'userMessage' => $message,
			]
		];
	}

	/**
	 * Reset variables for a clean new request
	 *
	 */
	private function reset() {
		$this->namespace = null;
		$this->id = null;
		$this->action = null;
		$this->params = array();
	}

}
