<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use Filament\Resources\ResourceConfiguration;

final class UserResourceConfiguration extends ResourceConfiguration
{
    protected bool $isArchived = false;

    protected ?string $navigationLabel = null;

    protected ?string $navigationGroup = null;

    public function archived(bool $condition = true): static
    {
        $this->isArchived = $condition;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function navigationLabel(string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }
}
