<?php
/**
 * @package         Sourcerer
 * @version         9.0.3
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemSourcererInstallerScript extends PlgSystemSourcererInstallerScriptHelper
{
	public $alias          = 'sourcerer';
	public $extension_type = 'plugin';
	public $name           = 'SOURCERER';

	public function onAfterInstall($route)
	{
		$this->deleteJoomla3Files();

		return parent::onAfterInstall($route);
	}

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'editors-xtd');
	}

	private function deleteJoomla3Files()
	{
		$this->delete(
			[
				JPATH_SITE . '/media/' . $this->alias . '/css',
				JPATH_SITE . '/media/' . $this->alias . '/js/script.js',
				JPATH_SITE . '/media/' . $this->alias . '/js/script.min.js',
				JPATH_SITE . '/media/' . $this->alias . '/less',
				JPATH_SITE . '/plugins/system/' . $this->alias . '/src/Code.php',
				JPATH_SITE . '/plugins/system/' . $this->alias . '/vendor',
			]
		);
	}
}
