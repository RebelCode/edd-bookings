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
 * @since 2.1.3
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
    public function addAsset($type, $handle, $src, array $deps = array(), $ver = false, array $extra = array(), $override = false)
    {
        $this->assets[$handle] = $this->normalizeAsset($type, $handle, $src, $deps, $ver, $extra, $override);

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
     * @param boolean $override Whether or not to override a previously registered asset with the same handle.
     * @return array The array containing the normalized asset data.
     */
    protected function normalizeAsset($type, $handle, $src, array $deps = array(), $ver = false, $extra = '', $override = false)
    {
        $data = array(
            'type'         => $type,
            'handle'       => $handle,
            'src'          => $src,
            'dependencies' => $deps,
            'version'      => (!$ver)
                ? EDD_BK_VERSION
                : $ver,
            'extra'        => $extra,
            'override'     => $override
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
     * @param string $action The action: [register, deregister, enqueue, dequeue].
     * @param string $typeOverride The asset type - only used in the event of using a handle for an asset that was not registered with this controller.
     * @return AssetsController
     */
    public function handleAsset($asset, $action = 'register', $typeOverride = 'script')
    {
        if (is_array($asset)) {
            $type = $asset['type'];
            $args = $this->wpArgsForAsset($asset);
        } else if ($this->hasAsset ($asset)) {
            $assetData = $this->getAsset($asset);
            $type = $assetData['type'];
            $args = array($asset);
        } else {
            $type = $typeOverride;
            $args = array($asset);
        }

        // If override property is set, dequeue and deregister any previous assets with the same handle
        if (is_array($asset) && $asset['override'] === true) {
            $this->handleAsset($asset['handle'], 'dequeue', $typeOverride);
            $this->handleAsset($asset['handle'], 'deregister', $typeOverride);
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

}
