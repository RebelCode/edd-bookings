<?php

namespace RebelCode\Storage\WordPress\Adapter\Validation;

use Dhii\Validation\AbstractValidatorBase;

/**
 * Common functionality for validation used by WordPress adapters.
 *
 * @since [*next-version*]
 */
abstract class AbstractValidator extends AbstractValidatorBase
{
    /**
     * The translator used by this instance to translate text.
     *
     * @todo Constrain to a real translation-dedicated type.
     *
     * @since [*next-version*]
     *
     * @var mixed
     */
    protected $translator;

    /**
     * Translates a string.
     *
     * @since [*next-version*]
     *
     * @param string $text    The string being translated.
     * @param string $context The context of translation.
     *
     * @return string The translated string.
     */
    protected function __($text, $context = null)
    {
        $translator = $this->_getTranslator();
        if (!$translator) {
            return $this->_noOpTranslate($text, $context);
        }

        return $translator->translate();
    }

    /**
     * Assigns the translator to be used by this instance to translate text.
     *
     * @since [*next-version*]
     *
     * @todo Constrain to a real translation-dedicated type.
     *
     * @param mixed $translator The translator.
     * 
     * @return $this This instance.
     */
    protected function _setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Retrieves the translator used by this instance to translate text
     *
     * @since [*next-version*]
     *
     * @todo Constrain to a real translation-dedicated type.
     *
     * @return mixed|null The translator, if any.
     */
    protected function _getTranslator()
    {
        return $this->translator;
    }

    /**
     * Triggered when attempting to translate a string, but no translator is found.
     *
     * @since [*next-version*]
     *
     * @param string $text The string that is being translated.
     * @param string $context The context of the translation.
     *
     * @return string The translated string.
     */
    protected function _noOpTranslate($text, $context)
    {
        return $text;
    }
}
