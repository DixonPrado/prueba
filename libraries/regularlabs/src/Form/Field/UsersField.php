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

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Form\FormField as RL_FormField;

class UsersField extends RL_FormField
{
	public $attributes     = [
		'show_current' => false,
	];
	public $is_select_list = true;
	public $use_ajax       = true;

	protected function getListOptions($attributes)
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(*)')
			->from('#__users AS u');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$query->clear('select')
			->select('u.name, u.username, u.id, u.block as disabled')
			->order('name');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		$list = array_map(function ($item) {
			if ($item->disabled)
			{
				$item->name .= ' (' . JText::_('JDISABLED') . ')';
			}

			return $item;
		}, $list);

		$options = $this->getOptionsByList($list, ['username', 'id']);

		if ( ! empty($attributes['show_current']))
		{
			array_unshift($options, JHtml::_('select.option', 'current', '- ' . JText::_('RL_CURRENT_USER') . ' -'));
		}

		return $options;
	}
}
