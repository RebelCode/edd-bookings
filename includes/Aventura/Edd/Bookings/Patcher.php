<?php

namespace Aventura\Edd\Bookings;

use \Aventura\Edd\Bookings\Controller\ControllerAbstract;

/**
 * Patcher class: responsible for applying patches on update.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Patcher extends ControllerAbstract
{
    
    /**
     * The patch classes.
     * 
     * @var array
     */
    protected $_patches = array();
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
        $this->init();
    }
    
    /**
     * Gets the patches.
     * 
     * @return array An associative array of patches, with versions as array keys and class names as array values.
     */
    public function getPatches()
    {
        return $this->_patches;
    }
    
    /**
     * Initializes the updates.
     */
    public function init()
    {
        $this->_patches = array(
                '2.0' => '\\Aventura\\Edd\\Bookings\\Patch\\V2_0_0'
        );
        ksort($this->_patches);
    }
    
    /**
     * Performs all required patch procedures.
     */
    public function applyPatches()
    {
        // Look for previous version db entry
        $previousVersion = get_option('edd_bk_previous_version', '1.0.3');
        // If the plugin has been activated for the first time, use 1.0.3 as $previousVersion
        // The 1.0.3 patch is harmless for first time activation, so we it anyway.
        foreach($this->getPatches() as $_patchVersion => $_patchClass) {
            // Only apply patch if its version is more recent than the previous version
            if (\version_compare($_patchVersion, $previousVersion, '<=')) {
                continue;
            }
            $success = $_patchClass::apply($this->getPlugin());
            // Check if patch failed
            if (!$success) {
                $this->getPlugin()->deactivate();
                \wp_die(_e('EDD Bookings failed to apply patch for plugin update.', $this->getPlugin()->getI18n()->getDomain()));
            }
        }
        update_option('edd_bk_previous_version', EDD_BK_VERSION);
        do_action('edd_bk_finished_patching');
    }
    
    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()->addAction('edd_bk_activated', $this, 'applyPatches');
    }

}
