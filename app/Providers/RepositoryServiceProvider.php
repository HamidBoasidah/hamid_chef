<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Repositories
use App\Repositories\UserRepository;
use App\Repositories\AreaRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\GovernorateRepository;
use App\Repositories\AddressRepository;
use App\Repositories\AdminRepository;
use App\Repositories\BookingRepository;
use App\Repositories\BookingTransactionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ChefCategoryRepository;
use App\Repositories\ChefGalleryRepository;
use App\Repositories\ChefServiceRatingRepository;
use App\Repositories\ChefRepository;
use App\Repositories\ChefServiceImageRepository;
use App\Repositories\ChefServiceRepository;
use App\Repositories\ChefServiceTagRepository;
use App\Repositories\ChefServiceEquipmentRepository;
use App\Repositories\ChefWalletRepository;
use App\Repositories\ChefWalletTransactionRepository;
use App\Repositories\ChefWithdrawalRequestRepository;
use App\Repositories\KycRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TagRepository;
use App\Repositories\WithdrawalMethodRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ChefWorkingHourRepository;

// Models
use App\Models\User;
use App\Models\Area;
use App\Models\District;
use App\Models\Governorate;
use App\Models\Address;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\BookingTransaction;
use App\Models\Category;
use App\Models\Chef;
use App\Models\ChefCategory;
use App\Models\ChefGallery;
use App\Models\ChefServiceRating;
use App\Models\ChefService;
use App\Models\ChefServiceImage;
use App\Models\ChefServiceTag;
use App\Models\ChefServiceEquipment;
use App\Models\ChefWallet;
use App\Models\ChefWalletTransaction;
use App\Models\ChefWithdrawalRequest;
use App\Models\Kyc;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tag;
use App\Models\WithdrawalMethod;
// Activity model from spatie
use Spatie\Activitylog\Models\Activity;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChefWorkingHour;

// Services
use App\Services\UserService;
use App\Services\AreaService;
use App\Services\DistrictService;
use App\Services\GovernorateService;
use App\Services\AddressService;
use App\Services\AdminService;
use App\Services\BookingService;
use App\Services\BookingConflictService;
use App\Services\BookingTransactionService;
use App\Services\CategoryService;
use App\Services\ChefCategoryService;
use App\Services\ChefGalleryService;
use App\Services\ChefServiceRatingService;
use App\Services\ChefService as ChefCoreService;
use App\Services\ChefServiceImageService;
use App\Services\ChefServiceService;
use App\Services\ChefServiceTagService;
use App\Services\ChefServiceEquipmentService;
use App\Services\ChefWalletService;
use App\Services\ChefWalletTransactionService;
use App\Services\ChefWithdrawalRequestService;
use App\Services\KycService;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\TagService;
use App\Services\WithdrawalMethodService;
use App\Services\ActivityLogService;
use App\Services\ConversationService;
use App\Services\MessageService;
use App\Services\ChefWorkingHourService;


