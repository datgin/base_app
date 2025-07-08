<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BulkActionController extends Controller
{
    public function bulkDelete(Request $request, $model)
    {
        $modelClass = $this->resolveModel($model);
        if (!$modelClass) {
            return errorResponse("Model không hợp lệ");
        }

        $ids = $request->input('ids', []);
        $modelClass::whereIn('id', $ids)->delete();

        return successResponse("Đã xoá thành công");
    }

    public function bulkArchive(Request $request, $model)
    {
        $modelClass = $this->resolveModel($model);
        if (!$modelClass) {
            return errorResponse("Model không hợp lệ");
        }

        $ids = $request->input('ids', []);
        $archivable = Schema::hasColumn((new $modelClass)->getTable(), 'archived');

        if (!$archivable) {
            return errorResponse("Model không hỗ trợ lưu trữ");
        }

        $modelClass::whereIn('id', $ids)->update(['archived' => true]);

        return successResponse("Đã lưu trữ thành công");
    }

    public function bulkPublish(Request $request, $model)
    {
        $modelClass = $this->resolveModel($model);
        if (!$modelClass) {
            return errorResponse("Model không hợp lệ");
        }

        $ids = $request->input('ids', []);

        $publishable = Schema::hasColumn((new $modelClass)->getTable(), 'published');
        if (!$publishable) {
            return errorResponse("Model không hỗ trợ publish/unpublish");
        }

        $updatedCount = 0;

        // Lấy ra các bản ghi cần cập nhật
        $items = $modelClass::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            $item->published = $item->published == 1 ? 2 : 1;
            $item->save();
            $updatedCount++;
        }

        return successResponse("Đã cập nhật trạng thái xuất bản cho {$updatedCount} bản ghi");
    }


    private function resolveModel($model)
    {
        $map = [
            'User' => \App\Models\User::class,
            'Example' => \App\Models\Example::class,
        ];

        return $map[$model] ?? null;
    }
}
