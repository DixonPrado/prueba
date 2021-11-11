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

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Form\FormField as RL_FormField;

class FieldField extends RL_FormField
{
	public $is_select_list = true;

	function getOptions()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT a.id, a.name, a.type, a.title')
			->from('#__fields AS a')
			->where('a.state = 1')
			->order('a.name');

		$db->setQuery($query);

		$fields = $db->loadObjectList();

		$options = [];

		$options[] = JHtml::_('select.option', '', '- ' . JText::_('RL_SELECT_FIELD') . ' -');

		foreach ($fields as &$field)
		{
			// Skip our own subfields type. We won't have subfields in subfields.
			if ($field->type == 'subfields' || $field->type == 'repeatable')
			{
				continue;
			}

			$options[] = JHtml::_('select.option', $field->name, ($field->title . ' [' . $field->type . ']'));
		}

		if ($this->get('show_custom'))
		{
			$options[] = JHtml::_('select.option', 'custom', '- ' . JText::_('RL_CUSTOM') . ' -');
		}

		return $options;
	}
}
