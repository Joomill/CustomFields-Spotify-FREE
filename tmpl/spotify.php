<?php
/*
 *  package: Custom Fields - Spotify plugin - FREE Version
 *  copyright: Copyright (c) 2026. Jeroen Moolenschot | Joomill
 *  license: GNU General Public License version 3 or later
 *  link: https://www.joomill-extensions.com
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$value = trim((string) $field->value);

if ($value === '') {
    return;
}

$allowedTypes = ['track', 'album', 'playlist', 'artist', 'show', 'episode'];

$type = '';
$id   = '';

if (stripos($value, 'http') === 0) {
    // Share URL form: https://open.spotify.com/{intl-xx/}{type}/{id}?si=...
    $segments = array_values(array_filter(explode('/', (string) parse_url($value, PHP_URL_PATH))));

    foreach ($segments as $i => $segment) {
        if (in_array($segment, $allowedTypes, true)) {
            $type = $segment;
            $id   = $segments[$i + 1] ?? '';
            break;
        }
    }
} else {
    // URI form: spotify:{type}:{id} (or legacy spotify:user:{user}:playlist:{id}).
    $parts = explode(':', $value);
    $type  = $parts[1] ?? '';
    $id    = $parts[2] ?? '';

    if ($type === 'user' && ($index = array_search('playlist', $parts, true)) !== false) {
        $type = 'playlist';
        $id   = $parts[$index + 1] ?? '';
    }
}

// Only render a known content type with a safe id; render nothing otherwise.
if (!in_array($type, $allowedTypes, true) || !preg_match('/^[a-zA-Z0-9]+$/', $id)) {
    return;
}

// Load the responsive container stylesheet via the WebAssetManager.
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('plg.fields.spotify', 'plugins/fields/spotify/tmpl/style.css');

// Escape every value before it is written into the markup.
$e = static fn ($string): string => htmlspecialchars((string) $string, ENT_QUOTES, 'UTF-8');

echo '<div class="spotify-align-left" id="sp_' . $e($id) . '">
	<iframe src="https://open.spotify.com/embed/' . $e($type) . '/' . $e($id) . '" width="300" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media" loading="lazy"></iframe>
</div>';
