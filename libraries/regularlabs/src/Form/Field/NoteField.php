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

class NoteField extends RL_FormField
{
	protected function getInput()
	{
		if (empty($this->element['label']))
		{
			return '';
		}

		return $this->getNote();
	}

	protected function getLabel()
	{
		if ( ! empty($this->element['label']))
		{
			return parent::getLabel();
		}

		$note = $this->getNote();

		if (empty($note))
		{
			return '';
		}

		return '</div><div>' . $note;
	}

	protected function getNote()
	{
		if (empty($this->element['title']) && empty($this->element['text']))
		{
			return '';
		}

		$title   = $this->prepareText($this->element['title']);
		$text    = $this->prepareText($this->element['text']);
		$heading = $this->element['heading'] ?: 'h4';
		$class   = ! empty($this->element['class']) ? ' class="' . $this->element['class'] . '"' : '';

		$html = [];

		$html[] = ! empty($title) ? '<' . $heading . '>' . $title . '</' . $heading . '>' : '';
		$html[] = $text ?: '';

		return '<div ' . $class . '>' . implode('', $html) . '</div>';
	}
}
