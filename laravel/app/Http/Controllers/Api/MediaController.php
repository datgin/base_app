<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Traits\Pagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MediaController extends Controller
{
    use Pagination;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->pagination(
            query: Media::query(),
        );

        $media = MediaResource::collection($response['data']);

        return response()->json([
            'media' => $media,
            'total' => $response['total']
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'file|image|max:10240', // max 10MB mỗi ảnh
        ]);

        try {
            DB::beginTransaction();

            $uploaded = uploadImages($request->file('images'), 'media');

            $inserted = [];

            foreach ($uploaded as $image) {
                $media = Media::create([
                    'name' => $image['name'],
                    'path' => $image['path'],
                    'format' => $image['format'],
                    'size' => $image['size'],
                    'width' => $image['width'],
                    'height' => $image['height'],
                    'created_by' => auth('api')->id(), // hoặc null nếu không có auth
                ]);

                $inserted[] = $media;
            }

            DB::commit();

            return response()->json([
                'message' => 'Tải ảnh lên thành công',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Upload thất bại:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi tải ảnh lên',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media,id',
        ]);

        try {
            $mediaList = Media::whereIn('id', $request->ids)->get();

            foreach ($mediaList as $media) {
                deleteImage($media->path);
                $media->delete();
            }

            return response()->json(['message' => 'Đã xoá ảnh thành công']);
        } catch (\Throwable $e) {
            logger()->error('Lỗi xoá ảnh:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Lỗi khi xoá ảnh'], 500);
        }
    }
}
