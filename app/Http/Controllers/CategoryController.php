<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CategoryResource::collection(Category::orderBy('created_at')->paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $validate = $request->validated();
        $category = Category::create($validate);

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  $category Может содержать либо id, либо slug
     * @return \Illuminate\Http\Response
     */
    public function show($category)
    {
        // Если $category это число, то предполагаем что там содержится id
        if (is_numeric($category)) {
            $response = Category::findOrFail($category);
        } else {
            $response = Category::where('slug', $category)->firstOrFail();
        }

        return new CategoryResource($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validate = $request->validated();
        $category->updateOrFail($validate);
        $category->refresh();
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response('204');
    }
}
