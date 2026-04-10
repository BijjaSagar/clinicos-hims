<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrescriptionItem;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendPillReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:pill-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch automated WhatsApp pill reminders based on frequency schedules (e.g., 1-1-1, 1-0-1)';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsApp)
    {
        $this->info('Starting Pill Reminders check...');
        $now = now('Asia/Kolkata');
        $currentHour = $now->hour;

        $bucket = $this->getBucket($currentHour);
        if (!$bucket) {
            $this->info("Current hour ({$currentHour}) is not in a designated medication window. Skipping.");
            return Command::SUCCESS;
        }

        $this->info("Processing {$bucket} medication window...");

        // Get prescriptions that are finalized and valid
        $prescriptions = \App\Models\Prescription::with(['patient', 'drugs', 'clinic'])
            ->where('status', 'finalised')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->filter(fn($p) => $p->isValid());

        $sentCount = 0;
        foreach ($prescriptions as $prescription) {
            $patient = $prescription->patient;
            if (!$patient || !$patient->phone) continue;

            // Filter drugs apply to this bucket
            $drugs = $prescription->drugs->filter(fn($d) => $this->shouldTakeDoseNow($d->frequency, $bucket));
            if ($drugs->isEmpty()) continue;

            // Deduplicate: Don't send the same bucket reminder twice per day
            $alreadySent = \App\Models\WhatsappMessage::where('patient_id', $patient->id)
                ->where('trigger_type', \App\Models\WhatsappMessage::TRIGGER_PILL_REMINDER)
                ->where('body', 'like', "%({$bucket})%")
                ->whereDate('created_at', now()->toDateString())
                ->exists();

            if ($alreadySent) {
                Log::info("Reminder already sent to patient {$patient->id} for {$bucket} today. Skipping.");
                continue;
            }

            // Send grouped reminder
            try {
                $drugList = $drugs->map(fn($d) => "• *" . ($d->drug_name ?? 'Medicine') . "* - " . ($d->dose ?? $d->strength ?? ''))
                    ->join("\n");
                
                $clinicName = $prescription->clinic->name ?? 'Clinic';
                $msg = "💊 *Pill Reminder ({$bucket})*\n\nHello {$patient->name}, here are your medicines scheduled for now:\n\n{$drugList}\n\nPlease take them as directed by {$clinicName}. Get well soon! 🙏";

                $response = $whatsApp->sendSessionTextWithFallback($patient->phone, $msg, 'pill_reminder', [
                    $patient->name,
                    $drugList,
                    $bucket
                ]);

                // Log it
                \App\Models\WhatsappMessage::create([
                    'clinic_id' => $prescription->clinic_id,
                    'patient_id' => $patient->id,
                    'direction' => 'outbound',
                    'wa_message_id' => $response['wa_message_id'] ?? null,
                    'wa_phone_to' => $response['to'] ?? $patient->phone,
                    'message_type' => 'text',
                    'trigger_type' => \App\Models\WhatsappMessage::TRIGGER_PILL_REMINDER,
                    'body' => $msg,
                    'status' => !empty($response['success']) ? 'sent' : 'failed',
                    'error_message' => empty($response['success']) ? ($response['error'] ?? 'API error') : null,
                    'sent_at' => !empty($response['success']) ? now() : null,
                    'created_at' => now(),
                ]);

                if (!empty($response['success'])) {
                    $sentCount++;
                }
            } catch (\Throwable $e) {
                Log::error('SendPillReminders error', ['patient' => $patient->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info("Successfully sent {$sentCount} grouped reminders for {$bucket}.");
        return Command::SUCCESS;
    }

    private function getBucket(int $hour): ?string
    {
        if ($hour >= 8 && $hour <= 11)  return 'morning';
        if ($hour >= 13 && $hour <= 16) return 'afternoon';
        if ($hour >= 20 && $hour <= 23) return 'night';
        return null;
    }

    /**
     * Evaluate if 1-1-1 / TDS / BD applies to the current bucket.
     */
    private function shouldTakeDoseNow(?string $frequency, string $bucket): bool
    {
        if (!$frequency) {
            return false;
        }

        $freq = strtoupper(trim($frequency));

        // Format mapping (Morning = index 0, Afternoon = index 1, Night = index 2)
        // Parses: 1-1-1, 1 - 1 - 1, 1x0x1, 1/0/1
        if (preg_match('/^(\d)\s*\D+\s*(\d)\s*\D+\s*(\d)$/', $freq, $matches)) {
            $morning = (int) $matches[1] > 0;
            $afternoon = (int) $matches[2] > 0;
            $night = (int) $matches[3] > 0;

            if ($bucket === 'morning') return $morning;
            if ($bucket === 'afternoon') return $afternoon;
            if ($bucket === 'night') return $night;
        }

        // Standard Medical Abbreviations mapping
        $mapping = [
            'OD' => ['morning'],
            'BD' => ['morning', 'night'],
            'TDS' => ['morning', 'afternoon', 'night'],
            'QID' => ['morning', 'afternoon', 'night'], // Simplified
            'HS' => ['night'],
        ];

        if (isset($mapping[$freq])) {
            return in_array($bucket, $mapping[$freq]);
        }

        return false;
    }

    /**
     * Check if visitDate + duration >= now
     */
    private function isPrescriptionActive(string $visitDate, ?string $durationStr): bool
    {
        if (!$durationStr) {
            return false;
        }

        $visitDate = Carbon::parse($visitDate);
        $days = 0;

        if (preg_match('/(\d+)\s*(day|week|month)s?/i', $durationStr, $matches)) {
            $num = (int) $matches[1];
            $unit = strtolower($matches[2]);
            if ($unit === 'day') $days = $num;
            if ($unit === 'week') $days = $num * 7;
            if ($unit === 'month') $days = $num * 30;
        } elseif (is_numeric($durationStr)) {
            $days = (int) $durationStr;
        }

        // If days is 0 or could not be parsed, default to active for 3 days
        if ($days === 0) {
            $days = 3; 
        }

        return now()->lessThanOrEqualTo($visitDate->copy()->addDays($days)->endOfDay());
    }
}
