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

use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\RegEx as RL_RegEx;

class ComponentsField extends RL_FormField
{
	public $attributes     = [
		'frontend' => true,
		'admin'    => true,
	];
	public $is_select_list = true;
	public $use_ajax       = true;

	protected function getListOptions($attributes)
	{
		$frontend = $attributes['frontend'];
		$admin    = $attributes['admin'];

		if ( ! $frontend && ! $admin)
		{
			return [];
		}

		$query = $this->db->getQuery(true)
			->select('e.name, e.element')
			->from('#__extensions AS e')
			->where('e.type = ' . $this->db->quote('component'))
			->where('e.name != ""')
			->where('e.element != ""')
			->group('e.element')
			->order('e.element, e.name');
		$this->db->setQuery($query);
		$components = $this->db->loadObjectList();

		$comps = [];
		$lang  = $this->app->getLanguage();

		foreach ($components as $component)
		{
			if (empty($component->element))
			{
				continue;
			}

			$component_folder = ($frontend ? JPATH_SITE : JPATH_ADMINISTRATOR) . '/components/' . $component->element;

			if ( ! JFolder::exists($component_folder) && $admin)
			{
				$component_folder = JPATH_ADMINISTRATOR . '/components/' . $component->element;
			}

			// return if there is no main component folder
			if ( ! JFolder::exists($component_folder))
			{
				continue;
			}

			// return if there is no view(s) folder
			if (
				! JFolder::exists($component_folder . '/src/View')
				&& ! JFolder::exists($component_folder . '/views')
				&& ! JFolder::exists($component_folder . '/view')
			)
			{
				continue;
			}

			if (strpos($component->name, ' ') === false)
			{
				// Load the core file then
				// Load extension-local file.
				$lang->load($component->element . '.sys', JPATH_BASE, null, false, false)
				|| $lang->load($component->element . '.sys', JPATH_ADMINISTRATOR . '/components/' . $component->element, null, false, false)
				|| $lang->load($component->element . '.sys', JPATH_BASE, $lang->getDefault(), false, false)
				|| $lang->load($component->element . '.sys', JPATH_ADMINISTRATOR . '/components/' . $component->element, $lang->getDefault(), false, false);

				$component->name = JText::_(strtoupper($component->name));
			}

			$comps[RL_RegEx::replace('[^a-z0-9_]', '', $component->name . '_' . $component->element)] = $component;
		}

		ksort($comps);

		$options = [];

		foreach ($comps as $component)
		{
			$options[] = JHtml::_('select.option', $component->element, $component->name);
		}

		return $options;
	}
}
