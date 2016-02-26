<?php

namespace Aventura\Diary\Bookable\Availability;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RuleInterface;
use \Aventura\Diary\Bookable\Availability\Timetable\TimetableAbstract;

/**
 * A standard Timetable class with an array of rules.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Timetable extends TimetableAbstract
{
    
    /**
     * The rules.
     * 
     * @var type 
     */
    protected $_rules;
    
    /**
     * Constructs a new Timetable instance.
     */
    public function __construct()
    {
        $this->_rules = array();
    }

    /**
     * Adds a rule.
     * 
     * @param RuleInterface $rule The rule to add.
     */
    protected function _addRule(RuleInterface $rule)
    {
        $this->_rules[] = $rule;
    }

    /**
     * Gets the rule at the given index.
     * 
     * @param integer $index The index of the rule to get.
     * @return RuleInterface The rule at the given index.
     */
    protected function _getRule($index)
    {
        return $this->_rules[$index];
    }

    /**
     * Checks if a rule exists at the given index.
     * 
     * @param integer $index The index to check.
     * @return boolean <b>True</b> if a rule exists at the given index, <b>false</b> otherwise.
     */
    protected function _hasRule($index)
    {
        return isset($this->_rules[$index]);
    }

    /**
     * Removes a rule.
     * 
     * @param integer $index The index of the rule to remove.
     */
    protected function _removeRule($index)
    {
        unset($this->_rules[$index]);
    }

    /**
     * Gets the rules.
     * 
     * @return array An array of <b>RuleInterface</b> instances.
     */
    public function getRules()
    {
        return $this->_rules;
    }

}
