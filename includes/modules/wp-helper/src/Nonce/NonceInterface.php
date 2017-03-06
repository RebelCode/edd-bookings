<?php

namespace RebelCode\Wp\Nonce;

/**
 * Represents a WordPress nonce.
 *
 * @since [*next-version*]
 */
interface NonceInterface
{
    /**
     * Gets the ID string for this nonce.
     *
     * @since [*next-version*]
     *
     * @return string The nonce ID.
     */
    public function getId();

    /**
     * Gets the nonce code.
     *
     * @since [*next-version*]
     *
     * @return string|null The nonce code or null if the nonce has not yet been created.
     */
    public function getCode();

    /**
     * Creates the nonce code and registers it with WordPress.
     *
     * @since [*next-version*]
     *
     * @return string The created code.
     */
    public function create();

    /**
     * Verifies if the given nonce code is correct and has not expired.
     *
     * @since [*next-version*]
     *
     * @param string $code The code to verify.
     *
     * @return bool True if the code is valid, false otherwise.
     */
    public function verify($code);
}
