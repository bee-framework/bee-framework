<?php
namespace Bee\Persistence\Doctrine2;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JsonSerializable;

/**
 * Class JsonSerializablePaginator
 * @package Bee\Tools\Entities
 */
class JsonSerializablePaginator extends Paginator implements JsonSerializable {

    /**
     * @return array
     */
    function jsonSerialize() {
		return $this->getIterator()->getArrayCopy();
	}
} 