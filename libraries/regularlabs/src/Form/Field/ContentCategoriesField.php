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
use RegularLabs\Library\ArrayHelper as RL_ArrayHelper;
use RegularLabs\Library\Form\FormField as RL_FormField;

class ContentCategoriesField extends RL_FormField
{
	public $is_select_list  = true;
	public $use_ajax        = true;
	public $use_tree_select = true;

	protected function getOptions()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(*)')
			->from('#__categories')
			->where('extension = ' . $this->db->quote('com_content'))
			->where('parent_id > 0')
			->where('published > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$this->value = RL_ArrayHelper::toArray($this->value);

		// assemble items to the array
		$options = [];
		if ($this->get('show_ignore'))
		{
			if (in_array('-1', $this->value))
			{
				$this->value = ['-1'];
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('RL_IGNORE') . ' -');
			$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', true);
		}

		$query->clear('select')
			->select('id, title as name, level, published, language')
			->order('lft');

		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		$options = array_merge($options, $this->getOptionsByList($list, ['language'], -1));

		return $options;
	}
}
