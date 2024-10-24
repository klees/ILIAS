<?php declare(strict_types=1);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/

/**
 * This class offers methods for timezone handling.
 * <code>ilTimeZone::_getDefault</code> tries to "guess" the server timezone in the following manner:
 * 1) PHP >= 5.2.0 use <code>date_default_timezone_get</code>
 * 2) Read ini option date.timezone if available
 * 3) Read environment PHP_TZ
 * 4) Read environment TZ
 * 5) Use <code>date('T')</code>
 * 6) Use UTC
 * @author  Stefan Meyer <smeyer.ilias@gmx.de>
 * @ingroup ServicesCalendar
 */
class ilTimeZone
{
    public const UTC = 'UTC';

    public static array $instances = array();
    public static array $valid_tz = array();

    protected static string $default_timezone = '';
    protected static string $current_timezone = '';
    protected static string $server_timezone = '';

    protected ilLogger $log;
    protected string $timezone = "UTC";

    /**
     * Create new timezone object
     * If no timezone is given, the default server timezone is chosen.
     */
    private function __construct(string $a_timezone)
    {
        global $DIC;

        $this->log = $DIC->logger()->cal();

        if ($a_timezone) {
            $this->timezone = $a_timezone;
        } else {
            $this->timezone = self::_getDefaultTimeZone();
        }

        if (!self::$server_timezone) {
            self::$server_timezone = self::_getDefaultTimeZone();
        }

        if (!self::$default_timezone) {
            self::_getDefaultTimeZone();
        }
    }

    public function __sleep()
    {
        return array('timezone');
    }

    public function __wakeup()
    {
        global $DIC;
        $this->log = $DIC->logger()->cal();
    }

    public function getIdentifier() : string
    {
        return $this->timezone;
    }

    /**
     * get instance by timezone
     * @throws ilTimeZoneException
     */
    public static function _getInstance(string $a_tz = '') : ilTimeZone
    {
        if (!$a_tz) {
            $a_tz = self::_getDefaultTimeZone();
        }

        if (isset(self::$instances[$a_tz])) {
            $instance = self::$instances[$a_tz];
        } else {
            $instance = self::$instances[$a_tz] = new ilTimeZone($a_tz);
        }

        // Validate timezone if it is not validated before
        if (!array_key_exists($instance->getIdentifier(), self::$valid_tz)) {
            if (!$instance->validateTZ()) {
                throw new ilTimeZoneException('Unsupported timezone given.');
            }
            self::$valid_tz[$instance->getIdentifier()] = true;
        }

        // now validate timezone setting
        return $instance;
    }

    /**
     * Switch timezone to given timezone
     */
    public function switchTZ()
    {
        try {
            self::_switchTimeZone($this->timezone);
            return true;
        } catch (ilTimeZoneException $exc) {
            // Shouldn't happen since this has been checked during initialisation
            $this->log->warning(': Unsupported timezone given: Timzone: ' . $this->timezone);
            return false;
        }
    }

    /**
     * Restore default timezone
     */
    public function restoreTZ() : bool
    {
        try {
            self::_switchTimeZone(self::$default_timezone);
            return true;
        } catch (ilTimeZoneException $e) {
            // Shouldn't happen since this has been checked during initialisation
            $this->log->warning(': Unsupported timezone given: Timzone: ' . $this->timezone);
            return false;
        }
    }

    public function validateTZ() : bool
    {
        // this is done by switching to the current tz
        if ($this->switchTZ() and $this->restoreTZ()) {
            return true;
        }
        return false;
    }

    protected static function _switchTimeZone(string $a_timezone) : bool
    {
        global $DIC;

        $logger = $DIC->logger()->cal();
        if (self::$current_timezone == $a_timezone) {
            return true;
        }

        // PHP >= 5.2.0
        if (function_exists('date_default_timezone_set')) {
            if (!date_default_timezone_set($a_timezone)) {
                $logger->info('Invalid timezone given. Timezone: ' . $a_timezone);
                throw new ilTimeZoneException('Invalid timezone given');
            }
            #$ilLog->write(__METHOD__.': Switched timezone to: '.$a_timezone);
            self::$current_timezone = $a_timezone;
            return true;
        }
        if (!putenv('TZ=' . $a_timezone)) {
            $logger->warning('Cannot set TZ environment variable. Please register TZ in php.ini (safe_mode_allowed_env_vars). Timezone');
            throw new ilTimeZoneException('Cannot set TZ environment variable.');
        }
        self::$current_timezone = $a_timezone;
        return true;
    }

    public static function _setDefaultTimeZone(string $a_tz) : void
    {
        // Save the server timezone, since there is no way to read later.
        if (!self::$server_timezone) {
            self::$server_timezone = self::_getDefaultTimeZone();
        }
        self::$default_timezone = $a_tz;
    }

    public static function _restoreDefaultTimeZone() : void
    {
        self::$default_timezone = self::$server_timezone;
        self::_switchTimeZone(self::$default_timezone);
    }

    /**
     * Calculate and set default time zone
     */
    public static function _getDefaultTimeZone() : string
    {
        if (strlen(self::$default_timezone)) {
            return self::$default_timezone;
        }
        // PHP >= 5.2.0
        // php throws a warning date_default_timezone_get relies on os determination. There is no way to check if this could happen.
        if (function_exists('date_default_timezone_get') and $tz = @date_default_timezone_get()) {
            return self::$default_timezone = $tz;
        }
        // PHP ini option (PHP >= 5.1.0)
        if ($tz = ini_get('date.timezone')) {
            return self::$default_timezone = $tz;
        }
        // is $_ENV['PHP_TZ'] set ?
        if ($tz = getenv('PHP_TZ')) {
            return self::$default_timezone = $tz;
        }
        // is $_ENV['TZ'] set ?
        if ($tz = getenv('TZ')) {
            return self::$default_timezone = $tz;
        }
        if (strlen($tz = date('T'))) {
            return self::$default_timezone = $tz;
        }
        return self::$default_timezone = self::UTC;
    }

    /**
     * Initialize default timezone from system settings
     */
    public static function initDefaultTimeZone(ilIniFile $ini) : string
    {
        $tz = $ini->readVariable('server', 'timezone');
        if (!strlen($tz)) {
            $tz = self::_getDefaultTimeZone();
        }
        if (!strlen($tz)) {
            $tz = 'UTC';
        }
        date_default_timezone_set($tz);
        return $tz;
    }
}
