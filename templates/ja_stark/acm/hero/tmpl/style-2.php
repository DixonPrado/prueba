<?php
  defined('_JEXEC') or die;
  $heroTextAlign  = $helper->get('hero-text-align');
  $count          = $helper->getRows('hero-content.hero-heading');
  
  $mod            = $module->id; 
  $headingtag     = $helper->get('hero-content-heading-tag');
?>

<div id="acm-hero-<?php echo $mod; ?>" class="acm-hero style-2 <?php echo $heroTextAlign; ?> <?php if( trim($heroHeading) ) echo ' show-intro'; ?>">
  <div class="row">
  <?php 
    for ($i=0; $i<$count; $i++) : ?>

      <?php 
        $heroHeading    = $helper->get('hero-content.hero-heading',$i);
        $heroIntro      = $helper->get('hero-content.hero-intro',$i);
        $btnText        = $helper->get('hero-content.hero-text',$i);
        $btnLink        = $helper->get('hero-content.hero-link',$i);
        $heroImg        = $helper->get('hero-content.hero-img',$i);
      ?>

      <div class="hero-content col-md-6">

        <?php if (trim($heroImg)) : ?>
          <img class="hero-img" src ="<?php echo JUri::root(true).'/'.htmlspecialchars($heroImg, ENT_COMPAT, 'UTF-8'); ?>" alt ="<?php echo $heroHeading ?>" />
        <?php endif; ?>

        <?php if( trim($heroHeading)) : ?>
        <<?php echo $headingtag?> class="hero-heading">
          <?php if( trim($btnLink)) : ?>
            <a href="<?php echo trim($btnLink); ?>" title="<?php echo trim($heroHeading); ?>">
          <?php endif; ?>
          <?php echo $heroHeading; ?>
          <?php if( trim($btnLink)) : ?>
           </a>
          <?php endif; ?>
        </<?php echo $headingtag?>>
        <?php endif; ?>
        
        <?php if( trim($heroIntro)) : ?>
        <p class="lead hero-intro">
          <?php echo $heroIntro; ?>
        </p>
        <?php endif; ?>
        
      </div>
  <?php endfor ?>
  </div>
</div>