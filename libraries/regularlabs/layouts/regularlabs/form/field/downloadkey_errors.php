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

use Joomla\CMS\Language\Text as JText;

defined('_JEXEC') or die;

/**
 * @var   array  $displayData
 * @var   int    $id
 * @var   string $extension
 */

extract($displayData);

$extension = $extension ?: 'all';

?>

<div class="key-errors">
	<div class="key-error-empty alert alert-danger hidden">
		<?php echo JText::sprintf(JText::_('RL_DOWNLOAD_KEY_ERROR_EMPTY'), JText::_(strtoupper($extension)), '<a href="https://regularlabs.com/download-keys" target="_blank">', '</a>'); ?>
	</div>
	<div class="key-error-invalid alert alert-danger hidden">
		<?php echo JText::sprintf(JText::_('RL_DOWNLOAD_KEY_ERROR_INVALID'), '<a href="https://regularlabs.com/download-keys" target="_blank">', '</a>'); ?>
	</div>
	<div class="key-error-expired alert alert-danger hidden">
		<?php echo JText::sprintf(JText::_('RL_DOWNLOAD_KEY_ERROR_EXPIRED'), '<a href="https://regularlabs.com/purchase" target="_blank">', '</a>'); ?>
	</div>
	<div class="key-error-local alert alert-danger hidden">
		<?php echo JText::_('RL_DOWNLOAD_KEY_ERROR_LOCAL'); ?>
	</div>
	<div class="key-error-external alert alert-danger hidden">
		<?php echo JText::sprintf(JText::_('RL_DOWNLOAD_KEY_ERROR_EXTERNAL'), '<a href="https://regularlabs.com/forum" target="_blank">', '</a>'); ?>
	</div>
</div>
