<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageTasks extends ManageRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->fillForm(fn (): array => [
                    'project_id' => $this->tableFilters['project']['value'] ?? null,
                ]),
        ];
    }

    /**
     * Filament numbers the dragged rows 1, 2, 3... which only works when every
     * task is on screen. When the list is filtered by project, the visible tasks
     * swap the priorities they already had, so the other tasks keep their place.
     */
    public function reorderTable(array $order, int|string|null $draggedRecordKey = null): void
    {
        if (! $this->getTable()->isReorderable()) {
            return;
        }

        $priorities = Task::whereKey($order)->orderBy('priority')->pluck('priority');

        DB::transaction(function () use ($order, $priorities) {
            foreach (array_values($order) as $index => $id) {
                Task::whereKey($id)->update(['priority' => $priorities[$index]]);
            }
        });

        $this->flushCachedTableRecords();
    }
}
