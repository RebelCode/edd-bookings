<?php

namespace RebelCode\EddBookings\Block\AvailabilityBuilder\Cell;

use \RebelCode\EddBookings\Block\AbstractBlock;
use \RebelCode\EddBookings\Block\Html\CompositeTag;

/**
 * Basic implementation of an availability builder cell.
 *
 * @since [*next-version*]
 */
class BaseCellBlock extends AbstractBlock
{
    /**
     * The cell class attribute pattern. Depends on the column of the cell.
     *
     * @since [*next-version*]
     */
    const CELL_CLASS_PATTERN = 'edd-bk-col-%s';

    /**
     * The column for this cell.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $column;

    /**
     * The cell content.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $content;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param type $column
     * @param type $content
     */
    public function __construct($column, $content)
    {
        $this->setColumn($column)
            ->setContent($content);
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
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

    /**
     * Gets the HTML class attribute pattern.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getCellClassPattern()
    {
        return static::CELL_CLASS_PATTERN;
    }

    /**
     * Gets the cell class for a given column.
     *
     * @since [*next-version*]
     *
     * @param string $column The column ID.
     *
     * @return string
     */
    public function getCellClass($column)
    {
        return sprintf($this->getCellClassPattern(), $column);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        $class      = sprintf($this->getCellClassPattern(), $this->getColumn());
        $content    = $this->getContent();
        $contentArr = is_array($content)
            ? $content
            : array($content);

        return new CompositeTag('div', array('class' => $class), $contentArr);
    }

}
