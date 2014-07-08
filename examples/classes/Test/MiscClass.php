<?php

namespace Test;


/**
 * Class MiscClass
 * @package Test
 */
class MiscClass {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @param int $id
	 */
	public function __construct($id) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
} 