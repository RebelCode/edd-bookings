<?php

namespace RebelCode\Wp\Nonce;

/**
 * Basic functionality for a nonce.
 *
 * @since [*next-version*]
 */
abstract class AbstractNonce
{
    /**
     * The nonce ID.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $id;

    /**
     * The nonce code.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $code;

    /**
     * Gets the nonce ID.
     *
     * @since [*next-version*]
     *
     * @return string The nonce ID.
     */
    protected function _getId()
    {
        return $this->id;
    }

    /**
     * Gets the nonce code.
     *
     * @since [*next-version*]
     *
     * @return string The nonce code.
     */
    protected function _getCode()
    {
        return $this->code;
    }

    /**
     * Sets the nonce ID.
     *
     * @since [*next-version*]
     *
     * @param string $id The nonce ID.
     *
     * @return $this This instance.
     */
    protected function _setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets the nonce code.
     *
     * @since [*next-version*]
     *
     * @param string $code The nonce code.
     *
     * @return $this This instance.
     */
    protected function _setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Creates the nonce code and registers it with WordPress.
     *
     * @since [*next-version*]
     *
     * @return string The created code.
     */
    protected function _create()
    {
        $id   = $this->_getId();
        $code = $this->_generateNonceCode($id);

        $this->_setCode($code);

        return $code;
    }

    /**
     * Verifies if the given nonce code is correct and has not expired.
     *
     * @since [*next-version*]
     *
     * @param string $code The code to verify.
     *
     * @return bool True if the code is valid, false otherwise.
     */
    protected function _verify($code)
    {
        return $this->_verifyNonceCode($code, $this->_getId());
    }

    /**
     * Generates a nonce code for a given ID.
     *
     * @since [*next-version*]
     *
     * @param string $id The ID of the nonce.
     *
     * @return string The created nonce code.
     */
    abstract protected function _generateNonceCode($id);

    /**
     * Verifies if a nonce ID and code combination are correct and not expired.
     *
     * @since [*next-version*]
     *
     * @param string $id   The nonce ID.
     * @param string $code The nonce code.
     *
     * @return bool True if the nonce is incorrect or expired, false otherwise.
     */
    abstract protected function _verifyNonceCode($id, $code);
}
