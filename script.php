<?php

/*
 *  package: CustomFields-Spotify-FREE
 *  copyright: Copyright (c) 2026. Jeroen Moolenschot | Joomill
 *  license: GNU General Public License version 3 or later
 *  link: https://www.joomill-extensions.com
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the installer script with the DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   5.2.0
     */
    public function register(Container $container): void
    {
        $container->set(
            InstallerScriptInterface::class,
            new class ($container->get(DatabaseInterface::class)) implements InstallerScriptInterface {
                /**
                 * The database driver.
                 *
                 * @var    DatabaseInterface
                 * @since  5.2.0
                 */
                private DatabaseInterface $db;

                /**
                 * Minimum Joomla version required to install this extension.
                 *
                 * @var    string
                 * @since  5.2.0
                 */
                private string $minimumJoomlaVersion = '5.0';

                /**
                 * Minimum PHP version required to install this extension.
                 *
                 * @var    string
                 * @since  5.2.0
                 */
                private string $minimumPhpVersion = JOOMLA_MINIMUM_PHP;

                public function __construct(DatabaseInterface $db)
                {
                    $this->db = $db;
                }

                /**
                 * Function called after the extension is installed.
                 *
                 * @param   InstallerAdapter  $adapter  The adapter calling this method
                 *
                 * @return  boolean  True on success
                 *
                 * @since   5.2.0
                 */
                public function install(InstallerAdapter $adapter): bool
                {
                    $this->enablePlugin();

                    return true;
                }

                /**
                 * Function called after the extension is updated.
                 *
                 * @param   InstallerAdapter  $adapter  The adapter calling this method
                 *
                 * @return  boolean  True on success
                 *
                 * @since   5.2.0
                 */
                public function update(InstallerAdapter $adapter): bool
                {
                    return true;
                }

                /**
                 * Function called after the extension is uninstalled.
                 *
                 * @param   InstallerAdapter  $adapter  The adapter calling this method
                 *
                 * @return  boolean  True on success
                 *
                 * @since   5.2.0
                 */
                public function uninstall(InstallerAdapter $adapter): bool
                {
                    return true;
                }

                /**
                 * Function called before extension installation/update/removal procedure commences.
                 *
                 * @param   string            $type     The type of change (install, update or discover_install, not uninstall)
                 * @param   InstallerAdapter  $adapter  The adapter calling this method
                 *
                 * @return  boolean  True on success
                 *
                 * @since   5.2.0
                 */
                public function preflight(string $type, InstallerAdapter $adapter): bool
                {
                    if ($type !== 'uninstall') {
                        // Check for the minimum PHP version before continuing
                        if (!empty($this->minimumPhpVersion) && version_compare(PHP_VERSION, $this->minimumPhpVersion, '<')) {
                            Log::add(
                                Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhpVersion),
                                Log::WARNING,
                                'jerror'
                            );

                            return false;
                        }

                        // Check for the minimum Joomla version before continuing
                        if (!empty($this->minimumJoomlaVersion) && version_compare(JVERSION, $this->minimumJoomlaVersion, '<')) {
                            Log::add(
                                Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomlaVersion),
                                Log::WARNING,
                                'jerror'
                            );

                            return false;
                        }
                    }

                    return true;
                }

                /**
                 * Function called after extension installation/update/removal procedure commences.
                 *
                 * @param   string            $type     The type of change (install, update or discover_install, not uninstall)
                 * @param   InstallerAdapter  $adapter  The adapter calling this method
                 *
                 * @return  boolean  True on success
                 *
                 * @since   5.2.0
                 */
                public function postflight(string $type, InstallerAdapter $adapter): bool
                {
                    if ($type === 'install' || $type === 'uninstall') {
                        echo '<style>a[target="_blank"]::before {display: none;}</style>';
                        echo '<div class="mb-3 text-center"><img src="https://www.joomill-extensions.com/images/joomill-logo.png" alt="Joomill Extensions" /></div>';
                        echo '<br>';
                        echo '<h3 class="text-center">' . Text::_('PLG_FIELDS_SPOTIFY_THANKYOU') . '</h3>';
                        echo '<br>';
                        echo '<div class="text-center">' . Text::_('PLG_FIELDS_SPOTIFY_FOLLOWME') . ':</div>';
                        echo '<div class="text-center">';
                        echo '<a class="m-2" href="https://www.linkedin.com/in/jeroenmoolenschot/" target="_blank"><i class="fa-brands fa-linkedin"> </i></a>';
                        echo '<a class="m-2" href="https://www.facebook.com/Joomill" target="_blank"><i class="fa-brands fa-facebook-f"> </i></a>';
                        echo '<a class="m-2" href="https://www.instagram.com/Joomill" target="_blank"><i class="fa-brands fa-instagram"> </i></a>';
                        echo '<a class="m-2" href="https://bsky.app/profile/joomill.bsky.social" target="_blank"><i class="fa-brands fa-bluesky"> </i></a>';
                        echo '<a class="m-2" href="https://joomla.social/@joomill" target="_blank"><i class="fa-brands fa-mastodon"></i> </i></a>';
                        echo '<a class="m-2" href="https://www.threads.net/@joomill" target="_blank"><i class="fa-brands fa-threads"></i> </i></a>';
                        echo '<a class="m-2" href="https://www.twitter.com/Joomill" target="_blank"><i class="fa-brands fa-brands fa-x-twitter"> </i></a>';
                        echo '<a class="m-2" href="https://community.joomla.org/service-providers-directory/listings/67:joomill.html" target="_blank"><i class="fa-brands fa-joomla"> </i></a>';
                        echo '</div>';
                    }

                    return true;
                }

                /**
                 * Enables the plugin after installation.
                 *
                 * @return  void
                 *
                 * @since   5.2.0
                 */
                private function enablePlugin(): void
                {
                    try {
                        $db    = $this->db;
                        $query = $db->getQuery(true)
                            ->update($db->quoteName('#__extensions'))
                            ->set($db->quoteName('enabled') . ' = ' . $db->quote(1))
                            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                            ->where($db->quoteName('folder') . ' = ' . $db->quote('fields'))
                            ->where($db->quoteName('element') . ' = ' . $db->quote('spotify'));
                        $db->setQuery($query);
                        $db->execute();
                    } catch (\Exception $e) {
                        return;
                    }
                }
            }
        );
    }
};
