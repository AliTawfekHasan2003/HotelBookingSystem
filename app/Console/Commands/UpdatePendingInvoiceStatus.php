<?php

namespace App\Console\Commands;

use App\Events\InvoicePaid;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Throwable;

class UpdatePendingInvoiceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-pending-invoice-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update the status of pending invoices for which the payment process hasn’t been completed.";

    /**
     * Execute the console command.
     */

    public function processInvoice($invoice)
    {
        DB::beginTransaction();

        try {

            $payment = PaymentIntent::retrieve($invoice->payment_id);

            $status = $payment->status === 'succeeded' ? 'paid' : 'cancelled';

            $invoice->update([
                'status' => $status,
            ]);

            if ($status === 'paid') {
                event(new InvoicePaid($invoice));
            }
            DB::commit();

            Log::info("Invoice ID {$invoice->id} successfully updated to status {$status}.");
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Error updating invoice ID {$invoice->id}: {$e->getMessage()}");
        }
    }

    public function handle()
    {
        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        $invoices = Invoice::where('status', 'pending')->where('created_at', '<=', $fiveMinutesAgo)->get();

        foreach ($invoices as $invoice) {

            $this->processInvoice($invoice);
        }
    }
}
