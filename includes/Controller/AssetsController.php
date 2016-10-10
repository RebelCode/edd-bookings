<?php

namespace Aventura\Edd\Bookings\Controller;

use \Aventura\Edd\Bookings\Integration\Fes\FesIntegration;

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
                ->addAction(static::HOOK_FRONTEND, $this, 'commonAssets', 100)
                ->addAction(static::HOOK_ADMIN, $this, 'commonAssets', 100)
                ->addAction(static::HOOK_FRONTEND, $this, 'frontendAssets', 100)
                ->addAction(static::HOOK_ADMIN, $this, 'backendAssets', 100);
    }

    /**
     * Loads the assets used on both backend and frontend.
     * 
     * @return AssetsController This instance.
     */
    public function commonAssets()
    {
        $this->enqueueScript('edd-bk-utils-js', EDD_BK_JS_URL . 'edd-bk-utils.js');
        $this->enqueueStyle('font-awesome', EDD_BK_CSS_URL . 'font-awesome.min.css');

        $this->registerStyle('edd-bk-bookings-css', EDD_BK_CSS_URL . 'bookings.css');

        // Mutltidatepicker addon
        $this->registerScript('jquery-ui-multidatespicker', EDD_BK_JS_URL . 'jquery-ui.multidatespicker.js',
                array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '1.6.4');

        // JS base classes
        $this->registerScript('eddbk.class', EDD_BK_JS_URL . 'eddbk/class.js');
        $this->registerScript('eddbk.object', EDD_BK_JS_URL . 'eddbk/object.js', array('eddbk.class'));
        $this->registerScript('eddbk.ajax', EDD_BK_JS_URL . 'eddbk/ajax.js');
        $this->registerScript('eddbk.utils', EDD_BK_JS_URL . 'eddbk/utils.js');

        $this->enqueueScript('eddbk.object.service', EDD_BK_JS_URL . 'eddbk/object/service.js', array('eddbk.object'));
        $this->enqueueScript('eddbk.object.sessions-storage', EDD_BK_JS_URL . 'eddbk/object/session-storage.js', array('eddbk.object'));
        // $this->enqueueScript('eddbk.object.ui.session-picker', EDD_BK_JS_URL . 'eddbk/object/ui/session-picker.js', array('eddbk.object'));

        $this->registerScript('eddbk.ui.widget', EDD_BK_JS_URL . 'eddbk/ui/widget.js', array('eddbk.ajax', 'eddbk.object'));
        $this->enqueueScript('eddbk.ui.widget.time-picker', EDD_BK_JS_URL . 'eddbk/ui/widget/time-picker.js',
            array('eddbk.ui.widget'));
        $this->enqueueScript('eddbk.ui.widget.duration-picker', EDD_BK_JS_URL . 'eddbk/ui/widget/duration-picker.js', array(
            'eddbk.ui.widget',
            'eddbk.utils'
        ));
        $this->enqueueScript('eddbk.ui.widget.date-picker', EDD_BK_JS_URL . 'eddbk/ui/widget/date-picker.js',
            array('eddbk.ui.widget', 'jquery-ui-multidatespicker'));
        $this->enqueueScript('eddbk.ui.widget.session-picker', EDD_BK_JS_URL . 'eddbk/ui/widget/session-picker.js', array(
            'eddbk.ui.widget',
            'eddbk.ui.widget.date-picker',
            'eddbk.ui.widget.time-picker',
            'eddbk.ui.widget.duration-picker'
        ));

        wp_localize_script('eddbk.ajax', 'EddBkAjaxLocalized', array(
            'url'   => admin_url('admin-ajax.php')
        ));

        wp_localize_script('eddbk.object.service', 'EddBkAjax', array(
            'url'   => admin_url('admin-ajax.php')
        ));

        // Notices script
        $this->enqueueScript('edd-bk-notices', EDD_BK_JS_URL . 'notices.js');

        // Registered default datepicker style if not enqueued or registered already
        if (!\wp_style_is('jquery-ui-style-css', 'enqueued') && !wp_style_is('jquery-ui-style-css', 'registered')) {
            $this->registerStyle('jquery-ui-style-css', EDD_BK_CSS_URL . 'jquery-ui.min.css');
        }
        // Our datepicker skin
        $this->enqueueStyle('edd-bk-datepicker-css', EDD_BK_CSS_URL . 'datepicker-skin.css',
                array('jquery-ui-style-css'));

        $this->registerStyle('edd-bk-fc-reset', EDD_BK_CSS_URL . 'fc-reset.css');
        $this->enqueueStyle('edd-bk-fullcalendar-css', EDD_BK_JS_URL . 'fullcalendar/fullcalendar.min.css', array('edd-bk-fc-reset'));
        $this->registerScript('edd-bk-moment-js', EDD_BK_JS_URL . 'fullcalendar/lib/moment.min.js');
        $this->enqueueScript('edd-bk-fullcalendar-js', EDD_BK_JS_URL . 'fullcalendar/fullcalendar.min.js',
                array('jquery', 'jquery-ui-core', 'jquery-ui-tooltip', 'edd-bk-moment-js'));
        $this->enqueueScript('edd-bk-bookings-calendar-js', EDD_BK_JS_URL . 'bookings-calendar.js',
                array('edd-bk-fullcalendar-js'));

        $this->enqueueStyle('edd-bk-bookings-css');

        wp_localize_script('edd-bk-bookings-calendar-js', 'EddBkFc', array(
            'postEditUrl' => admin_url('post.php?post=%s&action=edit'),
            'theme'       => !is_admin(),
            'fesLinks'    => !is_admin()
        ));

        wp_localize_script('edd-bk-bookings-calendar-js', 'EddBkLocalized', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));

        return $this;
    }

    /**
     * Loads the assets used on the frontend.
     * 
     * @return AssetsController This instance.
     */
    public function frontendAssets()
    {
        // Out frontend styles
        $this->enqueueStyle('edd-bk-service-frontend-css', EDD_BK_CSS_URL . 'service-frontend.css');

        // Our frontend scripts
        $this->registerScript('eddbk-session-picker', EDD_BK_JS_URL . 'class-session-picker.js',
            array('eddbk-class-service'));
        $this->registerScript('eddbk-purchase-form-session-picker', EDD_BK_JS_URL . 'class-purchase-form-session-picker.js',
            array('eddbk-session-picker'));
        $this->enqueueScript('edd-bk-service-frontend', EDD_BK_JS_URL . 'service-frontend.js',
            array('eddbk-purchase-form-session-picker'));

        // FES frontend assets
        if (FesIntegration::isFesLoaded() && FesIntegration::isFesFrontendPage()) {
            $this->enqueueStyle('edd-bk-fes-frontend-style', EDD_BK_CSS_URL . 'fes-frontend.css');
            $this->enqueueScript('edd-bk-fes-frontend-script', EDD_BK_JS_URL . 'fes-frontend.js');
            // Availability assets
            $this->enqueueStyle('edd-bk-availability-css', EDD_BK_CSS_URL . 'availability.css');
            $this->enqueueScript('edd-bk-availability-js', EDD_BK_JS_URL . 'availability.js', array('edd-bk-utils-js'));
            wp_localize_script('edd-bk-availability-js', 'EddBkLocalized', array(
                'ajaxurl' => admin_url('admin-ajax.php')
            ));
            // Timepicker replacer for FES-bundled timepicker
            wp_dequeue_script('jquery-ui-timepicker');
            $this->enqueueStyle('jquery-ui-timepicker-css', EDD_BK_CSS_URL . 'jquery-ui-timepicker.css');
            $this->enqueueScript('jquery-ui-timepicker-addon', EDD_BK_JS_URL . 'jquery-ui-timepicker.js',
                array('jquery-ui-datepicker'));
            $this->enqueueStyle('jquery-ui-timepicker-css', EDD_BK_CSS_URL . 'jquery-ui-timepicker.css');
        }

        // lodash
        // $this->enqueueScript('edd-bk-lodash', EDD_BK_JS_URL . 'lodash.min.js');

        // Load any FES calendar theme present in the uploads dir
        $fesCalendarTheme = FesIntegration::getCalendarThemeStylesheetUrl();
        if ($fesCalendarTheme !== false) {
            wp_enqueue_style('edd-bk-fes-calendar-theme', $fesCalendarTheme);
        }

        // Session picker localization
        wp_localize_script('eddbk-session-picker', 'EddBkSpI18n', array(
            'time'                  => __('Time', 'eddbk'),
            'duration'              => __('Duration', 'eddbk'),
            'loading'               => __('Loading', 'eddbk'),
            'price'                 => __('Price', 'eddbk'),
            'dateFixMsg'            => sprintf(
                __('The date %s was automatically selected for you as the start date to accomodate %s.', 'eddbk'),
                '<span class="edd-bk-datefix-date"></span>',
                '<span class="edd-bk-datefix-length"></span>'
            ),
            'invalidDateMsg'        => sprintf(
                __('The date %s cannot accomodate %s Kindly choose another date or duration.', 'eddbk'),
                '<span class="edd-bk-invalid-date"></span>',
                '<span class="edd-bk-invalid-length"></span>'
            ),
            'noTimesForDateMsg'     => __('No times are available for this date!', 'eddbk'),
            'bookingUnavailableMsg' => __('Your chosen session is unavailable. It may have been booked by someone else. If you believe this is a mistake, please contact the site administrator.', 'eddbk')
        ));

        return $this;
    }

    /**
     * Loads the assets used in the backend.
     * 
     * @return AssetsController This instance.
     */
    public function backendAssets()
    {
        $this->enqueueStyle('edd-bk-mainpage-css', EDD_BK_CSS_URL . 'mainpage.css');
        $this->enqueueStyle('edd-bk-availability-css', EDD_BK_CSS_URL . 'availability.css');
        $this->enqueueScript('edd-bk-availability-js', EDD_BK_JS_URL . 'availability.js', array());

        if (FesIntegration::isFesLoaded()) {
            wp_dequeue_script('jquery-ui-timepicker');
        }
        $this->enqueueStyle('jquery-ui-timepicker-css', EDD_BK_CSS_URL . 'jquery-ui-timepicker.css');
        $this->enqueueScript('jquery-ui-timepicker-addon', EDD_BK_JS_URL . 'jquery-ui-timepicker.js',
            array('jquery-ui-datepicker'));

        $this->enqueueScript('edd-bk-schedule-js', EDD_BK_JS_URL . 'schedule.js');
        $this->enqueueScript('edd-bk-service-js', EDD_BK_JS_URL . 'service.js');
        $this->enqueueScript('edd-bk-bookings-js', EDD_BK_JS_URL . 'bookings.js', array('jquery'));
        $this->enqueueStyle('edd-bk-service-css', EDD_BK_CSS_URL . 'service.css');
        $this->enqueueStyle('edd-bk-tooltips-css', EDD_BK_CSS_URL . 'tooltips.css');

        $this->enqueueScript('edd-bk-jquery-colorbox', EDD_BK_JS_URL . 'jquery.colorbox.js');
        $this->enqueueStyle('edd-bk-jquery-colorbox-css', EDD_BK_CSS_URL . 'colorbox.css');
        
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
        if (!$ver) {
            $ver = EDD_BK_VERSION;
        }
        // Call the enqueue/register function
        call_user_func_array($fn, array($handle, $src, $deps, $ver, $extra));
        return $this;
    }

}
