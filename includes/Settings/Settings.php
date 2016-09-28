<?php

namespace Aventura\Edd\Bookings\Settings;

use \Aventura\Edd\Bookings\Plugin;
use \Aventura\Edd\Bookings\Settings\Database\Record\RecordInterface;
use \Aventura\Edd\Bookings\Settings\Database\Record\SubRecord;
use \Aventura\Edd\Bookings\Settings\Section\SectionInterface;

/**
 * Standard implementation of a settings controller.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class Settings extends AbstractSettings
{

    /**
     * Constructs a new instance.
     *
     * @param RecordInterface $record The database record instance.
     */
    public function __construct(RecordInterface $record)
    {
        $this->setRecord($record)
            ->resetSections()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function createSectionRecord(SectionInterface $section)
    {
        return new SubRecord($this->getRecord(), $section->getId());
    }

}
