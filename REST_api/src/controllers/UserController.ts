import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import SOAP from "../libs/SOAP";


export class UserController {
  public registroCliente = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { name, password, documento, email, celular } = req.body;
      const user: any = { name, password, documento, email, celular };

      SOAP.client(req, res, next, { user }, 'registroCliente');
    } catch (error) {
      next(error);
    }
  };

  public consultaSaldo = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { documento, celular } = req.body;
      const user: any = { documento, celular };

      SOAP.client(req, res, next, { user }, 'consultaSaldo');
    } catch (error) {
      next(error);
    }
  }

  public login = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { documento, password } = req.body;
      const user: any = { documento, password };

      SOAP.client(req, res, next, { user }, 'login');
    } catch (error) {
      next(error);
    }
  }

  public logout = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const user: any = { session_token: req.body.session_token };

      SOAP.client(req, res, next, { user }, 'logout');
    } catch (error) {
      next(error);
    }
  }

  public listarUsuarios = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const transaction: any = { session_token: req.body.session_token };
console.log(req.body)
      SOAP.client(req, res, next, { transaction }, 'listarUsuarios')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }
}
