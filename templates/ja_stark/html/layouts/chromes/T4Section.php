<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * html5 (chosen html5 tag and font header tags)
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];


$badge          = preg_match ('/badge/', $params->get('moduleclass_sfx'))? '<span class="badge">&nbsp;</span>' : '';
$moduleTag      = htmlspecialchars($params->get('module_tag', 'div'));
$headerTag      = htmlspecialchars($params->get('header_tag', 'h4'));
$headerClass    = $params->get('header_class');
$bootstrapSize  = $params->get('bootstrap_size');
$moduleClass    = !empty($bootstrapSize) ? ' span' . (int) $bootstrapSize . '' : '';
$moduleClassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

$subtitle				= $params->get('sub-title');
$subdes					= $params->get('sub-desc');

$modlayouts			= $params->get('mod-layouts');

$firstCol				= 12;
$secondCol			= 12;

if (!$modlayouts) {
	$firstCol = 4;
	$secondCol = 8;
}

if (!empty ($module->content)) {
	$html = "<{$moduleTag} class=\"t4-module t4-section-module module{$moduleClassSfx} {$moduleClass}\" id=\"Mod{$module->id}\">" .
				"<div class=\"module-inner\">" . $badge;

	if ($module->showtitle != 0) {
		$html .= "<div class=\"row\">";
		$html .= "<div class=\"module-head-group col-md-{$firstCol}\">";
		$html .= "<{$headerTag} class=\"module-title {$headerClass}\"><span>{$module->title}</span></{$headerTag}>";
		$html .= "<h2 class=\"sub-title\">{$subtitle}</h2>";
		$html .= "<p class=\"sub-desc\">{$subdes}</p>";
		$html .= "</div>";
	}

	$html .= "<div class=\"module-ct col-md-{$secondCol}\">{$module->content}</div>";

	if ($module->showtitle != 0) {
		$html .= "</div>";
	}

	$html .= "</div></{$moduleTag}>";

	echo $html;
}