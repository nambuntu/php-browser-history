<?php 

namespace Service\Cache; 

/**
* A dummy cache service, in real life each product should has it own type of cache, won't implement anything here.
*/
class CacheService {

	private static $instance;

	/**
	 * Create a service instance 
	 */
	public static function getInstance() {
		if(null === static::$instance) {
			static::$instance = new self();
		} 
		
		return static::$instance;
	}
	

	public function set($key, $value) {

	}

	public function get($key) {

	}
}