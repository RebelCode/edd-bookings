<?php

namespace Aventura\Diary\Bookable\Availability\Timetable;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RuleInterface;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Abstract implementation of a Timetable.
 * 
 * Does not implement any storage functionality regarding rules.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class TimetableAbstract implements TimetableInterface
{
    
    /**
     * Adds a rule.
     * 
     * @param RuleInterface $rule The rule to add.
     * @return TimetableAbstract This instance.
     */
    public function addRule(RuleInterface $rule)
    {
        $this->_addRule($rule);
        return $this;
    }
    
    /**
     * Checks if this timetable has a rule at a specific index.
     * 
     * @param integer $index The index to check.
     * @return boolean <b>True</b> if the timetable has a rule at the given index, <b>false</b> otherwise.
     */
    public function hasRule($index)
    {
        return $this->_hasRule($index);
    }
    
    /**
     * Gets the rule at a specific index.
     * 
     * @param integer $index The index of the rule to retrieve.
     * @return RuleInterface The rule at the given index or <b>null</b> if no rule exists at the given index.
     */
    public function getRule($index)
    {
        return $this->hasRule($index)
                ? $this->_getRule($index)
                : null;
    }
    
    /**
     * Removes the rule at a specific index.
     * 
     * @param integer $index The index of the rule to remove.
     * @return TimetableAbstract This instance.
     */
    public function removeRule($index)
    {
        $this->_removeRule($index);
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period is available, <b>false</b> otherwise.
     */
    public function isAvailable(PeriodInterface $period)
    {
        foreach($this->getRules() as $rule) {
            /* @var $rule RuleInterface */
            if (!$rule->obeys($period)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @abstract To be implemented by extending classes.
     * @param RuleInterface $rule The rule to add.
     * @return TimetableAbstract This instance.
     */
    abstract protected function _addRule(RuleInterface $rule);
    
    /**
     * @abstract To be implemented by extending classes.
     * @param integer $index The index to check.
     * @return boolean <b>True</b> if the timetable has a rule at the given index, <b>false</b> otherwise.
     */
    abstract protected function _hasRule($index);
    
    /**
     * @internal There is no need to check if the index is valid before returning.
     * @abstract To be implemented by extending classes.
     * @param integer $index The index to check.
     * @return RuleInterface The rule instance at the given index.
     */
    abstract protected function _getRule($index);

    /**
     * @abstract To be implemented by extending classes.
     * @param integer $index The index of the rule to remove.
     * @return TimetableAbstract This instance
     */
    abstract protected function _removeRule($index);

}
