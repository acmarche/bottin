<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\Token;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

final class ManageTokenCommand extends Command
{
    protected $signature = 'bottin:token {action? : generate|regenerate}';

    protected $description = 'Manage merchant login tokens (generate or regenerate)';

    public function handle(): int
    {
        $action = $this->argument('action') ?? select(
            label: 'What would you like to do?',
            options: [
                'generate' => 'Generate a new token',
                'regenerate' => 'Generate a new token',
            ],
        );

        return match ($action) {
            'generate' => $this->generate(),
            'regenerate' => $this->regenerate(),
            default => $this->invalidAction($action),
        };
    }

    private function generate(): int
    {
        $shopId = $this->searchShopWithoutToken();

        if ($shopId === null) {
            $this->components->error('No shops without tokens found.');

            return self::FAILURE;
        }

        $token = $this->createTokenForShop($shopId);
        $this->displayTokenInfo($token);

        return self::SUCCESS;
    }

    private function regenerate(): int
    {
        $shops = Shop::query()
            ->get();

        foreach ($shops as $shop) {
            $token = $this->createTokenForShop($shop->id);
            $this->components->twoColumnDetail(
                $shop->company,
                route('merchant.login', $token->uuid),
            );
        }

        $this->components->info("{$shops->count()} token(s) generated.");

        return self::SUCCESS;
    }

    private function createTokenForShop(int $shopId): Token
    {
        return Token::updateOrCreate(
            ['shop_id' => $shopId],
            [
                'uuid' => Str::uuid()->toString(),
                'password' => Str::random(50),
                'expire_at' => now()->addYear(),
            ],
        );
    }

    private function displayTokenInfo(Token $token): void
    {
        $token->load('shop');
        $this->components->info("Token generated for {$token->shop?->company}");
        $this->components->twoColumnDetail('UUID', $token->uuid);
        $this->components->twoColumnDetail('Expires', $token->expire_at->format('d/m/Y'));
        $this->components->twoColumnDetail('Login URL', route('merchant.login', $token->uuid));
    }

    private function searchShopWithoutToken(): ?int
    {
        $shops = Shop::query()
            ->whereDoesntHave('token')
            ->get();

        if ($shops->isEmpty()) {
            return null;
        }

        return (int) search(
            label: 'Search for a shop',
            options: fn (string $value): array => Shop::query()
                ->whereDoesntHave('token')
                ->where('company', 'like', "%{$value}%")
                ->pluck('company', 'id')
                ->all(),
        );
    }

    private function invalidAction(string $action): int
    {
        $this->components->error("Invalid action: {$action}. Use generate or regenerate.");

        return self::FAILURE;
    }
}
