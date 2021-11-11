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
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\ShowOn as RL_ShowOn;
use RegularLabs\Library\StringHelper as RL_String;

class ConditionSelectionField extends RL_FormField
{
	protected function closeShowOn()
	{
		return RL_ShowOn::close();
	}

	protected function getInput()
	{
		$this->value     = (int) $this->value;
		$label           = $this->get('label');
		$param_name      = $this->get('name');
		$use_main_switch = $this->get('use_main_switch', 1);
		$showclose       = $this->get('showclose', 0);

		$html = [];

		if ( ! $label)
		{
			if ($use_main_switch)
			{
				$html[] = $this->closeShowOn();
			}

			$html[] = $this->closeShowOn();

			return '</div><div>' . implode('', $html);
		}

		RL_Document::script('regularlabs.admin-form');

		$label = RL_String::html_entity_decoder(JText::_($label));

		$html[] = '</div>';

		if ($use_main_switch)
		{
			$html[] = $this->openShowOn('show_conditions:1[OR]show_assignments:1[OR]' . $param_name . ':1,2');
		}

		$class = 'pb-0 w-100 rl-panel';
		switch ($this->value)
		{
			case 2:
				$class .= ' rl-panel-error';
				break;
			case 1:
				$class .= ' rl-panel-success';
				break;
			default:
				$class .= ' rl-panel-info';
				break;
		}

		$user = JFactory::getApplication()->getIdentity() ?: JFactory::getUser();

		$html[] = '<div class="' . $class . '">';
		if ($showclose && $user->authorise('core.admin'))
		{
			$html[] = '<button type="button" class="close" aria-label="Close">&times;</button>';
		}

		$html[] = '<div class="control-group">';

		$html[] = '<div class="control-label">';
		$html[] = '<label for="' . $this->id . '">' . $label . '</label>';
		$html[] = '</div>';

		$html[] = '<div class="controls">';

		$buttons = [
			'class'         => 'btn-group rl-btn-group',
			'id'            => $this->id,
			'name'          => $this->name,
			'label'         => $this->label,
			'value'         => (string) $this->value,
			'readonly'      => $this->readonly,
			'disabled'      => $this->disabled,
			'required'      => $this->required,
			'dataAttribute' => '',
			'options'       => [
				(object) [
					'value'   => 0,
					'text'    => JText::_('RL_IGNORE'),
					'onclick' => 'RegularLabs.AdminForm.setToggleTitleClass(this, 0)',
					'class'   => 'btn btn-outline-secondary',
				],
				(object) [
					'value'   => 1,
					'text'    => JText::_('RL_INCLUDE'),
					'onclick' => 'RegularLabs.AdminForm.setToggleTitleClass(this, 1)',
					'class'   => 'btn btn-outline-success',
				],
				(object) [
					'value'   => 2,
					'text'    => JText::_('RL_EXCLUDE'),
					'onclick' => 'RegularLabs.AdminForm.setToggleTitleClass(this, 2)',
					'class'   => 'btn btn-outline-danger',
				],
			],
		];

		$html[] = $this->getRenderer('joomla.form.field.radio.buttons')->render($buttons);

		$html[] = '</div>';

		$html[] = '</div>';

		$html[] = $this->openShowOn($param_name . ':1,2');

		$html[] = '<div><div>';

		return '</div>' . implode('', $html);
	}

	protected function getLabel()
	{
		return '';
	}

	protected function openShowOn($condition = '')
	{
		if ( ! $condition)
		{
			return $this->closeShowon();
		}

		$formControl = $this->get('form', $this->formControl);
		$formControl = $formControl == 'root' ? '' : $formControl;

		if ($this->group)
		{
			$formControl .= '[' . $this->group . ']';
		}

		return RL_ShowOn::open($condition, $formControl);
	}
}
