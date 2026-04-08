<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Pages;

use App\Exports\CategoryShopsExport;
use App\Exports\TagShopsExport;
use App\Filament\Resources\Tags\Schemas\TagInfolist;
use App\Filament\Resources\Tags\TagResource;
use App\Models\Tag;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function collect;
use function date;

final class ViewTag extends ViewRecord
{
    protected static string $resource = TagResource::class;

    public function infolist(Schema $schema): Schema
    {
        return TagInfolist::configure($schema);
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
            ->label('Export CSV')
            ->icon('tabler-file-spreadsheet')
            ->color('success')
            ->schema($schema)
            ->action(function (array $data): BinaryFileResponse {
                /** @var Tag $tag */
                $tag = $this->record;

                $selectedColumns = collect($data)->flatten()->all();

                $filename = 'tag-'.$tag->slug.'-'.date('Y-m-d').'.csv';

                return Excel::download(
                    new TagShopsExport($tag, $selectedColumns),
                    $filename,
                    \Maatwebsite\Excel\Excel::CSV,
                );
            });
    }
}
