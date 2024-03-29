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

use Joomla\CMS\Language\Text as JText;

class HeaderLibraryField extends HeaderField
{
	protected function getInput()
	{
		$extensions = [
//			'Add to Menu',
//			'Advanced Module Manager',
//			'Advanced Template Manager',
//			'Articles Anywhere',
//			'Articles Field',
//			'Better Preview',
//			'Better Trash',
//			'Cache Cleaner',
//			'CDN for Joomla!',
//			'Components Anywhere',
//			'Conditional Content',
//			'Content Templater',
//			'DB Replacer',
//			'Dummy Content',
//			'Email Protector',
//			'GeoIP',
//			'IP Login',
//			'Modals',
//			'Modules Anywhere',
//			'Quick Index',
//			'Regular Labs Extension Manager',
//			'ReReplacer',
//			'Simple User Notes',
//			'Snippets',
//			'Sourcerer',
//			'Tabs & Accordions',
//			'Tooltips',
'What? Nothing!',
		];

		$list = '<ul><li>' . implode('</li><li>', $extensions) . '</li></ul>';

		$attributes = $this->element->attributes();

		$warning = '';
		if (isset($attributes['warning']))
		{
			$warning = '<div class="alert alert-danger">' . JText::_($attributes['warning']) . '</div>';
		}

		$this->element->attributes()['description'] = JText::sprintf($attributes['description'], $warning, $list);

		return parent::getInput();
	}
}
