<?php
/**
 * NumerAlpha MediaWiki extension - Provides an incremental tag
 * with zero padded numbers, roman and alpha numbers
 *
 * @link http://www.mediawiki.org/wiki/Extension:NumerAlpha Documentation
 * @file NumerAlpha.php
 * @author Thierry G. Veilleux (Kronoxt), James Montalvo (jamesmontalvo3)
 * @copyright (C) 2009 Thierry G. Veilleux (Kronoxt)
 * @license http://www.publicdomainmanifesto.org/ Public Domain
 */

// Check if we are being called directly
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is an extension to MediaWiki and thus not a valid entry point.' );
}

// Tell everybody who we are
$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
	'name' => 'NumerAlpha',
	'version' => '0.7.0',
	'author' => array( 'Thierry G. Veilleux', '[https://www.mediawiki.org/wiki/User:Jamesmontalvo3 James Montalvo]', 'Emanspeaks' ),
	'descriptionmsg' => 'numeralpha-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:NumerAlpha'
);

$GLOBALS['wgMessagesDirs']['NumberAlpha'] = __DIR__ . '/i18n';
$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'NumerAlpha::onParserFirstCallInit';
$GLOBALS['wgAutoloadClasses']['NumerAlpha'] = __DIR__ . '/NumerAlpha.class.php';
$GLOBALS['wgExtensionMessagesFiles']['NumerAlphaMagic'] = __DIR__ . '/NumerAlpha.magic.php';
