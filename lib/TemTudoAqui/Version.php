<?php

namespace TemTudoAqui;

class Version
{
    /**
     * Current Doctrine Version.
     */
    const VERSION = '0.0.0';

    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}
