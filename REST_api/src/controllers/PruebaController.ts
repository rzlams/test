import { Request, Response, NextFunction } from "express"; // eslint-disable-line

export class PruebaController {
  private previewPath: string;
  private files: string[];
  private dirs: string[];
  private currentPath: string;

  constructor() {
    this.previewPath = "";
    this.files = [];
    this.dirs = [];
    this.currentPath = "";
  }

  public downloadView = async (
    req: Request,
    res: Response,
    next: NextFunction
  ) => {
    try {
    } catch (error) {
      next(error);
    }
  };
}
