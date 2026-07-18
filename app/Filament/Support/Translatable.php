<?php

namespace App\Filament\Support;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

/**
 * Helpers to edit Spatie translatable JSON attributes in Filament while the
 * admin runs in a single locale (Spanish by default). The value shown/edited
 * is the current locale; other locales are preserved on save.
 *
 * Note: the official filament/spatie-laravel-translatable-plugin does not yet
 * support Filament v4, so this lightweight bridge is used instead.
 */
class Translatable
{
    protected static function locale(): string
    {
        return app()->getLocale();
    }

    protected static function read(mixed $state): string
    {
        if (is_array($state)) {
            // Locale map (e.g. ['es' => ..., 'en' => ...]).
            if (array_key_exists(self::locale(), $state) || array_key_exists('es', $state) || array_key_exists('en', $state)) {
                $value = $state[self::locale()] ?? $state['es'] ?? reset($state);

                return is_array($value) ? implode("\n", $value) : (string) $value;
            }

            // Plain list of items -> one per line.
            return implode("\n", $state);
        }

        return (string) ($state ?? '');
    }

    protected static function write(string $name): callable
    {
        return function (mixed $state, ?Model $record) use ($name): array {
            $existing = $record ? $record->getTranslations($name) : [];

            return array_merge($existing, [self::locale() => $state]);
        };
    }

    public static function text(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->formatStateUsing(fn (mixed $state) => self::read($state))
            ->dehydrateStateUsing(self::write($name));
    }

    public static function area(string $name, string $label, int $rows = 4): Textarea
    {
        return Textarea::make($name)
            ->label($label)
            ->rows($rows)
            ->formatStateUsing(fn (mixed $state) => self::read($state))
            ->dehydrateStateUsing(self::write($name));
    }

    public static function markdown(string $name, string $label): MarkdownEditor
    {
        return MarkdownEditor::make($name)
            ->label($label)
            ->formatStateUsing(fn (mixed $state) => self::read($state))
            ->dehydrateStateUsing(self::write($name));
    }
}
