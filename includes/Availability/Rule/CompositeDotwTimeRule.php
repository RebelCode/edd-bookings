<?php

namespace Aventura\Edd\Bookings\Availability\Rule;

use \Aventura\Diary\DateTime\DateTimeInterface;

/**
 * A composite time rule made up of a set of DotwTimeRules
 *
 * @since [*next-version*]
 */
class CompositeDotwTimeRule extends AbstractCompositeTimeRule
{

    /**
     * The lower dotw index.
     *
     * @var int
     */
    protected $lowerDotw;

    /**
     * The upper dotw index.
     *
     * @var int
     */
    protected $upperDotw;

    /**
     * Constructor.
     *
     * @param int $lowerDotw Lower dotw index.
     * @param int $upperDotw Upper dotw index.
     * @param DateTimeInterface $startTime Lower day time.
     * @param DateTimeInterface $endTime Upper day time.
     */
    public function __construct($lowerDotw, $upperDotw, DateTimeInterface $startTime, DateTimeInterface $endTime)
    {
        parent::__construct($startTime, $endTime);
        $this->lowerDotw = $lowerDotw;
        $this->upperDotw = $upperDotw;
    }

    /**
     * Gets the range's lower day of the week.
     *
     * @return int The dotw index.
     */
    public function getLowerDotw()
    {
        return $this->lowerDotw;
    }

    /**
     * Gets the range's upper day of the week.
     *
     * @return int The dotw index.
     */
    public function getUpperDotw()
    {
        return $this->upperDotw;
    }

    /**
     * {@inheritdoc}
     */
    public function generateChildRules()
    {
        $rules = array();
        for ($i = $this->getLowerDotw(); $i <= $this->getUpperDotw(); $i++) {
            $rules[] = new DotwTimeRule($i, $this->getLower(), $this->getUpper());
        }

        return $rules;
    }

}
