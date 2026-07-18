<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Lead;
use App\Models\Profile;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact', [
            'profile' => Profile::current(),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Honeypot filled -> silently drop but pretend success.
        if (! empty($request->input('website'))) {
            return back()->with('contact_success', true);
        }

        $lead = Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'country' => $data['country'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'need_type' => $data['need_type'] ?? null,
            'estimated_value' => $data['estimated_value'] ?? null,
            'source' => 'website',
            'status' => 'new',
        ]);

        $lead->logActivity('created', 'Lead creado desde el formulario web');

        $this->notifyAdmins($lead);

        return back()->with('contact_success', true);
    }

    protected function notifyAdmins(Lead $lead): void
    {
        try {
            $to = Profile::current()->email ?: config('mail.from.address');
            if ($to) {
                Mail::raw(
                    "Nuevo contacto: {$lead->name} <{$lead->email}>\n\n{$lead->message}",
                    fn ($m) => $m->to($to)->subject('Nuevo lead: '.$lead->name)
                );
            }

            $admins = User::query()->whereIn('role', User::ROLES)->get();
            if ($admins->isNotEmpty()) {
                FilamentNotification::make()
                    ->title('Nuevo lead recibido')
                    ->body($lead->name.' · '.$lead->email)
                    ->sendToDatabase($admins);
            }
        } catch (\Throwable $e) {
            Log::warning('Lead notification failed: '.$e->getMessage());
        }
    }
}
