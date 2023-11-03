<?php

namespace Modules\RealEstate\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\RealEstate\Repositories\RlstCategoriesItem\RlstCategoriesItemInterface;
use Modules\RealEstate\Repositories\RlstCategoriesItem\RlstCategoriesItemRepository;
use Modules\RealEstate\Repositories\RlstCategoryItem\RlstCategoryItemInterface;
use Modules\RealEstate\Repositories\RlstCategoryItem\RlstCategoryItemRepository;
use Modules\RealEstate\Repositories\RlstCustomTable\RlstCustomTableInterface;
use Modules\RealEstate\Repositories\RlstCustomTable\RlstCustomTableRepository;
use Modules\RealEstate\Repositories\RlstInvoice\RlstInvoiceInterface;
use Modules\RealEstate\Repositories\RlstInvoice\RlstInvoiceRepository;
use Modules\RealEstate\Repositories\RlstItem\RlstItemInterface;
use Modules\RealEstate\Repositories\RlstItem\RlstItemRepository;

class RealEstateServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'RealEstate';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'realestate';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(RlstCategoryItemInterface::class, RlstCategoryItemRepository::class);
        $this->app->bind(RlstItemInterface::class, RlstItemRepository::class);
        $this->app->bind(RlstCategoriesItemInterface::class, RlstCategoriesItemRepository::class);
        $this->app->bind(RlstInvoiceInterface::class, RlstInvoiceRepository::class);
        $this->app->bind(RlstCustomTableInterface::class, RlstCustomTableRepository::class);

    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
