<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\IntentService;
use App\Filters\IntentFilters;
use App\Http\Requests\IntentRequest;

/**
 * @resource Intents
 *
 * For Endpoints handling Intents, which represent an intention of a user to perform some action
 *  in the future, such as "change-password". Each belongs to a particular Intent Type.
 */
class IntentController extends ApiController
{
    protected $service;

    public function __construct(IntentService $service) {
        $this->service = $service;
    }

    public function service() {
        return $this->service;
    }

    /**
     * Get Intent by ID
     * 
     * Responds with a specific Intent by its ID
     * - Rules of Access
     *   - User owns Intent
     */
    public function show(Request $request, IntentFilters $filters, $id) {
        $intent = $this->service()->repo()->single($id, $filters);
        $this->authorize('view', $intent); /** ensure the current user has view rights */
        return $intent;
    }

    /**
     * Get Intent by ID
     * 
     * Responds with a list of Intents
     * - Rules of Access
     *   - User owns Intents
     */
    public function index(Request $request, IntentFilters $filters) {
        $intents = $this->service()->repo()->list($request->user(), $filters);
        return $intents;
    }

    /**
     * Delete Intent
     * 
     * Removes an Intent from the System by ID
     * - Rules of Access
     *  - User owns Intent
     */
    public function destroy(Request $request, $id) {
        $intent = $this->service()->repo()->single($id);
        $this->authorize('delete', $intent); /** ensure the current user has delete rights */
        $this->service()->repo()->delete($id);
        return $this->ok();
    }

    /**
     * Create Intent
     * 
     * Supply Intent information to create a new one
     */
    public function store(IntentRequest $request) {
        $intent = $this->service()->create(array_merge([ 'user_id' => $request->user()->id ], $request->all()));
        return $this->created($intent);
    }

    /**
     * Update Intent (Not Supported)
     */
    public function update(Request $request, $id) {
        return $this->json([ 'message' => 'This action is to be handled by the System' ], 501);
    }
}
