<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Day;
use \Aventura\Diary\DateTime\Month;
use \Aventura\Edd\Bookings\CustomPostType\AvailabilityPostType;
use \Aventura\Edd\Bookings\Model\Availability;
use \Aventura\Edd\Bookings\Plugin;

/**
 * The factory class that creates Availabilities.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class AvailabilityFactory extends ModelCptFactoryAbstract
{

    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Availability';
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Creates an availability instance.
     * 
     * @param array $data Array of data to use for creating the instance.
     * @return Availability The created instance.
     */
    public function create(array $data)
    {
        if (!isset($data['id'])) {
            $availability = null;
        } else {
            /* @var $availability Availability */
            $className = $this->getClassName();
            $availability = new $className($data['id']);
            // Normalize the rules
            $rules = isset($data['rules'])
                    ? \maybe_unserialize($data['rules'])
                    : array();
            foreach ($rules as $ruleData) {
                // Get rule data
                $ruleClass = $ruleData['type'];
                $ruleStart = $ruleData['start'];
                $ruleEnd = $ruleData['end'];
                $available = filter_var($ruleData['available'], FILTER_VALIDATE_BOOLEAN);
                // Normalize the start/end values
                $normalizedStart = $this->normalizeRangeValue($ruleStart);
                $normalizedEnd = $this->normalizeRangeValue($ruleEnd);
                // Create the rule instance
                $rule = new $ruleClass($normalizedStart, $normalizedEnd);
                $rule->setNegation(!$available);
                // Add to availability
                $availability->addRule($rule);
            }
        }
        // Return created instance
        return $availability;
    }

    /**
     * Normalizes range values.
     * 
     * - Detects if the given range value is a date or time string and creates an object in its place.
     * - Properly typecasts integers and floats
     * 
     * @param mixed $value The value to normalize.
     * @return mixed The normalized value.
     */
    public function normalizeRangeValue($value)
    {
        $normalized = $value;
        
        // Prepare date/time matching regex
        $timePattern = '\\d+\:\\d+(\:\\d+)?';
        $datePattern = '\\d+-\\d+-\\d+';
        $timeRegex = sprintf('/^%s$/', $timePattern);
        $dateRegex = sprintf('/^%s\s?(%s)?$/', $datePattern, $timePattern);
        
        // Check if time value
        if (preg_match($timeRegex, $value)) {
            $normalized = DateTime::fromString($value, 0);
        } else if (preg_match($dateRegex, $value)) {
            $normalized = DateTime::fromString($value, 0);
        } else if (!filter_var($value, FILTER_VALIDATE_INT) === false) {
            $normalized = intval($value);
        } else if (!filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            $normalized = floatval($value);
        }
        // Shift to UTC if a DateTime instance
        if ($normalized instanceof DateTime) {
            $normalized = $this->getPlugin()->serverTimeToUtcTime($normalized);
        }
        return $normalized;
    }

    /**
     * Creates the CPT instance.
     * 
     * @param array $data Optional array of data. Default: array()
     */
    public function createCpt(array $data = array())
    {
        return new AvailabilityPostType($this->getPlugin());
    }

    /**
     * Creates an availability from legacy meta.
     * 
     * @param string $serviceName The name of the parent service.
     * @param meta $legacy The legacy meta
     * @return integer The created availability ID.
     */
    public function createFromLegacyMeta($serviceName, $legacy)
    {
        // Create the schedule
        $availabilityName = sprintf("%s's Availability", $serviceName);
        $availabilityId = $this->getPlugin()->getAvailabilityController()->insert(array(
                'post_title' => $availabilityName
        ));

        // Convert the rules
        $rules = array();
        foreach ($legacy as $legacyRule) {
            $newRule = array(
                    'type'      => $this->mapLegacyRuleType($legacyRule['range_type']),
                    'start'     => $this->maybeNormalizeLegacyDateFormat($legacyRule['from']),
                    'end'       => $this->maybeNormalizeLegacyDateFormat($legacyRule['to']),
                    'available' => $legacyRule['available']
            );
            // Add day star/end times for custom rule range values
            if ($legacyRule['range_type'] === 'custom') {
                $newRule['start'] .= ' 00:00:00';
                $newRule['end'] .= ' 23:59:59';
            }
            $rules[] = $newRule;
        }

        // Save meta
        $this->getPlugin()->getAvailabilityController()->saveMeta(
                $availabilityId, array(
                'rules' => $rules
                )
        );
        return $availabilityId;
    }

    /**
     * Maps a legacy rule type name into the newly used rule class name.
     * 
     * @param string $legacyRuleType The legacy rule type name.
     * @return string The matching rule type class name.
     */
    public function mapLegacyRuleType($legacyRuleType)
    {
        $ruleType = null;
        // Check for days of the week
        $days = array_keys(Day::getAll());
        if (in_array(strtoupper($legacyRuleType), $days)) {
            $ruleType = sprintf('Aventura\\Edd\\Bookings\\Availability\\Rule\\%sTimeRule', ucfirst($legacyRuleType));
        } else {
            switch ($legacyRuleType) {
                case 'all_week':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\AllWeekTimeRule';
                    break;
                case 'weekdays':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\WeekdaysTimeRule';
                    break;
                case 'weekends':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\WeekendTimeRule';
                    break;
                case 'custom':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\CustomDateRule';
                    break;
                case 'months':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\MonthRule';
                    break;
                case 'weeks':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\WeekNumRule';
                    break;
                case 'days':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Availability\\Rule\\DotwRule';
                    break;
            }
        }
        if (!is_null($ruleType)) {
            return str_replace('\\', '\\\\', $ruleType);
        }
        return null;
    }
    
    /**
     * Checks if the value is in legacy date format and if so, normalizes it.
     * 
     * @param string $value The original value.
     * @return string The value, maybe normalized.
     */
    public function maybeNormalizeLegacyDateFormat($value)
    {
        $matches = array();
        $newValue = $value;
        if (preg_match('/^(\\d+)\/(\\d+)\/(\\d+)$/', $value, $matches)) {
            $year = intval($matches[3]);
            $month = intval($matches[1]);
            $day = intval($matches[2]);
            $newValue = sprintf('%04d-%02d-%02d', $year, $month, $day);
        } else {
            $days = Day::getAll();
            $months = Month::getAll();
            $valueToUpper = strtoupper($value);
            if (in_array($valueToUpper, array_keys($days))) {
                $newValue = $days[$valueToUpper];
            } else if (in_array($valueToUpper, array_keys($months))) {
                $newValue = $months[$valueToUpper];
            }
        }
        return $newValue;
    }

}
