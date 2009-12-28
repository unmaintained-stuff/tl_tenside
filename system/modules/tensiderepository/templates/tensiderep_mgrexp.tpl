<?php
/**
 * TYPOlight Repository :: Template to display list of installed extensions
 *
 * @copyright	Copyright (C) 2008 by Peter Koch, IBK Software AG, 2009 by CyberSpectrum 
 * @author		Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package		Tenside
 * @license		LGPL 
 */
?>
<?php 
	$rep = &$this->rep;
	$theme = &$rep->theme;
	$text = &$GLOBALS['TL_LANG']['tl_repository'];
	$statext = &$GLOBALS['TL_LANG']['tl_repository_statext'];
	$state_options = &$GLOBALS['TL_LANG']['tl_repository_state_options'];
?>

<div id="tl_buttons" class="buttonwrapper">
<a href="<?php echo $rep->homeLink; ?>" class="header_back" title="<?php echo $text['goback']; ?>" accesskey="b" onclick="Backend.getScrollOffset();"><?php echo $text['goback']; ?></a>
</div>

<div class="mod_repository block">

<div class="extension_container">
<textarea cols="120" rows="10" style="width:100%">
<?php if (count($rep->extensions)>0) { ?>
<?php foreach ($rep->extensions as $ext) { ?>
<?php echo $ext->extension; ?> - <?php echo $ext->version; ?> <?php echo $ext->build."\n"; ?>
<?php } // foreach rep->extensions ?>
<?php } else { ?>
<?php echo $text['noextensionsfound']; ?>
<?php } // if count rep->extensions ?>
</textarea>
</div>

</div>
