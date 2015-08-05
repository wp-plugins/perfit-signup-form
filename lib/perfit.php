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
	private $defaultSettings = array(
		'url'				=> "http://test.myperfit.com:8881",
		'version'			=> null,
		'language'			=> 'es-es',
	);

	/**
	 * @var $curl_opts Curl options
	 */
	public $curl_opts = array(
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_USERAGENT => "PERFIT-PHP-SDK-1.0.0", 
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_CONNECTTIMEOUT => 10, 
		CURLOPT_RETURNTRANSFER => 1, 
		CURLOPT_TIMEOUT => 60,
		CURLOPT_HTTPHEADER => array (
			"Content-Type: application/x-www-form-urlencoded",
		),
	);

	/**
	 * @var array Permitted http methods
	 */
	private $httpMethods = array('GET', 'POST', 'PUT', 'DELETE');

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
	 * @var $account Stores current account for all calls
	 */
	protected $account = null;

	/**
	 * @var $id Stores id for next request
	 */
	protected $id = null;

	/**
	 * @var $action Action to execute for a resource
	 */
	protected $action = null;

	/**
	 * @var $methodOverride Force certain method
	 */
	protected $methodOverride = null;

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

		// Add default language
		array_push($this->curl_opts[CURLOPT_HTTPHEADER], "Accept-Language: ".$this->defaultSettings['language']);

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

		$params = array('user' => $user, 'password' => $password);
		if ($account) {
			$params['account'] = $account;
		}

		$request = '/login';

		if ($this->defaultSettings['version']) {
			$request = '/v'.$this->defaultSettings['version'].$request;
		}

		$response = $this->execute("POST", $request, $params);

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
			$this->curl_opts[CURLOPT_HTTPHEADER][] = "X-Auth-Token: $token";
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
		return $this->account;
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
	 * Overrides method
	 *
	 * @param string $method
	 * @return object
	 */
	public function method($method) {
		if (in_array(strtoupper($method), $this->httpMethods)) {
			$this->methodOverride = $method;
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

		if ($this->methodOverride) {
			$type = $this->methodOverride;
		}

		$type = strtoupper($type);
		$urlParams = '';

		// Add default params
		// $params['token'] = $this->token();
		// $params['_method'] = $type;

		// Add params
		if ($params) {
			$this->params($params);
		}

		if (!$url) {
			$url = $this->buildRequestUrl();
		}

		// Build curl opts
		$opts = $this->curl_opts;
		if (strtoupper($type) == 'GET') {
			$urlParams = http_build_query($this->params);
			$url .= '?'.$urlParams;
		}
		else {
			$opts[CURLOPT_POST] = true;
			// $opts[CURLOPT_POSTFIELDS] = $this->params;
			$opts[CURLOPT_POSTFIELDS] = http_build_query($this->params);
		}

		$request = array (
			'host' => $this->defaultSettings['url'],
			'method' => $type,
			'url' => $url,
			'params' => $params,
			'account' => $this->account,
		);

		$opts[CURLOPT_CUSTOMREQUEST] = strtoupper($type);

		// Execute curl request
		$url = $this->defaultSettings['url'].$url;
// if (in_array($type, ['POST', 'PUT'])) {
// echo '<pre>';
// print_r($this->params);
// echo $type.' '.$url;
// print_r($opts);
// }
		$ch = curl_init($url);
		if (!empty($opts)) {
			curl_setopt_array($ch, $opts);
		}

		$response = json_decode(curl_exec($ch));

		$response->request = (object)$request;
		// $response->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

// if (in_array($type, ['POST', 'PUT'])) {
// print_r($response);
// $response->request = $url;
// $response->method = $type;
// $response->params = $this->params;
// die('a');
// }
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
		return array(
			'success' => false,
			'error' => array(
				'status' => $code,
				'type' => 'Bad Request',
				'userMessage' => $message,
			)
		);
	}

	/**
	 * Reset variables for a clean new request
	 *
	 */
	private function reset() {
		$this->namespace = null;
		$this->id = null;
		$this->action = null;
		$this->methodOverride = null;
		$this->params = array();
	}

}
