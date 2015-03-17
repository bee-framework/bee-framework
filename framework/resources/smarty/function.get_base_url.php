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
function smarty_function_get_base_url(array $params, Smarty_Internal_Template $template) {
	return Bee\Utils\Env::getBaseUrl();
}