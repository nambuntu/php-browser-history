<?php

namespace Service\History;

use Dto\Screen;
use Service\Cache\CacheService;
use function is_array;

/**
 * Navigation service to manage different redirect situation and contexts.
 *
 * @author namnvhue
 *        
 */
class NavigationService {
	const KEY_PREFIX = 'my-history-';
	
	/**
	 *
	 * @var NavigationService
	 */
	private static $instance;
	private $session_id;
	private $history;
	private $key;
	private $cacheService;
	
	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->cacheService = CacheService::getInstance();
		$this->init();
	}
	
	/**
	 * Initialize for next action.
	 */
	public function init() {
		$this->session_id = session_id();
		$this->user = $_SESSION['user_data'];
		$this->buildKey();
		$this->history = $this->getHistory();
	}
	
	/**
	 * Create a service instance 
	 */
	public static function getInstance() {
		if(null === static::$instance) {
			static::$instance = new self();
		} else {
			static::$instance->init();
		}
		
		return static::$instance;
	}
	
	/**
	 * Only store some certain steps.
	 */
	public function addHistory(Screen $screen) {
		if(count( $this->history ) == self::KEY_PREFIX_LENGTH) {
			array_shift( $this->history );
		}
		
		$recentScreen = $this->getCurrentScreen();
		if($recentScreen != null && $screen != null) {
			// case of recent screen = current screen (refresh page), not add
			if($recentScreen->getId() == $screen->getId()) {
				return;
			}
		}
		
		$this->history[] = $screen;
		$this->storeHistory();
	}
	
	/**
	 * Get the current user history.
	 */
	public function getHistory() {
		$storage = $this->cacheService->get( $this->key );
		$this->history = array ();
		if(! empty( $storage ) && is_array($storage)) {
			foreach ( $storage as $screen ) {
				$screen = unserialize( $screen );
				$this->history[] = $screen;
			}
		}
		
		return $this->history;
	}
	
	/**
	 * Get previous screen in browsing history.
	 *
	 * @return null | Screen
	 */
	public function getRecentScreen() {
		$backStep = 1;
		$recentScreen = $this->back( $backStep );
		return $recentScreen;
	}
	
	/**
	 * Get current screen.
	 *
	 * @return Screen
	 */
	public function getCurrentScreen() {
		$historySize = count( $this->history );
		if($historySize > 0) {
			return $this->history[$historySize - 1];
		}
		
		return NULL;
	}
	
	/**
	 * Return a screen in history based on a number of back stepping number.
	 *
	 * @param unknown $step:
	 *        	number of steps to go back.
	 * @return Screen
	 */
	public function back($step) {
		$historySize = count( $this->history ) - 1;
		if($historySize - $step >= 0) {
			return $this->history[$historySize - $step];
		}
		return NULL;
	}
	
	/**
	 * Store browsing history to storage service.
	 */
	public function storeHistory() {
		$storage = array ();
		if(! empty( $this->history )) {
			foreach ( $this->history as $screen ) {
				$storage[] = serialize( $screen );
			}
		}
		$this->cacheService->set( $this->key, $storage );
	}
	
	/**
	 * History support for user or anonymous, use the session key as UID in such case.
	 */
	public function buildKey($id = NULL) {
		if(empty( $id )) {
			if(! empty( $this->user )) {
				$id = $this->user->getId();
			} else {
				$id = $this->session_id;
			}
		}
		$key = self::KEY_PREFIX . $id;
		$this->key = $key;
		
		return $this->key;
	}
	
	/**
	 * Clear user history (associate with a session id) when user logins.
	 */
	public function onLoginClearHistory() {
		if(! empty( $this->user )) {
			$id = $this->user->getId();
			$oldKey = $this->buildKey( $this->session_id );
			$this->history = $this->cacheService->get( $this->key );
			$this->buildKey( $id ); // New key
			$this->cacheService->set( $this->key, $this->history );
			// Clear the old cache.
			$this->cacheService->remove( $oldKey );
		}
	}
	
	/**
	 * Clear history
	 */
	public function clearHistory() {
		$this->cacheService->remove( $this->key );
	}
}
