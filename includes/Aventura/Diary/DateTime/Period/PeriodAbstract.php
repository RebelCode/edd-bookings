<?php

namespace Aventura\Diary\DateTime\Period;

/**
 * PeriodAbstract
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class PeriodAbstract implements PeriodInterface
{

    /**
     * {@inheritdoc}
     * 
     * @param \Aventura\Diary\DateTime\Period\PeriodInterface $other The other instance to compare with.
     * @return boolean <b>True</b> if the instances have equal start and duration, <b>false</b> otherwise.
     */
    public function isEqualTo(PeriodInterface $other)
    {
        return $this->getStart()->isEqualTo($other->getStart()) &&
                $this->getDuration()->isEqualTo($other->getDuration());
    }

    /**
     * {@inheritdoc}
     * 
     * @param PeriodInterface $other The other period to compare with.
     * @return boolean <b>True</b> if the periods overlap, <b>false</b> otherwise.
     */
    public function overlaps(PeriodInterface $other)
    {
        if ($this->isEqualTo($other)) {
            return true;
        }
        $s1 = $this->getStart();
        $e1 = $this->getEnd();
        $s2 = $other->getStart();
        $e2 = $other->getEnd();
        return !( $s1->isBefore($s2) && $e1->isBefore($s2) || $s1->isAfter($e2) && $e1->isAfter($e2) );
    }

}
