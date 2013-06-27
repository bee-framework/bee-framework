<?php
namespace Persistence\Doctrine;
	/*
	 * Copyright 2008-2010 the original author or authors.
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 *      http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 */

/**
 * User: mp
 * Date: 20.05.13
 * Time: 14:07
 */
class OrderedColorsEntity extends \Doctrine_Record {

	public function setTableDefinition() {
		$this->setTableName('ordered_colors_doctrine');
		$this->option('collate', 'utf8_unicode_ci');
		$this->option('charset', 'utf8');

		$this->hasColumn('name', 'string', 255);
		$this->hasColumn('hex_value', 'string', 10);
		$this->hasColumn('color_grp', 'int');
		$this->hasColumn('pos', 'int');

		$drvName = \Doctrine_Manager::connection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
		if($drvName == 'mysql') {
		// sqlite does generally not support ordered updates
			$this->index('grp_pos', array(
				'fields' => array(
					'color_grp',
					'pos' => array('sorting' => 'ASC')
				),
				'type' => 'unique',
			));
		}
	}

	/**
	 * @param string $hexValue
	 */
	public function setHexValue($hexValue) {
		$this->hex_value = $hexValue;
	}

	/**
	 * @return string
	 */
	public function getHexValue() {
		return $this->hex_value;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param int $colorGrp
	 */
	public function setColorGrp($colorGrp) {
		$this->color_grp = $colorGrp;
	}

	/**
	 * @return int
	 */
	public function getColorGrp() {
		return $this->color_grp;
	}

	/**
	 * @param int $pos
	 */
	public function setPos($pos) {
		$this->pos = $pos;
	}

	/**
	 * @return int
	 */
	public function getPos() {
		return $this->pos;
	}
}
