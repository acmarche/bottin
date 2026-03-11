<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Pages;

use App\Exports\CategoryShopsExport;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Schemas\CategoryInfolist;
use App\Models\Category;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function collect;

final class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    /** @return array<string, string> */
    public function getBreadcrumbs(): array
    {
        /** @var Category $category */
        $category = $this->record;

        $ancestors = collect();
        $current = $category;

        while ($current->parent !== null) {
            $current = $current->parent;
            $ancestors->prepend($current);
        }

        $breadcrumbs = [
            CategoryResource::getUrl() => 'Catégories',
        ];

        foreach ($ancestors as $ancestor) {
            $breadcrumbs[CategoryResource::getUrl('view', ['record' => $ancestor])] = $ancestor->name;
        }

        $breadcrumbs[] = $category->name;

        return $breadcrumbs;
    }

    public function infolist(Schema $schema): Schema
    {
        return CategoryInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->exportXlsAction(),
            Actions\EditAction::make()
                ->icon('tabler-edit'),
            Actions\DeleteAction::make()
                ->icon('tabler-trash'),
        ];
    }

    private function exportXlsAction(): Action
    {
        $schema = [];

        foreach (CategoryShopsExport::availableColumns() as $group => $columns) {
            $schema[] = Section::make($group)
                ->collapsed()
                ->schema([
                    CheckboxList::make($group)
                        ->label('')
                        ->options($columns)
                        ->columns(2),
                ]);
        }

        return Action::make('exportXls')
            ->label('Export XLS')
            ->icon('tabler-file-spreadsheet')
            ->color('success')
            ->schema($schema)
            ->action(function (array $data): BinaryFileResponse {
                /** @var Category $category */
                $category = $this->record;

                $selectedColumns = collect($data)->flatten()->all();

                $filename = 'category-'.$category->slug.'-'.date('Y-m-d').'.xlsx';

                return Excel::download(
                    new CategoryShopsExport($category, $selectedColumns),
                    $filename,
                );
            });
    }
}
