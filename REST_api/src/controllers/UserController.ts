import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import SOAP from "../libs/SOAP";
import { User } from "../interfaces";


export class UserController {
  public registroCliente = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { name, password, documento, email, celular } = req.body;
      const user: User = { name, password, documento, email, celular };

      SOAP.client(req, res, next, { user }, 'registroCliente');
    } catch (error) {
      next(error);
    }
  };

  public consultaSaldo = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const user: User = { id: req.params.id };

      SOAP.client(req, res, next, { user }, 'consultaSaldo');
    } catch (error) {
      next(error);
    }
  }

  public login = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { documento, password } = req.body;
      const user: User = { documento, password };

      SOAP.client(req, res, next, { user }, 'login');
    } catch (error) {
      next(error);
    }
  }

  public logout = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const user: User = { session_token: req.body.session_token };

      SOAP.client(req, res, next, { user }, 'logout');
    } catch (error) {
      next(error);
    }
  }
}
