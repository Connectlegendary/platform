<?php
/**
 * Base for all Plant classes
 *
 * @package seed.org.cashmusic
 * @author CASH Music
 * @link http://cashmusic.org/
 *
 * Copyright (c) 2010, CASH Music
 * Licensed under the Affero General Public License version 3.
 * See http://www.gnu.org/licenses/agpl-3.0.html
 *
 *
 */abstract class PlantBase extends SeedData {
	protected $request_method,$request_type,$action=false,$request,$response,$db_required=true;

	/**
	 * Called by SedRequest to begin action and return an instance of SeedResponse 
	 *
	 */abstract public function processRequest();
	
	/**
	 * Sets object parameters and makes database connections if needed
	 *
	 * @param {string} $request_method - 'get'/'post'/'direct'/'commandline'
	 * @param {array} $request - an associative array containing all request parameters
	 * @return void
	 */protected function plantPrep($request_method,$request) {
		$this->request_method = $request_method;
		$this->request = $request;
		if (isset($this->request['seed_action'])) {
			$this->action = strtolower($this->request['seed_action']);
		}
		$this->response = new SeedResponse();
		if ($this->db_required) {
			$this->connectDB();
		}
	}

	/**
	 * Checks the current request method ($this->request_method) against one
	 * or more strings representing allowed methods: 'get','post','direct', or
	 * 'commandline'
	 *
	 * @param {string} one or more strings specifying allowed request methods
	 * @return boolean
	 */protected function checkRequestMethodFor() {
		$args_count = func_num_args();
		if ($args_count > 0) {
			$args = func_get_args();
			foreach ($args as $arg) {
			    if ($arg == $this->request_method) {
					return true;
				}
			}
			$this->response->pushResponse(
				400, $this->request_type, $this->action,
				$this->request,
				"please try another request method, '{$this->request_method}' is not allowed"
			);
			return false;
		} else {
			// error: at least one argument must be given
			return false;
		}
	}
	
	/**
	 * Checks the request for certain required parameters, quits and returns
	 * an error response if not foun
	 *
	 * @param {string} one or more strings specifying allowed request methods
	 * @return boolean
	 */protected function requireParameters() {
		$args_count = func_num_args();
		if ($args_count > 0) {
			$args = func_get_args();
			foreach ($args as $arg) {
			    if (!isset($this->request["$arg"])) {
					$this->response->pushResponse(
						400, $this->request_type, $this->action,
						$this->request,
						"required parameter missing: '$arg'"
					);
					return false;
				}
			}
		}
		return true;
	}
} // END class 
?>