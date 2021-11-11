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

namespace RegularLabs\Library\Form;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\FileLayout as JFileLayout;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;

class Form
{
	public static function prepareSelectItem($string, $remove_first = 0)
	{
		if (empty($string))
		{
			return '';
		}

		$string = str_replace(['&nbsp;', '&#160;'], ' ', $string);
		$string = RL_RegEx::replace('- ', '  ', $string);

		for ($i = 0; $remove_first > $i; $i++)
		{
			$string = RL_RegEx::replace('^  ', '', $string, '');
		}

		if (RL_RegEx::match('^( *)(.*)$', $string, $match, ''))
		{
			[$string, $pre, $name] = $match;

			$pre = str_replace('  ', ' ·  ', $pre);
			$pre = RL_RegEx::replace('(( ·  )*) ·  ', '\1 »  ', $pre);
			$pre = str_replace('  ', ' &nbsp; ', $pre);

			$string = $pre . $name;
		}

		return $string;
	}

	/**
	 * Render a full select list
	 *
	 * @param array  $options
	 * @param string $name
	 * @param string $value
	 * @param string $id
	 * @param array  $attributes
	 * @param bool   $treeselect
	 *
	 * @return string
	 */
	public static function selectList($options, $name, $value, $id, $attributes = [], $treeselect = false)
	{
		if (empty($options))
		{
			return '<fieldset class="radio">' . JText::_('RL_NO_ITEMS_FOUND') . '</fieldset>';
		}

		$params = RL_Parameters::getPlugin('regularlabs');

		if ( ! is_array($value))
		{
			$value = explode(',', $value);
		}

		if (count($value) === 1 && strpos($value[0], ',') !== false)
		{
			$value = explode(',', $value[0]);
		}

		$count = 0;
		if ($options != -1)
		{
			foreach ($options as $option)
			{
				$count++;
				if (isset($option->links))
				{
					$count += count($option->links);
				}
				if ($count > $params->max_list_count)
				{
					break;
				}
			}
		}

		if ($options == -1 || $count > $params->max_list_count)
		{
			if (is_array($value))
			{
				$value = implode(',', $value);
			}
			if ( ! $value)
			{
				$input = '<textarea name="' . $name . '" id="' . $id . '" cols="40" rows="5">' . $value . '</textarea>';
			}
			else
			{
				$input = '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" size="60">';
			}

			$plugin = JPluginHelper::getPlugin('system', 'regularlabs');

			$url = ! empty($plugin->id)
				? 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $plugin->id
				: 'index.php?option=com_plugins&filter_folder=&filter_search=Regular%20Labs%20Library';

			$label   = JText::_('RL_ITEM_IDS');
			$text    = JText::_('RL_MAX_LIST_COUNT_INCREASE');
			$tooltip = JText::_('RL_MAX_LIST_COUNT_INCREASE_DESC,' . $params->max_list_count . ',RL_MAX_LIST_COUNT');
			$link    = '<a href="' . $url . '" target="_blank" id="' . $id . '_msg"'
				. ' class="hasPopover" title="' . $text . '" data-content="' . htmlentities($tooltip) . '">'
				. '<span class="icon icon-cog"></span>'
				. $text
				. '</a>';

			$script = 'jQuery("#' . $id . '_msg").popover({"html": true,"trigger": "hover focus","container": "body"})';

			return '<fieldset class="radio">'
				. '<label for="' . $id . '">' . $label . ':</label>'
				. $input
				. '<br><small>' . $link . '</small>'
				. '</fieldset>'
				. '<script>' . $script . '</script>';
		}

		$data = array_merge(
			compact('id', 'name', 'value', 'options'),
			[
				'multiple'      => false,
				'autofocus'     => false,
				'onchange'      => '',
				'dataAttribute' => '',
				'readonly'      => false,
				'disabled'      => '',
				'hint'          => false,
				'required'      => false,
			],
			$attributes
		);

		$renderer = new JFileLayout(
			$treeselect
				? 'regularlabs.form.field.treeselect'
				: (
			$data['multiple']
				? 'joomla.form.field.list-fancy-select'
				: 'joomla.form.field.list'
			),
			$treeselect ? JPATH_SITE . '/libraries/regularlabs/layouts' : null
		);

		return $renderer->render($data);
	}

