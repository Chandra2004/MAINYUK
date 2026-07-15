<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompletePastBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:complete-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks active bookings as completed if their scheduled time has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $todayDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        // Bookings before today, OR today but time_end is before current time
        $bookingsToComplete = \App\Models\Booking::where('status', 'active')
            ->where(function ($query) use ($todayDate, $currentTime) {
                $query->where('date', '<', $todayDate)
                      ->orWhere(function ($q) use ($todayDate, $currentTime) {
                          $q->where('date', '=', $todayDate)
                            ->where('time_end', '<', $currentTime);
                      });
            })->get();

        $count = $bookingsToComplete->count();

        if ($count > 0) {
            foreach ($bookingsToComplete as $booking) {
                $booking->update(['status' => 'completed']);
            }
            $this->info("Successfully completed {$count} past bookings.");
        } else {
            $this->info('No past active bookings found to complete.');
        }
    }
}
