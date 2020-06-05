<?php
/**
 * Custom Fields - Spotify plugin for Joomla
 *
 * @author Joomill (info@joomill-extensions.com)
 * @copyright Copyright (c) 2017 Joomill
 * @license GNU Public License
 * @link https://www.joomill-extensions.com/
 */

defined('_JEXEC') or die;

class plgFieldsSpotifyInstallerScript
{
	public function install($parent)
	{
		jimport('joomla.filesystem.file');
        JFactory::getDBO()->setQuery("UPDATE `#__extensions` SET `enabled` = 1 WHERE `type` = 'plugin' AND`element` = 'spotify'")->execute();
	}
}