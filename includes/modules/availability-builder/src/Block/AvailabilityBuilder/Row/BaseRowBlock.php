<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Row;

/**
 * A block for a single row in the availability builder.
 *
 * @since [*next-version*]
 */
class BaseRowBlock extends AbstractRowBlock
{
    protected $content;

    public function __construct($content)
    {
        $this->setContent($content);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
