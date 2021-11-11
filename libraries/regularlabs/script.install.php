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

defined('_JEXEC') or die;

if ( ! class_exists('RegularLabsInstallerScript'))
{
	require_once __DIR__ . '/script.install.helper.php';

	class RegularLabsInstallerScript extends RegularLabsInstallerScriptHelper
	{
		public $alias          = 'regularlabs';
		public $extension_type = 'library';
		public $name           = 'Regular Labs Library';
		public $soft_break     = true;

		public function onBeforeInstall($route)
		{
			if ( ! parent::onBeforeInstall($route))
			{
				return false;
			}

			return $this->isNewer();
		}

		public function onAfterInstall($route)
		{
			$this->deleteJoomla3Files();

			return parent::onAfterInstall($route);
		}

		public function uninstall($adapter)
		{
			$this->uninstallPlugin($this->extname, 'system');
		}

		private function deleteJoomla3Files()
		{
			$this->delete(
				[
					JPATH_SITE . '/libraries/' . $this->alias . '/fields',
					JPATH_SITE . '/libraries/' . $this->alias . '/helpers',
					JPATH_SITE . '/libraries/' . $this->alias . '/layouts/range.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/layouts/repeatable-table',
					JPATH_SITE . '/libraries/' . $this->alias . '/layouts/repeatable-table.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/CacheNew.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/Database.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/EditorButton.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/EditorButtonHelper.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/Field.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/FieldGroup.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/Form.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/Log.php',
					JPATH_SITE . '/libraries/' . $this->alias . '/src/ParametersNew.php',
					JPATH_SITE . '/media/' . $this->alias . '/css/codemirror.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/codemirror.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/color.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/color.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/colorpicker.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/colorpicker.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/form.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/form.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/frontend.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/frontend.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/multiselect.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/multiselect.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/popup.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/popup.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/style.css',
					JPATH_SITE . '/media/' . $this->alias . '/css/style.min.css',
					JPATH_SITE . '/media/' . $this->alias . '/fonts',
					JPATH_SITE . '/media/' . $this->alias . '/images/icon-color.png',
					JPATH_SITE . '/media/' . $this->alias . '/images/logo.png',
					JPATH_SITE . '/media/' . $this->alias . '/images/minicolors.png',
					JPATH_SITE . '/media/' . $this->alias . '/js/codemirror.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/codemirror.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/color.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/color.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/colorpicker.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/colorpicker.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/form.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/form.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/jquery.cookie.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/jquery.cookie.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/multiselect.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/multiselect.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/textareaplus.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/textareaplus.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/toggler.js',
					JPATH_SITE . '/media/' . $this->alias . '/js/toggler.min.js',
					JPATH_SITE . '/media/' . $this->alias . '/less',
				]
			);
		}
	}
}
