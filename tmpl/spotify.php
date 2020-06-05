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

//add stylesheet for responsive container
$document = JFactory::getDocument();
$document->addStylesheet('plugins/fields/spotify/tmpl/style.css');

$value = $field->value;
$button = $fieldParams->get('button','playbutton');

if ($value == '')
{
	return;
}

if ($button == 'playbutton')
{
echo '<div align="left" id="sp_'. $value.'">
	<iframe src="https://open.spotify.com/embed?uri='. $value.'&theme=dark&view=coverart" width="300" height="380" frameborder="0" allowtransparency="true"></iframe>
</div>';
}

if ($button == 'followbutton')
{
echo '<div align="left" id="sp_'. $value.'">
	<iframe src="https://embed.spotify.com/follow/1/?uri='. $value.'&size=basic&show-count=1&theme=light" width="200" height="30" scrolling="no" frameborder="0" style="border:none; overflow:hidden;" allowtransparency="true"></iframe>
</div>';
}

