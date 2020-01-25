<?php
/**
 * NumerAlpha MediaWiki extension - Provides an incremental tag
 * with zero padded numbers, roman and alpha numbers
 *
 * @link https://www.mediawiki.org/wiki/Extension:NumerAlpha Documentation
 * @file NumerAlpha.php
 * @author Thierry G. Veilleux (Kronoxt), James Montalvo (jamesmontalvo3)
 * @copyright (C) 2009 Thierry G. Veilleux (Kronoxt)
 * @license http://www.publicdomainmanifesto.org/ Public Domain
 */
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'NumerAlpha' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['NumerAlpha'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['NumerAlphaMagic'] = __DIR__ . '/NumerAlpha.magic.php';
	wfWarn(
		'Deprecated PHP entry point used for the NumerAlpha extension. ' .
		'Please use wfLoadExtension() instead, ' .
		'see https://www.mediawiki.org/wiki/Special:MyLanguage/Manual:Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the NumerAlpha extension requires MediaWiki 1.29+' );
}

