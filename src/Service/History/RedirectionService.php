<?php

namespace Service\History;

use Dto\Screen;

/**
 * Redirection service.
 *
 * @author namnvhue
 *        
 */
class RedirectionService {
	/**
	 *
	 * @var RedirectionService
	 */
	private static $instance;
	
	/**
	 * Constructor.
	 */
	private function __construct() {
	}
	
	/**
	 * Singleton instance.
	 */
	public static function getInstance() {
		if(null === self::$instance) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Make redirection base on predefined rules array.
	 */
	public function perform(Screen $previous = NULL, $rules = array()) {
		$navigator = NavigationService::getInstance();
		$current = $navigator->getCurrentScreen();
		if(! empty( $rules )) {
			foreach ( $rules as $screen ) { // Overwritten if any.
				$currentId = $screen->getId();
				$prevId = $screen->getPrev();
				$nextId = $screen->getNext();
				
				$conditionMatch = false;
				/* As we dont check for condition match each condition, the following checking must be in in reverse order of importance. */
				if(! empty( $current ) && startsWith( $current->getId(), $currentId ) && ! empty( $nextId )) {
					if($previous != null && ($prevId == 'any' || startsWith( $previous->getId(), $prevId ))) {
						$conditionMatch = true;
					}
					
					if($prevId == 'empty' && empty( $previous )) { // No need for further checking.
						$conditionMatch = true;
					}
				}
				
				if($conditionMatch) {
					if(is_numeric( $nextId )) { // step using history
					                            // Right now we support to go back to the screen before only, this should be a negative number.
						$nextScreen = $navigator->back( abs( $nextId ) );
						if(! empty( $nextScreen )) {
							return $nextScreen->getId();
						} else {
							return NULL;
						}
					} else { // This is a URI, just return.
						if($nextId == 'prev') {
							return $previous->getId();
						} else {
							return $nextId;
						}
					}
				}
			}
		}
		
		if(! empty( $previous )) { // Priority previous screen.
			return $previous->getId();
		} else {
			return DEFAULT_REDIRECT_AFTER_LOGIN;
		}
	}
	
}