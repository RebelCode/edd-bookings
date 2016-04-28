<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Day;
use \Aventura\Diary\DateTime\Month;
use \Aventura\Edd\Bookings\CustomPostType\TimetablePostType;
use \Aventura\Edd\Bookings\Model\Timetable;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Description of TimetableFactory
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class TimetableFactory extends ModelCptFactoryAbstract
{

    /**
     * {@inheritdoc}
     */
    const DEFAULT_CLASSNAME = 'Aventura\\Edd\\Bookings\\Model\\Timetable';
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
    }

    /**
     * Creates a timetable instance.
     * 
     * @param array $data Array of data to use for creating the instance.
     * @return Timetable The created instance.
     */
    public function create(array $data)
    {
        if (!isset($data['id'])) {
            $timetable = null;
        } else {
            /* @var $timetable Timetable */
            $className = $this->getClassName();
            $timetable = new $className($data['id']);
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
                // Add to timetable
                $timetable->addRule($rule);
            }
        }
        // Return created instance
        return $timetable;
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
        return new TimetablePostType($this->getPlugin());
    }

    /**
     * Creates a timetable from legacy meta.
     * 
     * @param string $serviceName The name of the parent service.
     * @param meta $legacy The legacy meta
     * @return integer The created timetable ID.
     */
    public function createFromLegacyMeta($serviceName, $legacy)
    {
        // Create the schedule
        $timetableName = sprintf("%s's Timetable", $serviceName);
        $timetableId = $this->getPlugin()->getTimetableController()->insert(array(
                'post_title' => $timetableName
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
        $this->getPlugin()->getTimetableController()->saveMeta(
                $timetableId, array(
                'rules' => $rules
                )
        );
        return $timetableId;
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
            $ruleType = sprintf('Aventura\\Edd\\Bookings\\Timetable\\Rule\\%sTimeRule', ucfirst($legacyRuleType));
        } else {
            switch ($legacyRuleType) {
                case 'all_week':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\AllWeekTimeRule';
                    break;
                case 'weekdays':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\WeekdaysTimeRule';
                    break;
                case 'weekends':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\WeekendTimeRule';
                    break;
                case 'custom':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\CustomDateRule';
                    break;
                case 'months':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\MonthRule';
                    break;
                case 'weeks':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\WeekNumRule';
                    break;
                case 'days':
                    $ruleType = 'Aventura\\Edd\\Bookings\\Timetable\\Rule\\DotwRule';
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
