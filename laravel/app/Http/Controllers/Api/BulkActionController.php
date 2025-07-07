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

    private function resolveModel($model)
    {
        $map = [
            'User' => \App\Models\User::class,
        ];

        return $map[$model] ?? null;
    }
}
