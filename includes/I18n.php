<?php

namespace Aventura\Edd\Bookings;

/**
 * Internationalization class.
 */
class I18n
{

    /**
     * The plugin text domain in use.
     * 
     * @var string
     */
    protected $_domain;

    /**
     * The language directory.
     * 
     * @var string
     */
    protected $_langDir;

    /**
     * Constructs a new instance.
     * 
     * @param string $domain The domain
     */
    public function __construct($domain, $langDir)
    {
        $this->setDomain($domain)
            ->setLangDir($langDir);
    }

    /**
     * Gets the text domain.
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Sets the text domain.
     * 
     * @param string $domain The domain.
     * @return I18n This instance.
     */
    public function setDomain($domain)
    {
        $this->_domain = $domain;
        return $this;
    }

    /**
     * Gets the language directory.
     * 
     * @return string
     */
    public function getLangDir()
    {
        return $this->_langDir;
    }

    /**
     * Sets the language directory.
     * 
     * @param string $langDir The language directory.
     * @return I18n This instance.
     */
    public function setLangDir($langDir)
    {
        $this->_langDir = $langDir;
        return $this;
    }

    /**
     * Loads the plugin text domain.
     */
    public function loadTextdomain()
    {
        load_plugin_textdomain($this->getDomain(), false, $this->getLangDir());
    }

}
