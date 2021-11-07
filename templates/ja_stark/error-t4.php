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

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo JText::_('TPL_T4_ENABLED_T4_ERROR_TITLE') ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/jpages.css" type="text/css" />
</head>

<body class="t4-error-page">
  <div class="t4-error-msg">
    <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/info-circle-light.svg" alt="Info icon" />
  	<h1><?php echo JText::_('TPL_T4_ENABLED_T4_ERROR_TITLE') ?></h1>
  	<p class="error-message"><?php echo JText::_('TPL_T4_ENABLED_T4_ERROR_DESC') ?></p>

    <div class="cta-wrap">
      <h3>Resources</h3>
      <a href="#" title="<?php echo JText::_('TPL_T4_DOWNLOAD') ?>"><?php echo JText::_('TPL_T4_DOWNLOAD') ?></a>
      <a href="#" title="<?php echo JText::_('TPL_T4_DOCUMENTATION') ?>"><?php echo JText::_('TPL_T4_DOCUMENTATION') ?></a>
      <a href="#" title="<?php echo JText::_('TPL_T4_SUPPORT') ?>"><?php echo JText::_('TPL_T4_SUPPORT') ?></a>
    </div>
  </div>
</body>
</html>
