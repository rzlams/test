import { Request, Response, NextFunction } from "express"; // eslint-disable-line
import { HttpException } from "../libs/ErrorHandler";

export class HomeController {
  public index = async (req: Request, res: Response, next: NextFunction) => {
    try {
      console.log('llego a homeController');
      res.status(204);
      //throw new HttpException(500, "Internal server error");
    } catch (error) {
      next(error);
    }
  };
}
