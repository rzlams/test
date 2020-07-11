<?php

use App\User;
use App\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Mail\ConfirmarPago;
use Carbon\Carbon;


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
        'Registra un nuevo usuario en la plataforma'
    );

    $server->register('recargaBilletera',
        array('transaction' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#recargaBilletera',
        'rpc',
        'encoded',
        'Recarga saldo a la billetera. Ingreso de la plaforma'
    );

    $server->register('solicitaPago',
        array('transaction' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#solicitaPago',
        'rpc',
        'encoded',
        'Solicita un pago a otro usuario registrado'
    );

    $server->register('confirmaPago',
        array('transaction' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#confirmaPago',
        'rpc',
        'encoded',
        'Confirma las transacciones con status = pending'
    );

    $server->register('reenvioCorreoConfirmacion',
        array('transaction' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#reenvioCorreoConfirmacion',
        'rpc',
        'encoded',
        'Reenvia el correo para la aprobacion de una transaccion'
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
            'email' => 'required|unique:users|email|max:255',
            'celular' => 'required|max:20',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            return array('code' => 400, 'message' => 'Error. Por favor verifique sus datos');
        }

		try {
            $newUser = new User;
            $newUser->name = $user['name'];
            $newUser->password = Hash::make($user['password']);
            $newUser->documento = $user['documento'];
            $newUser->email = $user['email'];
            $newUser->celular = $user['celular'];
            $newUser->balance = 0;

			$newUser->save();

			return array( 'code' => 201, 'message' => 'Usuario registrado exitosamente');
		}
    	catch(\Exception $e) {
       		return array('code' => 400, 'message' => 'Error al registrar el usuario');
    	}
    }

    // CASO 1: recargar billetera  -  type = IN  -  status = aproved (inmediatamente)
    // solo se graba un registro con user_action = receive

    // CASO 2: retirar saldo de billetera  -  type = OUT  -  status = aproved (inmediatamente)
    // solo se graba un registro con user_action = send

    // CASO 3: enviar pago  -  type = STAY  -  status = aproved (inmediatamente)
    // se graban dos registros, uno con user_action = send y otro con user_action = receive

    // CASO 4: solicitar pago  -  type = STAY  -  status = pending (hasta que el sender apruebe)
    // se graban dos registros, uno con user_action = send y otro con user_action = receive
    // la diferencia en este caso es el status = pending que el usuario con user_action = send
    // es el unico que puede cambiar a status = aproved o status = denied

    function recargaBilletera($transaction)
    {
        $validator = Validator::make($transaction, [
            'documento' => 'required|max:20',
            'celular' => 'required|max:20',
            'amount' => 'required|min:0|max:1000000000',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            return array('code' => 400, 'message' => 'Error. Por favor verifique sus datos');
        }

		try {
        	$user = User::where('documento', $transaction['documento'])
        				->where('celular', $transaction['celular'])
        				->first();

        	if(empty($user)) {
            	return array('code' => 400, 'message' => 'Error. El usuario no esta registrado');
        	}

        	$newTransaction = new Transaction;
        	$newTransaction->receiver_id = $user->id;
        	$newTransaction->type = config('database.transaction_types.IN');
        	$newTransaction->status = config('database.transaction_status.APROVED');
        	$newTransaction->amount = $transaction['amount'];
        	$newTransaction->save();

        	$user->balance = $user->balance + $transaction['amount'];
        	$user->update();

			return array( 'code' => 201, 'message' => 'Transaccion registrada exitosamente');
		}
    	catch(\Exception $e) {
       		return array('code' => 400, 'message' => 'Error al registrar la transaccion');
    	}
    }

    function solicitaPago($transaction)
    {
        $validator = Validator::make($transaction, [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|min:0|max:1000000000',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            return array('code' => 400, 'message' => 'Error. Por favor verifique sus datos');
        }

		try {
			$user_id = $transaction['sender_id'];
        	$user = User::find($user_id);
			// Verifico que el sender tenga saldo suficiente para el pago solicitado
            if($user->balance < $transaction['amount']) {
                return array('code' => 400, 'message' => 'Saldo insuficiente');
            }

        	$newTransaction = new Transaction;
        	$newTransaction->sender_id = $transaction['sender_id'];
        	$newTransaction->receiver_id = $transaction['receiver_id'];
        	$newTransaction->type = config('database.transaction_types.STAY');
        	$newTransaction->status = config('database.transaction_status.PENDING');
        	$newTransaction->amount = $transaction['amount'];

        	$newTransaction->save();

        	enviaCorreoConfirmacion($user, $newTransaction->id);

        	return array(
			    'code' => 201,
			    'message' => 'Transaccion pendiente. Le enviamos un correo para la aprobacion'
			);
		}
    	catch(\Exception $e) {
       		return array('code' => 400, 'message' => 'Error al registrar la transaccion');
    	}
    }

    function confirmaPago()
    {

        /*
        enviar correo con un boton para confirmar operacion
        el url del correo incluye el jwt, con eso se valida que este logueado el usuario
        y te manda a una vista donde debes meter el token de 6 numeros para confirmar el pago (cae en este metodo)

        el token de confirmacion tiene un tiempo de expiracion que voy a setear en config/app.php (30 minutos)
        si vence hay que darle al usuario la oportunidad de recibir un nuevo correo con un nuevo token
        con el $transaction_id que estan en el correo se puede generar el nuevo correo
        el JWT sirve para manetener la sesion, si expira el usuario se debe autenticar otra vez
        */

        // verifico si el token de confirmacion sigue vigente
        // if(Carbon::parse($date)->lt(Carbon::now())) {}
    }

    function reenvioCorreoConfirmacion($transaction)
    {
        // $transaction trae el sender_id y el transaction_id que estaban en el correo original
        // con esos datos se buscan el usuario y la transaccion para genera un nuevo correo
        // enviaCorreoConfirmacion($user, $transaction_id);
    }

    function generaTokenConfirmacion($n)
    {
    	$characters = '0123456789';
    	$randomString = '';

    	for ($i = 0; $i < $n; $i++) {
        	$index = rand(0, strlen($characters) - 1);
        	$randomString .= $characters[$index];
    	}

    	return (int) $randomString;
	}

	function enviaCorreoConfirmacion($user, $transaction_id)
	{
	    $senderEmail = $user->email; // el usuario que debe aprobar
        $tokenSesion = 'JWT'; // de la sesion actual
        $confirmation_token = generaTokenConfirmacion(6);
	    $expires_at = Carbon::now()->addMinutes(30);
	    Transaction::where('id', $transaction_id)
	    		->update([
	    			'confirmation_token' => $confirmation_token,
	    			'expires_at' => $expires_at,
	    		]);

        // Mail::to($senderEmail)
            // ->send(new ConfirmarPago($tokenSesion, $tokenConfirmacion, $transaction_id));
	}

    $rawPostData = file_get_contents("php://input");
    return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));

});
