<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Example\ExampleCollection;
use App\Http\Resources\Example\ExampleResource;
use App\Models\Example;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    use Pagination;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resource = $this->pagination(query: Example::query(), filters: ['archive']);

        $response = new ExampleCollection($resource);

        return successResponse("Lấy danh sách example thành công.", $response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        logger($request->toArray());
    }

    /**
 * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
