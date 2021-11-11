<?php
/**
 * @version $Id$
 * @package DJ-ImageSlider
 * @subpackage DJ-ImageSlider Component
 * @copyright Copyright (C) 2017 DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 *
 * DJ-ImageSlider is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-ImageSlider is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-ImageSlider. If not, see <http://www.gnu.org/licenses/>.
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\Button\FeaturedButton;
use Joomla\CMS\Button\PublishedButton;
use Joomla\CMS\Button\TransitionButton;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;
use Joomla\Utilities\ArrayHelper;

HTMLHelper::_('behavior.multiselect');

$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));


$saveOrder = $listOrder == 'a.ordering';


if ($saveOrder && !empty($this->items)) {
    $saveOrderingUrl = 'index.php?option=com_djimageslider&task=items.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}


?>

    <form action="<?php echo JRoute::_('index.php?option=com_djimageslider&view=items'); ?>" method="post"
          name="adminForm"
          id="adminForm">
        <div class="row">
            <div class="col-md-12">
                <div id="j-main-container" class="j-main-container">
                    <?php
                    // Search tools bar
                    echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                    ?>
                    <?php if (empty($this->items)) : ?>
                        <div class="alert alert-info">
                            <span class="icon-info-circle" aria-hidden="true"></span><span
                                    class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                        </div>
                    <?php else : ?>
                        <table class="table itemList" id="articleList">
                            <caption class="visually-hidden">
                                <?php echo Text::_('COM_CONTENT_ARTICLES_TABLE_CAPTION'); ?>,
                                <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                                <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                            </caption>
                            <thead>
                            <tr>
                                <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                    <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', ''); ?>
                                </th>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                    <?php echo JText::_('COM_DJIMAGESLIDER_IMAGE'); ?>
                                </th>
                                <th scope="col" style="min-width:100px">
                                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-3 d-none d-lg-table-cell">
                                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody<?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                            <?php foreach ($this->items as $i => $item) :


                                $ordering	= ($listOrder == 'a.ordering');
                                $canCreate	= $user->authorise('core.create',		'com_djimageslider.category.'.$item->catid);
                                $canEdit	= $user->authorise('core.edit',			'com_djimageslider.category.'.$item->catid);
                                $canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canEditOwn	= true; //$user->authorise('core.edit.own',		'com_djimageslider.category.'.$item->catid) && $item->created_by == $userId;
                                $canChange	= $user->authorise('core.edit.state',	'com_djimageslider.category.'.$item->catid) && $canCheckin;

                                ?>
                                <tr class="row<?php echo $i % 2; ?>"
                                    data-draggable-group="<?php echo $item->catid; ?>"
                                >
                                    <td class="text-center d-none d-md-table-cell">
                                        <?php
                                        $iconClass = '';
                                        if (!$canChange) {
                                            $iconClass = ' inactive';
                                        } elseif (!$saveOrder) {
                                            $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                        }
                                        ?>
                                        <span class="sortable-handler<?php echo $iconClass ?>">
										<span class="icon-ellipsis-v" aria-hidden="true"></span>
									</span>
                                        <?php if ($canChange && $saveOrder) : ?>
                                            <input type="text" name="order[]" size="5"
                                                   value="<?php echo $item->ordering; ?>"
                                                   class="width-20 text-area-order hidden">
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell">
                                        <?php if ($item->image) : ?>
                                            <a class="mf-popup" href="<?php echo $item->image; ?>"><img src="<?php echo $item->image; ?>" alt="<?php echo $this->escape($item->title); ?>" style="border: 1px solid #ccc; padding: 1px; max-height: 40px; max-width: 60px;" /></a>
                                        <?php endif; ?>
                                    </td>

                                    <th scope="row" class="has-context">
                                        <div class="break-word">

                                            <?php if ($item->checked_out) : ?>
                                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
                                            <?php endif; ?>
                                            <?php if ($canEdit) : ?>
                                                <a href="<?php echo JRoute::_('index.php?option=com_djimageslider&task=item.edit&id='.(int) $item->id); ?>"
                                                   title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                                    <?php echo $this->escape($item->title); ?></a>
                                            <?php else : ?>
                                                <span title="<?php echo Text::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
                                            <?php endif; ?>
                                            <div class="small break-word">
                                                <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                            </div>
                                            <div class="small break-word">
                                                <?php
                                                $desc = strip_tags($item->description);
                                                echo substr($desc, 0, 120);
                                                if (strlen($desc) > 120) echo '...'; ?></div>
                                        </div>
                                    </th>
                                    <td class="center">
                                        <div class="btn-group">
                                            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', true, 'cb'	); ?>
                                        </div>
                                    </td>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo (empty($item->category_title) == false) ? $item->category_title : '<span style="color: red">'.JText::_('COM_DJMEDIATOOLS_UNASSIGNED').'</span>'; ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php echo (int)$item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php // load the pagination. ?>
                        <?php echo $this->pagination->getListFooter(); ?>


                    <?php endif; ?>


                    <input type="hidden" name="task" value=""/>
                    <input type="hidden" name="boxchecked" value="0"/>
                    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
                    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
                    <?php echo JHtml::_('form.token'); ?>
                </div>
            </div>
        </div>
    </form>
<?php echo DJIMAGESLIDERFOOTER; ?>