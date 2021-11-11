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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * @var   array  $displayData
 * @var   int    $id
 * @var   string $name
 * @var   array  $value
 * @var   array  $options
 */

extract($displayData);
if (empty($options))
{
	return;
}

if ( ! is_array($value))
{
	$value = [$value];
}
?>

<div class="card rl-treeselect" id="rl-treeselect-<?php echo $id; ?>">
	<div class="card-header">
		<section class="d-flex align-items-center flex-wrap w-100">
			<div class="d-flex align-items-center flex-fill mb-1" role="group"
			     aria-label="<?php echo Text::_('JSELECT'); ?>">
				<?php echo Text::_('JSELECT'); ?>
				<button data-action="checkAll" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('JALL'); ?>
				</button>
				<button data-action="uncheckAll" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('JNONE'); ?>
				</button>
				<button data-action="toggleAll" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('RL_TOGGLE'); ?>
				</button>
			</div>
			<div class="d-flex align-items-center mb-1 flex-fill" role="group"
			     aria-label="<?php echo Text::_('RL_EXPAND'); ?>">
				<?php echo Text::_('RL_EXPAND'); ?>
				<button data-action="expandAll" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('JALL'); ?>
				</button>
				<button data-action="collapseAll" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('JNONE'); ?>
				</button>
			</div>
			<div class="d-flex align-items-center mb-1 flex-fill" role="group"
			     aria-label="<?php echo Text::_('JSHOW'); ?>">
				<?php echo Text::_('JSHOW'); ?>
				<button data-action="showAll" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('JALL'); ?>
				</button>
				<button data-action="showSelected" class="btn btn-secondary btn-sm mx-1" type="button">
					<?php echo Text::_('RL_SELECTED'); ?>
				</button>
			</div>
			<div role="search" class="flex-grow-1">
				<label for="treeselectfilter" class="visually-hidden">
					<?php echo Text::_('JSEARCH'); ?>
				</label>
				<input type="text" name="treeselectfilter" class="form-control search-query" autocomplete="off"
				       placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>">
			</div>
		</section>
	</div>
	<div class="card-body">
		<ul class="treeselect">
			<?php $previous_level = 0; ?>
			<?php foreach ($options

			as $i => $option) : ?>
			<?php
			$option->level = $option->level ?? 0;
			if ($previous_level < $option->level)
			{
				echo '<ul class="treeselect-sub">';
			}

			if ($previous_level > $option->level)
			{
				for ($i = 0; $i < $previous_level - $option->level; $i++)
				{
					echo '</ul></li>';
				}
			}

			$selected = in_array($option->value, $value);
			?>
			<li>
				<div class="treeselect-item">
					<?php if (empty($option->no_checkbox)) : ?>
						<input type="checkbox" class="novalidate"
						       name="<?php echo $name; ?>" id="<?php echo $id . $option->value; ?>" value="<?php echo (int) $option->value; ?>"
							<?php echo $selected ? ' checked="checked"' : ''; ?>
							<?php echo ! empty($option->disable) ? ' disabled="disabled"' : ''; ?>>
					<?php endif; ?>
					<label for="<?php echo $id . $option->value; ?>" class="">
						<span class="<?php echo ! empty($option->disable) ? ' disabled' : ''; ?><?php echo ! empty($option->heading) ? ' text-uppercase fw-bold' : ''; ?>">
							<?php echo $option->text; ?>
						</span>
						<?php if (Multilanguage::isEnabled() && $option->language != '' && $option->language != '*') : ?>
							<?php if ($option->language_image) : ?>
								<?php echo HTMLHelper::_('image', 'mod_languages/' . $option->language_image . '.gif', $option->language_title, ['title' => $option->language_title], true); ?>
							<?php else : ?>
								<?php echo '<span class="badge bg-secondary" title="' . $option->language_title . '">' . $option->language_sef . '</span>'; ?>
							<?php endif; ?>
						<?php endif; ?>
						<?php if (isset($option->published) && $option->published == 0) : ?>
							<?php echo ' <span class="badge bg-secondary">' . Text::_('JUNPUBLISHED') . '</span>'; ?>
						<?php endif; ?>
					</label>
				</div>
				<?php
				$previous_level = $option->level;
				?>
				<?php endforeach; ?>
				<?php
				for ($i = 0; $i < $previous_level; $i++)
				{
					echo '</ul></li>';
				}
				?>
		</ul>
		<joomla-alert type="warning" style="display:none"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></joomla-alert>
		<div class="sub-tree-select hidden">
			<div class="nav-hover treeselect-menu">
				<div class="dropdown">
					<button type="button" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm btn-light">
						<span class="visually-hidden"><?php echo Text::sprintf('JGLOBAL_TOGGLE_DROPDOWN'); ?></span>
					</button>
					<div class="dropdown-menu">
						<h1 class="dropdown-header"><?php echo Text::_('RL_SUBITEMS'); ?></h1>
						<button type="button" data-action="checkNested" class="dropdown-item">
							<span class="icon-check-square" aria-hidden="true"></span>
							<?php echo Text::_('RL_SELECT_ALL'); ?>
						</button>
						<button type="button" data-action="uncheckNested" class="dropdown-item">
							<span class="icon-square" aria-hidden="true"></span>
							<?php echo Text::_('RL_UNSELECT_ALL'); ?>
						</button>
						<button type="button" data-action="toggleNested" class="dropdown-item">
							<span class="icon-question-circle" aria-hidden="true"></span>
							<?php echo Text::_('RL_TOGGLE_SELECTION'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
