<?php
  defined('_JEXEC') or die;
  $heroAnimation  = $helper->get('hero-animation');
  $heroTextAlign  = $helper->get('hero-text-align');
  $heroHeading    = $helper->get('hero-heading');
  $heroIntro      = $helper->get('hero-intro');
  $btnText        = $helper->get('hero-text');
  $btnLink        = $helper->get('hero-link');
  $btnClass       = $helper->get('hero-class');
  $mod            = $module->id; 
  $headingtag     = $helper->get('hero-content-heading-tag');
?>

<div id="acm-hero-<?php echo $mod; ?>" class="acm-hero style-1 <?php echo $heroTextAlign; ?> <?php if( trim($heroHeading) ) echo ' show-intro'; ?>">

  <div class="hero-content">
    <?php if( trim($heroHeading)) : ?>
    <<?php echo $headingtag?> class="hero-heading animated" data-animated-type="<?php echo $heroAnimation; ?>">
      <?php echo $heroHeading; ?>
    </<?php echo $headingtag?>>
    <?php endif; ?>
    
    <?php if( trim($heroIntro)) : ?>
    <p class="lead hero-intro animated delay-1s" data-animated-type="<?php echo $heroAnimation; ?>">
      <?php echo $heroIntro; ?>
    </p>
    <?php endif; ?>
    
    <?php if( trim($btnText)) : ?>
    <div class="hero-btn-actions animated delay-2s" data-animated-type="<?php echo $heroAnimation; ?>">
      <a class="btn btn-lg <?php echo $btnClass; ?>" href="<?php echo trim($btnLink); ?>" title="<?php echo trim($btnText); ?>">
        <?php echo trim($btnText); ?> <i class="fas fa-long-arrow-alt-right"></i>
      </a>
    </div>
    <?php endif; ?>
  </div>

</div>