<?php

namespace RebelCode\EddBookings\Model\Availability;

use \Dhii\Data\ValueAwareInterface;
use \Dhii\Espresso\EvaluationException;
use \Dhii\Espresso\Term\LiteralTerm;
use \Dhii\Expression\AbstractExpression;
use \RebelCode\Bookings\Expression\BookingContext;
use \RebelCode\Bookings\Model\Availability\RangeRuleInterface;

/**
 * Description of SimpleRangeRule
 *
 * @since [*next-version*]
 */
class SimpleRangeRule extends AbstractExpression implements RangeRuleInterface
{
    protected $negated;

    public function __construct($start, $end, $negated = false)
    {
        $this->_setTerms(array(
            new LiteralTerm($start),
            new LiteralTerm($end)
        ));
        $this->negated = $negated;
    }

    public function getEnd()
    {
        return $this->_getTerm(1)->evaluate();
    }

    public function getStart()
    {
        return $this->_getTerm(0)->evaluate();
    }

    public function evaluate(ValueAwareInterface $ctx = null)
    {
        if (!$ctx instanceof BookingContext) {
            throw new EvaluationException(
                sprintf(
                    'Context given in %1$s::%2$s is not a BookingContext. %3$s given.',
                    get_called_class(), __METHOD__, get_class($ctx)
                )
            );
        }

        return true;
    }

    public function getTerms()
    {
        return $this->_getTerms();
    }

    public function isNegated()
    {
        return $this->negated;
    }
}
