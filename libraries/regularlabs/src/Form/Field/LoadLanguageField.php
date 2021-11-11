<?php
/**
 * @package         Regular Labs Library
 * @version         21.11.1666
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library\Form\Field;

defined('_JEXEC') or die;

use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Language as RL_Language;

class LoadLanguageField extends RL_FormField
{
	function loadLanguage($extension, $admin = 1)
	{
		if ( ! $extension)
		{
			return;
		}

		RL_Language::load($extension, $admin ? JPATH_ADMINISTRATOR : JPATH_SITE);
	}

	protected function getInput()
	{
		$extension = $this->get('extension');
		$admin     = $this->get('admin', 1);

		self::loadLanguage($extension, $admin);

		return '';
	}

	protected function getLabel()
	{
		return '';
	}
}

