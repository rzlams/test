<?php

use Illuminate\Support\Facades\Route;
use App\User;

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
    $server->configureWSDL('productservice', 'urn:ProductModel', Request::url());
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

Route::any('soap/payco', 'SoapController@server');
