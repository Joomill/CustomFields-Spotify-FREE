# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Joomla **Custom Fields plugin** (`plg_fields_spotify`) that adds a "Spotify" field type. The entered
field value (a Spotify URI) is rendered as an embedded Spotify player using Spotify's official iframe
embed. This repo is the **FREE** edition; it renders the player with fixed defaults and exposes the
configurable settings only as `pro`/`upgrade` placeholders that point users to the PRO edition. A
separate PRO edition shares the same namespace and codebase but ships the real parameters.

Namespace: `Joomill\Plugin\Fields\Spotify` (mapped to `src/` in `spotify.xml`).
Target: Joomla 5.0+ / PHP per `JOOMLA_MINIMUM_PHP`. Author: Jeroen Moolenschot (Joomill Extensions).

## No build / test / lint tooling

This is plain PHP packaged as a Joomla extension. There is no Composer, npm, or test suite. "Building"
means zipping the repo so Joomla can install it. Verification is manual: install the zip in a Joomla
site, add a Spotify custom field, enter a Spotify URI, and view the rendered player.

## Architecture

Joomla loads the plugin through dependency injection, not by file convention:

- `services/provider.php` — DI entry point. Registers the `Spotify` plugin with Joomla's container.
  `spotify.xml` points the `services` folder here via `<folder plugin="spotify">services</folder>`.
- `src/Extension/Spotify.php` — the plugin class, extends Joomla's `FieldsPlugin`. Intentionally empty;
  all default field-plugin behavior is inherited. The class name `spotify` (lowercased) wires it to the
  template and params by Joomla's Fields convention.
- `params/spotify.xml` — the per-field settings form shown in the field's "Custom Fields" admin panel.
  In this FREE edition every setting (`playcolor`, `mode`, `width`, `height`, `align`) is a `pro`
  placeholder, followed by an `upgrade` field; none of them are functional. `addfieldprefix` lets it
  load the custom field types from `src/Field/`.
- `tmpl/spotify.php` — the **frontend render template** and where all output logic lives. Reads
  `$field->value`, parses the stored URI into a `{type}/{id}` pair, validates it against a whitelist of
  content types and a safe id charset (renders nothing if it does not match), loads the stylesheet via
  the WebAssetManager (`$wa->registerAndUseStyle('plg.fields.spotify', ...)`), and echoes a
  `<div class="spotify-align-left" id="sp_<id>">` containing the Spotify iframe at fixed 300x380. Every
  value is escaped with `htmlspecialchars(..., ENT_QUOTES)`. The configurable theme/size/alignment are
  PRO-only.
- `src/Field/ProField.php` (`pro`), `src/Field/UpgradeField.php` (`upgrade`) — placeholder form-field
  types that display the "PRO only" / upsell messages used by the params form above.
- `script.php` — install lifecycle, written the Joomla 6 way: the file returns a
  `ServiceProviderInterface` that registers an anonymous `InstallerScriptInterface` in the DI container
  (the `DatabaseInterface` is injected). `preflight` enforces minimum Joomla 5.0 / PHP versions;
  `install` auto-enables the plugin via a direct `#__extensions` UPDATE; `postflight` prints the Joomill
  branding/social block. The legacy `plgFieldsSpotifyInstallerScript` class name is **not** used —
  Joomla deprecated it and removes it in 6.0.
- `tmpl/style.css` — `.responsive-container` (16:9 wrapper, used by the PRO responsive mode) and the
  `.spotify-align-left|center|right` helpers (replaces the deprecated HTML `align` attribute).

## The Spotify embed

The player uses Spotify's current iframe endpoint:

```
https://open.spotify.com/embed/{type}/{id}
```

- `{type}` and `{id}` are parsed from the stored value, which may be **either** a URI
  (`spotify:track:ID`) **or** a share URL (`https://open.spotify.com/track/ID?si=...`, including an
  optional `intl-xx/` locale segment). Supported types: `track`, `album`, `playlist`, `artist`, `show`,
  `episode`. Legacy `spotify:user:{user}:playlist:{id}` URIs and `/user/x/playlist/id` URLs are
  normalized to `playlist`.
- The FREE edition always renders the default (light) theme; theme selection is a PRO feature.
- The old `open.spotify.com/embed?uri=...` / `embed.spotify.com/follow` endpoints are **not** used.
  Spotify discontinued the Follow Button in 2021, so the follow-button option was removed entirely
  (params, template branch, and language strings). Do not reintroduce it.

## Conventions specific to this repo

- **Edit `tmpl/spotify.php` for any change to how the player is built or displayed.** The plugin class
  is deliberately empty; do not add rendering logic there.
- All user-facing strings are language constants (`PLG_FIELDS_SPOTIFY_*`) defined in
  `language/<locale>/plg_fields_spotify.ini`. When adding a string, add the key to **every** locale
  (de-DE, en-GB, es-ES, fr-FR, it-IT, nl-NL) and use `Text::_()` / `Text::sprintf()` in PHP.
- Every PHP file starts with the Joomill copyright header block and `defined('_JEXEC') or die;`.
- The version lives in `spotify.xml` (`<version>`), together with `<creationDate>` and `<copyright>`.
  Bump them there on release; recent git history shows one commit per version (e.g. "V5.1.0").
- This FREE repo and the PRO repo are kept in sync. When changing shared files, remember a parallel PRO
  edition exists; the difference is which fields/params are active, not the file layout.
