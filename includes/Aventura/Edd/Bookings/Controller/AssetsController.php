<?php

namespace Aventura\Edd\Bookings\Controller;

/**
 * This class is responsible for registering and enqueueing static asset files, such as stylesheets, scripts and fonts.
 * 
 * On it's own, this class will enqueue assets on three instances:
 *  AssetsController#commonAssets() is called on every page load
 *  AssetsController#backendAssets() is called on the backend
 *  AssetsController#frontendAssets() is called on the frontend
 * 
 * However, the registration/enqueueing methods may be used externally, given that they are called at the appropriate
 * point in time (on the correct WP hook).
 * 
 * @version 1.0.0
 * @since [*next-version*]
 */
class AssetsController extends ControllerAbstract
{

    /**
     * @constant A script-type asset.
     */
    const TYPE_SCRIPT = 'script';
    
    /**
     * @constant A style-type asset.
     */
    const TYPE_STYLE = 'style';
    
    /**
     * @constant The WP hook used to register/enqueue assets on the frontend.
     */
    const HOOK_FRONTEND = 'wp_enqueue_scripts';
    
    /**
     * @constant The WP hook used to register/enqueue assets on the backend.
     */
    const HOOK_ADMIN = 'admin_enqueue_scripts';
    
    /**
     * @constant The WP hook used to register/enqueue assets on the login page.
     */
    const HOOK_LOGIN = 'login_enqueue_scripts';

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        // Register hooks for loading assets
        $this->getPlugin()->getHookManager()
                ->addAction(static::HOOK_FRONTEND, $this, 'commonAssets')
                ->addAction(static::HOOK_ADMIN, $this, 'commonAssets')
                ->addAction(static::HOOK_FRONTEND, $this, 'frontendAssets')
                ->addAction(static::HOOK_ADMIN, $this, 'backendAssets');
    }

    /**
     * Loads the assets used on both backend and frontend.
     * 
     * @return AssetsController This instance.
     */
    public function commonAssets()
    {
        $this->enqueueStyle('font-awesome', EDD_BK_CSS_URL . 'font-awesome.min.css');
        return $this;
    }

    /**
     * Loads the assets used on the frontend.
     * 
     * @return AssetsController This instance.
     */
    public function frontendAssets()
    {
        // Registered default datepicker style if not enqueued or registered already
        if (!\wp_style_is('jquery-ui-style-css', 'enqueued') && !wp_style_is('jquery-ui-style-css', 'registered')) {
            $this->registerStyle('jquery-ui-style-css', EDD_BK_CSS_URL . 'jquery-ui.min.css');
        }
        // Our datepicker skin
        $this->enqueueStyle('edd-bk-datepicker-css', EDD_BK_CSS_URL . 'datepicker-skin.css',
                array('jquery-ui-style-css'));

        // Out frontend styles
        $this->enqueueStyle('edd-bk-service-frontend-css', EDD_BK_CSS_URL . 'service-frontend.css');

        // Mutltidatepicker addon
        $this->enqueueScript('jquery-ui-multidatepicker', EDD_BK_JS_URL . 'jquery-ui.multidatespicker.js',
                array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '1.6.3');
        // Our frontend scripts
        $this->enqueueScript('edd-bk-service-frontend', EDD_BK_JS_URL . 'service-frontend.js');
        // lodash
        $this->enqueueScript('edd-bk-lodash', EDD_BK_JS_URL . 'lodash.min.js');

        return $this;
    }

    /**
     * Loads the assets used in the backend.
     * 
     * @return AssetsController This instance.
     */
    public function backendAssets()
    {
        $this->enqueueStyle('edd-bk-timetable-css', EDD_BK_CSS_URL . 'timetable.css');
        $this->enqueueScript('edd-bk-timetable-js', EDD_BK_JS_URL . 'timetable.js');
        $this->enqueueStyle('jquery-ui-timepicker-css', EDD_BK_CSS_URL . 'jquery-ui-timepicker.css');
        $this->enqueueScript('jquery-ui-timepicker-addon', EDD_BK_JS_URL . 'jquery-ui-timepicker.js',
                array('jquery-ui-datepicker'));
        $this->enqueueScript('edd-bk-service-js', EDD_BK_JS_URL . 'service.js');
        $this->enqueueStyle('edd-bk-service-css', EDD_BK_CSS_URL . 'service.css');
        $this->enqueueStyle('edd-bk-bookings-css', EDD_BK_CSS_URL . 'bookings.css');
        $this->enqueueStyle('edd-bk-tooltips-css', EDD_BK_CSS_URL . 'tooltips.css');

        return $this;
    }

    /**
     * Registers a script.
     *
     * @uses AssetsController::script()
     * @see AssetsController::script()
     */
    public function registerScript($handle, $src, $deps = array(), $ver = false, $in_footer = false)
    {
        return $this->script(false, $handle, $src, $deps, $ver, $in_footer);
    }

    /**
     * Enqueues a script.
     *
     * @uses AssetsController::script()
     * @see AssetsController::script()
     */
    public function enqueueScript($handle, $src = null, $deps = array(), $ver = false, $in_footer = false)
    {
        return $this->script(true, $handle, $src, $deps, $ver, $in_footer);
    }

    /**
     * All in one handler method for scripts.
     *
     * @param  boolean $enqueue   If true, the script is enqueued. If false, the script is only registered.
     * @param  string  $handle    The script handle
     * @param  string  $src       The path to the source file of the script
     * @param  array   $deps      An array of script handles that this script depends upon. Default: array()
     * @param  boolean $ver       The version of the script. Default: false
     * @param  boolean $in_footer If true, the script is added to the footer of the page. If false, it is added to the document head. Default: false
     * @return AssetsController
     */
    protected function script($enqueue, $handle, $src = null, $deps = array(), $ver = false, $in_footer = false)
    {
        return $this->handleAsset('script', $enqueue, $handle, $src, $deps, $ver, $in_footer);
    }

    /**
     * Registers a style.
     *
     * @uses AssetsController::style()
     * @see AssetsController::style()
     */
    public function registerStyle($handle, $src, $deps = array(), $ver = false, $media = 'all')
    {
        return $this->style(false, $handle, $src, $deps, $ver, $media);
    }

    /**
     * Enqueues a style.
     *
     * @uses AssetsController::style()
     * @see AssetsController::style()
     */
    public function enqueueStyle($handle, $src = null, $deps = array(), $ver = false, $media = 'all')
    {
        return $this->style(true, $handle, $src, $deps, $ver, $media);
    }

    /**
     * All in one handler method for styles.
     *
     * @param  boolean $enqueue If true, the style is enqueued. If false, the style is only registered.
     * @param  string  $handle  The style handle
     * @param  string  $src     The path to the source file of the style
     * @param  array   $deps    An array of style handles that this style depends upon. Default: array()
     * @param  boolean $ver     The version of the style. Default: false
     * @param  string  $media   The style's media scope. Default: all
     * @return AssetsController
     */
    public function style($enqueue, $handle, $src, $deps = array(), $ver = false, $media = 'all')
    {
        return $this->handleAsset('style', $enqueue, $handle, $src, $deps, $ver, $media);
    }

    /**
     * All in one method for setting up a hook and callback for an asset.
     * 
     * @param  string  $type    Asset::TYPE_SCRIPT or Asset::TYPE_STYLE
     * @param  boolean $enqueue If true, the asset is enqueued. If false, the asset is only registered.
     * @param  string  $handle  The asset's handle string
     * @param  string  $src     Path to the asset's source file
     * @param  array   $deps    Array of other similar asset handles that this asset depends on.
     * @param  string  $ver     String version of the asset, for caching purposes.
     * @param  mixed   $extra   Extra data to be included, such as style media or script location in document.
     * @return AssetsController
     */
    protected function handleAsset($type, $enqueue, $handle, $src, $deps, $ver, $extra)
    {
        // Generate name of function to use (whether for enqueueing or registration)
        $enqueueOrRegister = ($enqueue === true)
                ? 'enqueue'
                : 'register';
        $fn = sprintf('\wp_%1$s_%2$s', $enqueueOrRegister, $type);
        // Call the enqueue/register function
        call_user_func_array($fn, array($handle, $src, $deps, $ver, $extra));
        return $this;
    }

}
