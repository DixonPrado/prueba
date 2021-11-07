<?php
/**
 * ------------------------------------------------------------------------
 * JA Stark Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$moduleclass_sfx = $params->get('moduleclass_sfx','');

if ($grouped) {
	// flat the group list
	foreach ($list as $group_name => $group) {
		foreach ($group as $item) {
			$_list[] = $item;
		}
	}
} else {
	$_list = $list;
}
$catids = $params->get('catid');
if(isset($catids) && $catids['0'] != ''){
	$catid = $catids[0];	
	$jacategoriesModel = JCategories::getInstance('content');
	$jacategory = $jacategoriesModel->get($catid);
}

?>
<div class="category-module<?php echo $moduleclass_sfx; ?> mod-blog mod-grid">
	<?php 
	//Get category info
	if(isset($jacategory)) : ?>
		<a class="category-link" href="<?php echo Route::_(ContentHelperRoute::getCategoryRoute($jacategory->id));?>">
			<?php echo Text::_('TPL_VIEW_ALL'); ?> <i class="fas fa-long-arrow-alt-right"></i>
		</a>
	<?php endif;
	//End add
	?>
	<div class="row">
		<?php $count = 0; foreach ($list as $item) : ?>
			<div class="col-md-6 col-lg-3 <?php if ($count%4 == 0) echo 'clear'; ?>">
				<?php echo JLayoutHelper::render('joomla.content.intro_image', $item); ?>

				<div class="item-info">
					<?php if ($item->displayDate) : ?>
						<span class="mod-articles-category-date">
							<?php echo $item->displayDate; ?>
						</span>
					<?php endif; ?>
					
					<?php if ($params->get('link_titles') == 1) : ?>
					<h5 class="mod-articles-category-title">	
						<a class="mod-articles-category-title-link <?php echo $item->active; ?>" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
					</h5>
					<?php else : ?>
						<?php echo $item->title; ?>
					<?php endif; ?>

					<?php if ($item->displayHits) : ?>
						<span class="mod-articles-category-hits">
							(<?php echo $item->displayHits; ?>)
						</span>
					<?php endif; ?>

					<?php if ($params->get('show_author')) : ?>
						<span class="mod-articles-category-writtenby">
							<?php echo $item->displayAuthorName; ?>
						</span>
					<?php endif; ?>

					<?php if ($item->displayCategoryTitle) : ?>
						<span class="mod-articles-category-category">
							(<?php echo $item->displayCategoryTitle; ?>)
						</span>
					<?php endif; ?>

					<?php if ($params->get('show_tags', 0) && $item->tags->itemTags) : ?>
						<div class="mod-articles-category-tags">
							<?php echo JLayoutHelper::render('joomla.content.tags', $item->tags->itemTags); ?>
						</div>
					<?php endif; ?>

					<?php if ($params->get('show_introtext')) : ?>
						<p class="mod-articles-category-introtext">
							<?php echo $item->displayIntrotext; ?>
						</p>
					<?php endif; ?>

					<?php if ($params->get('show_readmore')) : ?>
						<p class="mod-articles-category-readmore">
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php if ($item->params->get('access-view') == false) : ?>
									<?php echo Text::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
								<?php elseif ($readmore = $item->alternative_readmore) : ?>
									<?php echo $readmore; ?>
									<?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
								<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
									<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
								<?php else : ?>
									<?php echo Text::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
									<?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
								<?php endif; ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php $count++; endforeach; ?>
		</div>
</div>
