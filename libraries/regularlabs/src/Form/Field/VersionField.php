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
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Version as RL_Version;

class VersionField extends RL_FormField
{
	protected function getInput()
	{
		$extension = $this->get('extension');
		$xml       = $this->get('xml');

		if ( ! $xml && $this->form->getValue('element'))
		{
			if ($this->form->getValue('folder'))
			{
				$xml = 'plugins/' . $this->form->getValue('folder') . '/' . $this->form->getValue('element') . '/' . $this->form->getValue('element') . '.xml';
			}
			else
			{
				$xml = 'administrator/modules/' . $this->form->getValue('element') . '/' . $this->form->getValue('element') . '.xml';
			}
			if ( ! file_exists(JPATH_SITE . '/' . $xml))
			{
				return '';
			}
		}

		if (empty($extension) || empty($xml))
		{
			return '';
		}

		$user      = JFactory::getApplication()->getIdentity() ?: JFactory::getUser();
		$authorise = $user->authorise('core.manage', 'com_installer');

		if ( ! $authorise)
		{
			return '';
		}

		return RL_Version::getMessage($extension);
	}

	protected function getLabel()
	{
		return '';
	}
}
