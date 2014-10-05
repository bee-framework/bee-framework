<?php
namespace Bee\Persistence\Doctrine2;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Bee\Utils\IJsonSerializable;

/**
 * Class JsonSerializablePaginator
 * @package Bee\Tools\Entities
 */
class JsonSerializablePaginator extends Paginator implements IJsonSerializable {

	public function jsonSerialize() {
		return $this->getIterator()->getArrayCopy();
	}
} 