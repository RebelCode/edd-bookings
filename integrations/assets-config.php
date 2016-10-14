<?php

/**
 * A simple interal integration that loads a configuration file of assets.
 */
use \Aventura\Edd\Bookings\Integration\Core\IntegrationAbstract;

/**
 * Assets config integration.
 */
class EddBkAssetsConfig extends IntegrationAbstract
{

    /**
     * The base url for all scripts.
     *
     * @var string
     */
    protected $baseScriptUrl ;

    /**
     * The base url for all styles.
     *
     * @var type
     */
    protected $baseStyleUrl;

    /**
     * Gets the base script url.
     *
     * @return string
     */
    public function getBaseScriptUrl()
    {
        return $this->baseScriptUrl ;
    }

    /**
     * Gets the base style url.
     *
     * @return string
     */
    public function getBaseStyleUrl()
    {
        return $this->baseStyleUrl;
    }

    /**
     * Sets the base script url.
     *
     * @param string $baseScriptUrl
     * @return \EddBkAssetsConfig
     */
    public function setBaseScriptUrl($baseScriptUrl)
    {
        $this->baseScriptUrl = $baseScriptUrl;
        return $this;
    }

    /**
     * Sets the base style url.
     *
     * @param string $baseStyleUrl
     * @return \EddBkAssetsConfig
     */
    public function setBaseStyleUrl($baseStyleUrl)
    {
        $this->baseStyleUrl = $baseStyleUrl;
        return $this;
    }

    /**
     * Loads an assets config XML file.
     *
     * @param string $path The path to the file.
     * @return boolean True on success, false on failure.
     */
    public function loadFile($path)
    {
        $xml = eddBookings()->loadXmlFile($path);
        if (!is_null($xml)) {
            $this->processXmlFile($xml);
            return true;
        }
        return false;
    }

    /**
     * Processes the parsed contents of an XML file.
     *
     * @param SimpleXMLElement $xml
     */
    protected function processXmlFile(SimpleXMLElement $xml)
    {
        // Iterate assets
        foreach ($xml as $asset) {
            $this->processXmlAsset($asset);
        }

        return $this;
    }

    /**
     * Processes an asset XML node and registers it.
     *
     * @param SimpleXMLElement $asset
     */
    protected function processXmlAsset(SimpleXMLElement $asset)
    {
        // Get asset data
        $type = $asset->getName();
        $baseUrl = ($type === 'style')
            ? $this->getBaseStyleUrl()
            : $this->getBaseScriptUrl();
        $handle = (string) $asset['id'];
        $src = sprintf('%s%s', $baseUrl, (string) $asset['path']);
        $version = isset($asset['version'])
            ? $asset['version']
            : false;
        $override = isset($asset['override'])
            ? filter_var($asset['override'], FILTER_VALIDATE_BOOLEAN)
            : false;
        // Generate extra info array
        $extra = array();
        if ($type === 'style') {
            $extra['media'] = isset($asset['media'])
                ? (string) $asset['media']
                : 'all';
        } else {
            $extra['footer'] = isset($asset['footer'])
                ? (string) $asset['footer']
                : false;
        }
        // Compile the list of dependencies
        $dependencies = array();
        foreach ($asset->children() as $dep) {
            $dependencies[] = trim((string) $dep);
        }
        // Register asset
        $this->getPlugin()->getAssetsController()->addAsset($type, $handle, $src, $dependencies, $version, $extra, $override);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hook()
    {
        $this->loadFile(eddBookings()->getConfigFilePath('assets'));
    }

}

// Register integration.
$instance = new EddbkAssetsConfig(eddBookings());
$instance->setBaseScriptUrl(EDD_BK_JS_URL)
    ->setBaseStyleUrl(EDD_BK_CSS_URL);
eddBookings()->addIntegration('assetsConfig', $instance);
