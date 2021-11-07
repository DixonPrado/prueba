<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
$article = $displayData['article'];

$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($article->publish_up > $currentDate)
  || !is_null($article->publish_down) && ($article->publish_down < $currentDate);
if(version_compare(JVERSION, "4", 'ge')):
  $tooltip = $displayData['tooltip'];
  $nowDate = strtotime(Factory::getDate());

  $icon = $article->state ? 'edit' : 'eye-slash';

  if ($isUnpublished)
  {
  	$icon = 'eye-slash';
  }
  $aria_described = 'editarticle-' . (int) $article->id;

  ?>
  <span class="icon-<?php echo $icon; ?>" aria-hidden="true"></span>
  	<?php echo Text::_('JGLOBAL_EDIT'); ?>
  <div role="tooltip" id="<?php echo $aria_described; ?>">
  	<?php echo $tooltip; ?>
  </div>
<?php else: 
  JHtml::_('bootstrap.tooltip');
  $overlib = $displayData['overlib'];
  // @deprecated  4.0  The legacy icon flag will be removed from this layout in 4.0
  $legacy  = $displayData['legacy'];


  if ($legacy)
  {
    $icon = $article->state ? 'edit.png' : 'edit_unpublished.png';

    if ($isUnpublished)
    {
      $icon = 'edit_unpublished.png';
    }
  }
  else
  {
    $icon = $article->state ? 'edit' : 'eye-close';

    if ($isUnpublished)
    {
      $icon = 'eye-close';
    }
  }

  ?>
  <?php if ($legacy) : ?>
    <?php echo JHtml::_('image', 'system/' . $icon, JText::_('JGLOBAL_EDIT'), null, true); ?>
  <?php else : ?>
    <span class="hasTooltip icon-<?php echo $icon; ?> tip" title="<?php echo JHtml::tooltipText(JText::_('COM_CONTENT_EDIT_ITEM'), $overlib, 0, 0); ?>"></span>
    <?php echo JText::_('JGLOBAL_EDIT'); ?>
  <?php endif; ?>

<?php endif; ?>