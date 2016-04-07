<?php

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\CallbackRule;
use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RuleInterface;
use \Aventura\Diary\DateTime\Period\PeriodInterface;

namespace Aventura\Diary\Bookable\Availability\Timetable\Rule;

/**
 * Description of CallbackRule
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class CallbackRule implements RuleInterface
{
    
    /**
     * The callback.
     * 
     * @var callable
     */
    protected $_callback;
    
    /**
     * Constructs a new instance.
     * 
     * @param callable|null $callback The callback.
     */
    public function __construct($callback = null)
    {
        $this->setCallback($callback);
    }
    
    /**
     * Gets the callback
     * 
     * @return callable
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * Sets the callback.
     * 
     * @param callable $callback The callback.
     * @return CallbackRule This instance.
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * Executes the callback using the given parameters.
     * 
     * @param array $params The params to be passed to the callback, as an indexed array.
     * @return mixed The return value of the callback.
     * @throws Exception If the callback is not a valid callable.
     */
    public function executeCallback(array $params = array())
    {
        if (is_callable($this->getCallback())) {
            return call_user_func_array($this->getCallback(), $params);
        } else {
            throw new Exception('Callback is not a valid callable.');
        }
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $period The period to check.
     * @return boolean <b>True</b> if the period obeys the rule, <b>false</b> otherwise.
     */
    public function obeys(PeriodInterface $period)
    {
        return $this->executeCallback(array($period));
    }

}
