<?php

namespace RebelCode\Wp\Block;

use RebelCode\Block\AbstractBlock;
use RebelCode\Wp\Nonce\NonceInterface;

/**
 * A block for a nonce field.
 *
 * @since [*next-version*]
 */
class NonceFieldBlock extends AbstractBlock
{
    /**
     * The default nonce field name.
     *
     * @since [*next-version*]
     */
    const DEFAULT_FIELD_NAME = '_wpnonce';

    /**
     * The nonce instance.
     *
     * @since [*next-version*]
     *
     * @var NonceInterface
     */
    protected $nonce;

    /**
     * The name to use for the field.
     *
     * This will reflect the index where the nonce can be found in the submitted request.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $name;

    /**
     * Referer usage flag.
     *
     * If true, a second hidden field will be used to specify th referer.
     *
     * @since [*next-version*]
     *
     * @var bool
     */
    protected $useReferer;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param NonceInterface $nonce The nonce instance.
     * @param string $name The "name" to use for the field and index in the request.
     * @param bool $useReferer True to also include the referer, false to exclude.
     */
    public function __construct(
        NonceInterface $nonce,
        $name = self::DEFAULT_FIELD_NAME,
        $useReferer = true
    ) {
        $this->setNonce($nonce)
            ->setName($name)
            ->useReferer($useReferer);
    }

    /**
     * Gets the nonce instance.
     *
     * @since [*next-version*]
     *
     * @return NonceInterface
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Gets the field name.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets whether the referer will also be included.
     *
     * @since [*next-version*]
     *
     * @return bool True if the referer will be included, false if not.
     */
    public function getUseReferer()
    {
        return $this->useReferer;
    }

    /**
     * Sets the nonce instance.
     *
     * @since [*next-version*]
     *
     * @param NonceInterface $nonce
     *
     * @return $this
     */
    public function setNonce(NonceInterface $nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }


    /**
     * Sets the field name.
     *
     * @since [*next-version*]
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets whether to include the referer.
     *
     * @since [*next-version*]
     *
     * @param bool $useReferer
     *
     * @return $this
     */
    public function setUseReferer($useReferer)
    {
        $this->useReferer = $useReferer;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getOutput()
    {
        return \wp_nonce_field(
            $this->getNonce()->getId(),
            $this->getName(),
            $this->useReferer()
        );
    }
}
