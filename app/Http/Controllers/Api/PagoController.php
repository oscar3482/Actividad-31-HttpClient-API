<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PagoController extends Controller
{
    private function getAccessToken()
    {
        $response = Http::withBasicAuth(
            config('paypal.client_id'),
            config('paypal.secret')
        )->asForm()->post('https://api-m.sandbox.paypal.com/v1/oauth2/token', [
            'grant_type' => 'client_credentials'
        ]);

        return $response->json()['access_token'] ?? null;
    }

    public function iniciarPago(Request $request, $id)
    {
        $pedido = Pedido::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$pedido) {
            return response()->json(['error' => 'Pedido no encontrado'], 404);
        }

        if ($pedido->estado_pago === 'pagado') {
            return response()->json(['error' => 'Este pedido ya fue pagado'], 400);
        }

        $token = $this->getAccessToken();

        if (!$token) {
            return response()->json(['error' => 'Error al conectar con PayPal'], 500);
        }

        $response = Http::withToken($token)
            ->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'MXN',
                        'value' => number_format($pedido->total, 2, '.', '')
                    ],
                    'description' => 'Pago de pedido #' . $pedido->id
                ]],
                'application_context' => [
                    'return_url' => url('/api/pagos/' . $pedido->id . '/ejecutar'),
                    'cancel_url' => url('/api/pagos/' . $pedido->id . '/cancelar'),
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $approvalUrl = collect($data['links'])->firstWhere('rel', 'approve')['href'];
            return response()->json([
                'approval_url' => $approvalUrl,
                'order_id'     => $data['id']
            ]);
        }

        return response()->json(['error' => 'Error al crear pago en PayPal'], 500);
    }

    public function ejecutarPago(Request $request, $id)
{
    \Log::info('PayPal callback recibido', $request->all());

    $pedido = Pedido::find($id);

    if (!$pedido) {
        return redirect(env('CLIENT_URL') . '/pedidos?error=Pedido no encontrado');
    }

    $token = $this->getAccessToken();
    $orderId = $request->token;

    \Log::info('Order ID de PayPal: ' . $orderId);

    // Usar CURL directamente para evitar problemas con Laravel HTTP
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$orderId}/capture");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ]);
    $responseBody = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    \Log::info('Respuesta PayPal capture curl', ['status' => $httpCode, 'body' => $responseBody]);

    $data = json_decode($responseBody, true);

    if ($httpCode === 201 || $httpCode === 200) {
        $transaccionId = $data['purchase_units'][0]['payments']['captures'][0]['id'] ?? $orderId;

        $pedido->update([
            'transaccion_id' => $transaccionId,
            'estado_pago'    => 'pagado',
            'fecha_pago'     => now(),
        ]);

        return redirect(env('CLIENT_URL') . '/pedidos?success=Pago realizado correctamente');
    }

    return redirect(env('CLIENT_URL') . '/pedidos?error=Error al procesar el pago');
}

    public function cancelarPago($id)
    {
        return redirect(env('CLIENT_URL') . '/pedidos?error=Pago cancelado');
    }
}