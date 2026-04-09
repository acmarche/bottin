<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Tables;

use App\Models\Tag;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

final class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                ColorColumn::make('color')
                    ->label('Couleur'),
                TextColumn::make('tagGroup.name')
                    ->label('Groupe')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('shops_count')
                    ->label('Sociétés')
                    ->counts('shops')
                    ->sortable(),
                IconColumn::make('private')
                    ->label('Privé')
                    ->falseIcon(false)
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('private')
                    ->label('Privé'),
                SelectFilter::make('tagGroup')
                    ->label('Groupe')
                    ->relationship('tagGroup', 'name')
                    ->preload(),
            ])
            ->groups([
                Group::make('tagGroup.name')
                    ->label('')
                    ->getTitleFromRecordUsing(fn (Tag $record): HtmlString => new HtmlString(
                        '<span class="text-lg font-bold text-primary-600 dark:text-primary-400">-- '.e(
                            $record->tagGroup?->name
                        ).'</span>',
                    )),
            ])
            ->defaultGroup('tagGroup.name')
            ->defaultPaginationPageOption(50)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
