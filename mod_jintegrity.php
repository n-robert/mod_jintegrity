<?php
/**
 * @package  	mod_jintegrity
 *
 * @copyright   © Robert N. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root(true) . '/administrator/modules/mod_jintegrity/assets/css/mod_jintegrity.css');

require JModuleHelper::getLayoutPath('mod_jintegrity', $params->get('layout', 'default'));
