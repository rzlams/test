<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmarPago extends Mailable
{
    use Queueable, SerializesModels;

    private $baseUrl;
    public $tokenConfirmacion;
    public $tokenSesion;
    public $transaction_id;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tokenSesion, $tokenConfirmacion, $transaction_id)
    {
        $this->tokenConfirmacion = $tokenConfirmacion;
        $this->baseUrl = env('WSDL_URL', 'http://127.0.0.1/www/laravel_projects/test_payco/SOAP_service/public/soap/payco?wsdl');
        $this->url = $this->baseUrl . ':4444/soap/confirmar-pago?sessionid=' . $tokenSesion. '&transid=' . $transaction_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('confirm@payco.co')
               ->view('confirmarPago');
    }
}
