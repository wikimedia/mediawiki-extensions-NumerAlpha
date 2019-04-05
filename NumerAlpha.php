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

// Tell everybody who we are
$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
	'path' => '__FILE__',
	'name' => 'NumerAlpha',
	'version' => '0.7.0',
	'author' => array(
		'Thierry G. Veilleux',
		'[https://www.mediawiki.org/wiki/User:Jamesmontalvo3 James Montalvo]',
		'Emanspeaks',
		'...'
	),
	'descriptionmsg' => 'numeralpha-desc',
	'url' => 'https://www.mediawiki.org/wiki/Extension:NumerAlpha',
	'license-name' => 'Unlicense'
);

// Register extension messages
$GLOBALS['wgMessagesDirs']['NumberAlpha'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['NumerAlphaMagic'] = __DIR__ . '/NumerAlpha.magic.php';

// Load extension's class
$GLOBALS['wgAutoloadClasses']['NumerAlpha'] = __DIR__ . '/NumerAlpha.class.php';

// Register hook
$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'NumerAlpha::onParserFirstCallInit';
