<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->state(fn (Category $record): string => $record->fullPath())
                    ->searchable()
                    ->sortable(),
                IconColumn::make('principal')
                    ->boolean(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                $this->attachCategoryAction(),
                DetachBulkAction::make(),
            ]);
    }

    /**
     * @return array<Select>
     */
    private static function levelSelects(): array
    {
        $selects = [];

        for ($level = 0; $level <= 4; $level++) {
            $selects[] = Select::make("level_{$level}")
                ->label(__('Niveau').' '.($level + 1))
                ->options(function (Get $get) use ($level): array {
                    if ($level === 0) {
                        return Category::query()
                            ->whereNull('parent_id')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    }

                    $parentId = $get('level_'.($level - 1));

                    if ($parentId === null) {
                        return [];
                    }

                    return Category::query()
                        ->where('parent_id', (int) $parentId)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->visible(function (Get $get) use ($level): bool {
                    if ($level === 0) {
                        return true;
                    }

                    $parentId = $get('level_'.($level - 1));

                    if ($parentId === null) {
                        return false;
                    }

                    return Category::query()
                        ->where('parent_id', (int) $parentId)
                        ->exists();
                })
                ->live()
                ->afterStateUpdated(function (callable $set) use ($level): void {
                    for ($i = $level + 1; $i <= 4; $i++) {
                        $set("level_{$i}", null);
                    }
                });
        }

        return $selects;
    }

    private function attachCategoryAction(): Action
    {
        return Action::make('attach')
            ->label(__('filament-actions::attach.single.label'))
            ->schema([
                Select::make('category_search')
                    ->label(__('Recherche par nom'))
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Category::query()
                            ->leaves()
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (Category $category): array => [
                                $category->id => $category->fullPath(),
                            ])
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function (mixed $value): ?string {
                        $category = Category::find($value);

                        return $category?->fullPath();
                    })
                    ->live(),

                ...self::levelSelects(),

                Toggle::make('principal')
                    ->default(false),
            ])
            ->action(function (array $data): void {
                $categoryId = $this->resolveSelectedCategory($data);

                if ($categoryId === null) {
                    Notification::make()
                        ->title(__('Veuillez sélectionner une catégorie'))
                        ->danger()
                        ->send();

                    return;
                }

                $category = Category::find($categoryId);

                if (! $category instanceof Category || ! $category->isLeaf()) {
                    Notification::make()
                        ->title(__('Seules les catégories finales peuvent être attachées'))
                        ->danger()
                        ->send();

                    return;
                }

                /** @var \App\Models\Shop $shop */
                $shop = $this->getOwnerRecord();

                if ($shop->categories()->where('categories.id', $categoryId)->exists()) {
                    Notification::make()
                        ->title(__('Cette catégorie est déjà attachée'))
                        ->warning()
                        ->send();

                    return;
                }

                $shop->categories()->attach($categoryId, [
                    'principal' => $data['principal'] ?? false,
                ]);

                Notification::make()
                    ->title(__('Catégorie attachée'))
                    ->success()
                    ->send();
            });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveSelectedCategory(array $data): ?int
    {
        if (! empty($data['category_search'])) {
            return (int) $data['category_search'];
        }

        for ($level = 4; $level >= 0; $level--) {
            if (! empty($data["level_{$level}"])) {
                return (int) $data["level_{$level}"];
            }
        }

        return null;
    }
}
