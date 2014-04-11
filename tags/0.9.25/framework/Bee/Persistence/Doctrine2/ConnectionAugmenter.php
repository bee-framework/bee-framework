<?php
namespace Bee\Persistence\Doctrine2;
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