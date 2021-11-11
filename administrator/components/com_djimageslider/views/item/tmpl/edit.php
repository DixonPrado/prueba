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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;


$app = Factory::getApplication();
$input = $app->input;


$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');


// In case of modal
$isModal = $input->get('layout') === 'modal';
$layout = $isModal ? 'modal' : 'edit';
$tmpl = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
$params = JComponentHelper::getParams( 'com_djimageslider' );
?>


<form action="<?php echo JRoute::_('index.php?option=com_djimageslider&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="item-form" class="form-validate">
    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', empty($this->item->id) ? JText::_('COM_DJIMAGESLIDER_NEW') : JText::sprintf('COM_DJIMAGESLIDER_EDIT', $this->item->id)); ?>
        <div class="row">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('image'); ?></div>
            </div>
            <div style="clear:both"></div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', JText::_('COM_DJIMAGESLIDER_PUBLISHING_OPTIONS')); ?>
        <div class="row">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('publish_up'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
            </div>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
            </div>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'params', JText::_('COM_DJIMAGESLIDER_PARAMS')); ?>
            <?php echo $this->loadTemplate('params'); ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>


        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
