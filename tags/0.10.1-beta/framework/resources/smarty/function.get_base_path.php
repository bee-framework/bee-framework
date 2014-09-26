<?php
/**
 * User: mp
 * Date: 30.09.13
 * Time: 23:20
 *
 *
 */
function smarty_function_get_base_path(array $params, Smarty_Internal_Template $template) {
	$abs = array_key_exists('absolute', $params) && $params['absolute'];
	$basePath = $abs ? Bee_Utils_Env::getAbsoluteBasePath() : Bee_Utils_Env::getBasePath();
	return Bee_Utils_Strings::hasText($basePath) ? ($abs ? '' : '/') . $basePath : '';
}