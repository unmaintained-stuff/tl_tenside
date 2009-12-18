<?php 
	$rep = &$this->rep;
	$theme = &$rep->theme;
	$text = &$GLOBALS['TL_LANG']['tl_repository'];
?>
<div id="tl_buttons" class="buttonwrapper">
<?php echo $theme->createMainButton('dbcheck16', $rep->updateLink, $text['updatedatabase']); ?> &nbsp; :: &nbsp; 
<?php echo $theme->createMainButton('install16', $rep->installLink, $text['installextension']); ?> 
</div>
<div class="mod_repository block">
<div class="extension_container">
<p class="error"><?php echo $this->error; ?></p>
</div>
</div>
