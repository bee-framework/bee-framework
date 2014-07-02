<?php
class Bee_MVC_ViewResolver_Loopback extends Bee_MVC_ViewResolver_Basic implements Bee_Context_Config_IContextAware{

	/**
	 * Callback that supplies the owning context to a bean instance.
	 * <p>Invoked after the population of normal bean properties
	 * but before an initialization callback such as
	 * {@link InitializingBean#afterPropertiesSet()} or a custom init-method.
	 * @param Bee_IContext $context owning context (never <code>null</code>).
	 * The bean can immediately call methods on the context.
	 */
    public function setBeeContext(Bee_IContext $context) {
        $this->setContext($context);
    }
}
