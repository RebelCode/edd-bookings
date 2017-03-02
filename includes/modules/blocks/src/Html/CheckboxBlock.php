<?php

namespace RebelCode\Block\Html;

/**
 * Description of CheckboxBlock
 *
 * @since [*next-version*]
 */
class CheckboxBlock extends InputTag
{

    const INPUT_TYPE = 'checkbox';
    const CHECKED_PROP = 'checked';

    public function __construct($id, $name, $value, $checked, $attributes = array())
    {
        parent::__construct(static::INPUT_TYPE, $id, $name, $value, $attributes);

        $this->setChecked($checked);
    }

    public function setChecked($checked)
    {
        if ($checked) {
            $this->setAttribute(static::CHECKED_PROP, static::CHECKED_PROP);

            return $this;
        }

        $this->removeAttribute(static::CHECKED_PROP);

        return $this;
    }

    public function isChecked()
    {
        return $this->getAttribute(static::CHECKED_PROP, '') === static::CHECKED_PROP;
    }
}
