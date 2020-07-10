<?php

use App\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
    //return view('welcome');
//});

Route::any('soap', function()
{
	// $user = User::where('votes', '>', 100)->firstOrFail();

	// Retrieve the user by the attributes, or create it if it doesn't exist...
	// $user = User::firstOrCreate(['name' => 'John']);

    $server = new soap_server();
    $server->configureWSDL('productservice', 'urn:ProductModel', \Request::url());
    $server->wsdl->schemaTargetNamespaces = 'urn:ProductModel';
    $server->wsdl->addComplexType('Producto', 'complexType', 'struct', 'all', '', array('idproducto' => array('name' => 'idproducto', 'type' => 'xsd:string'), 'titulo' => array('name' => 'titulo', 'type' => 'xsd:string'), 'descripcion' => array('name' => 'descripcion', 'type' => 'xsd:string'), 'precio' => array('name' => 'precio', 'type' => 'xsd:integer'), 'gatosdeenvio' => array('name' => 'gatosdeenvio', 'type' => 'xsd:integer'), 'marca' => array('name' => 'marca', 'type' => 'xsd:string'), 'createdAt' => array('name' => 'createdAt', 'type' => 'xsd:string'), 'iddescuento' => array('name' => 'iddescuento', 'type' => 'xsd:string'), 'idcolor' => array('name' => 'idcolor', 'type' => 'xsd:string'), 'idtalla' => array('name' => 'idtalla', 'type' => 'xsd:string'), 'stock' => array('name' => 'stock', 'type' => 'xsd:string'), 'idsubcategoria' => array('name' => 'idsubcategoria', 'type' => 'xsd:string'), 'idimagen' => array('name' => 'idimagen', 'type' => 'xsd:integer'), 'path' => array('name' => 'path', 'type' => 'xsd:string')));
    $server->wsdl->addComplexType('Productos', 'complexType', 'array', 'sequence', '', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Producto[]')), 'tns:Producto');
	$server->register("ProductModel.selecWithCategorySubcatAndProduct",
		array("category" => "xsd:string", "subcategory" => "xsd:string", "group" => "xsd:string"),
		array("return" => "tns:Productos"),
		"urn:ProductModel",
		"urn:ProductModel#selecWithCategorySubcatAndProduct",
		"rpc",
		"encoded",
		"Get products by category or subcategory"
	);
	$server->register("ProductModel.toArray",
		array("id" => "xsd:string"),
		array("return" => "xsd:Array"),
		"urn:ProductModel",
		"urn:ProductModel#toArray",
		"rpc",
		"encoded",
		"Get products id or all"
	);
    $rawPostData = file_get_contents("php://input");
    return Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});

// Route::any('soap/payco', 'SoapController@server');

Route::any('soap/payco', function(Request $request) {
    $server = new \nusoap_server;

    $server->configureWSDL('wallet.service','urn:payco', Request::url());

    $server->wsdl->schemaTargetNamespace = 'urn:payco';

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
        	'message' => array('name' => 'message', 'type' => 'xsd:string'),
        	'data' => array('name' => 'data', 'type' => 'tns:User')
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
    	'xsd:string'
	);

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

    $server->register('registroCliente',
        array('user' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#registroCliente',
        'rpc',
        'encoded',
        'Registra un usuario'
    );

    $server->register('registroTransaccion',
        array('transaction' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#registroTransaccion',
        'rpc',
        'encoded',
        'Registra un ingreso o egreso de la billetera'
    );

    function hello($name)
    {
        return 'Hello '.$name;
    }

    function registroCliente($user)
    {
        $validator = Validator::make($user, [
            'name' => 'required|max:255',
            'password' => 'required|min:6|max:32',
            'documento' => 'required|unique:users|max:20',
            'email' => 'required|unique:users|max:255',
            'celular' => 'required|max:20',
        ]);

        if($validator->fails()) {
             $errors = $validator->errors();
             myDebug($errors);
            return array('code' => 400, 'message' => 'Error al registrar el usuario');
        }

		try {
        	$userCount = User::where('documento', $user['documento'])
        					->orWhere('email', $user['email'])
        					->count();

        	if($userCount > 0) {
            	return array('code' => 400, 'message' => 'Este usuario ya esta registrado');
        	}

            $newUser = new User;
            $newUser->name = 'a';
            $newUser->password = Hash::make('123456');
            $newUser->email = 'b';
            $newUser->documento = 'c';
            $newUser->celular = 'd';

			$newUser->save();

			return array( 'code' => 201, 'message' => 'Usuario registrado exitosamente');
		}
    	catch(\Exception $e) {
    		myDebug($e->getMessage());
       		return array('code' => 400, 'message' => 'Error al registrar el usuario');
    	}
    }

    function registroTransaccion($transaction)
    {
    myDebug($transaction, true);
        $validator = Validator::make($transaction, [
            'documento' => 'required|unique:users|max:20',
            'celular' => 'required|max:20',
            'amount' => 'required|min:0|max:1000000000',
        ]);

        if($validator->fails()) {
             $errors = $validator->errors();
             myDebug($errors);
            return array('code' => 400, 'message' => 'Error al registrar el usuario');
        }

		try {
        	$userCount = User::where('documento', $user['documento'])
        					->orWhere('email', $user['email'])
        					->count();

        	if($userCount > 0) {
            	return array('code' => 400, 'message' => 'Este usuario ya esta registrado');
        	}

            $newUser = new User;
            $newUser->name = 'a';
            $newUser->password = Hash::make('123456');
            $newUser->email = 'b';
            $newUser->documento = 'c';
            $newUser->celular = 'd';

			$newUser->save();

			return array( 'code' => 201, 'message' => 'Usuario registrado exitosamente');
		}
    	catch(\Exception $e) {
    		myDebug($e->getMessage());
       		return array('code' => 400, 'message' => 'Error al registrar el usuario');
    	}
    }

    $rawPostData = file_get_contents("php://input");
    return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));
});
