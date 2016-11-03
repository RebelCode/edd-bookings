<?php

namespace Aventura\Edd\Bookings\Utils;

/**
 * A utility class for time units and their respective labels.
 *
 * @since 2.1.3
 */
abstract class UnitUtils
{

    /**
     * @const The key identifier for the "seconds" unit
     */
    const UNIT_SECONDS = 'seconds';

    /**
     * @const The key identifier for the "minutes" unit
     */
    const UNIT_MINUTES = 'minutes';

    /**
     * @const The key identifier for the "hours" unit
     */
    const UNIT_HOURS = 'hours';

    /**
     * @const The key identifier for the "days" unit
     */
    const UNIT_DAYS = 'days';

    /**
     * @const The key identifier for the "weeks" unit
     */
    const UNIT_WEEKS = 'weeks';

    /**
     * @const The array key for singular unit labels.
     */
    const LABELS_SINGULAR = 'singular';

    /**
     * @const The array key of plural unit labels.
     */
    const LABELS_PLURAL = 'plural';

    /**
     * Gets the unit identifiers.
     *
     * @return array An array of unit identifiers.
     */
    public static function getUnits()
    {
        $ref = new \ReflectionClass(get_called_class());
        $constants = $ref->getConstants();

        $filtered = array_map(function($value, $key)
        {
            return (stripos($key, 'unit_') === 0)
                ? $value
                : null;
        }, array_values($constants), array_keys($constants));

        return array_filter($filtered);
    }

    /**
     * Gets the unit labels.
     *
     * @param boolean $groupByUnit If true, the resulting array will be grouped by unit identifier keys.
     * @return array An associative array with two keys: "singular" and "plural", each mapping to their own associative array of unit identifiers to unit translated labels.
     *      Alternatively, if the $groupByUnit argument is true, the resulting array will consist of unit identifier keys, each mapping to an associative array with two keys,
     *      "singular" and "plural".
     */
    public static function getUnitLabels($groupByUnit = false)
    {
        $labels = array(
            static::LABELS_SINGULAR => static::getSingularUnitLabels(),
            static::LABELS_PLURAL   => static::getPluralUnitLabels()
        );

        if ($groupByUnit) {
            $result = array();
            foreach (static::getUnits() as $unit) {
                $result[$unit] = array(
                    static::LABELS_SINGULAR => $labels[static::LABELS_SINGULAR][$unit],
                    static::LABELS_PLURAL   => $labels[static::LABELS_PLURAL][$unit],
                );
            }
        } else {
            $result = $labels;
        }

        return $result;
    }

    /**
     * Gets the singular unit labels.
     *
     * @return array An associative array consisting of unit identifier keys mapping to translated singular unit labels.
     */
    public static function getSingularUnitLabels()
    {
        return array(
            static::UNIT_SECONDS => __('second', 'eddbk'),
            static::UNIT_MINUTES => __('minute', 'eddbk'),
            static::UNIT_HOURS   => __('hour', 'eddbk'),
            static::UNIT_DAYS    => __('day', 'eddbk'),
            static::UNIT_WEEKS   => __('week', 'eddbk')
        );
    }

    /**
     * Gets the plural unit labels.
     *
     * @return array An associative array consisting of unit identifier keys mapping to translated plural unit labels.
     */
    public static function getPluralUnitLabels()
    {
        return array(
            static::UNIT_SECONDS => __('seconds', 'eddbk'),
            static::UNIT_MINUTES => __('minutes', 'eddbk'),
            static::UNIT_HOURS   => __('hours', 'eddbk'),
            static::UNIT_DAYS    => __('days', 'eddbk'),
            static::UNIT_WEEKS   => __('weeks', 'eddbk')
        );
    }

}
