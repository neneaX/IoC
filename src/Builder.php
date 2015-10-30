<?php
namespace IoC;

/**
 * Builder class used for creating instances of the requested objects
 *
 * @author Andrei Pirjoleanu <andrei.pirjoleanu@avangate.com>
 * @author Bogdan Anton <contact@bogdananton.ro>
 */
class Builder
{
    /**
     * @var \Closure|object|string|null
     */
    protected $binding;
    
    /**
     * @var array
     */
    protected $parameters = array();

    public function __construct($binding)
    {
        $this->binding = $binding;
    }
    
    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
    
    /**
     * Run the builder, create the binding's instance and return it
     * 
     * @return object
     */
    public function run()
    {
        if ($this->binding instanceof \Closure) {
            return call_user_func_array($this->binding, $this->parameters);
        }
        
        if (is_object($this->binding)) {
            return $this->binding;
        }
        
        if (is_string($this->binding)) {
            /*
             * @todo Implement this functionality
             */
            return null;
        }
        
        return $this->binding;
    }
}