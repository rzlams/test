<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class SoapController extends Controller
{
    public function __construct()
    {

    }

    public function server(Request $request)
    {
    	$server = new \nusoap_server;

    	$server->configureWSDL('wallet.service','urn:payco', $request->url());

    	$server->wsdl->schemaTargetNamespace = 'urn:payco';

    	$server->register('hello', // string $name the name of the PHP function, class.method or class..method
        	// array $in assoc array of input values: key = param name, value = param type
        	array('name' => 'xsd:string'),
        	// array $out assoc array of output values: key = param name, value = param type
        	array('return' => 'xsd:string'), // response
        	// mixed $namespace the element namespace for the method or false
        	'urn:payco',
        	// mixed $soapaction the soapaction for the method or false
        	'urn:payco#hello',
        	// mixed $style optional (rpc|document) or false Note: when 'document' is specified,
        	// parameter and return wrappers are created for you automatically
        	'rpc',
        	// mixed $use optional (encoded|literal) or false
        	'encoded',
         	// string $documentation optional Description to include in WSDL
        	'Say hello'
    	);

    	hello();

    	$rawPostData = file_get_contents("php://input");
    	return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
    	}
}
