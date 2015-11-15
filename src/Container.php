<?php
namespace IoC;

/**
 * Inversion of Control - Dependency Container
 * 
 * @author Andrei Pirjoleanu <andreipirjoleanu@gmail.com>
 * @author Bogdan Anton <contact@bogdananton.ro>
 *
 * Usage:
 * <code class="php"><pre>
 * <?php
 * namespace Example;
 *
 * class Application
 * {
 *     public function __construct($config)
 *     {
 *         $this->container = \IoC\Container::getInstance();
 *
 *         $this->container->register('storage', function() use ($config) {
 *             return new \CustomDB\Client([
 *                 'user' => $config['customdb.user'],
 *                 'key' => $config['customdb.key']
 *             ]);
 *         });
 *
 *         $resolver = function ($outputFile) {
 *             return new \Logger\Dual(new \StreamLogger($outputFile));
 *         };
 *
 *         $this->container->singleton('logger', $resolver);
 *     }
 * }
 *
 * class OneComponentSomewhere
 * {
 *     protected $storage;
 *     protected $logger;
 *
 *     public function __construct()
 *     {
 *         $container = \IoC\Container::getInstance();
 *
 *         $this->storage = $container->resolve('storage');
 *         $this->logger = $container->resolve('logger', ['/var/logs/app/storage.log']);
 *         
 *         // force a new instance of logger, with a new log path, even though it is registered as a singleton 
 *         $this->loggerB = $container->resolve('logger', ['/var/logs/app/storageB.log'], true);
 *     }
 *
 *     public function getItem($key)
 *     {
 *         $this->logger->info('Get item by key ' . $key);
 *         return $this->storage->get($key);
 *     }
 * }
 * </pre></code>
 */
class Container
{
    /**
     * Singleton instance of Container
     * 
     * @var Container
     */
    private static $instance;
    
    protected $registry = array();
    
    /**
     * 
     * @return Container
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            $instance = new static();
            $instance->initRegister();

            static::$instance = $instance;
        }

        return static::$instance;
    }
    
    /**
     * Add initial required class registrations
     */
    protected function initRegister()
    {
    }

    /**
     * Register a binding with the registry
     * 
     * @param string $alias The alias used for registering the binding
     * @param \Closure|object|string|null $binding The binding requested to register
     * @param boolean $shared The type of the binding: shared (singleton) or not
    */
    public function register($alias, $binding, $shared = false)
    {
        $this->registry[$alias] = new Resolver(new Builder($binding), $shared);
    }
    
    /**
     * Register a shared binding with the registry
     * 
     * @param string $alias The alias used for registering the binding
     * @param \Closure|object|string|null $binding The binding requested to register
     */
    public function singleton($alias, $binding)
    {
        $this->register($alias, $binding, true);
    }
    
    /**
     * Execute the Resolver and return the requested binding instance
     * 
     * @param string $alias The registered binding's alias
     * @param array $parameters
     * @param boolean $force Force the overriding of the "shared" option and return a new instance of the requested binding
     *
     * @return object
     * @throws \Exception When no resolver is found for the handle name.
     */
    public function resolve($alias, array $parameters = array(), $force = false)
    {
        if ($this->registered($alias)) {
            $resolver = $this->registry[$alias];

            return $resolver->execute($parameters, $force);
        }
    
        throw new \Exception('No class found registered with that name: ' . $alias);
    }
    
    /**
     * Determine whether the alias is registered
     * 
     * @param string $alias The registered binding's alias
     * @return bool Whether the alias is registered or not
     */
    public function registered($alias)
    {
        return (array_key_exists($alias, $this->registry));
    }

    public static function reset()
    {
        static::$instance = null;
    }
}
