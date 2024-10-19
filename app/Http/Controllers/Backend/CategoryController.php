<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Backend\CategoryService;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
    ){}
    /**
     * Display a listing of the resource.
     */
    Public function index(): View
    {
        return view('backend.categories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        try {

            $this->categoryService->create($data);

            return response()->json(['message' => 'Data Kategori Berhasil Ditambah!']);
        } catch (\Exception $err) {
            return response()->json(['message' => $err->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        try {
            $data = $this->categoryService->getFirstBy('uuid', $uuid);

            return response()->json(['data' => $data]);
        } catch (\Exception $err) {
            return response()->json(['message' => $err->getMessage()], 404);
        }
    }

       public function update(CategoryRequest $request, string $uuid)
    {
        $data = $request->validated();

        try {
            $this->categoryService->update($data, $uuid);

            return response()->json(['message' => 'Data Kategori Berhasil Diubah!']);
        } catch (\Exception $err) {
            return response()->json(['message' => $err->getMessage()], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $getData = $this->categoryService->getFirstBy('uuid', $uuid);

            $getData->delete();

            return response()->json(['message' => 'Data Kategori Berhasil Dihapus!']);
        } catch (\Exception $err) {
            return response()->json(['message' => $err->getMessage()], 404);
        }
    }

    public function serverside(Request $request): JsonResponse
    {
        return $this->categoryService->dataTable($request);
    }
}

