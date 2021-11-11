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

use Joomla\CMS\HTML\HTMLHelper as JHTMLHelper;
use RegularLabs\Library\Form\FormField as RL_FormField;

class LanguagesField extends RL_FormField
{
	public $is_select_list = true;

	protected function getOptions()
	{
		$languages = JHTMLHelper::_('contentlanguage.existing');

		$value = $this->get('value', []);

		if ( ! is_array($value))
		{
			$value = [$value];
		}

		$options = [];

		foreach ($languages as $language)
		{
			if (empty($language->value))
			{
				continue;
			}

			$options[] = (object) [
				'value'    => $language->value,
				'text'     => $language->text . ' [' . $language->value . ']',
				'selected' => in_array($language->value, $value),
			];
		}

		return $options;
	}
}
