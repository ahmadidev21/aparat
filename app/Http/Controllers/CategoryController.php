<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UploadBannerCategoryRequest;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        return Category::all();
    }

    public function getMyCategories()
    {

        return auth()->user()->categories;
    }

    public function uploadBanner(UploadBannerCategoryRequest $request)
    {
        try {
            $banner = $request->file('banner');
            $fileName = time() . '_' . Str::random(10) . '-banner';
            Storage::disk('category')->put('temp/' . $fileName, $banner->get());

            return response(['banner' => $fileName], Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create(CreateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $user = auth()->user();
            if ($request->banner_id) {
                Storage::disk('category')->move('temp/' . $request->banner_id, auth()->id() . '/' . $request->banner_id);
            }
            $category = $user->categories()->create($data);
            DB::commit();

            return response([$category], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
