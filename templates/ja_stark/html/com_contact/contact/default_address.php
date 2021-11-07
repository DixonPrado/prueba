<?php
/**
 * ------------------------------------------------------------------------
 * JA Focus Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<dl class="contact-address dl-horizontal" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
	<?php if (($this->params->get('address_check') > 0) &&
		($this->item->address || $this->item->suburb  || $this->item->state || $this->item->country || $this->item->postcode)) : ?>
		<?php if ($this->params->get('address_check') > 0) : ?>
			<dt>
				<span class="<?php echo $this->params->get('marker_class'); ?>" >
					<?php echo $this->params->get('marker_address'); ?>
				</span>
			</dt>
		<?php endif; ?>

		<?php if ($this->item->address && $this->params->get('show_street_address')) : ?>
			<dd>
				<span class="contact-street" itemprop="streetAddress">
					<span class="fa fa-map-marker"> </span><?php echo $this->item->address .'<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>

		<?php if ($this->item->suburb && $this->params->get('show_suburb')) : ?>
			<dd>
				<span class="contact-suburb" itemprop="addressLocality">
					<span class="fa fa-location-arrow"></span><?php echo $this->item->suburb .'<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->item->state && $this->params->get('show_state')) : ?>
			<dd>
				<span class="contact-state" itemprop="addressRegion">
					<span class="fa fa-location-arrow"></span><?php echo $this->item->state . '<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->item->postcode && $this->params->get('show_postcode')) : ?>
			<dd>
				<span class="contact-postcode" itemprop="postalCode">
					<span class="fa fa-magic"></span><?php echo $this->item->postcode .'<br/>'; ?>
				</span>
			</dd>
		<?php endif; ?>
		<?php if ($this->item->country && $this->params->get('show_country')) : ?>
		<dd>
			<span class="contact-country" itemprop="addressCountry">
				<span class="fa fa-building-o"></span><?php echo $this->item->country .'<br/>'; ?>
			</span>
		</dd>
		<?php endif; ?>
	<?php endif; ?>

<?php if ($this->item->email_to && $this->params->get('show_email')) : ?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>" itemprop="email">
			<?php echo nl2br($this->params->get('marker_email')); ?>
		</span>
	</dt>
	<dd>
		<span class="contact-emailto">
			<span class="fa fa-envelope-o"></span><?php echo $this->item->email_to; ?>
		</span>
	</dd>
<?php endif; ?>

<?php if ($this->item->telephone && $this->params->get('show_telephone')) : ?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<?php echo $this->params->get('marker_telephone'); ?>
		</span>
	</dt>
	<dd>
		<span class="contact-telephone" itemprop="telephone">
			<span class="fa fa-phone"></span><?php echo nl2br($this->item->telephone); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->item->fax && $this->params->get('show_fax')) : ?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>">
			<span class="fa fa-print"></span><?php echo $this->params->get('marker_fax'); ?>
		</span>
	</dt>
	<dd>
		<span class="contact-fax" itemprop="faxNumber">
			<span class="fa fa-print"></span><?php echo nl2br($this->item->fax); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->item->mobile && $this->params->get('show_mobile')) :?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
			<?php echo $this->params->get('marker_mobile'); ?>
		</span>
	</dt>
	<dd>
		<span class="contact-mobile" itemprop="telephone">
			<span class="fa fa-phone-square"></span><?php echo nl2br($this->item->mobile); ?>
		</span>
	</dd>
<?php endif; ?>
<?php if ($this->item->webpage && $this->params->get('show_webpage')) : ?>
	<dt>
		<span class="<?php echo $this->params->get('marker_class'); ?>" >
		</span>
	</dt>
	<dd>
		<span class="contact-webpage">
			<span class="fa fa-globe"></span><a href="<?php echo $this->item->webpage; ?>" target="_blank" itemprop="url">
			<?php echo $this->item->webpage; ?></a>
		</span>
	</dd>
<?php endif; ?>
</dl>
