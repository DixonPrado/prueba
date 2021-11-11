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

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry as JRegistry;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\DownloadKey as RL_DownloadKey;
use RegularLabs\Library\Extension as RL_Extension;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\SystemPlugin as RL_SystemPlugin;
use RegularLabs\Library\Uri as RL_Uri;
use RegularLabs\Plugin\System\RegularLabs\QuickPage as RL_QuickPage;

if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml')
	|| ! is_file(JPATH_LIBRARIES . '/regularlabs/src/SystemPlugin.php')
	|| ! is_file(JPATH_LIBRARIES . '/regularlabs/src/DownloadKey.php')
)
{
	return;
}

RL_Language::load('plg_system_regularlabs');

$config = new JConfig;

$input = JFactory::getApplication()->input;

// Deal with error reporting when loading pages we don't want to break due to php warnings
if ( ! in_array($config->error_reporting, ['none', '0'])
	&& (
		($input->get('option') == 'com_regularlabsmanager'
			&& ($input->get('task') == 'update' || $input->get('view') == 'process')
		)
		||
		($input->getInt('rl_qp') == 1 && $input->get('url') != '')
	)
)
{
	RL_Extension::orderPluginFirst('regularlabs');

	error_reporting(E_ERROR);
}

class PlgSystemRegularLabs extends RL_SystemPlugin
{
	public $_enable_in_admin = true;
	public $_jversion        = 4;

	public function getAjaxClass($field, $field_type = '')
	{
		if (empty($field))
		{
			return false;
		}

		if ($field_type)
		{
			return $this->getFieldClass($field, $field_type);
		}

		$field = ucfirst($field);

		$file = JPATH_LIBRARIES . '/regularlabs/src/Form/Field/' . $field . 'Field.php';

		if ( ! file_exists($file))
		{
			return $this->getFieldClass($field, $field);
		}

		require_once $file;

		return 'RegularLabs\\Library\\Form\Field\\' . $field . 'Field';
	}

	public function getFieldClass($field, $field_type)
	{
		$file = JPATH_PLUGINS . '/fields/' . strtolower($field_type) . '/fields/' . strtolower($field) . '.php';

		if ( ! file_exists($file))
		{
			return false;
		}

		require_once $file;

		return 'JFormField' . ucfirst($field);
	}

	public function onAjaxRegularlabs()
	{
		$input = JFactory::getApplication()->input;

		$format = $input->getString('format', 'json');

		if ($input->getBool('getDownloadKey'))
		{
			return RL_DownloadKey::get();
		}

		if ($input->getBool('checkDownloadKey'))
		{
			return $this->checkDownloadKey();
		}

		if ($input->getBool('saveDownloadKey'))
		{
			return $this->saveDownloadKey();
		}

		$attributes = RL_Uri::getCompressedAttributes();
		$attributes = new JRegistry($attributes);

		$field      = $attributes->get('field');
		$field_type = $attributes->get('fieldtype');

		$class = $this->getAjaxClass($field, $field_type);

		if (empty($class) || ! class_exists($class))
		{
			return false;
		}

		$type = $attributes->type ?? '';

		$method = 'getAjax' . ucfirst($format) . ucfirst($type);

		$class = new $class;

		if ( ! method_exists($class, $method))
		{
			return false;
		}

		return $class->$method($attributes);
	}

	protected function loadStylesAndScripts(&$buffer)
	{
		self::addStylesheetToInstaller();
	}

	/**
	 * @return  void
	 */
	public function onAfterRoute(): void
	{
		if ( ! is_file(JPATH_LIBRARIES . '/regularlabs/regularlabs.xml'))
		{
			if (JFactory::getApplication()->isClient('administrator'))
			{
				JFactory::getApplication()->enqueueMessage('The Regular Labs Library folder is missing or incomplete: ' . JPATH_LIBRARIES . '/regularlabs', 'error');
			}

			return;
		}

		//RL_DownloadKey::update();

		//RL_SearchHelper::load();

		RL_QuickPage::render();
	}

	/**
	 * @return  void
	 */
	public function onAfterRender(): void
	{
		if ( ! RL_Document::isAdmin(true) || ! RL_Document::isHtml())
		{
			return;
		}

		$this->removeEmptyFormControlGroups();
		$this->removeFormColumnLayout();
	}

	private function addStylesheetToInstaller()
	{
		if (JFactory::getApplication()->input->getCmd('option') !== 'com_installer')
		{
			return;
		}

		if ( ! self::hasRegularLabsMessages())
		{
			return;
		}

		RL_Document::style('regularlabs.admin-form');
	}

	private function checkDownloadKey()
	{
		$key       = JFactory::getApplication()->input->getString('key');
		$extension = JFactory::getApplication()->input->getString('extension', 'all');

		return RL_DownloadKey::isValid($key, $extension);
	}

	private function hasRegularLabsMessages()
	{
		foreach (JFactory::getApplication()->getMessageQueue() as $message)
		{
			if ( ! isset($message['message'])
				|| strpos($message['message'], 'class="rl-') === false)
			{
				continue;
			}

			return true;
		}

		return false;
	}

	private function removeEmptyFormControlGroups()
	{
		$html = $this->app->getBody();

		if ($html == '')
		{
			return;
		}

		$html = RL_RegEx::replace(
			'<div class="(control-label|controls)">\s*</div>',
			'',
			$html
		);

		$html = RL_RegEx::replace(
			'<div class="control-group">\s*</div>',
			'',
			$html
		);

		$this->app->setBody($html);
	}

	private function removeFormColumnLayout()
	{
		if ($this->app->isClient('site'))
		{
			return;
		}

		if ($this->app->input->get('option') != 'com_plugins'
			|| $this->app->input->get('view') != 'plugin'
			|| $this->app->input->get('layout') != 'edit')
		{
			return;
		}

		$html = $this->app->getBody();

		if ($html == '')
		{
			return;
		}

		$html = str_replace('column-count-md-2 column-count-lg-3', '', $html);

		$this->app->setBody($html);
	}

	private function saveDownloadKey()
	{
		$key = JFactory::getApplication()->input->getString('key');

		return RL_DownloadKey::store($key);
	}
}
