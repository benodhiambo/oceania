<?php
namespace App\Classes;
use Log;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;

/* API implementation of: Invenco's Site Systems Interface */

class InvSSI
{
	private $url;
	private $ipaddr;
	private $ch = null;

    public function __construct($ipaddr = null) {
		if (!empty($ipaddr)) {
			$this->ipaddr = $ipaddr; 

		} else {
			$this->ipaddr = env('EPS_IPADDR');
		}

		$this->url = "http://".$this->ipaddr;
		Log::debug('ipaddr='.$this->ipaddr);
		Log::debug('url='.$this->url);
    }


	/* $api_suffix = ":8189/api/1.0/terminals" */
	public function set_channel($api_suffix) {
		$url = $this->url.$api_suffix;
		$this->ch = curl_init($url);

		dump('url='.$url);

		//curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT, 10);
		//curl_setopt($this->ch,CURLOPT_TIMEOUT, 10);

		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}

	/* LIST OF SSI APIs:
	POST : http://<host>:8189/api/1.1/sales/processed/{transactionId}
	POST : http://<host>:8189/api/1.1/sales/processed?date={date}&time={time}


	GET : http://<host>:8189/api/1.0/transactions/sale/{transactionId}? vtid={vtid}&format={format}
	POST http://<host>:8189/api/1.0/transactions/sale/?vtid={vtid}
	POST http://<host>:8189/api/1.0/transactions/authorise/?vtid={vtid}
	GET : http://<host>:8189/api/1.0/transactions/sale/{transactionId}? vtid={vtid}&format={format}
	GET : http://<host>:8189/api/1.0/transactions/ authorise/{transactionId}?vtid={vtid}&format={format}
	DELETE http://<host>:8189/api/1.0/transactions/authorise/{transactionId}?vtid={vtid}
	POST http://<host>:8189/api/1.0/reconciliation/dayclose
	POST http://<host>:8189/api/1.0/reconciliation/dayclose?vtid=2
	GET : http://<host>:8189/api/1.0/reconciliation/dayclose/{transactionId}?format={format}
	GET : http://<host>:8189/api/1.0/terminals

	POST http://<host>:8189/api/1.0/transactions/balance?vtid={vtid}
	GET : http://<host>:8189/api/1.0/transactions/balance/{transactionId}?vtid={vtid}
&format={format}
	POST http://<host>:8189/api/1.0/transactions/reprint/?vtid={vtid}
	GET : http://<host>:8189/api/1.0/transactions/reprint/{transactionId}?vtid={vtid}&format={format}
	*/


	/*
	GET : http://<host>:8189/api/1.0/terminals
	*/
	public function get_all_terminals() {

		$api_suffix = ":8189/api/1.0/terminals";
		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;
	}

	/*
	GET : http://<host>:8189/api/1.0/terminal/status
	curl -X 'GET'   http://$EPS_IPADDR:8189/api/1.0/terminal/status \
		-H 'accept: application/json'
	*/
	public function get_all_terminal_status() {

		$api_suffix = ":8189/api/1.0/terminal/status";
		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;
	}

	/*
	GET : http://<host>:8189/api/1.1/sales/new{/noOfSales}
	*/
	public function get_completed_sales($no_of_sales=20) {
		$api_suffix = ":8189/api/1.1/sales/new/".$no_of_sales.'?format=pdb';

		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;

	}


	/*
	POST : http://<host>:8189/api/1.1/sales/processed/{saleId}
	*/
	public function process_sale($saleId) {
		$api_suffix = ":8189/api/1.1/sales/processed/".$saleId;

		$this->set_channel($api_suffix);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");

		$response = json_decode(curl_exec($this->ch));
		$http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); 

		$ret['response'] = $response;
		$ret['http_code'] = $http_code;

		return $ret;

	}


	public function close_channel() {
		if (!empty($this->ch)) {
			curl_close($this->ch);
		}
	}
}
?> 
