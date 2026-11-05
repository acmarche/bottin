<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Exports\SelectedCategoriesShopsExport;
use App\Models\Category;
use App\Models\Selection;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

use function array_intersect;
use function array_keys;
use function array_merge;
use function collect;
use function count;
use function date;
use function in_array;
use function response;
use function sys_get_temp_dir;
use function tempnam;

/**
 * @phpstan-type CategoryNode array{id: int, name: string, state: string, children: array<int, mixed>}
 */
final class ExportShops extends Page
{
    /** @var array<int, int> */
    #[Locked]
    public array $selectedCategoryIds = [];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'Shops';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Export de fiches';

    protected static ?string $title = 'Export de fiches';

    protected string $view = 'filament.pages.export-shops';

    public function mount(): void
    {
        $this->selectedCategoryIds = Selection::query()
            ->where('user', $this->selectionOwner())
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Checks the category and all its descendants, or unchecks them when they are all checked.
     */
    public function toggleCategory(int $categoryId): void
    {
        $subtreeIds = Category::query()->findOrFail($categoryId)->descendantsAndSelfIds();

        $this->saveSelection($subtreeIds->diff($this->selectedCategoryIds)->isEmpty()
            ? collect($this->selectedCategoryIds)->diff($subtreeIds)->values()->all()
            : collect($this->selectedCategoryIds)->merge($subtreeIds)->unique()->values()->all());
    }

    public function clearSelection(): void
    {
        $this->saveSelection([]);
    }

    public function exportAction(): Action
    {
        return Action::make('export')
            ->label('Exporter les fiches')
            ->icon('tabler-file-spreadsheet')
            ->color('success')
            ->disabled(fn (): bool => $this->selectedCategoryIds === [])
            ->modalHeading('Colonnes à exporter')
            ->modalSubmitActionLabel('Exporter')
            ->schema($this->columnsSchema())
            ->action(function (array $data, Action $action): BinaryFileResponse {
                $selectedColumns = collect($data)->flatten()->all();

                if ($selectedColumns === []) {
                    Notification::make()
                        ->title('Sélectionnez au moins une colonne')
                        ->danger()
                        ->send();

                    $action->halt();
                }

                return $this->downloadXlsx($selectedColumns);
            });
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'tree' => $this->categoryTree(),
            'selectedCategoriesCount' => count($this->selectedCategoryIds),
            'selectedShopsCount' => new SelectedCategoriesShopsExport($this->selectedCategoryIds)->count(),
        ];
    }

    /** @param  array<int, int>  $categoryIds */
    private function saveSelection(array $categoryIds): void
    {
        $owner = $this->selectionOwner();

        DB::transaction(function () use ($categoryIds, $owner): void {
            Selection::query()->where('user', $owner)->delete();

            Selection::query()->insert(collect($categoryIds)
                ->map(fn (int $categoryId): array => ['category_id' => $categoryId, 'user' => $owner])
                ->all());
        });

        $this->selectedCategoryIds = $categoryIds;
    }

    private function selectionOwner(): string
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->username;
    }

    /** @return array<int, Section> */
    private function columnsSchema(): array
    {
        $schema = [];

        foreach (SelectedCategoriesShopsExport::availableColumns() as $group => $columns) {
            $isDefaultGroup = in_array($group, ['Business', 'Catégories'], true);

            $schema[] = Section::make($group)
                ->collapsed(! $isDefaultGroup)
                ->schema([
                    CheckboxList::make($group)
                        ->hiddenLabel()
                        ->options($columns)
                        ->default($isDefaultGroup ? array_keys($columns) : [])
                        ->columns(2),
                ]);
        }

        return $schema;
    }

    /** @param  array<int, string>  $selectedColumns */
    private function downloadXlsx(array $selectedColumns): BinaryFileResponse
    {
        $export = new SelectedCategoriesShopsExport($this->selectedCategoryIds, $selectedColumns);

        $tempPath = tempnam(sys_get_temp_dir(), 'export_');

        $writer = new XlsxWriter();
        $writer->openToFile($tempPath);
        $writer->addRow(Row::fromValues($export->headings()));

        foreach ($export->collection() as $shop) {
            $writer->addRow(Row::fromValues($export->map($shop)));
        }

        $writer->close();

        return response()->download($tempPath, 'fiches-categories-'.date('Y-m-d').'.xlsx')->deleteFileAfterSend();
    }

    /** @return array<int, CategoryNode> */
    private function categoryTree(): array
    {
        $categoriesByParent = Category::query()
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name'])
            ->groupBy(fn (Category $category): int => (int) $category->parent_id);

        return $this->buildNodes(0, $categoriesByParent)['nodes'];
    }

    /**
     * @param  Collection<int|string, covariant Collection<int, Category>>  $categoriesByParent
     * @return array{nodes: array<int, CategoryNode>, ids: array<int, int>}
     */
    private function buildNodes(int $parentId, Collection $categoriesByParent): array
    {
        $nodes = [];
        $ids = [];

        foreach ($categoriesByParent->get($parentId, []) as $category) {
            $descendants = $this->buildNodes($category->id, $categoriesByParent);
            $subtreeIds = array_merge([$category->id], $descendants['ids']);

            $nodes[] = [
                'id' => $category->id,
                'name' => $category->name,
                'state' => $this->selectionState($subtreeIds),
                'children' => $descendants['nodes'],
            ];

            $ids = array_merge($ids, $subtreeIds);
        }

        return ['nodes' => $nodes, 'ids' => $ids];
    }

    /** @param  array<int, int>  $subtreeIds */
    private function selectionState(array $subtreeIds): string
    {
        $selectedCount = count(array_intersect($subtreeIds, $this->selectedCategoryIds));

        return match ($selectedCount) {
            0 => 'unchecked',
            count($subtreeIds) => 'checked',
            default => 'partial',
        };
    }
}
