<?php

namespace Modules\Stock\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Stock\Repositories\AllTransactionInterface;
use Modules\Stock\Repositories\AllTransactionRepository;
use Modules\Stock\Repositories\ClosingBalanceInterface;
use Modules\Stock\Repositories\ClosingBalanceRepository;
use Modules\Stock\Repositories\OpenningBalanceInterface;
use Modules\Stock\Repositories\OpenningBalanceRepository;
use Modules\Stock\Repositories\ProfitReportInterface;
use Modules\Stock\Repositories\ProfitReportRepository;
use Modules\Stock\Repositories\RealizedUnrealizedInterface;
use Modules\Stock\Repositories\RealizedUnrealizedRepository;
use Modules\Stock\Repositories\StCustomTable\StCustomTableInterface;
use Modules\Stock\Repositories\StCustomTable\StCustomTableRepository;
use Modules\Stock\Repositories\StockInterface;
use Modules\Stock\Repositories\StockMarketInterface;
use Modules\Stock\Repositories\StockMarketRepository;
use Modules\Stock\Repositories\StockRepository;
use Modules\Stock\Repositories\StockSalePurchaseDetailInterface;
use Modules\Stock\Repositories\StockSalePurchaseDetailRepository;
use Modules\Stock\Repositories\StockSectorInterface;
use Modules\Stock\Repositories\StockSectorRepository;
use Modules\Stock\Repositories\WalletInterface;
use Modules\Stock\Repositories\WalletRepository;

class StockServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Stock';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'stock';

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
        $this->app->bind (StockInterface::class,StockRepository::class);
        $this->app->bind (AllTransactionInterface::class,AllTransactionRepository::class);
        $this->app->bind (ClosingBalanceInterface::class,ClosingBalanceRepository::class);
        $this->app->bind (OpenningBalanceInterface::class,OpenningBalanceRepository::class);
        $this->app->bind (ProfitReportInterface::class,ProfitReportRepository::class);
        $this->app->bind (RealizedUnrealizedInterface::class,RealizedUnrealizedRepository::class);
        $this->app->bind (StockMarketInterface::class,StockMarketRepository::class);
        $this->app->bind (StockSalePurchaseDetailInterface::class,StockSalePurchaseDetailRepository::class);
        $this->app->bind (StockSectorInterface::class,StockSectorRepository::class);
        $this->app->bind (WalletInterface::class,WalletRepository::class);
        $this->app->bind (StCustomTableInterface::class,StCustomTableRepository::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
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
            $sourcePath => $viewPath
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