class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepository::class, fn($app) => new UserRepository($app->make(User::class)));
        $this->app->bind(AreaRepository::class, fn($app) => new AreaRepository($app->make(Area::class)));
        $this->app->bind(DistrictRepository::class, fn($app) => new DistrictRepository($app->make(District::class)));
        $this->app->bind(GovernorateRepository::class, fn($app) => new GovernorateRepository($app->make(Governorate::class)));
        $this->app->bind(AddressRepository::class, fn($app) => new AddressRepository($app->make(Address::class)));
        $this->app->bind(AdminRepository::class, fn($app) => new AdminRepository($app->make(Admin::class)));
        $this->app->bind(BookingRepository::class, fn($app) => new BookingRepository($app->make(Booking::class)));
        $this->app->bind(BookingTransactionRepository::class, fn($app) => new BookingTransactionRepository($app->make(BookingTransaction::class)));
        $this->app->bind(CategoryRepository::class, fn($app) => new CategoryRepository($app->make(Category::class)));
        $this->app->bind(ChefRepository::class, fn($app) => new ChefRepository($app->make(Chef::class)));
        $this->app->bind(ChefCategoryRepository::class, fn($app) => new ChefCategoryRepository($app->make(ChefCategory::class)));
        $this->app->bind(ChefGalleryRepository::class, fn($app) => new ChefGalleryRepository($app->make(ChefGallery::class)));
        $this->app->bind(ChefServiceRatingRepository::class, fn($app) => new ChefServiceRatingRepository($app->make(ChefServiceRating::class)));
        $this->app->bind(ChefServiceRepository::class, fn($app) => new ChefServiceRepository($app->make(ChefService::class)));
        $this->app->bind(ChefServiceImageRepository::class, fn($app) => new ChefServiceImageRepository($app->make(ChefServiceImage::class)));
        $this->app->bind(ChefServiceTagRepository::class, fn($app) => new ChefServiceTagRepository($app->make(ChefServiceTag::class)));
        $this->app->bind(ChefServiceEquipmentRepository::class, fn($app) => new ChefServiceEquipmentRepository($app->make(ChefServiceEquipment::class)));
        $this->app->bind(ChefWalletRepository::class, fn($app) => new ChefWalletRepository($app->make(ChefWallet::class)));
        $this->app->bind(ChefWalletTransactionRepository::class, fn($app) => new ChefWalletTransactionRepository($app->make(ChefWalletTransaction::class)));
        $this->app->bind(ChefWithdrawalRequestRepository::class, fn($app) => new ChefWithdrawalRequestRepository($app->make(ChefWithdrawalRequest::class)));
        $this->app->bind(KycRepository::class, fn($app) => new KycRepository($app->make(Kyc::class)));
        $this->app->bind(PermissionRepository::class, fn($app) => new PermissionRepository($app->make(Permission::class)));
        $this->app->bind(RoleRepository::class, fn($app) => new RoleRepository($app->make(Role::class)));
        $this->app->bind(TagRepository::class, fn($app) => new TagRepository($app->make(Tag::class)));
        $this->app->bind(WithdrawalMethodRepository::class, fn($app) => new WithdrawalMethodRepository($app->make(WithdrawalMethod::class)));
        $this->app->bind(ActivityLogRepository::class, fn($app) => new ActivityLogRepository($app->make(Activity::class)));
        $this->app->bind(ConversationRepository::class, fn($app) => new ConversationRepository($app->make(Conversation::class)));
        $this->app->bind(MessageRepository::class, fn($app) => new MessageRepository($app->make(Message::class)));
        $this->app->bind(ChefWorkingHourRepository::class, fn($app) => new ChefWorkingHourRepository($app->make(ChefWorkingHour::class)));

        // Bind Services to their Repositories
        $this->app->bind(UserService::class, fn($app) => new UserService($app->make(UserRepository::class)));
        $this->app->bind(AreaService::class, fn($app) => new AreaService($app->make(AreaRepository::class)));
        $this->app->bind(DistrictService::class, fn($app) => new DistrictService($app->make(DistrictRepository::class)));
        $this->app->bind(GovernorateService::class, fn($app) => new GovernorateService($app->make(GovernorateRepository::class)));
        $this->app->bind(AddressService::class, fn($app) => new AddressService($app->make(AddressRepository::class)));
        $this->app->bind(AdminService::class, fn($app) => new AdminService($app->make(AdminRepository::class)));
        $this->app->bind(BookingConflictService::class, fn($app) => new BookingConflictService($app->make(BookingRepository::class)));
        $this->app->bind(BookingService::class, fn($app) => new BookingService(
            $app->make(BookingRepository::class),
            $app->make(BookingConflictService::class)
        ));
        $this->app->bind(BookingTransactionService::class, fn($app) => new BookingTransactionService($app->make(BookingTransactionRepository::class)));
        $this->app->bind(CategoryService::class, fn($app) => new CategoryService(
            $app->make(CategoryRepository::class),
            $app->make(\App\Services\SVGIconService::class)
        ));
        $this->app->bind(ChefCoreService::class, fn($app) => new ChefCoreService(
            $app->make(ChefRepository::class),
            $app->make(ChefGalleryService::class)
        ));
        $this->app->bind(ChefCategoryService::class, fn($app) => new ChefCategoryService($app->make(ChefCategoryRepository::class)));
        $this->app->bind(ChefGalleryService::class, fn($app) => new ChefGalleryService($app->make(ChefGalleryRepository::class)));
        $this->app->bind(ChefServiceRatingService::class, fn($app) => new ChefServiceRatingService($app->make(ChefServiceRatingRepository::class)));
        $this->app->bind(ChefServiceImageService::class, fn($app) => new ChefServiceImageService($app->make(ChefServiceImageRepository::class)));
        $this->app->bind(ChefServiceService::class, fn($app) => new ChefServiceService(
            $app->make(ChefServiceRepository::class),
            $app->make(ChefServiceImageService::class)
        ));
        $this->app->bind(ChefServiceTagService::class, fn($app) => new ChefServiceTagService($app->make(ChefServiceTagRepository::class)));
        $this->app->bind(ChefServiceEquipmentService::class, fn($app) => new ChefServiceEquipmentService($app->make(ChefServiceEquipmentRepository::class)));
        $this->app->bind(ChefWalletService::class, fn($app) => new ChefWalletService($app->make(ChefWalletRepository::class)));
        $this->app->bind(ChefWalletTransactionService::class, fn($app) => new ChefWalletTransactionService($app->make(ChefWalletTransactionRepository::class)));
        $this->app->bind(ChefWithdrawalRequestService::class, fn($app) => new ChefWithdrawalRequestService($app->make(ChefWithdrawalRequestRepository::class)));
        $this->app->bind(KycService::class, fn($app) => new KycService($app->make(KycRepository::class)));
        $this->app->bind(PermissionService::class, fn($app) => new PermissionService($app->make(PermissionRepository::class)));
        $this->app->bind(RoleService::class, fn($app) => new RoleService($app->make(RoleRepository::class)));
        $this->app->bind(TagService::class, fn($app) => new TagService($app->make(TagRepository::class)));
        $this->app->bind(WithdrawalMethodService::class, fn($app) => new WithdrawalMethodService($app->make(WithdrawalMethodRepository::class)));
        $this->app->bind(ActivityLogService::class, fn($app) => new ActivityLogService($app->make(ActivityLogRepository::class)));
        $this->app->bind(ConversationService::class, fn($app) => new ConversationService($app->make(ConversationRepository::class)));
        $this->app->bind(MessageService::class, fn($app) => new MessageService(
            $app->make(MessageRepository::class),
            $app->make(ConversationService::class)
        ));
        $this->app->bind(ChefWorkingHourService::class, fn($app) => new ChefWorkingHourService(
            $app->make(ChefWorkingHourRepository::class)
        ));
    }
}
