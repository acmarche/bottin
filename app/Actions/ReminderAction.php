<?php

declare(strict_types=1);

namespace App\Actions;

use App\Filament\Resources\Shops\Schemas\ShopForm;
use App\Mails\ReminderMail;
use App\Models\Shop;
use App\Models\User;
use Exception;
use Filament\Actions\Action as ActionAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

final class ReminderAction
{
    public static function createAction(Model|Shop $shop): ActionAction
    {
        $defaultRecipients = [];

        return ActionAction::make('reminder')
            ->label('Envoyer un mail')
            ->icon('tabler-school-bell')
            ->modal()
            ->modalDescription('Envoyer un mail aux agents')
            ->modalHeading('Où en sommes-nous actuellement ?')
            ->modalContentFooter(new HtmlString('Un lien vers l\'action sera automatiquement ajouté'))
            ->schema(
                ShopForm::fieldsReminder()
            )
            ->fillForm([
                'recipients' => $defaultRecipients,
            ])
            ->action(function (array $data, Shop $shop) {
                $emails = User::query()
                    ->whereIn('id', $data['recipients'])
                    ->pluck('email')
                    ->unique()
                    ->values();

                if ($emails->isEmpty()) {
                    $emails = collect(['jf@marche.be']);
                }

                try {
                    Mail::to($emails)
                        ->send(new ReminderMail($shop, $data));
                } catch (Exception $e) {
                    report($e);
                }
            });
    }
}
