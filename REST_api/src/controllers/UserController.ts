import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import { HttpException } from "../libs/ErrorHandler";
import SOAP from "../libs/SOAP";


export class UserController {
  public index = async (req: Request, res: Response, next: NextFunction) => {
    try {
      SOAP.client({name: 'otrovalue'}, 'hello');
      res.status(204);
      //throw new HttpException(500, "Internal server error");
    } catch (error) {
      next(error);
    }
  };

  public create = async (req: Request, res: Response, next: NextFunction) => { }
  public store = async (req: Request, res: Response, next: NextFunction) => { }
  public show = async (req: Request, res: Response, next: NextFunction) => { }
  public edit = async (req: Request, res: Response, next: NextFunction) => { }
  public update = async (req: Request, res: Response, next: NextFunction) => { }
  public destroy = async (req: Request, res: Response, next: NextFunction) => { }
}
