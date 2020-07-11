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

    	$server->wsdl->addComplexType(
    		'User',
    		'complexType',
    		'struct',
    		'all',
    		'',
    		array(
        		'name' => array('name' => 'name', 'type' => 'xsd:string'),
        		'password' => array('name' => 'password', 'type' => 'xsd:string'),
        		'documento' => array('name' => 'documento', 'type' => 'xsd:string'),
        		'email' => array('name' => 'email', 'type' => 'xsd:string'),
        		'celular' => array('name' => 'celular', 'type' => 'xsd:string')
    		)
		);

		$server->wsdl->addComplexType(
    		'Response',
    		'complexType',
    		'struct',
    		'all',
    		'',
    		array(
        		'code' => array('name' => 'code', 'type' => 'xsd:int'),
        		'message' => array('name' => 'message', 'type' => 'xsd:string')
    		)
		);

		$server->wsdl->addComplexType(
    		'strArray',
    		'complexType',
    		'array',
    		'',
    		'SOAP-ENC:Array',
    		array(),
    		array(
        		array(
            		'ref' => 'SOAP-ENC:arrayType',
            		'wsdl:arrayType' => 'xsd:string[]'
        		)
    		),
    		'xsd:integer'
		);

    	$server->register('registroCliente',
        	array('user' => 'tns:User'),
        	array('return' => 'tns:Response'),
        	'urn:payco',
        	'urn:payco#hello',
        	'rpc',
        	'encoded',
        	'Registra un usuario'
    	);

    	$rawPostData = file_get_contents("php://input");
    	return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
    	}
}
