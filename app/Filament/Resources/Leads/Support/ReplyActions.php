<?php

namespace App\Filament\Resources\Leads\Support;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Reusable "reply to a contact message" actions, shared by the messages table
 * and the message view page.
 */
class ReplyActions
{
    /**
     * Reply straight from the admin, sending the email through the app mailer
     * and logging the reply as a CRM activity.
     */
    public static function send(): Action
    {
        return Action::make('replySend')
            ->label('Responder')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->color('primary')
            ->modalHeading('Responder al mensaje')
            ->modalWidth('2xl')
            ->fillForm(fn (Lead $record): array => [
                'to' => $record->email,
                'subject' => 'Re: '.($record->subject ?: 'Tu mensaje'),
                'body' => "Hola {$record->name},\n\nGracias por escribirme.\n\n\n\n—\nAntonio Benalcázar\nantoniobc.net",
            ])
            ->schema([
                TextInput::make('to')
                    ->label('Para')
                    ->email()
                    ->required()
                    ->readOnly(),
                TextInput::make('subject')
                    ->label('Asunto')
                    ->required(),
                Textarea::make('body')
                    ->label('Mensaje')
                    ->rows(10)
                    ->required(),
            ])
            ->action(function (array $data, Lead $record): void {
                try {
                    Mail::raw($data['body'], function ($mail) use ($data) {
                        $mail->to($data['to'])->subject($data['subject']);
                    });
                } catch (\Throwable $e) {
                    Log::warning('Lead reply failed: '.$e->getMessage());
                    Notification::make()
                        ->danger()
                        ->title('No se pudo enviar la respuesta')
                        ->body('Revisa la configuración de correo (SMTP).')
                        ->send();

                    return;
                }

                $record->logActivity('email', 'Respuesta enviada por email · '.$data['subject']);
                $record->update([
                    'contacted_at' => now(),
                    'status' => $record->status === 'new' ? 'contacted' : $record->status,
                ]);

                if (config('mail.default') === 'log') {
                    Notification::make()
                        ->warning()
                        ->title('Respuesta registrada (modo log)')
                        ->body('El correo NO se ha enviado de verdad: el servidor está en modo "log". Configura SMTP para enviarlo realmente.')
                        ->persistent()
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title('Respuesta enviada a '.$record->email)
                    ->send();
            });
    }

    /**
     * Open the reply in your own email client (works right now, no SMTP needed).
     */
    public static function mailto(): Action
    {
        return Action::make('replyMailto')
            ->label('Abrir en mi correo')
            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
            ->color('gray')
            ->url(fn (Lead $record): string => 'mailto:'.$record->email
                .'?subject='.rawurlencode('Re: '.($record->subject ?: 'Tu mensaje'))
                .'&body='.rawurlencode("Hola {$record->name},\n\n"))
            ->openUrlInNewTab(false);
    }
}
