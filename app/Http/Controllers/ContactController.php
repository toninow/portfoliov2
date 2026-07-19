<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Lead;
use App\Models\Profile;
use App\Models\User;
use App\Support\Locale;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Http\JsonResponse;
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

    public function store(ContactRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $back = Locale::route('contact').'#estado-contacto';
        $wantsJson = $request->expectsJson() || $request->ajax();

        // Honeypot filled -> silently drop but pretend success.
        if (! empty($request->input('website'))) {
            return $this->successResponse($wantsJson, $back);
        }

        try {
            $reason = $data['need_type'];
            $subject = $data['subject'] ?? null;
            if (in_array($reason, ['project', 'consulting'], true) && ! empty($data['systems'])) {
                $subject = $data['systems'];
            }

            $extras = [];
            if (! empty($data['offer_url'])) {
                $extras[] = 'Oferta: '.$data['offer_url'];
            }
            if (! empty($data['estimated_value']) && $reason === 'job') {
                $extras[] = 'Modalidad: '.$data['estimated_value'];
            }
            $message = $data['message'];
            if ($extras !== []) {
                $message .= "\n\n—\n".implode("\n", $extras);
            }

            $lead = Lead::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'company' => $data['company'] ?? null,
                'country' => $data['country'] ?? null,
                'subject' => $subject,
                'message' => $message,
                'need_type' => $reason,
                'estimated_value' => $data['estimated_value'] ?? null,
                'source' => 'website',
                'status' => 'new',
            ]);

            $lead->logActivity('created', 'Lead creado desde el formulario web');
            $this->notifyAdmins($lead);
        } catch (\Throwable $e) {
            Log::error('Contact form failed: '.$e->getMessage(), ['exception' => $e]);

            if ($wantsJson) {
                return response()->json([
                    'ok' => false,
                    'title' => __('portfolio.contact.error_title'),
                    'message' => __('portfolio.contact.error'),
                ], 500);
            }

            return redirect()->to($back)->withInput()->with('contact_error', true);
        }

        return $this->successResponse($wantsJson, $back);
    }

    protected function successResponse(bool $wantsJson, string $back): JsonResponse|RedirectResponse
    {
        if ($wantsJson) {
            return response()->json([
                'ok' => true,
                'title' => __('portfolio.contact.success_title'),
                'message' => __('portfolio.contact.success'),
            ]);
        }

        return redirect()->to($back)->with('contact_success', true);
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
