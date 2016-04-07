<?php

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

use \Aventura\Diary\DateTime\Period\PeriodInterface;

/**
 * RangeRuleAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class RangeRuleAbstract implements RuleInterface
{

    /**
     * The range's lower value.
     * 
     * @var mixed
     */
    protected $_lower;

    /**
     * The range's upper values.
     * 
     * @var mixed
     */
    protected $_upper;

    /**
     * Range lower value inclusive flag.
     * 
     * @var boolean
     */
    protected $_lowerInclusive;

    /**
     * Range upper value inclusive flag.
     * 
     * @var boolean
     */
    protected $_upperInclusive;

    /**
     * Negation flag.
     * 
     * @var boolean
     */
    protected $_negation;
    
    /**
     * Gets the range lower value.
     * 
     * @return mixed The range lower value.
     */
    public function getLower()
    {
        return $this->_lower;
    }

    /**
     * Gets the range upper value.
     * 
     * @return mixed The range upper value.
     */
    public function getUpper()
    {
        return $this->_upper;
    }

    /**
     * Gets whether the range lower value is inclusive or not.
     * 
     * @return boolean <b>True</b> if the range lower value is inclusive, <b>false</b> otherwise.
     */
    public function isLowerInclusive()
    {
        return $this->_lowerInclusive;
    }

    /**
     * Gets whether the range upper value is inclusive or not.
     * 
     * @return boolean <b>True</b> if the range upper value is inclusive, <b>false</b> otherwise.
     */
    public function isUpperInclusive()
    {
        return $this->_upperInclusive;
    }
    
    /**
     * Gets whether or not the rule is negated.
     * 
     * @return boolean <b>True</b> if the rule is negated, <b>false</b> otherwise.
     */
    public function isNegated()
    {
        return $this->_negation;
    }

    /**
     * Sets the range lower value.
     * 
     * @param mixed $lower The range lower value.
     * @return RangeRuleAbstract This instance.
     */
    public function setLower($lower)
    {
        $this->_lower = $lower;
        return $this;
    }

    /**
     * Sets the range upper value.
     * 
     * @param mixed $upper The range upper value.
     * @return RangeRuleAbstract This instance.
     */
    public function setUpper($upper)
    {
        $this->_upper = $upper;
        return $this;
    }

    /**
     * Sets whether or not the range lower value is inclusive.
     * 
     * @param boolean $lowerInclusive <b>True</b> to set the range lower value as inclusive, <b>false</b> for exclusive.
     * @return RangeRuleAbstract
     */
    public function setLowerInclusive($lowerInclusive)
    {
        $this->_lowerInclusive = $lowerInclusive;
        return $this;
    }

    /**
     * Sets whether or not the range upper value is inclusive.
     * 
     * @param boolean $upperInclusive <b>True</b> to set the range upper value as inclusive, <b>false</b> for exclusive.
     * @return RangeRuleAbstract
     */
    public function setUpperInclusive($upperInclusive)
    {
        $this->_upperInclusive = $upperInclusive;
        return $this;
    }

    /**
     * Sets the negation for this rule.
     * 
     * @param boolean $negation <b>True</b> to negate the rule, <b>false</b> for not negation.
     */
    public function setNegation($negation)
    {
        $this->_negation = $negation;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period obeys the rule, <b>false</b> if not.
     */
    public function obeys(PeriodInterface $period)
    {
        return $this->_obeys($period) && !$this->isNegated();
    }
    
    /**
     * Internal obeys method for overriding by subclasses.
     */
    abstract protected function _obeys(PeriodInterface $period);

}
