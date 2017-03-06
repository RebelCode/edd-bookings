<?php

namespace RebelCode\Wp\Nonce;

/**
 * Standard implementation of a WordPress nonce.
 *
 * @since [*next-version*]
 */
class Nonce extends AbstractNonce implements NonceInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $id     The nonce ID.
     * @param bool   $create [Optional] If true, the nonce code will be auto created. Default: false
     */
    public function __construct($id, $create = false)
    {
        $this->setId($id)
            ->setCode(null);

        if ($create) {
            $this->create();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getId()
    {
        return $this->_getId();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getCode()
    {
        return $this->_getCode();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function create()
    {
        return $this->_create();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function verify($code)
    {
        return $this->_verify($code);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _generateNonceCode($id)
    {
        return wp_create_nonce($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _verifyNonceCode($id, $code)
    {
        return wp_verify_nonce($code, $id);
    }
}
