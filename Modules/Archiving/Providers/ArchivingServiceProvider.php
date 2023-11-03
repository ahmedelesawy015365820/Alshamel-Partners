<?php

namespace Modules\Archiving\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Archiving\Repositories\ArchCustomTable\ArchCustomTableInterface;
use Modules\Archiving\Repositories\ArchCustomTable\ArchCustomTableRepository;
use Modules\Archiving\Repositories\ClosedReferenceInterface;
use Modules\Archiving\Repositories\ClosedReferenceRepository;
use Modules\Archiving\Repositories\DepartmentInterface;
use Modules\Archiving\Repositories\DepartmentRepository;
use Modules\Archiving\Repositories\DocStatusInterface;
use Modules\Archiving\Repositories\DocStatusRepository;
use Modules\Archiving\Repositories\DocTypeFieldInterface;
use Modules\Archiving\Repositories\DocTypeFieldRepository;
use Modules\Archiving\Repositories\DocumentDtlInterface;
use Modules\Archiving\Repositories\DocumentDtlRepository;
use Modules\Archiving\Repositories\DocumentFieldInterface;
use Modules\Archiving\Repositories\DocumentFieldRepository;
use Modules\Archiving\Repositories\DocumentInterface;
use Modules\Archiving\Repositories\DocumentRepository;
use Modules\Archiving\Repositories\DocTypeInterface;
use Modules\Archiving\Repositories\DocTypeRepository;
use Modules\Archiving\Repositories\DocumentStatusInterface;
use Modules\Archiving\Repositories\DocumentStatusRepository;

class ArchivingServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Archiving';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'archiving';

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

        $this->app->bind(DocumentFieldInterface::class, DocumentFieldRepository::class);
        $this->app->bind(ClosedReferenceInterface::class, ClosedReferenceRepository::class);
        $this->app->bind(DepartmentInterface::class, DepartmentRepository::class);
        $this->app->bind(DocumentInterface::class, DocumentRepository::class);
        $this->app->bind(DocStatusInterface::class, DocStatusRepository::class);

        $this->app->bind(DocTypeInterface::class, DocTypeRepository::class);
        $this->app->bind(DocTypeFieldInterface::class, DocTypeFieldRepository::class);
        $this->app->bind(DocumentDtlInterface::class, DocumentDtlRepository::class);
        $this->app->bind(DocumentStatusInterface::class, DocumentStatusRepository::class);
        $this->app->bind(ArchCustomTableInterface::class, ArchCustomTableRepository::class);
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
