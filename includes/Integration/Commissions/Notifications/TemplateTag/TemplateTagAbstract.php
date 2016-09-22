<?php

namespace Aventura\Edd\Bookings\Integration\Commissions\Notifications\TemplateTag;

/**
 * Abstract implementation of an EDD Commissions notification email template tag.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class TemplateTagAbstract implements TemplateTagInterface
{

    /**
     * The template tag name.
     * 
     * @var string
     */
    protected $name;

    /**
     * The template tag description.
     * 
     * @var string
     */
    protected $desc;

    /**
     * Constructs a new instance.
     * 
     * @param string $name The template tag name.
     * @param string $desc The tmeplate tag description.
     */
    public function __construct($name, $desc)
    {
        $this->setName($name)
            ->setDesc($desc);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->desc;
    }

    /**
     * Sets the template tag name.
     * 
     * @param string $name The template tag name.
     * @return TemplateTagAbstract This instance.
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the template tag description.
     * 
     * @param string $desc The tmeplate tag description.
     * @return TemplateTagAbstract This instance.
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
        return $this;
    }

}
