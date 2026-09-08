<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateIdea
{
    public function handle(array $attributes, Idea $idea): Idea
    {
        $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->all();

        $oldImagePath = $idea->image_path;
        $newImagePath = null;

        if (isset($attributes['image']) && $attributes['image'] instanceof UploadedFile) {
            $newImagePath = $attributes['image']->store('ideas', 'public');
            $data['image_path'] = $newImagePath;
        }

        $steps = collect($attributes['steps'] ?? [])->map(fn (array $step) => [
            'description' => $step['description'],
            'completed' => (bool) ($step['completed'] ?? false),
        ])->all();

        try {
            DB::transaction(function () use ($idea, $data, $steps) {
                $idea->update($data);

                $idea->steps()->delete();
                $idea->steps()->createMany($steps);
            });

            // Cleanup old image if transaction succeeds and a new image was uploaded
            if ($newImagePath && $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } catch (\Throwable $e) {
            // Cleanup newly uploaded image if transaction fails
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }
            throw $e;
        }

        return $idea->refresh();
    }
}
