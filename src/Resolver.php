<?php

namespace IoC;

/**
 * Resolver class used for building instances of the requested objects
 *
 * @author Andrei Pirjoleanu <andrei.pirjoleanu@avangate.com>
 */
class Resolver
{

    /**
     * The builder used for creating an instance of the requested object
     *
     * @var Builder
     */
    protected $builder;

    /**
     * The type of the resolver (shared or not)
     * If the resolver is shared, it will always return the same instance of the requested object
     *
     * @var boolean
     */
    protected $shared;

    /**
     * The instance of the requested object
     *
     * @var object
     */
    protected $instance;

    public function __construct(Builder $builder, $shared = false)
    {
        $this->builder = $builder;
        $this->shared = (bool)$shared;
    }

    /**
     * Execute the resolver and return the instance
     *
     * @param array      $parameters
     * @param bool|false $force
     *
     * @return object
     */
    public function execute(array $parameters = [], $force = false)
    {
        if ($force === true || $this->checkBuildConditions()) {
            $this->builder->setParameters($parameters);
            $this->build();
        }

        return $this->instance;
    }

    /**
     * Build the instance of the requested object
     */
    protected function build()
    {
        $this->instance = $this->builder->run();
    }

    /**
     * Check if the required build conditions are met
     *
     * @return boolean
     */
    protected function checkBuildConditions()
    {
        /**
         * If the resolver is not shared, we need to build the instance
         */
        if ($this->shared === false) {
            /**
             * The build conditions are met
             */
            return true;
        }

        /**
         * If the resolver is shared, but the instance is not yet built,
         * we need to build it
         */
        if (!is_object($this->instance) || null === $this->instance) {
            /**
             * The build conditions are met
             */
            return true;
        }

        return false;
    }
}
