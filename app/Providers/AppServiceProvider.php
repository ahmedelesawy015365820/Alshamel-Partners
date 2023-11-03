<?php

namespace App\Providers;
use App\Repositories\BreakSettlement\BreakSettlementInterface;
use App\Repositories\BreakSettlement\BreakSettlementRepository;
use App\Repositories\ClientType\ClientTypeRepository;
use App\Repositories\ClientType\ClientTypeRepositoryInterface;
use App\Repositories\DocumentCompanyModuleStatus\DocumentCompanyModuleStatusInterface;
use App\Repositories\DocumentCompanyModuleStatus\DocumentCompanyModuleStatusRepository;
use App\Repositories\DocumentModuleType\DocumentModuleTypeInterface;
use App\Repositories\DocumentModuleType\DocumentModuleTypeRepository;
use Illuminate\Support\Facades\Validator;

use App\Repositories\Attendant\AttendantInterface;
use App\Repositories\Attendant\AttendantRepository;
use App\Repositories\Attribute\AttributeInterface;
use App\Repositories\Attribute\AttributeRepository;
use App\Repositories\Avenue\AvenueInterface;
use App\Repositories\Avenue\AvenueRepository;
use App\Repositories\BankAccount\BankAccountInterface;
use App\Repositories\BankAccount\BankAccountRepository;
use App\Repositories\Bank\BankInterface;
use App\Repositories\Bank\BankRepository;
use App\Repositories\Branch\BranchRepository;
use App\Repositories\Branch\BranchRepositoryInterface;
use App\Repositories\Brand\BrandInterface;
use App\Repositories\Brand\BrandRepository;
use App\Repositories\City\CityRepository;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\Color\ColorInterface;
use App\Repositories\Color\ColorRepository;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\Country\CountryInterface;
use App\Repositories\Country\CountryRepository;
use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Currency\CurrencyRepositoryInterface;
use App\Repositories\CustomerCategory\CustomerCategoryRepository;
use App\Repositories\CustomerGroup\CustomerGroupInterface;
use App\Repositories\CustomerGroup\CustomerGroupRepository;
use App\Repositories\CustomerSource\CustomerSourceInterface;
use App\Repositories\CustomerSource\CustomerSourceRepository;
use App\Repositories\CustomTable\CustomTableInterface;
use App\Repositories\CustomTable\CustomTableRepository;
use App\Repositories\DatabaseBackup\DatabaseBackupInterface;
use App\Repositories\DatabaseBackup\DatabaseBackupRepository;
use App\Repositories\Depertment\CustomerCategoryInterface;
use App\Repositories\Depertment\DepertmentInterface;
use App\Repositories\Depertment\DepertmentRepository;
use App\Repositories\DepertmentTask\DepertmentTaskInterface;
use App\Repositories\DepertmentTask\DepertmentTaskRepository;
use App\Repositories\Document\DocumentInterface;
use App\Repositories\Document\DocumentIRepository;
use App\Repositories\DocumentApprovalDetail\DocumentApprovalDetailInterface;
use App\Repositories\DocumentApprovalDetail\DocumentApprovalDetailRepository;
use App\Repositories\DocumentHeader\DocumentHeaderInterface;
use App\Repositories\DocumentHeader\DocumentHeaderRepository;
use App\Repositories\DocumentHeaderDetail\DocumentHeaderDetailInterface;
use App\Repositories\DocumentHeaderDetail\DocumentHeaderDetailRepository;
use App\Repositories\DocumentStatuse\DocumentStatuseInterface;
use App\Repositories\DocumentStatuse\DocumentStatuseRepository;
use App\Repositories\Employee\EmployeeInterface;
use App\Repositories\Employee\EmployeeRepository;
use App\Repositories\ExternalSalesmen\ExternalSalesmenInterface;
use App\Repositories\ExternalSalesmen\ExternalSalesmenRepository;
use App\Repositories\FinancialYear\FinancialYearInterface;
use App\Repositories\FinancialYear\FinancialYearRepository;
use App\Repositories\GeneralCustomer\GeneralCustomerRepository;
use App\Repositories\GeneralCustomer\GeneralCustomerRepositoryInterface;
use App\Repositories\GeneralItem\GeneralItemInterface;
use App\Repositories\GeneralItem\GeneralItemRepository;
use App\Repositories\Governorate\GovernorateInterface;
use App\Repositories\Governorate\GovernorateRepository;
use App\Repositories\Group\GroupInterface;
use App\Repositories\Group\GroupRepository;
use App\Repositories\InternalSalesman\InternalSalesmanRepository;
use App\Repositories\InternalSalesman\InternalSalesmanRepositoryInterface;
use App\Repositories\ItemBreakDown\ItemBreakDownInterface;
use App\Repositories\ItemBreakDown\ItemBreakDownRepository;
use App\Repositories\Message\MessageInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Module\ModuleInterface;
use App\Repositories\Module\ModuleRepository;
use App\Repositories\PaymentMethod\PaymentMethodInterface;
use App\Repositories\PaymentMethod\PaymentMethodRepository;
use App\Repositories\PaymentType\PaymentTypeInterface;
use App\Repositories\PaymentType\PaymentTypeRepository;
use App\Repositories\ReportDocument\ReportDocumentInterface;
use App\Repositories\ReportDocument\ReportDocumentRepository;
use App\Repositories\RestartPeriod\RestartPeriodInterface;
use App\Repositories\RestartPeriod\RestartPeriodRepository;
use App\Repositories\RoleScreenHotfield\RoleScreenHotfieldRepository;
use App\Repositories\RoleScreenHotfield\RoleScreenHotfieldRepositoryInterface;
use App\Repositories\RoleType\RoleTypeRepository;
use App\Repositories\RoleType\RoleTypeRepositoryInterface;
use App\Repositories\RoleWorkflowButton\RoleWorkflowButtonRepository;
use App\Repositories\RoleWorkflowButton\RoleWorkflowButtonRepositoryInterface;
use App\Repositories\RoleWorkflow\RoleWorkflowRepository;
use App\Repositories\RoleWorkflow\RoleWorkflowRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Salesman\SalesmanInterface;
use App\Repositories\Salesman\SalesmanRepository;
use App\Repositories\Salesman\SalesmanRepositoryInterface;
use App\Repositories\SalesmenPlansSource\SalesmenPlansSourceInterface;
use App\Repositories\SalesmenPlansSource\SalesmenPlansSourceRepository;
use App\Repositories\SalesmenType\SalesmenTypeInterface;
use App\Repositories\SalesmenType\SalesmenTypeRepository;
use App\Repositories\ScreenTreeProperty\ScreenTreePropertyRepository;
use App\Repositories\ScreenTreeProperty\ScreenTreePropertyRepositoryInterface;
use App\Repositories\Sector\SectorInterface;
use App\Repositories\Sector\SectorRepository;
use App\Repositories\Serial\SerialRepository;
use App\Repositories\Serial\SerialRepositoryInterface;
use App\Repositories\Status\StatusInterface;
use App\Repositories\Status\StatusRepository;
use App\Repositories\Store\StoreInterface;
use App\Repositories\Store\StoreRepository;
use App\Repositories\Supplier\SupplierInterface;
use App\Repositories\Supplier\SupplierRepository;
use App\Repositories\Tax\TaxInterface;
use App\Repositories\Tax\TaxRepository;
use App\Repositories\Translation\TranslationInterface;
use App\Repositories\Translation\TranslationRepository;
use App\Repositories\TreeProperty\TreePropertyRepository;
use App\Repositories\TreeProperty\TreePropertyRepositoryInterface;
use App\Repositories\Unit\UnitInterface;
use App\Repositories\Unit\UnitRepository;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\VoucherHeader\VoucherHeaderInterface;
use App\Repositories\VoucherHeader\VoucherHeaderRepository;
use App\Repositories\WorkflowHotfield\WorkflowHotfieldRepository;
use App\Repositories\WorkflowHotfield\WorkflowHotfieldRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use App\DoctrineTypes\MediumIntegerType;
use App\DoctrineTypes\EnumType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(ModuleInterface::class, ModuleRepository::class);
        $this->app->bind(StoreInterface::class, StoreRepository::class);
        $this->app->bind(EmployeeInterface::class, EmployeeRepository::class);
        $this->app->bind(CountryInterface::class, CountryRepository::class);

        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(PaymentTypeInterface::class, PaymentTypeRepository::class);

        $this->app->bind(GovernorateInterface::class, GovernorateRepository::class);

        $this->app->bind(ExternalSalesmenInterface::class, ExternalSalesmenRepository::class);

        $this->app->bind(FinancialYearInterface::class, FinancialYearRepository::class);
        $this->app->bind(UnitInterface::class, UnitRepository::class);
        $this->app->bind(AvenueInterface::class, AvenueRepository::class);

        $this->app->bind(CountryInterface::class, CountryRepository::class);

        $this->app->bind(ColorInterface::class, ColorRepository::class);

        $this->app->bind(SalesmenTypeInterface::class, SalesmenTypeRepository::class);
        $this->app->bind(SalesmanInterface::class, SalesmanRepository::class);

        $this->app->bind(BankInterface::class, BankRepository::class);

        $this->app->bind(BankAccountInterface::class, BankAccountRepository::class);

        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
        $this->app->bind(SerialRepositoryInterface::class, SerialRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoleTypeRepositoryInterface::class, RoleTypeRepository::class);
        $this->app->bind(RoleWorkflowRepositoryInterface::class, RoleWorkflowRepository::class);
        $this->app->bind(RoleScreenHotfieldRepositoryInterface::class, RoleScreenHotfieldRepository::class);
        $this->app->bind(RoleWorkflowButtonRepositoryInterface::class, RoleWorkflowButtonRepository::class);
        $this->app->bind(WorkflowHotfieldRepositoryInterface::class, WorkflowHotfieldRepository::class);
        $this->app->bind(SalesmanRepositoryInterface::class, SalesmanRepository::class);
        $this->app->bind(TreePropertyRepositoryInterface::class, TreePropertyRepository::class);
        $this->app->bind(InternalSalesmanRepositoryInterface::class, InternalSalesmanRepository::class);
        $this->app->bind(InternalSalesmanRepositoryInterface::class, InternalSalesmanRepository::class);
        $this->app->bind(ScreenTreePropertyRepositoryInterface::class, ScreenTreePropertyRepository::class);
        $this->app->bind(GeneralCustomerRepositoryInterface::class, GeneralCustomerRepository::class);
        $this->app->bind(CustomTableInterface::class, CustomTableRepository::class);
        $this->app->bind(DocumentInterface::class, DocumentIRepository::class);
        $this->app->bind(StatusInterface::class, StatusRepository::class);
        $this->app->bind(DepertmentInterface::class, DepertmentRepository::class);
        $this->app->bind(TranslationInterface::class, TranslationRepository::class);

        $this->app->bind(PaymentMethodInterface::class, PaymentMethodRepository::class);
        $this->app->bind(DatabaseBackupInterface::class, DatabaseBackupRepository::class);


        $this->app->bind(DepertmentTaskInterface::class, DepertmentTaskRepository::class);
        $this->app->bind(RestartPeriodInterface::class, RestartPeriodRepository::class);
        $this->app->bind(SalesmenPlansSourceInterface::class, SalesmenPlansSourceRepository::class);
        $this->app->bind(DocumentApprovalDetailInterface::class, DocumentApprovalDetailRepository::class);
        $this->app->bind(DocumentStatuseInterface::class, DocumentStatuseRepository::class);
        $this->app->bind(DocumentHeaderInterface::class, DocumentHeaderRepository::class);
        $this->app->bind(DocumentHeaderDetailInterface::class, DocumentHeaderDetailRepository::class);

        $this->app->bind(ItemBreakDownInterface::class, ItemBreakDownRepository::class);

        $this->app->bind(SectorInterface::class, SectorRepository::class);
        $this->app->bind(CustomerCategoryInterface::class, CustomerCategoryRepository::class);
        $this->app->bind(VoucherHeaderInterface::class, VoucherHeaderRepository::class);
        $this->app->bind(ReportDocumentInterface::class, ReportDocumentRepository::class);



        $this->app->bind(CustomerGroupInterface::class, CustomerGroupRepository::class);
        $this->app->bind(SupplierInterface::class, SupplierRepository::class);

        $this->app->bind(BrandInterface::class, BrandRepository::class);


        $this->app->bind(GroupInterface::class, GroupRepository::class);

