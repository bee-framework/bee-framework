<?php
use Bee\MVC\View\RedirectView;

require_once '../vendor/autoload.php';
session_start();

$requestBuilder = new \Bee\MVC\DefaultRequestBuilder();
$request = $requestBuilder->buildRequestObject();

$view = new \Bee\MVC\View\RequestStoringRedirectView();
$view->setStores(array(
		'storedRequestId' => new \Bee\MVC\Redirect\RedirectRequestStorage()
));
$view->render(array(
				RedirectView::MODEL_KEY_GET_PARAMS => array(
						'foo[ab]' => 'barab',
						'foo[cd]' => 'barcd'
				),
				RedirectView::MODEL_KEY_REDIRECT_URL => '/BeeFramework/examples/redirected.php')
);