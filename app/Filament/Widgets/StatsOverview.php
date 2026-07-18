<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Leads nuevos', Lead::where('status', 'new')->count())
                ->description('Sin gestionar')
                ->color('info'),
            Stat::make('Seguimientos próximos', Lead::whereNotNull('next_follow_up_at')
                ->whereBetween('next_follow_up_at', [now(), now()->addWeek()])->count())
                ->description('Próximos 7 días')
                ->color('warning'),
            Stat::make('Tareas vencidas', Task::overdue()->count())
                ->color(Task::overdue()->count() > 0 ? 'danger' : 'success'),
            Stat::make('Proyectos publicados', Project::where('status', 'published')->count())
                ->description(Project::where('status', 'draft')->count().' en borrador')
                ->color('success'),
        ];
    }
}
