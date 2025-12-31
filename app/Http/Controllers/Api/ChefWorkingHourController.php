<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ChefWorkingHourDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertChefWorkingHoursRequest;
use App\Http\Requests\StoreChefWorkingHourRequest;
use App\Http\Requests\UpdateChefWorkingHourRequest;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use App\Services\ChefWorkingHourService;

class ChefWorkingHourController extends Controller
{
    use ExceptionHandler, SuccessResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get weekly working hours for the current chef
     */
    public function index(ChefWorkingHourService $service)
    {
        $items = $service->getForCurrentChef();
        $data = $items->map(fn($m) => ChefWorkingHourDTO::fromModel($m)->toArray());
        return $this->successResponse($data, __('messages.list_success'), 200);
    }

    /**
     * Replace weekly working hours for the current chef
     */
    /** Create a single interval */
    public function store(StoreChefWorkingHourRequest $request, ChefWorkingHourService $service)
    {
        $record = $service->createForCurrentChef($request->validated());
        return $this->createdResponse(ChefWorkingHourDTO::fromModel($record)->toArray(), __('messages.created_success'));
    }

    /** Update a single interval */
    public function update(UpdateChefWorkingHourRequest $request, ChefWorkingHourService $service, $id)
    {
        $record = $service->updateForCurrentChef((int) $id, $request->validated());
        return $this->updatedResponse(ChefWorkingHourDTO::fromModel($record)->toArray(), __('messages.updated_success'));
    }

    /** Delete a single interval */
    public function destroy(ChefWorkingHourService $service, $id)
    {
        $service->deleteForCurrentChef((int) $id);
        return $this->deletedResponse(__('messages.deleted_success'));
    }
}
