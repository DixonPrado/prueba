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

use Joomla\CMS\Language\Multilanguage as JMultilanguage;
use Joomla\CMS\Language\Text as JText;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper as JMenusHelper;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\RegEx as RL_RegEx;

class MenuItemsField extends RL_FormField
{
	public $is_select_list  = true;
	public $use_ajax        = true;
	public $use_tree_select = true;

	protected function getOptions()
	{
		RL_Language::load('com_modules', JPATH_ADMINISTRATOR);
		$menuTypes = JMenusHelper::getMenuLinks();

		$options = [];

		foreach ($menuTypes as &$type)
		{
			$option = (object) [
				'value'      => 'type.' . $type->menutype,
				'text'       => $type->title,
				'level'      => 0,
				'class'      => 'hidechildren',
				'labelclass' => 'nav-header',
			];

			$options[] = $option;

			foreach ($type->links as $link)
			{
				$check1 = RL_RegEx::replace('[^a-z0-9]', '', strtolower($link->text));
				$check2 = RL_RegEx::replace('[^a-z0-9]', '', $link->alias);

				$text   = [];
				$text[] = $link->text;

				if ($check1 !== $check2)
				{
					$text[] = '<small class="text-muted">[' . $link->alias . ']</small>';
				}

				if (in_array($link->type, ['separator', 'heading', 'alias', 'url']))
				{
					$text[] = '<span class="badge bg-secondary">' . JText::_('COM_MODULES_MENU_ITEM_' . strtoupper($link->type)) . '</span>';
					// Don't disable, as you need to be able to select the 'Also on Child Items' option
					// $link->disable = 1;
				}

				if (JMultilanguage::isEnabled() && $link->language != '' && $link->language != '*')
				{
					$text[] = $link->language_image
						? JHtml::_('image', 'mod_languages/' . $link->language_image . '.gif', $link->language_title, ['title' => $link->language_title], true)
						: '<span class="label" title="' . $link->language_title . '">' . $link->language_sef . '</span>';
				}

				$link->text = implode(' ', $text);

				$options[] = $link;
			}
		}

		return $options;
	}
}
