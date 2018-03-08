<?php

namespace CascadiaPHP\Site\Template;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Plates\Engine;

class ServiceProvider extends AbstractServiceProvider
{

    protected $provides = [
        Engine::class,
        MarkdownExtension::class,
        AssetExtension::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        // Set up templating
        $this->container->share(Engine::class, function () {
            return $this->getTemplateEngine();
        });

        // Set up extensions
        $this->container->add(MarkdownExtension::class, function () {
            $parsedown = $this->container->get(\Parsedown::class);
            return new MarkdownExtension($parsedown, __DIR__ . '/../../content');
        });

        // Set up extensions
        $this->container->add(AssetExtension::class, function () {
            return new AssetExtension( __DIR__ . '/../../');
        });
    }

    private function getTemplateEngine()
    {
        $engine = new Engine(__DIR__ . '/../../templates');
        $engine->addData([
            'container' => $this->container
        ]);
        $engine->loadExtension($this->container->get(MarkdownExtension::class));
        $engine->loadExtension($this->container->get(TimeExtension::class));
        $engine->loadExtension($this->container->get(AssetExtension::class));

        return $engine;
    }
}
