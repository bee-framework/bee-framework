<?php
/**
 * User: mp
 * Date: 30.09.13
 * Time: 23:20
 *
 * @param array $params
 * @param Smarty_Internal_Template $template
 * @return string
 */
function smarty_function_get_base_path(array $params, Smarty_Internal_Template $template) {
	$abs = array_key_exists('absolute', $params) && $params['absolute'];
	$basePath = $abs ? Bee\Utils\Env::getAbsoluteBasePath() : Bee\Utils\Env::getBasePath();
	return Bee\Utils\Strings::hasText($basePath) ? ($abs ? '' : '/') . $basePath : '';
}