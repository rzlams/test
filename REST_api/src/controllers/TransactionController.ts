import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import SOAP from "../libs/SOAP";
import { Transaction } from "../interfaces";

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
      const { sender_id, receiver_id, amount } = req.body;
      const transaction: Transaction = { sender_id, receiver_id, amount };

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
      const jwt = { jwt: 'bearer' }; // req.header('bearer')
      // agregar al cliente SOAP el codigo para enviar el jwt por un header
      // o ver como la libreria de laravel lo puede sacar del body de la request
      const transaction: Transaction = { id: req.body.transactionId };

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
      const jwt = { jwt: 'bearer' }; // req.header('bearer')
      // agregar al cliente SOAP el codigo para enviar el jwt por un header
      // o ver como la libreria de laravel lo puede sacar del body de la request
      const { transactionId, confirmation_token } = req.body;
      const transaction: Transaction = { id: transactionId, confirmation_token };

      SOAP.client(req, res, next, { transaction }, 'confirmaPago')
        .then((result: any) => {
          console.log(result);
        });
    } catch (error) {
      next(error);
    }
  }
}
