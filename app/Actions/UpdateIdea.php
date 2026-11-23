<?php

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateIdea
{
    public function handle(array $data, Idea $idea): Idea
    {
        // ==========================================
        // 1. مرحلة المعالجة والتحضير (خارج الداتابيس / في الذاكرة)
        // ==========================================

        $title = $data['title'];
        $description = $data['description'] ?? null;
        $status = $data['status'] ?? 'pending';
        $links = $data['links'] ?? [];

        // معالجة الصورة: إذا تم رفع صورة جديدة نقوم بتخزينها، وإلا نحتفظ بالقديمة
        $imagePath = $idea->image_path;
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $imagePath = $data['image']->store('ideas', 'public');
        }

        // معالجة وتنقية الخطوات وتجهيزها كـ مصفوفة نظيفة في الذاكرة
        $incomingSteps = collect($data['steps'] ?? [])->map(function ($step) {
            return [
                'id' => $step['id'] ?? null,
                'description' => trim($step['description'] ?? ''),
                'completed' => filter_var($step['completed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        })->filter(fn ($step) => !empty($step['description']))->values();

        // استخلاص الـ IDs الخاصة بالخطوات الواردة لمقارنتها لاحقاً
        $incomingStepIds = $incomingSteps->pluck('id')->filter()->toArray();

        // ==========================================
        // 2. مرحلة الحفظ الفعلي (داخل الـ DB Transaction)
        // ==========================================

        return DB::transaction(function () use ($idea, $title, $description, $status, $links, $imagePath, $incomingSteps, $incomingStepIds) {

            // أ) تحديث البيانات الأساسية والروابط ومسار الصورة للفكرة
            $idea->update([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'links' => $links,
                'image_path' => $imagePath,
            ]);

            // ب) تنظيف الحذف: أي خطوة قديمة غير موجودة في القائمة المعالجة يتم حذفها
            $idea->steps()->whereNotIn('id', $incomingStepIds)->delete();

            // جـ) التحديث أو الإنشاء (Upsert) للخطوات الجاهزة
            foreach ($incomingSteps as $stepData) {
                $idea->steps()->updateOrCreate(
                    [
                        'id' => $stepData['id']
                    ],
                    [
                        'description' => $stepData['description'],
                        'completed' => $stepData['completed'],
                    ]
                );
            }

            return $idea;
        });
    }
}
