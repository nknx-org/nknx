<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\GenIdJob;
use App\Models\Node;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class PaymentController extends Controller
{
    function ProcessPayment(Request $request)
    {

        $nodes = Node::whereIn('id', $request->input('node_ids'))->get();
        $addresses = [];

        foreach ($nodes as $node) {
            if (!$node->walletAddress) {
                return Redirect::back()->with('error', 'One of your nodes is not generating an ID.');
            }
            array_push($addresses, $node->walletAddress);
        }

        $user = Auth::user();

        try {

            $upper_fluct_limit = $request->input('nkn_price') * 1.01;
            $lower_fluct_limit = $request->input('nkn_price') * 0.99;
            $percent_discount = 0;

            $prices = Cache::remember('prices', 60, function () {

                $apiRequest = Http::get('https://api.coingecko.com/api/v3/simple/token_price/ethereum?contract_addresses=0x5cf04716ba20127f1e2297addcf4b5035000c9eb&vs_currencies=usd%2Ceth&include_market_cap=false&include_24hr_vol=true&include_24hr_change=true&include_last_updated_at=false');
                $response = json_decode($apiRequest->body(), true, 512, JSON_BIGINT_AS_STRING);

                return $response['0x5cf04716ba20127f1e2297addcf4b5035000c9eb'];
            });

            if ($prices['usd'] <= $upper_fluct_limit && $prices['usd'] >= $lower_fluct_limit) {
                $charge_price = $request->input('nkn_price') * ((104 - $percent_discount) / 100) * 10;
                $gen_id_job_ids = [];
                foreach ($nodes as $node) {
                    $gen_id_job = new GenIdJob([
                        'user_id' => $user->id,
                        'node_id' => $node->id,
                        'address' => $node->walletAddress,
                        'ip' => $node->addr,
                        'status' => 'PAYMENT_INITIATED'
                    ]);
                    $gen_id_job->save();
                    array_push($gen_id_job_ids, $gen_id_job->id);
                }

                try {
                    $invoice = $user->createInvoiceForProduct('NKNx node ID generation service', round($charge_price * 100), [
                        'quantity' => count($request->input('node_ids')),
                        'metadata' => [
                            'ids'               => implode(",", $gen_id_job_ids),
                        ],
                        'description' => 'Node id generate'
                    ]);
                } finally {
                    GenIdJob::whereIn('id', $gen_id_job_ids)->update([
                        'status' => 'PAYMENT_PENDING',
                    ]);
                }
            } else {
                throw ValidationException::withMessages(
                    [
                        'nkn_price' => 'The price of our service changed too much since you confirmed payment.'
                    ]
                );
            }
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Error in processing payment');
        }
        return response()->json($invoice->getData()['id']);
    }

}
