import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import * as soap from 'soap';

class SOAP {
  constructor() {}

  static client = async (
    req: Request,
    res: Response,
    next: NextFunction,
    args: Object | null,
    funcName: string
  ) => {
    const url = process.env.SOAP_URL || 'http://127.0.0.1:8000/soap/payco?wsdl';

    await soap.createClient(url, function(err, client) {
      client[funcName](args, function(err: any, result: any) {
        try{
          console.log(result);
          const code = Number(result.return.code.$value);
          const message = result.return.message.$value;
          const data = result.return.data.$value;

          res.status(code).json({ code, message, data });
        } catch (error) {
          next(error);
        }
      });
    });
  }
}

export default SOAP;
