<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * Description of DotwTimeRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class DotwTimeRangeRule implements RuleInterface
{
    
    /**
     * The time range rule.
     * 
     * @var TimeRangeRule
     */
    protected $_timeRangeRule;
    
    /**
     * The dotw range rule.
     * 
     * @var DotwRangeRule
     */
    protected $_dotwRangeRule;
    
    /**
     * Constructs a new instance.
     */
    public function __construct($dotwLower, $dotwUpper, $timeLower, $timeUpper)
    {
        $this->_dotwRangeRule = new DotwRangeRule($dotwLower, $dotwUpper);
        $this->_timeRangeRule = new TimeRangeRule($timeLower, $timeUpper);
    }

    /**
     * Gets the day of the week range rule
     * 
     * @return DotwRangeRule The day of the week range rule.
     */
    public function getDotwRangeRule()
    {
        return $this->_dotwRangeRule;
    }
    
    /**
     * Gets the time range rule
     * 
     * @return TimeRangeRule The time range rule.
     */
    public function getTimeRangeRule()
    {
        return $this->_timeRangeRule;
    }
    
    /**
     * Sets the negation for this rule.
     * 
     * @param boolean $negation <b>True</b> to negate the rule, <b>false</b> for not negation.
     */
    public function setNegation($negation)
    {
        $this->getDotwRangeRule()->setNegation($negation);
        $this->getTimeRangeRule()->setNegation($negation);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period obeys this rule, <b>false</b> otherwise.
     */
    public function obeys(PeriodInterface $period)
    {
        return $this->getDotwRangeRule()->obeys($period) &&
                $this->getTimeRangeRule()->obeys($period);
    }

}
