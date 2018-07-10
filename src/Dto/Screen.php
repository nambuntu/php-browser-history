<?php

namespace Dto;

/**
 * Data transfer object to store a flow of user access screen or a single screen.
 *
 * @author namnvhue
 *        
 */
class Screen {
	private $id;
	private $prev;
	private $next;
	
	/**
	 * Default constructor.
	 *
	 * @param unknown $id
	 *        	: current screen
	 * @param unknown $prev:
	 *        	previous screen
	 * @param unknown $next:
	 *        	next screen (this is not necessary a URI, can be a number to indicate history back stepping.
	 */
	public function __construct($id, $prev = NULL, $next = NULL) {
		$this->id = $id;
		$this->prev = $prev;
		$this->next = $next;
	}
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getPrev() {
		return $this->prev;
	}
	public function setPrev($prev) {
		$this->prev = $prev;
		return $this;
	}
	public function getNext() {
		return $this->next;
	}
	public function setNext($next) {
		$this->next = $next;
		return $this;
	}
	public function toString() {
		return "current:$this->id;prev:$this->prev;next:$this->next";
	}
}