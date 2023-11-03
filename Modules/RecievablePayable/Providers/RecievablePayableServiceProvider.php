<?php

namespace Modules\RecievablePayable\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\RecievablePayable\Repositories\RpCustomTable\RpCustomTableInterface;
use Modules\RecievablePayable\Repositories\RpCustomTable\RpCustomTableRepository;
use Modules\RecievablePayable\Repositories\RpDebitNoteInterface;
use Modules\RecievablePayable\Repositories\RpDebitNoteRepository;
use Modules\RecievablePayable\Repositories\RpDocumentPlanRepository;
use Modules\RecievablePayable\Repositories\RpDocumentPlanRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpInstallmentCondationRepository;
use Modules\RecievablePayable\Repositories\RpInstallmentCondationRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentPlanRepository;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentPlanRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentTypeRepository;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentTypeRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpInstallmentStatusRepository;
use Modules\RecievablePayable\Repositories\RpInstallmentStatusRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpMainContactGroupRepository;
use Modules\RecievablePayable\Repositories\RpMainContactGroupRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpOpeningBalanceInterface;
use Modules\RecievablePayable\Repositories\RpOpeningBalanceRepository;
use Modules\RecievablePayable\Repositories\RpPaymentPlanInstallmentRepository;
use Modules\RecievablePayable\Repositories\RpPaymentPlanInstallmentRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpScreenSubContactGroupRepository;
use Modules\RecievablePayable\Repositories\RpScreenSubContactGroupRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpSubContactGroupRepository;
use Modules\RecievablePayable\Repositories\RpSubContactGroupRepositoryInterface;

class RecievablePayableServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'RecievablePayable';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'recievablepayable';

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

        $this->app->bind (RpInstallmentPaymentTypeRepositoryInterface::class,RpInstallmentPaymentTypeRepository::class);
        $this->app->bind (RpInstallmentStatusRepositoryInterface::class,RpInstallmentStatusRepository::class);
        $this->app->bind (RpPaymentPlanInstallmentRepositoryInterface::class,RpPaymentPlanInstallmentRepository::class);
        $this->app->bind (RpMainContactGroupRepositoryInterface::class,RpMainContactGroupRepository::class);
        $this->app->bind (RpSubContactGroupRepositoryInterface::class,RpSubContactGroupRepository::class);
        $this->app->bind (RpInstallmentPaymentPlanRepositoryInterface::class,RpInstallmentPaymentPlanRepository::class);
        $this->app->bind (RpScreenSubContactGroupRepositoryInterface::class,RpScreenSubContactGroupRepository::class);
        $this->app->bind (RpDocumentPlanRepositoryInterface::class,RpDocumentPlanRepository::class);
        $this->app->bind (RpInstallmentCondationRepositoryInterface::class,RpInstallmentCondationRepository::class);
        $this->app->bind (RpOpeningBalanceInterface::class,RpOpeningBalanceRepository::class);
        $this->app->bind (RpDebitNoteInterface::class,RpDebitNoteRepository::class);
        $this->app->bind (RpCustomTableInterface::class,RpCustomTableRepository::class);


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
