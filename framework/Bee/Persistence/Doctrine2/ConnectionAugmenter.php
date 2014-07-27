<?php
namespace Bee\Persistence\Doctrine2;
/*
 * Copyright 2008-2014 the original author or authors.
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
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;


/**
 * Class ConnectionAugmenter
 * @package Bee\Persistence\Doctrine2
 */
class ConnectionAugmenter  implements EventSubscriber {

	/**
	 * @var string
	 */
	private $postConnectScript;

	/**
	 * @param string $postConnectScript
	 */
	public function setPostConnectScript($postConnectScript) {
		$this->postConnectScript = $postConnectScript;
	}

	/**
	 * @return string
	 */
	public function getPostConnectScript() {
		return $this->postConnectScript;
	}

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function getSubscribedEvents() {
		return array(Events::postConnect);
	}

	public function postConnect(ConnectionEventArgs $eventArgs) {
		$eventArgs->getConnection()->beginTransaction();
		if(\Bee_Utils_Strings::hasText($this->getPostConnectScript())) {
			$eventArgs->getConnection()->exec($this->getPostConnectScript());
		}
		$eventArgs->getConnection()->commit();
	}
}