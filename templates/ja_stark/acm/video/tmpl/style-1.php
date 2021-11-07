<?php
  defined('_JEXEC') or die;
  $btnLink        = $helper->get('hero-link');
  $btnIcon        = $helper->get('hero-icon');
  $btnClass       = $helper->get('hero-class');
  $btnAction      = $helper->get('hero-action');
  $heroBg         = $helper->get('hero-img');
  $mod            = $module->id; 
?>

<div id="acm-video-<?php echo $mod; ?>" class="acm-hero style-1" style="background: url(<?php echo $heroBg; ?>) no-repeat;">
  <a class="btn <?php echo $btnClass ? $btnClass : 'btn-play'; ?> <?php echo $btnAction ? 'html5'.$btnAction : ''; ?>" data-group="myvideo-<?php echo $mod; ?>" href="<?php echo trim($btnLink); ?>" title="<?php echo trim($btnText); ?>">
    <?php if(trim($btnIcon)) : ?>
      <span class="<?php echo trim($btnIcon); ?>"></span>
    <?php endif; ?>
  </a>
</div>

<script type="text/javascript">
(function($){
  jQuery(document).ready(function($) {
    $("#acm-video-<?php echo $mod; ?> .html5lightbox").html5lightbox({
      autoslide: true,
      showplaybutton: false,
      jsfolder: "<?php echo JUri::base(true).'/templates/ja_stark/js/html5lightbox/' ?>"
    });
  });
})(jQuery);
</script>