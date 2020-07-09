import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import { HttpException } from "../libs/ErrorHandler";

export class TransactionController {
  public index = async (req: Request, res: Response, next: NextFunction) => {
    try {
      console.log('llego a transactionController');
      res.status(204);
      //throw new HttpException(500, "Internal server error");
    } catch (error) {
      next(error);
    }
  };
}
