<?php

namespace Aventura\Edd\Bookings\Factory;

use \Aventura\Diary\DateTime;
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
            foreach($rules as $ruleData) {
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
     * Detects if the given range value is a date or time and creates an object in its place.
     * 
     * @param mixed $value The value to normalize.
     * @return mixed The normalized value.
     */
    public function normalizeRangeValue($value) {
        $normalized = $value;
        // Check if time value
        if (preg_match('/^\\d+:\\d+(:\\d+)?$/', $value)) {
            $normalized = DateTime::fromString($value, 0);
        } else if (preg_match('/^\\d+-\\d+-\\d+$/', $value)) {
            $normalized = DateTime::fromString($value);
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

}
