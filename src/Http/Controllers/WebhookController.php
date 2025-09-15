<?php

namespace Schorpy\TriggerDev\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;


use Freemius\Laravel\Events\WebhookHandled;
use Freemius\Laravel\Events\WebhookReceived;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Symfony\Component\HttpFoundation\Response;


final class WebhookController extends Controller
{
    /**
     * Create a new WebhookController instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('triggerdev.secret_key')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a Freemius webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        $payload = $request->all();

        if (! isset($payload['type'])) {
            return new Response('Webhook received but no event name was found.');
        }

        $method = 'handle' . Str::studly(str_replace('.', '_', $payload['type']));

        WebhookReceived::dispatch($payload);

        if (method_exists($this, $method)) {
            try {
                $this->{$method}($payload);
            } catch (BadRequestHttpException $e) {
                return new Response($e->getMessage(), 400);
            } catch (NotFound $e) {
                return new Response($e->getMessage(), 404);
            } catch (\Exception $e) {
                return new Response(sprintf('Internal server error: %s', $e->getMessage()), 500);
            }

            WebhookHandled::dispatch($payload);

            return new Response('Webhook was handled.');
        }

        return new Response('Webhook received but no handler found.');
    }
}
