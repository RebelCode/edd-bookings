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
     * @constant The frontend context for enqueueing.
     */
    const CONTEXT_FRONTEND = 'frontend';

    /**
     * @constant The backend context for enqueueing.
     */
    const CONTEXT_BACKEND = 'backend';

    /**
     * @constant The login context for enqueueing.
     */
    const CONTEXT_LOGIN = 'login';

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
     * @constant The action hook triggered by this class to recieve the list asset handles to be enqueued.
     */
    const HOOK_ENQUEUE = 'eddbk_enqueue_assets';

    /**
     * The assets.
     *
     * @var array
     */
    protected $assets = array();

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        // Register hooks for loading assets
        $this->getPlugin()->getHookManager()
            ->addAction(static::HOOK_FRONTEND, $this, 'enqueueFrontendAssets', 100)
            ->addAction(static::HOOK_ADMIN, $this, 'enqueueBackendAssets', 100)
            ->addAction(static::HOOK_LOGIN, $this, 'enqueueLoginAssets', 100);
        ;
    }

    /**
     * Adds an enqueue hook.
     *
     * @param mixed $component The object that implements the callback. Can be null.
     * @param callable $callback The callback.
     * @return AssetsController This instance.
     */
    public function nq($component, $callback)
    {
        $this->getPlugin()->getHookManager()->addFilter(static::HOOK_ENQUEUE, $component, $callback, 10, 3);

        return $this;
    }

    /**
     * Gets the registered assets.
     *
     * @return array
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Gets a registered asset by its handle.
     *
     * @param string $handle The asset handle.
     * @return array|null The asset array or null if the handle is not registered.
     */
    public function getAsset($handle)
    {
        return $this->hasAsset($handle)
            ? $this->assets[$handle]
            : null;
    }

    /**
     * Checks if an asset handle is registered.
     *
     * @param string $handle The string handle.
     * @return boolean True if the handle is registered, false if not.
     */
    public function hasAsset($handle)
    {
        return isset($this->assets[$handle]);
    }

    /**
     * Registers an asset.
     *
     * @param string $type The type: {@link AssetsController::TYPE_SCRIPT} or {@link AssetsController::TYPE_STYLE}.
     * @param string $handle The string handle.
     * @param string $src The path to the asset's source file.
     * @param array $deps An array of asset handles that the asset being registered depends on.
     * @param string $ver The version of the asset.
     * @param array $extra Optional extra information.
     * @return AssetsController This instance.
     */
    public function addAsset($type, $handle, $src, array $deps = array(), $ver = false, array $extra = array())
    {
        $this->assets[$handle] = $this->normalizeAsset($type, $handle, $src, $deps, $ver, $extra);

        return $this;
    }

    /**
     * Removes an asset.
     *
     * @param string $handle The handle of the registered asset to be removed.
     * @return AssetsController This instance.
     */
    public function removeAsset($handle)
    {
        unset($this->assets[$handle]);

        return $this;
    }

    /**
     * Resets the list of registered assets back to empty.
     *
     * @return AssetsController This instance.
     */
    public function resetAssets()
    {
        $this->assets = array();

        return $this;
    }

    /**
     * Noramlizes asset information.
     *
     * @param string $type The type: {@link AssetsController::TYPE_SCRIPT} or {@link AssetsController::TYPE_STYLE}.
     * @param string $handle The string handle.
     * @param string $src The path to the asset's source file.
     * @param array $deps An array of asset handles that the asset being registered depends on.
     * @param string $ver The version of the asset.
     * @param string $extra Optional extra information.
     * @return array The array containing the normalized asset data.
     */
    protected function normalizeAsset($type, $handle, $src, array $deps = array(), $ver = false, $extra = '')
    {
        $data = array(
            'type'         => $type,
            'handle'       => $handle,
            'src'          => $src,
            'dependencies' => $deps,
            'version'      => (!$ver)
                ? EDD_BK_VERSION
                : $ver,
            'extra'        => $extra
        );
        return array_merge($data, $extra);
    }

    /**
     * Enqueues the backend assets.
     *
     * @return AssetsController This instance.
     */
    public function enqueueBackendAssets()
    {
        $this->enqueueAssetsForContext(static::CONTEXT_BACKEND);

        return $this;
    }

    /**
     * Enqueues the frontend assets.
     *
     * @return AssetsController This instance.
     */
    public function enqueueFrontendAssets()
    {
        $this->enqueueAssetsForContext(static::CONTEXT_FRONTEND);

        return $this;
    }

    /**
     * Enqueues the login page assets.
     *
     * @return AssetsController This instance.
     */
    public function enqueueLoginAssets()
    {
        $this->enqueueAssetsForContext(static::CONTEXT_LOGIN);

        return $this;
    }

    /**
     * Enqueues the assets for a specific context.
     *
     * @param type $context The context string: ["login", "backend", "frontend", "common"]
     */
    public function enqueueAssetsForContext($context)
    {
        // First register all the assets
        array_map(array($this, 'registerAsset'), $this->getAssets());
        // Then enqueue selectively
        $assetHandles = $this->getAssetsToEnqueue($context);
        array_map(array($this, 'enqueueAsset'), $assetHandles);

        return $this;
    }

    /**
     * Gets the assets to be enqueued for a specific context.
     *
     * @param string $context The context: ["frontend", "backend", "login", "common"]
     * @return array
     */
    public function getAssetsToEnqueue($context)
    {
        return apply_filters(static::HOOK_ENQUEUE, array(), $context, $this);
    }

    /**
     * Registers an asset.
     *
     * @param  array $asset The asset data assoc. array
     * @return AssetsController
     */
    public function registerAsset(array $asset)
    {
        $this->handleAsset($asset);

        return $this;
    }

    /**
     * Unregisters an asset.
     *
     * @param  array $asset The asset data assoc. array
     * @return AssetsController
     */
    public function unregisterAsset(array $asset)
    {
        $this->handleAsset($asset, 'deregister');

        return $this;
    }

    /**
     * Enqueues an asset.
     *
     * @param array|string $asset The asset data assoc. array or the handle.
     * @return AssetsController
     */
    public function enqueueAsset($asset)
    {
        $this->handleAsset($asset, 'enqueue');

        return $this;
    }

    /**
     * Dequeues an asset.
     *
     * @param array|string $asset The asset data assoc. array or the handle.
     * @return AssetsController
     */
    public function dequeueAsset($asset)
    {
        $this->handleAsset($asset, 'dequeue');

        return $this;
    }

    /**
     * All in one method for handling assets with WordPress.
     *
     * @param array|string $asset The asset data assoc. array or the asset handle.
     * @param string $action The action: [register, deregister, enqueue, dequeue]
     * @return AssetsController
     */
    public function handleAsset($asset, $action = 'register')
    {
        if (is_array($asset)) {
            $type = $asset['type'];
            $args = $this->wpArgsForAsset($asset);
        } else if ($this->hasAsset ($asset)) {
            $assetData = $this->getAsset($asset);
            $type = $assetData['type'];
            $args = array($asset);
        } else {
            trigger_error('Cannot enqueue given asset: ' . $asset);
            return $this;
        }
        // Call the WordPress function
        $wpFn = sprintf('wp_%s_%s', $action, $type);
        call_user_func_array($wpFn, $args);

        return $this;
    }

    /**
     * Gets the arguments to pass to a WordPress function for a particular asset.
     *
     * @param array $asset The asset data assoc. array
     * @return array The arguments.
     */
    protected function wpArgsForAsset(array $asset)
    {
        $args = array($asset['handle'], $asset['src'], $asset['dependencies']);
        // Prepare the version arg
        $args[] = !$asset['version']
            ? EDD_BK_VERSION
            : $asset['version'];
        // Prepare the final extra arg
        switch ($asset['type']) {
            case static::TYPE_SCRIPT:
                $args[] = $asset['footer'];
                break;
            case static::TYPE_STYLE:
                $args[] = $asset['media'];
                break;
        }

        return $args;
    }

    /**
     * Localizes an asset with JS data.
     *
     * @param string $handle The handle of the asset to localize.
     * @param string $key The key.
     * @param array $data An associative array containing the object data.
     * @return AssetsController
     */
    public function attachScriptData($handle, $key, $data)
    {
        wp_localize_script($handle, sprintf('EddBkLocalized_%s', $key), $data);

        return $this;
    }

    /**
     * Loads the assets used on both backend and frontend.
     *
     * @return AssetsController This instance.
     */
    public function commonAssets()
    {

        $this->registerStyle('edd-bk-bookings-css', EDD_BK_CSS_URL . 'bookings.css');

        wp_localize_script('eddbk.ajax', 'EddBkAjaxLocalized',
            array(
            'url' => admin_url('admin-ajax.php')
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
        $this->enqueueStyle('edd-bk-fullcalendar-css', EDD_BK_JS_URL . 'fullcalendar/fullcalendar.min.css',
            array('edd-bk-fc-reset'));
        $this->registerScript('edd-bk-moment-js', EDD_BK_JS_URL . 'fullcalendar/lib/moment.min.js');
        $this->enqueueScript('edd-bk-fullcalendar-js', EDD_BK_JS_URL . 'fullcalendar/fullcalendar.min.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-tooltip', 'edd-bk-moment-js'));
        $this->enqueueScript('edd-bk-bookings-calendar-js', EDD_BK_JS_URL . 'bookings-calendar.js',
            array('edd-bk-fullcalendar-js'));

        $this->enqueueStyle('edd-bk-bookings-css');

        wp_localize_script('edd-bk-bookings-calendar-js', 'EddBkFc',
            array(
            'postEditUrl' => admin_url('post.php?post=%s&action=edit'),
            'theme'       => !is_admin(),
            'fesLinks'    => !is_admin()
        ));

        wp_localize_script('edd-bk-bookings-calendar-js', 'EddBkLocalized',
            array(
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
        $this->registerScript('eddbk-purchase-form-session-picker',
            EDD_BK_JS_URL . 'class-purchase-form-session-picker.js', array('eddbk-session-picker'));
        $this->enqueueScript('edd-bk-service-frontend', EDD_BK_JS_URL . 'service-frontend.js',
            array('eddbk-purchase-form-session-picker'));

        // FES frontend assets
        if (FesIntegration::isFesLoaded() && FesIntegration::isFesFrontendPage()) {
            $this->enqueueStyle('edd-bk-fes-frontend-style', EDD_BK_CSS_URL . 'fes-frontend.css');
            $this->enqueueScript('edd-bk-fes-frontend-script', EDD_BK_JS_URL . 'fes-frontend.js');
            // Availability assets
            $this->enqueueStyle('edd-bk-availability-css', EDD_BK_CSS_URL . 'availability.css');
            $this->enqueueScript('edd-bk-availability-js', EDD_BK_JS_URL . 'availability.js', array('edd-bk-utils-js'));
            wp_localize_script('edd-bk-availability-js', 'EddBkLocalized',
                array(
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
        wp_localize_script('eddbk-session-picker', 'EddBkSpI18n',
            array(
            'time'                  => __('Time', 'eddbk'),
            'duration'              => __('Duration', 'eddbk'),
            'loading'               => __('Loading', 'eddbk'),
            'price'                 => __('Price', 'eddbk'),
            'dateFixMsg'            => sprintf(
                __('The date %s was automatically selected for you as the start date to accomodate %s.', 'eddbk'),
                '<span class="edd-bk-datefix-date"></span>', '<span class="edd-bk-datefix-length"></span>'
            ),
            'invalidDateMsg'        => sprintf(
                __('The date %s cannot accomodate %s Kindly choose another date or duration.', 'eddbk'),
                '<span class="edd-bk-invalid-date"></span>', '<span class="edd-bk-invalid-length"></span>'
            ),
            'noTimesForDateMsg'     => __('No times are available for this date!', 'eddbk'),
            'bookingUnavailableMsg' => __('Your chosen session is unavailable. It may have been booked by someone else. If you believe this is a mistake, please contact the site administrator.',
                'eddbk')
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

}
