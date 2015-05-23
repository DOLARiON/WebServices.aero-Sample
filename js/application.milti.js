/**
 * @package		Travel Agency Web Services (WebServices.aero)
 * @subpackage	Js\Application
 * @copyright	Copyright (C) 2000 - 2015 Eugene Design. All rights reserved.
 * @license		GNU/GPLv2
 */

jQuery.fn.serializeAndEncode = function()
{
	return escape(this.find('select,textarea,input').serialize());
}

