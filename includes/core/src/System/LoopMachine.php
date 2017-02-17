<?php

namespace RebelCode\EddBookings\System;

use \Iterator;
use \SplObjectStorage;
use \SplObserver;
use \SplSubject;

/**
 * A state machine that processes a list of items and informs observers.
 *
 * @since [*next-version*]
 */
class LoopMachine implements SplSubject
{

    /**
     * Constant used to indicate that the machine is about to begin processing.
     *
     * @since [*next-version*]
     */
    const STATE_START = 1;

    /**
     * Constant used to indicate that the machine is currently processing an item.
     *
     * @since [*next-version*]
     */
    const STATE_LOOP = 2;

    /**
     * Constant used to indicate that the machine has finished processing.
     *
     * @since [*next-version*]
     */
    const STATE_END = 0;

    /**
     * The observer instances.
     *
     * @since [*next-version*]
     *
     * @var SplObjectStorage
     */
    protected $observers;

    /**
     * The machine's state.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $state;

    /**
     * The current iteration item.
     *
     * @since [*next-version*]
     *
     * @var mixed
     */
    protected $current;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     */
    public function __construct()
    {
        $this->detachAll()
            ->_setState(static::STATE_END);
    }

    /**
     * Retrieves the currently attached observers.
     *
     * @since [*next-version*]
     *
     * @return SplObserver[] The observer instances.
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Retrieves the current state of the machine.
     *
     * @since [*next-version*]
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the current state of the machine.
     *
     * @since [*next-version*]
     *
     * @param int $state The state of the machine.
     *
     * @return $this This instance.
     */
    protected function _setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Retrieves the current iteration item.
     *
     * @since [*next-version*]
     *
     * @return type
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Sets the current iteration item.
     *
     * @since [*next-version*]
     *
     * @param mixed $current The current item in the loop.
     *
     * @return $this This instance.
     */
    protected function _setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param int $priority The priority: higher numbers indicate earlier notification. Default: 0
     */
    public function attach(SplObserver $observer, $priority = 0)
    {
        $this->observers[$observer] = $priority;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function detach(SplObserver $observer)
    {
        unset($this->observers[$observer]);

        return $this;
    }

    /**
     * Detaches all observers.
     *
     * @since [*next-version*]
     *
     * @return $this This instance.
     */
    public function detachAll()
    {
        $this->observers = new SplObjectStorage();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function notify()
    {
        $sortedObservers = $this->_getSortedObservers();

        foreach ($sortedObservers as $_observer) {
            /* @var $_observer SplObserver */
            $_observer->update($this);
        }

        return $this;
    }

    /**
     * Processes a given iterable.
     *
     * @since [*next-version*]
     *
     * @param Iterator|array $iterable The iterable.
     *
     * @return $this This instance.
     */
    public function process($iterable)
    {
        $this->_processStart($iterable);

        foreach ($iterable as $_item) {
            $this->_processLoop($iterable, $_item);
        }

        $this->_processEnd($iterable);

        return $this;
    }

    /**
     * Starts the processing of the iterables.
     *
     * @since [*next-version*]
     *
     * @param Iterator|array $iterable The iterable.
     *
     * @return $this This instance.
     */
    protected function _processStart($iterable)
    {
        $this->_setState(static::STATE_START)
            ->_setCurrent(null)
            ->notify();

        return $this;
    }

    /**
     * Performs the processing of an item in the loop.
     *
     * @since [*next-version*]
     *
     * @param Iterator|array $iterable The iterable.
     *
     * @param mixed $item The loop item.
     *
     * @return $this This instance.
     */
    protected function _processLoop($iterable, $item)
    {
        $this->_setState(static::STATE_LOOP)
            ->_setCurrent($item)
            ->notify();

        return $this;
    }

    /**
     * Ends the processing of the iterable.
     *
     * @since [*next-version*]
     *
     * @param Iterator|array $iterable The iterable.
     *
     * @return $this This instance.
     */
    protected function _processEnd($iterable)
    {
        $this->_setState(static::STATE_END)
            ->_setCurrent(null)
            ->notify();

        return $this;
    }

    /**
     * Retrieves the observers sorted by priority.
     *
     * @since [*next-version*]
     *
     * @return array An associative array
     */
    protected function _getSortedObservers()
    {
        $array = $this->_splObjectStorageToArray($this->observers);

        usort($array, function($a, $b) {
            return (int) $a['data'] < (int) $b['data'];
        });

        return array_map(function($item) {
            return $item['obj'];
        }, $array);
    }

    /**
     * Transforms an SplObjectStorage instance with integer values into an associative array.
     *
     * @since [*next-version*]
     *
     * @param SplObjectStorage $storage The object storage. Must have integer-like values.
     *
     * @return array An associative array.
     */
    protected function _splObjectStorageToArray(SplObjectStorage $storage)
    {
        $result = array();
        foreach ($storage as $_observer) {
            $result[] = array(
                'obj'  => $_observer,
                'data' => $storage->getInfo()
            );
        }
        return $result;
    }
}
