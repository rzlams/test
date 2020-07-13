<?php

use App\User;
use App\Transaction;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Mail\ConfirmarPago;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;


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
        	'data' => array('name' => 'data', 'type' => 'xsd:string')
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

    $server->register('registroCliente', // string $name the name of the PHP function, class.method or class..method
        // array $in assoc array of input values: key = param name, value = param type
        array('user' => 'tns:strArray'),
        // array $out assoc array of output values: key = param name, value = param type
        array('return' => 'tns:Response'), // response
        // mixed $namespace the element namespace for the method or false
        'urn:payco',
        // mixed $soapaction the soapaction for the method or false
        'urn:payco#registroCliente',
        // mixed $style optional (rpc|document) or false Note: when 'document' is specified,
        // parameter and return wrappers are created for you automatically
        'rpc',
        // mixed $use optional (encoded|literal) or false
        'encoded',
        // string $documentation optional Description to include in WSDL
        'Registra un nuevo usuario en la plataforma'
    );

    $server->register('login',
        array('credentials' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#login',
        'rpc',
        'encoded',
        'Inicia sesion y crea session_token'
    );

    $server->register('logout',
        array('user' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#logout',
        'rpc',
        'encoded',
        'Cierra sesion e invalida session_token'
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

    $server->register('enviaCorreoConfirmacion',
        array('transaction' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#enviaCorreoConfirmacion',
        'rpc',
        'encoded',
        'Envia correo para la aprobacion de una transaccion'
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

    $server->register('consultaSaldo',
        array('user' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#consultaSaldo',
        'rpc',
        'encoded',
        'Calcular y actualiza el balance del usuario'
    );

    $server->register('listarTransaccionesPendientes',
        array('user' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#listarTransaccionesPendientes',
        'rpc',
        'encoded',
        'Lista las transacciones pendientes del usuario recibido en el input'
    );

    $server->register('listarUsuarios',
        array('user' => 'tns:strArray'),
        array('return' => 'tns:Response'),
        'urn:payco',
        'urn:payco#listarUsuarios',
        'rpc',
        'encoded',
        'Lista los usuarios disponibles para recibir solicitudes de pago'
    );

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
            $errors = $validator->errors()->toArray();
            $data = Arr::flatten($errors)[0];
            return SOAPResponse(400, 'Error. Por favor verifique sus datos', $data);
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

			return SOAPResponse( 201, 'Usuario registrado exitosamente');
		}
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al registrar el usuario');
    	}
    }

    function login($credentials)
    {
        $validator = Validator::make($credentials, [
            'password' => 'required|min:6|max:32',
            'documento' => 'required|max:20',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $data = Arr::flatten($errors)[0];
            return SOAPResponse(400, 'Error. Por favor verifique sus datos', $data);
        }

		try {
            $user = User::where('documento', $credentials['documento'])->first();

            if (Hash::check($credentials['password'], $user->password)) {
                // Tuve problemas para implementar la libreria de JWT asi que
                // para poder cumplir con el plazo de entrega, solo genero el jwt
                // y lo controlo como el confirmation_token sin usar refresh token
                // JWTAuth::attempt($credentials);
                $session_token = generaToken(8);
                $expires_at = Carbon::now()->addMinutes(20);
                $user->expires_at = $expires_at;
                $user->session_token = $session_token;
                $user->update();

				return SOAPResponse(200, 'Login exitoso', $session_token);
            }

            return SOAPResponse( 400, 'Error. Credenciales incorrectas');
		}
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al autenticar usuario');
    	}
    }

    function logout($user)
    {
		try {
			// JWTAuth::refresh($jwt);
			// JWTAuth::parseToken($jwt);
			// $jwt = JWTAuth::invalidate($user['jwt']);
			$user = User::where('session_token', $user['session_token'])->first();
			$user->expires_at = Carbon::now();
			$user->update();

			return SOAPResponse(200, 'Sesion finalizada');
		}
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al cerrar sesion');
    	}
    }

    function recargaBilletera($transaction)
    {
        $validator = Validator::make($transaction, [
            'documento' => 'required|max:20',
            'celular' => 'required|max:20',
            'amount' => 'required|min:1|max:1000000000',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $data = Arr::flatten($errors)[0];
            return SOAPResponse(400, 'Error. Por favor verifique sus datos', $data);
        }

		try {
        	$user = User::where('documento', $transaction['documento'])
        				->where('celular', $transaction['celular'])
        				->first();

        	if(empty($user)) {
            	return SOAPResponse(400, 'Error. El usuario no esta registrado');
        	}

        	$newTransaction = new Transaction;
        	$newTransaction->receiver_id = $user->id;
        	$newTransaction->type = config('database.transaction_types.IN');
        	$newTransaction->status = config('database.transaction_status.APROVED');
        	$newTransaction->amount = $transaction['amount'];
        	$newTransaction->save();

        	$user->balance = $user->balance + $transaction['amount'];
        	$user->update();

			return SOAPResponse( 201, 'Transaccion registrada exitosamente');
		}
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al registrar la transaccion');
    	}
    }

    function solicitaPago($transaction)
    {
        $validator = Validator::make($transaction, [
            'sender_id' => 'required|exists:users,id',
            'session_token' => 'required',
            'amount' => 'required|min:1|max:1000000000',
        ]);

        if($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $data = Arr::flatten($errors)[0];
            return SOAPResponse(400, 'Error. Por favor verifique sus datos', $data);
        }

		try {
        	$sender = User::find($transaction['sender_id']);
        	$receiver = User::where('session_token', $transaction['session_token'])->first();

            if($sender->balance < $transaction['amount']) {
                return SOAPResponse(400, 'El saldo del usuarios seleccionado es insuficiente. Intente un monto menor');
            }

        	$newTransaction = new Transaction;
        	$newTransaction->sender_id = $sender->id;
        	$newTransaction->receiver_id = $receiver->id;
        	$newTransaction->type = config('database.transaction_types.STAY');
        	$newTransaction->status = config('database.transaction_status.PENDING');
        	$newTransaction->amount = $transaction['amount'];

        	$newTransaction->save();

        	return SOAPResponse(201, 'Transaccion procesada. Pendiente por aprobacion');
		}
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al registrar la transaccion');
    	}
    }

    function confirmaPago($transaction)
    {
        try
		{
        	$transaction = Transaction::where('id', $transaction['id'])
        			->where('confirmation_token', $transaction['confirmation_token'])
        			->where('status', config('database.transaction_status.PENDING'))
        			->first();

        	if(empty($transaction)) {
            	return SOAPResponse(400, 'Error. No existe una transaccion por aprobar con los datos que ingreso');
        	}

        	if(Carbon::parse($transaction->expires_at)->lt(Carbon::now())) {
          		return SOAPResponse(400, 'Error. Expiro el token de confirmacion');
        	}

        	$sender = User::find($transaction->sender_id);

        	if(Carbon::parse($sender->expires_at)->lt(Carbon::now())) {
          		return SOAPResponse(400, 'Error. Expiro el token de sesion');
        	}

        	$sender->balance = $sender->balance - $transaction->amount;
        	$sender->update();

        	$receiver = User::find($transaction->receiver_id);
        	$receiver->balance = $receiver->balance + $transaction->amount;
        	$receiver->update();

        	$transaction->status = config('database.transaction_status.APROVED');
        	$transaction->update();

        	return SOAPResponse(200, 'Transaccion aprobada exitosamente');
        }
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al registrar la transaccion');
    	}
    }

    function consultaSaldo($user)
    {
        try
		{
        	$validator = Validator::make($user, [
                'documento' => 'required|exists:users|max:20',
                'celular' => 'required|exists:users|max:20',
        	]);

        	if($validator->fails()) {
            	$errors = $validator->errors()->toArray();
	            $data = Arr::flatten($errors)[0];
            	return SOAPResponse(400, 'Error. Por favor verifique sus datos', $data);
        	}

        	$user = User::where('documento', $user['documento'])
        				->where('celular', $user['celular'])
        				->first();

        	$transactions = Transaction::where('status', config('database.transaction_status.APROVED'))
        					->where('sender_id', $user->id)
        					->orWhere('receiver_id', $user->id)
        					->get();

        	$transactionsArr = $transactions->toArray();
        	$sendedAmount = 0;
        	$receivedAmount = 0;
        	foreach($transactionsArr as $key => $value)
        	{
        	    if($value['sender_id'] == $user['id']) {
        	        $sendedAmount += $value['amount'];
        	    }
        	    if($value['receiver_id'] == $user['id']) {
        	        $receivedAmount += $value['amount'];
        	    }
        	}

        	$balance = $receivedAmount - $sendedAmount;

        	$user = User::find($user['id']);
        	$user->balance = $balance;
        	$user->update();

        	return SOAPResponse(200, 'Consulta de saldo exitosa', $balance);
		}
    	catch(\Exception $e) {
    		return SOAPResponse(400, 'Error al consultar saldo');
    	}
    }

	function enviaCorreoConfirmacion($transaction)
	{
		try
		{
	    	$user = User::where('session_token', $transaction['session_token'])->first();

	    	if(Carbon::parse($user->expires_at)->lt(Carbon::now())) {
          		return SOAPResponse(400, 'Error. Expiro el token de sesion');
        	}

	    	$transaction = Transaction::where('id', $transaction['id'])
	    			->where('status', config('database.transaction_status.PENDING'))
	    			->first();

	    	if(empty($transaction)) {
	    		return SOAPResponse(400, 'Error. La transaccion no tiene status pendiente');
	    	}

	    	$confirmation_token = generaToken(6);
	    	$expires_at = Carbon::now()->addMinutes(10);
	    	$transaction->confirmation_token = $confirmation_token;
    		$transaction->expires_at = $expires_at;
	    	$transaction->update();

        	Mail::to($user->email)
            	->send(new ConfirmarPago($user, $confirmation_token));

        	return SOAPResponse(200, 'Correo de confirmacion enviado exitosamente');
		}
    	catch(\Exception $e) {
       		return SOAPResponse(400, 'Error al registrar la transaccion');
    	}
	}

	function listarTransaccionesPendientes($user)
	{
	    try
		{
        	$user = User::where('session_token', $user['session_token'])->first();

        	$transactions = Transaction::where('status', config('database.transaction_status.PENDING'))
        					->where('sender_id', $user->id)
        					->get();

        	$transactions = $transactions->pluck('id');
        	$transactions = implode('|&|', $transactions->toArray());

        	if(empty($transactions)) { $transactions = 0; }

        	return SOAPResponse(200, 'Transacciones listadas', $transactions);
		}
    	catch(\Exception $e) {
    		return SOAPResponse(400, 'Error al listar transacciones');
    	}
	}

	function listarUsuarios($user)
	{
	    try
		{
        	$users = User::where('session_token', '!=', $user['session_token'])
        				->orWhereNull('session_token')
        				->get();

        	$users = $users->pluck('id');
        	$users = implode('|&|', $users->toArray());

        	if(empty($users)) { $users = 0; }

        	return SOAPResponse(200, 'Usuarios listados', $users);
		}
    	catch(\Exception $e) {
    		return SOAPResponse(400, 'Error al listar usuarios');
    	}
	}

	function generaToken($n)
    {
    	$characters = '0123456789';
    	$randomString = '';

    	for ($i = 0; $i < $n; $i++) {
        	$index = rand(0, strlen($characters) - 1);
        	$randomString .= $characters[$index];
    	}

    	return (int) $randomString;
	}

	function SOAPResponse($code, $message, $data = '')
	{
		return array('code' => $code, 'message' => $message, 'data' => $data);
	}

    $rawPostData = file_get_contents("php://input");
    return \Response::make($server->service($rawPostData), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));

});

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
