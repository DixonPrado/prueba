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
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\Form as RL_Form;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\ShowOn as RL_ShowOn;

class SimpleCategoriesField extends RL_FormField
{
	protected function getInput()
	{
		$categories = $this->getOptions();
		$options    = parent::getOptions();

		if ($this->get('show_none', 1))
		{
			$options[] = JHtml::_('select.option', '', '- ' . JText::_('JNONE') . ' -');
		}

		if ($this->get('show_new', 1))
		{
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('RL_NEW_CATEGORY') . ' -');
		}

		$options = array_merge($options, $categories);

		if ( ! $this->get('show_new', 1))
		{
			$data            = $this->getLayoutData();
			$data['options'] = $options;

			return $this->getRenderer('joomla.form.field.list')->render($data);
		}

		RL_Document::script('regularlabs.simplecategories');

		$selectlist = RL_Form::selectList(
			$options,
			$this->getName($this->fieldname . '_select'),
			$this->value,
			$this->getId('', $this->fieldname . '_select')
		);

		$html = [];

		$html[] = '<div class="rl_simplecategory">';

		$html[] = '<div class="rl_simplecategory_select">' . $selectlist . '</div>';

		$html[] = RL_ShowOn::show(
			'<div class="rl_simplecategory_new">'
			. '<input type="text" id="' . $this->id . '_new" class="form-control" value="" placeholder="' . JText::_('RL_NEW_CATEGORY_ENTER') . '">'
			. '</div>',
			$this->fieldname . '_select:-1', $this->formControl
		);

		$html[] = '<input type="hidden" class="rl_simplecategory_value" id="' . $this->id . '" name="' . $this->name . '" value="' . $this->value . '" />';

		$html[] = '</div>';

		return implode('', $html);
	}

	protected function getOptions()
	{
		$table = $this->get('table');

		if ( ! $table)
		{
			return [];
		}

		// Get the user groups from the database.
		$query = $this->db->getQuery(true)
			->select([
				$this->db->quoteName('category', 'value'),
				$this->db->quoteName('category', 'text'),
			])
			->from($this->db->quoteName('#__' . $table))
			->where($this->db->quoteName('category') . ' != ' . $this->db->quote(''))
			->group($this->db->quoteName('category'))
			->order($this->db->quoteName('category') . ' ASC');
		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}
}
