<?php
/**
 * ------------------------------------------------------------------------
 * JA Extension Manager Component
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JaextmanagerController extends JAEMController
{
	protected $default_view = 'default';

	public function getLink()
	{
		return "index.php?option=com_jaextmanager";
	}
}
