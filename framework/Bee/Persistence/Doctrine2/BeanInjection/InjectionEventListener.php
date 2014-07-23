<?php
namespace Bee\Persistence\Doctrine2\BeanInjection;

use Bee_IContext;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Class InjectionEventListener
 */
class InjectionEventListener implements EventSubscriber, \Bee_Context_Config_IContextAware {

	/**
	 * @var Reader
	 */
	private $reader;

	/**
	 * @var Bee_IContext
	 */
	private $context;

	/**
	 * @param LifecycleEventArgs $eventArgs
	 */
	public function postLoad(LifecycleEventArgs $eventArgs) {
		$entity = $eventArgs->getEntity();
		$reflClass = new ReflectionClass($entity);

		foreach($reflClass->getProperties() as $prop) {
			$annot = $this->reader->getPropertyAnnotation($prop, 'Bee\Persistence\Doctrine2\BeanInjection\Inject');
			if ($annot instanceof Inject) {
				$this->injectIntoProperty($entity, $prop, $annot);
			}
		}

		foreach($reflClass->getMethods(ReflectionProperty::IS_PUBLIC) as $method) {
			if($method->getNumberOfRequiredParameters() == 1) {
				$annot = $this->reader->getMethodAnnotation($method, 'Bee\Persistence\Doctrine2\BeanInjection\Inject');
				if ($annot instanceof Inject) {
					$this->injectIntoSetter($entity, $method, $annot);
				}
			}
		}
	}

	/**
	 * @param $entity
	 * @param ReflectionProperty $prop
	 * @param Inject $annotation
	 */
	protected function injectIntoProperty($entity, ReflectionProperty $prop, Inject $annotation) {
		$value = $this->context->getBean($annotation->beanName);
		$prop->setAccessible(true);
		$prop->setValue($entity, $value);
	}

	/**
	 * @param $entity
	 * @param ReflectionMethod $method
	 * @param Inject $annotation
	 */
	protected function injectIntoSetter($entity, ReflectionMethod $method, Inject $annotation) {
		$value = $this->context->getBean($annotation->beanName);
		$method->setAccessible(true);
		$method->invoke($entity, $value);
	}

	/**
	 * @param LoadClassMetadataEventArgs $eventArgs
	 */
//	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs) {
//		$classMata = $eventArgs->getClassMetadata();
//	}

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function getSubscribedEvents() {
		return array(
				Events::postLoad//,
//				Events::loadClassMetadata
		);
	}

	/**
	 * @return Reader
	 */
	public function getReader() {
		return $this->reader;
	}

	/**
	 * @param Reader $reader
	 */
	public function setReader(Reader $reader) {
		$this->reader = $reader;
	}

	/**
	 * @param Bee_IContext $context
	 */
	public function setBeeContext(Bee_IContext $context) {
		$this->context = $context;
	}
}