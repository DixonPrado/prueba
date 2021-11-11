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

namespace RegularLabs\Plugin\EditorButton\Sourcerer;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\EditorButtonPopup as RL_EditorButtonPopup;
use RegularLabs\Library\RegEx as RL_RegEx;

class Popup extends RL_EditorButtonPopup
{
	protected $extension         = 'sourcerer';
	protected $require_core_auth = false;

	protected function loadScripts()
	{
		$editor_name = JFactory::getApplication()->input->getString('editor', 'text');
		// Remove any dangerous character to prevent cross site scripting
		$editor_name = RL_RegEx::replace('[\'\";\s]', '', $editor_name);

		RL_Document::script('sourcerer.popup');

		$script = "document.addEventListener('DOMContentLoaded', function(){RegularLabs.Sourcerer.Popup.init('" . $editor_name . "')});";
		RL_Document::scriptDeclaration($script, 'Sourcerer', true, 'after');
	}
}
