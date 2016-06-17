<?php
/**
 * Part of bdrem
 *
 * PHP version 5
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @link      http://cweiske.de/bdrem.htm
 */
namespace bdrem;

/**
 * Configuration options for bdrem
 *
 * @category  Tools
 * @package   Bdrem
 * @author    Christian Weiske <cweiske@cweiske.de>
 * @copyright 2014 Christian Weiske
 * @license   http://www.gnu.org/licenses/agpl.html GNU AGPL v3
 * @version   Release: @package_version@
 * @link      http://cweiske.de/bdrem.htm
 */
class Config
{
    /**
     * Current date, YYYY-MM-DD
     * @var string
     */
    public $date;

    /**
     * Days to show before $date
     * @var integer
     */
    public $daysPrev;

    /**
     * Days to show after $date
     * @var integer
     */
    public $daysNext;

    /**
     * Development helper
     * @var boolean
     */
    public $debug;

    /**
     * Locale to render the dates in, e.g. "de_DE.UTF-8"
     * @var string
     */
    public $locale;

    /**
     * Renderer name to use (e.g. "console")
     * @var string
     */
    public $renderer;

    /**
     * Event source configuration.
     * - First value is source name ("Ldap", "Sql")
     * - Second value is the source configuration
     * @var array
     */
    public $source;

    /**
     * Do not output anything if there are no events to show
     * @var boolean
     */
    public $stopOnEmpty;

    /**
     * List of config file paths that were tried to load
     * @var array
     */
    public $cfgFiles = array();

    /**
     * If a configuration file could be found
     * @var boolean
     */
    public $cfgFileExists;



    /**
     * Init configuration file path loading
     */
    public function __construct()
    {
        $this->loadConfigFilePaths();
    }

    /**
     * Load the configuration from the first configuration file found.
     *
     * @return void
     */
    public function load()
    {
        foreach ($this->cfgFiles as $file) {
            if (file_exists($file)) {
                $this->cfgFileExists = true;
                return $this->loadFile($file);
            }
        }
        $this->cfgFileExists = false;
    }

    /**
     * Load possible configuration file paths into $this->cfgFiles.
     *
     * @return void
     */
    protected function loadConfigFilePaths()
    {
        $pharFile = \Phar::running();
        if ($pharFile == '') {
            $this->cfgFiles[] = __DIR__ . '/../../data/bdrem.config.php';
        } else {
            //remove phar:// from the path
            $this->cfgFiles[] = substr($pharFile, 7) . '.config.php';
        }

        //TODO: add ~/.config/bdrem.php

        $this->cfgFiles[] = '/etc/bdrem.php';
    }

    /**
     * Load a single configuration file and set the config class variables
     *
     * @param string $filename Configuration file path
     *
     * @return void
     */
    protected function loadFile($filename)
    {
        include $filename;
        $vars = get_defined_vars();
        foreach ($vars as $k => $value) {
            if (!isset($this->$k) || $this->$k === null) {
                $this->$k = $value;
            }
        }
    }

    /**
     * Load a event source from $this->source.
     * Class name has to be \bdrem\Source_$source
     *
     * @return object Source object
     */
    public function loadSource()
    {
        if ($this->source === null) {
            throw new \Exception('No source defined');
        }

        $settings = $this->source;
        $class = '\\bdrem\\Source_' . array_shift($settings);

        return new $class($settings[0]);
    }

    /**
     * Set the current date
     *
     * @param string $date Date in any format
     *
     * @return void
     */
    public function setDate($date)
    {
        if ($date === null) {
            $this->date = date('Y-m-d');
        } else {
            $dt = new \DateTime($date);
            $this->date = $dt->format('Y-m-d');
        }
    }

    /**
     * Get a configuration variable
     *
     * @param string $varname Configuration variable name
     * @param string $default Default value in case the variable is not set
     *                        or is empty
     *
     * @return mixed Configuration value or default
     */
    public function get($varname, $default = '')
    {
        if (!isset($this->$varname) || $this->$varname == '') {
            return $default;
        }
        return $this->$varname;
    }
}
?>
