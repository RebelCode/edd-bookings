<?php

namespace Aventura\Edd\Bookings;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;

/**
 * Updater class: responsible for performing update procedures.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Updater extends ControllerAbstract
{
    
    /**
     * The update classes.
     * 
     * @var array
     */
    protected $_updates = array();
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
        $this->init();
    }
    
    /**
     * Gets the updates.
     * 
     * @return array An associative array of updates, with versions as array keys and class names as array values.
     */
    public function getUpdates()
    {
        return $this->_updates;
    }
    
    /**
     * Initializes the updates.
     */
    public function init()
    {
        $this->_updates = array(
                '2.0' => '\\Aventura\\Edd\\Bookings\\Update\\V2P0P0'
        );
        ksort($this->_updates);
    }
    
    /**
     * Performs all required update procedures.
     */
    public function update()
    {
        // Look for previous version db entry
        $previousVersion = get_option('edd_bk_previous_version', '1.0.3');
        // If the plugin has been activated for the first time, use 1.0.3 as $previousVersion
        // The 1.0.3 updates are harmless for first time activation, so we perform them anyway.
        foreach($this->getUpdates() as $version => $updateClass) {
            // Only perform update if the its version is more recent than the previous version
            if (\version_compare($version, $previousVersion, '<=')) {
                continue;
            }
            $success = $updateClass::update($this->getPlugin());
            // Check if update failed
            if (!$success) {
                $this->getPlugin()->deactivate();
                \wp_die(_e('EDD Bookings failed to run update procedures.', $this->getPlugin()->getI18n()->getDomain()));
            }
        }
    }
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()->addAction('edd_bk_activated', $this, 'update');
    }

}
