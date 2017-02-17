<?php

namespace RebelCode\EddBookings\System\Migration;

use \RebelCode\EddBookings\Version\PatchNotFoundException;

/**
 * Basic implementation of a migrater that uses patches to incrementally bring a system up-to-date.
 *
 * @todo Depend on the plugin instance
 *
 * @since [*next-version*]
 */
class Migrater implements MigraterInterface
{
    /**
     * The threshold for version component numbers.
     *
     * More specifically, it represents the maximum number of possibilities for each version component.
     * This does not apply to the "major" component.
     *
     * For example, a threshold of 1000 means that the minor and patch components can be as high as
     * 999 (since 0 is included).
     *
     * @since [*next-version*]
     */
    const VERSION_NUMBER_THRESHOLD = 1000;

    /**
     * The patches.
     *
     * @since [*next-version*]
     *
     * @var PatchInterface[]
     */
    public $patches;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     */
    public function __construct()
    {
        $this->resetPatches();
    }

    /**
     * Removes all patches.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function resetPatches()
    {
        $this->patches = array();

        return $this;
    }

    /**
     * Gets the patches.
     *
     * @since [*next-version*]
     *
     * @return PatchInterface[] An array of patch instances.
     */
    public function getPatches()
    {
        $this->_sortPatches();

        return $this->patches;
    }

    /**
     * Sets the patches.
     *
     * @since [*next-version*]
     *
     * @param PatchInterface[] $patches An array of patch instances mapped using versions as keys.
     *
     * @return $this This instance.
     */
    public function setPatches(array $patches)
    {
        foreach ($patches as $_version => $_patch) {
            $this->setPatch($_version, $_patch);
        }

        return $this;
    }

    /**
     * Checks if a patch exists for a specific version.
     *
     * @since [*next-version*]
     *
     * @param string $version The version.
     *
     * @return bool True if a patch exists for the given version, otherwise false.
     */
    public function hasPatch($version)
    {
        $key = $this->_genVersionKey($version);

        return isset($this->patches[$key]);
    }

    /**
     * Retrieves the patch for a specific version.
     *
     * @param sting $version The version.
     *
     * @return PatchInterface The patch for the given version.
     *
     * @throws PatchNotFoundException If no patch for the given version was found.
     */
    public function getPatch($version)
    {
        if ($this->hasPatch($version)) {
            $key = $this->_genVersionKey($version);

            return $this->patches[$key];
        }

        throw $this->_createPatchNotFoundException($version);
    }

    /**
     * Sets a patch for the given version.
     *
     * @since [*next-version*]
     *
     * @param string $version The patch version.
     * @param PatchInterface $patch The patch instance.
     *
     * @return $this This instance.
     */
    public function setPatch($version, PatchInterface $patch)
    {
        $key = $this->_genVersionKey($version);

        $this->patches[$key] = $patch;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function migrate($from, $to)
    {
        $patches = $this->_getPatchesBetween($from, $to);

        foreach ($patches as $_patch) {
            $this->_applyPatch($_patch);
        }

        return $this;
    }

    /**
     * Applies the given patch.
     *
     * @param PatchInterface $patch The patch to apply.
     *
     * @return $this This instance.
     *
     * @throws PatchException If an error occurs while applying the patch.
     */
    protected function _applyPatch(PatchInterface $patch)
    {
        $patch->apply();

        return $this;
    }

    /**
     * Generates a key for a given version.
     *
     * @since [*next-version*]
     *
     * @param string $version The input version string.
     *
     * @return int The generated key.
     */
    protected function _genVersionKey($version)
    {
        $intKey     = 0;
        $multiplier = 1;
        $parts      = explode('.', $version);

        foreach (array_reverse($parts) as $_part) {
            $_intPart    = intval($_part);
            $_multiplied = $_intPart * $multiplier;
            $intKey      = $intKey + $_multiplied;
            $multiplier  = $multiplier * static::VERSION_NUMBER_THRESHOLD;
        }

        return $intKey;
    }

    /**
     * Gets the patches whose versions lie in a given range.
     *
     * @since [*next-version*]
     *
     * @param string $from The version range start.
     * @param string $to   The version range end.
     *
     * @return PatchInterface[] An array of patch instances.
     */
    protected function _getPatchesBetween($from, $to)
    {
        $fromKey = $this->_genVersionKey($from);
        $toKey   = $this->_genVersionKey($to);

        $patches = array_filter($this->getPatches(), function($key) use ($fromKey, $toKey) {
            return $key > $fromKey && $key <= $toKey;
        }, ARRAY_FILTER_USE_KEY);

        return $patches;
    }

    /**
     * Sorts the patches.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    protected function _sortPatches()
    {
        ksort($this->patches);

        return $this;
    }

    /**
     * Creates an exception for when a patch is not found.
     *
     * @todo Obtain the exception message from a container
     *
     * @since [*next-version*]
     *
     * @param string $version The version used when searching for a patch.
     *
     * @return PatchNotFoundException The exception instance.
     */
    protected function _createPatchNotFoundException($version)
    {
        return new PatchNotFoundException(
            sprintf(
                __('No patch found for version %s', 'eddbk'),
                $version
            )
        );
    }
}
