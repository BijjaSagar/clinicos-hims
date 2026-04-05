<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Bed;
use App\Models\IpdAdmission;
use App\Models\PharmacyDispensing;
use App\Models\PharmacyStock;
use App\Models\WhatsappMessage;
use App\Observers\AuditableObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent N+1 queries in non-production environments.
        Model::preventLazyLoading(! app()->isProduction());

        // Prevent silently discarding attributes not in $fillable.
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        // Rate limiting for auth POST routes only (see web.php — GET login/register are not throttled).
        // Per-IP limit: enough for typos; still slows brute force. Shared office NAT may need higher values.
        RateLimiter::for('auth', function ($request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        // Rate limiting for API
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiting for webhooks
        RateLimiter::for('webhooks', function ($request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        // Audit logging for clinical models
        IpdAdmission::observe(AuditableObserver::class);
        Bed::observe(AuditableObserver::class);
        PharmacyDispensing::observe(AuditableObserver::class);
        PharmacyStock::observe(AuditableObserver::class);

        View::composer('layouts.app', function ($view): void {
            if (! auth()->check()) {
                $view->with([
                    'headerNotificationCount' => 0,
                    'navBadgeSchedule' => null,
                    'navBadgeWhatsapp' => null,
                ]);

                return;
            }

            $user = auth()->user();
            $cid = $user->clinic_id;
            if (! $cid) {
                $view->with([
                    'headerNotificationCount' => 0,
                    'navBadgeSchedule' => null,
                    'navBadgeWhatsapp' => null,
                ]);

                return;
            }

            try {
                $whUnread = WhatsappMessage::where('clinic_id', $cid)
                    ->where('direction', WhatsappMessage::DIRECTION_INBOUND)
                    ->whereNull('read_at')
                    ->count();

                $schedCount = Appointment::where('clinic_id', $cid)
                    ->where('scheduled_at', '>=', now()->startOfDay())
                    ->where('scheduled_at', '<=', now()->addDays(7)->endOfDay())
                    ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_NO_SHOW])
                    ->count();

                Log::debug('layouts.app composer nav badges', [
                    'clinic_id' => $cid,
                    'whatsapp_unread' => $whUnread,
                    'schedule_7d' => $schedCount,
                ]);

                $view->with([
                    'headerNotificationCount' => $whUnread,
                    'navBadgeSchedule' => $schedCount > 0 ? (string) $schedCount : null,
                    'navBadgeWhatsapp' => $whUnread > 0 ? (string) $whUnread : null,
                ]);
            } catch (\Throwable $e) {
                Log::warning('layouts.app composer failed', ['error' => $e->getMessage()]);
                $view->with([
                    'headerNotificationCount' => 0,
                    'navBadgeSchedule' => null,
                    'navBadgeWhatsapp' => null,
                ]);
            }
        });
    }
}
