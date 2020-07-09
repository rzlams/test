import * as soap from 'soap';

class SOAP {
  constructor() {}

  static client(args: Object | null, funcName: string) {
    const url = 'http://127.0.0.1/www/laravel_projects/test_payco/SOAP_service/public/soap/payco?wsdl';

    soap.createClient(url, function(err, client) {
      client[funcName](args, function(err: any, result: any) {
          console.log(result.return.$value);
          return result;
      });
    });
  }
}

export default SOAP;
