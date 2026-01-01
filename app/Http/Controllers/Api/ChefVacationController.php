<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ChefVacationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChefVacationRequest;
use App\Http\Requests\UpdateChefVacationRequest;
use App\Http\Requests\ListChefVacationsByMonthRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Services\ChefVacationService;

class ChefVacationController extends Controller
{
    use ExceptionHandler, SuccessResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List vacations for current chef
     */
    public function index(ChefVacationService $service)
    {
        $items = $service->getForCurrentChef();
        $data = $items->map(fn($m) => ChefVacationDTO::fromModel($m)->toArray());
        return $this->successResponse($data, __('messages.list_success'), 200);
    }

    /**
     * List vacations for current chef filtered by month (YYYY-MM). Defaults to current month.
     */
    public function monthly(ListChefVacationsByMonthRequest $request, ChefVacationService $service)
    {
        // DEBUG: Log month request
        $month = $request->input('month');
        $items = $service->getForCurrentChefByMonth($month);
        $data = $items->map(fn($m) => ChefVacationDTO::fromModel($m)->toArray());
        return $this->successResponse($data, __('messages.list_success'), 200);
    }

    /**
     * Show single vacation
     */
    public function show(ChefVacationService $service, $id)
    {
        $record = $service->showForCurrentChef((int) $id);
        return $this->resourceResponse(ChefVacationDTO::fromModel($record)->toArray(), __('messages.show_success'));
    }

    /**
     * Create vacation (single date)
     */
    public function store(StoreChefVacationRequest $request, ChefVacationService $service)
    {
        // DEBUG: Log raw request data
        $record = $service->createForCurrentChef($request->validated());
        return $this->createdResponse(ChefVacationDTO::fromModel($record)->toArray(), __('messages.created_success'));
    }

    /**
     * Update vacation
     */
    public function update(UpdateChefVacationRequest $request, ChefVacationService $service, $id)
    {
        $record = $service->updateForCurrentChef((int) $id, $request->validated());
        return $this->updatedResponse(ChefVacationDTO::fromModel($record)->toArray(), __('messages.updated_success'));
    }

    /**
     * Delete vacation
     */
    public function destroy(ChefVacationService $service, $id)
    {
        $service->deleteForCurrentChef((int) $id);
        return $this->deletedResponse(__('messages.deleted_success'));
    }
}
