<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

use App\BTCPay\Invoice;
use App\Jobs\ProcessNodeIdPayment;
use App\Jobs\SnapshotNode;
use App\Models\Counter;
use App\Models\GenIdJob;
use App\Models\User;
use App\Notifications\IdGenerationStarted;

class WebhookController extends Controller
{
    /**
     * Handle a Stripe webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {

        if ($this->verifyRequest($request)) {
            $invoice = $this->getVerifiedInvoice($request);
            if ($invoice) {

                $payload = json_decode($request->getContent(), true);

                $method = 'handle' . Str::studly($payload['type']);

                if (method_exists($this, $method)) {
                    $response = $this->{$method}($invoice);
                    return $response;
                }

                return $this->missingMethod($payload);
            }
        }
        return new Response;
    }

    /**
     * Handle PaymentReceived
     *
     * @param  array $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleInvoiceReceivedPayment(\BTCPayServer\Result\Invoice $invoice)
    {
    }

    /**
     * Handle PamentReceived
     *
     * @param  array $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleInvoiceSettled(\BTCPayServer\Result\Invoice $invoice)
    {
        $user = User::where('email', $invoice->getData()['metadata']['buyerEmail'])->first();
        if ($invoice->getData()['metadata']['orderId'] == 'product') {

            $posData = json_decode($invoice->getData()['metadata']['posData'], true);
            if (array_key_exists('ids', $posData)) {
                GenIdJob::whereIn('id', explode(",", $posData['ids']))->update([
                    'status' => 'PAYMENT_SUCCESSFUL',
                    'invoice_id' => $invoice->getData()['id']
                ]);

                $gen_id_jobs = GenIdJob::whereIn('id', explode(",", $posData['ids']))->get();

                $user->notify(new IdGenerationStarted($invoice->getData()['amount'], $gen_id_jobs));

                $counter = Counter::first();
                if (!$counter) {
                    $counter = new Counter();
                }
                $counter->consecutiveGenIds = $counter->consecutiveGenIds +  count($gen_id_jobs);
                $counter->save();

                foreach ($gen_id_jobs as $gen_id_job) {
                    ProcessNodeIdPayment::dispatch($gen_id_job)->onQueue('nodeIdPaymentProcessor');
                }
            }
        }
    }


    /**
     * Handle customer subscription created.
     *
     * @param  array $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleConfirmedSubscription(array $payload)
    {
        dd("handleConfirmedSubscription");
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function missingMethod($parameters = [])
    {
        return new Response;
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function verifyRequest(Request $request)
    {

        $sig = $request->header('Btcpay-Sig');
        if ($sig !== "sha256=" . hash_hmac('sha256', $request->getContent(), config('btcpay.webhook-secret'))) {
            throw new \Exception('Invalid BTCPayServer payment notification message received - signature did not match.');
        }

        return true;
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getVerifiedInvoice(Request $request): \BTCPayServer\Result\Invoice
    {

        $content = json_decode($request->getContent(), true);
        if (true === empty($content['invoiceId'])) {
            throw new \Exception('Invalid BTCPayServer payment notification message received - did not receive invoice ID.');
        }

        try {
            $client = new Invoice();
            $invoice = $client->getInvoice(config('btcpay.store-id'), $content['invoiceId']);
        } catch (\Throwable $e) {
            Log::debug('invoice not found');
            return false;
        }

        return $invoice;
    }
}
