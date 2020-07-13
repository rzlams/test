import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import SOAP from "../libs/SOAP";

export class TransactionController {

  public recargaBilletera = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { documento, celular, amount } = req.body;
      const transaction: any = { documento, celular, amount };

      SOAP.client(req, res, next, { transaction }, 'recargaBilletera')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }

  public solicitaPago = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { sender_id, session_token, amount } = req.body;
      const transaction: any = { sender_id, session_token, amount };

      SOAP.client(req, res, next, { transaction }, 'solicitaPago')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }

  public enviaCorreoConfirmacion = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { transaction_id, session_token } = req.body;
      const transaction: any = { id: transaction_id, session_token };

      SOAP.client(req, res, next, { transaction }, 'enviaCorreoConfirmacion')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }

  public confirmaPago = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const { transaction_id, confirmation_token, session_token } = req.body;
      const transaction: any = {
        id: transaction_id,
        confirmation_token,
        session_token
      };

      SOAP.client(req, res, next, { transaction }, 'confirmaPago')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }

  public listarTransaccionesPendientes = async (req: Request, res: Response, next: NextFunction) => {
    try {
      const transaction: any = { session_token: req.body.session_token };

      SOAP.client(req, res, next, { transaction }, 'listarTransaccionesPendientes')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }
}