//        $this->app->bind(VariantAttributeInterface::class, VariantAttributeRepository::class);
        $this->app->bind(AttributeInterface::class, AttributeRepository::class);


        $this->app->bind(TaxInterface::class, TaxRepository::class);
        $this->app->bind(CustomerSourceInterface::class, CustomerSourceRepository::class);


        $this->app->bind(GeneralItemInterface::class, GeneralItemRepository::class);

        $this->app->bind(AttendantInterface::class, AttendantRepository::class);

        $this->app->bind(MessageInterface::class, MessageRepository::class);



        $this->app->bind(ClientTypeRepositoryInterface::class, ClientTypeRepository::class);
        $this->app->bind(BreakSettlementInterface::class, BreakSettlementRepository::class);


        $this->app->bind(DocumentModuleTypeInterface::class, DocumentModuleTypeRepository::class);


        $this->app->bind(DocumentCompanyModuleStatusInterface::class, DocumentCompanyModuleStatusRepository::class);




        Type::addType('mediuminteger', MediumIntegerType::class);

        Type::addType('enum', EnumType::class);









    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {

        
        Schema::defaultStringLength(191);


        Validator::extend('max_digits', function ($attribute, $value, $parameters, $validator) {
            $maxDigits = $parameters[0];

            return strlen((string) $value) <= $maxDigits;
        });

        Validator::replacer('max_digits', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max_digits', $parameters[0], $message);
        });

    }
}
