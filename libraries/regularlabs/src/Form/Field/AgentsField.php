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

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Form\FormField as RL_FormField;

class AgentsField extends RL_FormField
{
	public $attributes     = [
		'group' => 'os',
	];
	public $is_select_list = true;

	protected function getListOptions($attributes)
	{
		$agents = [];
		switch ($attributes['group'])
		{
			/* OS */
			case 'os':
				$agents[] = ['Windows', 'Windows'];
				$agents[] = ['Mac OS', '#(Mac OS|Mac_PowerPC|Macintosh)#'];
				$agents[] = ['Linux', '#(Linux|X11)#'];
				$agents[] = ['Open BSD', 'OpenBSD'];
				$agents[] = ['Sun OS', 'SunOS'];
				$agents[] = ['QNX', 'QNX'];
				$agents[] = ['BeOS', 'BeOS'];
				$agents[] = ['OS/2', 'OS/2'];
				break;

			/* Browsers */
			case 'browsers':
				if ($this->get('simple') && $this->get('simple') !== 'false')
				{
					$agents[] = ['Chrome', 'Chrome'];
					$agents[] = ['Firefox', 'Firefox'];
					$agents[] = ['Edge', 'Edge'];
					$agents[] = ['Internet Explorer', 'MSIE'];
					$agents[] = ['Opera', 'Opera'];
					$agents[] = ['Safari', 'Safari'];
					break;
				}

				$agents[] = ['Chrome', 'Chrome'];
				$agents[] = ['Firefox', 'Firefox'];
				$agents[] = ['Microsoft Edge', 'MSIE Edge']; // missing MSIE is added to agent string in assignments/agents.php
				$agents[] = ['Internet Explorer', 'MSIE [0-9]']; // missing MSIE is added to agent string in assignments/agents.php
				$agents[] = ['Opera', 'Opera'];
				$agents[] = ['Safari', 'Safari'];
				break;

			/* Mobile browsers */
			case 'mobile':
				$agents[] = [JText::_('JALL'), 'mobile'];
				$agents[] = ['Android', 'Android'];
				$agents[] = ['Android Chrome', '#Android.*Chrome#'];
				$agents[] = ['Blackberry', 'Blackberry'];
				$agents[] = ['IE Mobile', 'IEMobile'];
				$agents[] = ['iPad', 'iPad'];
				$agents[] = ['iPhone', 'iPhone'];
				$agents[] = ['iPod Touch', 'iPod'];
				$agents[] = ['NetFront', 'NetFront'];
				$agents[] = ['Nokia', 'NokiaBrowser'];
				$agents[] = ['Opera Mini', 'Opera Mini'];
				$agents[] = ['Opera Mobile', 'Opera Mobi'];
				$agents[] = ['UC Browser', 'UC Browser'];
				break;

			default:
				break;
		}

		$options = [];
		foreach ($agents as $agent)
		{
			$option    = JHtml::_('select.option', $agent[1], $agent[0]);
			$options[] = $option;
		}

		return $options;
	}
}
