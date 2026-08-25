<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * 2Moons number modifier plugin
 *
 * Type:     modifier<br>
 * Name:     number<br>
 * Purpose:  convert string to formated number
 *
 * @author Jan Kröpke
 * @param array $params parameters
 * @return string with compiled code
 */

function smarty_modifiercompiler_number($params, $compiler)
{
	if (isset($params[1])) {
		return 'pretty_number(' . $params[0] . ', ' . $params[1] . ')';
	}

	return 'pretty_number(' . $params[0] . ')';
}

?>
