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

class TemplatesField extends RL_FormField
{
	public $is_select_list  = true;
	public $use_ajax        = true;
	public $use_tree_select = true;

	protected function getOptions()
	{
		$options = [];

		$templates = $this->getTemplates();

		foreach ($templates as $styles)
		{
			$level = 0;
			foreach ($styles as $style)
			{
				$style->level = $level;
				$options[]    = $style;

				if (count($styles) <= 2)
				{
					break;
				}

				$level = 1;
			}
		}

		return $options;
	}

	protected function getTemplates()
	{
		$query = $this->db->getQuery(true)
			->select('s.id, s.title, e.name as name, s.template')
			->from('#__template_styles as s')
			->where('s.client_id = 0')
			->join('LEFT', '#__extensions as e on e.element=s.template')
			->where('e.enabled=1')
			->where($this->db->quoteName('e.type') . '=' . $this->db->quote('template'))
			->order('s.template')
			->order('s.title');

		$this->db->setQuery($query);
		$styles = $this->db->loadObjectList();

		if (empty($styles))
		{
			return [];
		}

		$lang = $this->app->getLanguage();

		$groups = [];

		foreach ($styles as $style)
		{
			$template = $style->template;
			$lang->load('tpl_' . $template . '.sys', JPATH_SITE)
			|| $lang->load('tpl_' . $template . '.sys', JPATH_SITE . '/templates/' . $template);
			$name = JText::_($style->name);

			if ( ! isset($groups[$template]))
			{
				$groups[$template]   = [];
				$groups[$template][] = JHtml::_('select.option', $template, $name);
			}

			$groups[$template][] = JHtml::_('select.option', $template . '--' . $style->id, $style->title);
		}

		return $groups;
	}
}