	/**
	 * Render a select list loaded via Ajax
	 *
	 * @param string $field
	 * @param string $name
	 * @param string $value
	 * @param string $id
	 * @param array  $attributes
	 * @param bool   $treeselect
	 *
	 * @return string
	 */
	public static function selectListAjax($field, $name, $value, $id, $attributes = [], $treeselect = false)
	{
		RL_Document::style('regularlabs.admin-form');
		RL_Document::script('regularlabs.admin-form');
		RL_Document::script('regularlabs.regular');
		RL_Document::script('regularlabs.script');

		if ($treeselect)
		{
			RL_Document::script('regularlabs.treeselect');
		}

		if (is_array($value))
		{
			$value = implode(',', $value);
		}

		$ajax_data = [
			'field'      => $field,
			'value'      => $value,
			'attributes' => $attributes,
			'treeselect' => $treeselect,
		];

		return '<div>'
			. '<textarea name="' . $name . '" id="' . $id . '" cols="40" rows="5" class="w-100"'
			. ' data-rl-ajax="' . htmlspecialchars(json_encode($ajax_data)) . '">' . $value . '</textarea>'
			. '<div class="rl-spinner"></div>'
			. '</div>';
	}

//	/**
//	 * Render a simple select list
//	 *
//	 * @param array  $options
//	 * @param        $string $name
//	 * @param string $value
//	 * @param string $id
//	 * @param int    $size
//	 * @param bool   $multiple
//	 *
//	 * @return string
//	 */
//	public static function selectListSimple(&$options, $name, $value, $id, $size = 0, $multiple = false)
//	{
//		return self::selectlist($options, $name, $value, $id, $size, $multiple, true);
//	}

//	/**
//	 * Render a simple select list loaded via Ajax
//	 *
//	 * @param string $field
//	 * @param string $name
//	 * @param string $value
//	 * @param string $id
//	 * @param array  $attributes
//	 *
//	 * @return string
//	 */
//	public static function selectListSimpleAjax($field, $name, $value, $id, $attributes = [])
//	{
//		return self::selectListAjax($field, $name, $value, $id, $attributes, true);
//	}
//	/**
//	 * Replace style placeholders with actual style attributes
//	 *
//	 * @param string $string
//	 *
//	 * @return string
//	 */
//	private static function handlePreparedStyles($string)
//	{
//		// No placeholders found
//		if (strpos($string, '[[:') === false)
//		{
//			return $string;
//		}
//
//		// Doing following replacement in 3 steps to prevent the Regular Expressions engine from exploding
//
//		// Replace style tags right after the html tags
//		$string = RegEx::replace(
//			';?:\]\]\s*\[\[:',
//			';',
//			$string
//		);
//		$string = RegEx::replace(
//			'>\s*\[\[\:(.*?)\:\]\]',
//			' style="\1">',
//			$string
//		);
//
//		// No more placeholders found
//		if (strpos($string, '[[:') === false)
//		{
//			return $string;
//		}
//
//		// Replace style tags prepended with a minus and any amount of whitespace: '- '
//		$string = RegEx::replace(
//			'>((?:-\s*)+)\[\[\:(.*?)\:\]\]',
//			' style="\2">\1',
//			$string
//		);
//
//		// No more placeholders found
//		if (strpos($string, '[[:') === false)
//		{
//			return $string;
//		}
//
//		// Replace style tags prepended with whitespace, a minus and any amount of whitespace: ' - '
//		$string = RegEx::replace(
//			'>((?:\s+-\s*)+)\[\[\:(.*?)\:\]\]',
//			' style="\2">\1',
//			$string
//		);
//
//		return $string;
//	}
}
