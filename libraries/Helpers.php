<?php
/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	TA\Helpers
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

// No direct access.
defined('_TAEXEC') or die;

class Helpers
{
	public static function _debug()
	{
		if (func_num_args() === 0)
			return;

		// Get params
		$Params = func_get_args();
		$Output = array();

		foreach ($Params as $Var)
			$Output[] = '<pre style="text-align:left">('.gettype($Var).') '.print_r($Var, TRUE).'</pre>';

		return implode("\n", $Output);
	}

	public static function UserIP()
	{
		return (empty($_SERVER['HTTP_CLIENT_IP'])?
				(empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['REMOTE_ADDR']:$_SERVER ['HTTP_X_FORWARDED_FOR'])
					:$_SERVER ['HTTP_CLIENT_IP']);
	}
}

