<?php

namespace Modules\ClubMembers\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ClubMembers\Repositories\CmCustomTable\CmCustomTableInterface;
use Modules\ClubMembers\Repositories\CmCustomTable\CmCustomTableRepository;
use Modules\ClubMembers\Repositories\CmFinancialStatus\CmFinancialStatusInterface;
use Modules\ClubMembers\Repositories\CmFinancialStatus\CmFinancialStatusRepository;
use Modules\ClubMembers\Repositories\CmMemberPermission\CmMemberPermissionInterface;
use Modules\ClubMembers\Repositories\CmMemberPermission\CmMemberPermissionRepository;
use Modules\ClubMembers\Repositories\CmMemberRequest\CmMemberRequestInterface;
use Modules\ClubMembers\Repositories\CmMemberRequest\CmMemberRequestRepository;
use Modules\ClubMembers\Repositories\CmMemberSetting\CmMemberSettingInterface;
use Modules\ClubMembers\Repositories\CmMemberSetting\CmMemberSettingRepository;
use Modules\ClubMembers\Repositories\CmMembershipRenewal\CmMembershipRenewalInterface;
use Modules\ClubMembers\Repositories\CmMembershipRenewal\CmMembershipRenewalRepository;
use Modules\ClubMembers\Repositories\CmMemberStatus\CmMemberStatusInterface;
use Modules\ClubMembers\Repositories\CmMemberStatus\CmMemberStatusRepository;
use Modules\ClubMembers\Repositories\CmMemberType\CmMemberTypeInterface;
use Modules\ClubMembers\Repositories\CmMemberType\CmMemberTypeRepository;
use Modules\ClubMembers\Repositories\CmMember\CmMemberInterface;
use Modules\ClubMembers\Repositories\CmMember\CmMemberRepository;
use Modules\ClubMembers\Repositories\CmPendingMember\CmPendingMemberInterface;
use Modules\ClubMembers\Repositories\CmPendingMember\CmPendingMemberRepository;
use Modules\ClubMembers\Repositories\CmSponser\CmSponserInterface;
use Modules\ClubMembers\Repositories\CmSponser\CmSponserRepository;
use Modules\ClubMembers\Repositories\CmStatus\CmStatusInterface;
use Modules\ClubMembers\Repositories\CmStatus\CmStatusRepository;
use Modules\ClubMembers\Repositories\CmTransaction\CmTransactionInterface;
use Modules\ClubMembers\Repositories\CmTransaction\CmTransactionRepository;


class ClubMembersServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'ClubMembers';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'clubmembers';

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
        $this->app->bind(CmPendingMemberInterface::class, CmPendingMemberRepository::class);
        $this->app->bind(CmSponserInterface::class, CmSponserRepository::class);
        $this->app->bind(CmMemberInterface::class, CmMemberRepository::class);
        $this->app->bind(CmFinancialStatusInterface::class, CmFinancialStatusRepository::class);
        $this->app->bind(CmMemberTypeInterface::class, CmMemberTypeRepository::class);
        $this->app->bind(CmMemberPermissionInterface::class, CmMemberPermissionRepository::class);
        $this->app->bind(CmPendingMemberInterface::class, CmPendingMemberRepository::class);
        $this->app->bind(CmMemberPermissionInterface::class, CmMemberPermissionRepository::class);
        $this->app->bind(CmMembershipRenewalInterface::class, CmMembershipRenewalRepository::class);
        $this->app->bind(CmMemberSettingInterface::class, CmMemberSettingRepository::class);
        $this->app->bind(CmTransactionInterface::class, CmTransactionRepository::class);
        $this->app->bind(CmCustomTableInterface::class, CmCustomTableRepository::class);
        $this->app->bind(CmMemberRequestInterface::class, CmMemberRequestRepository::class);
        $this->app->bind(CmStatusInterface::class, CmStatusRepository::class);

        $this->commands([
            \Modules\ClubMembers\Console\TransactionDb::class,
            \Modules\ClubMembers\Console\MemberDb::class,
            \Modules\ClubMembers\Console\PrefixDb::class,
            \Modules\ClubMembers\Console\FullNameDb::class,
            \Modules\ClubMembers\Console\CreateMembersDb::class,
            \Modules\ClubMembers\Console\MemberTypeIdDb::class,
            \Modules\ClubMembers\Console\PermissionDb::class,
        ]);

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
