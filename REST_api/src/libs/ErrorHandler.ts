import { NextFunction, Request, Response } from "express";

class HttpException extends Error {
  statusCode: number;
  message: string;

  constructor(statusCode: number, message: string) {
    super(message);
    this.statusCode = statusCode;
    this.message = message;
  }
}

const errorHandler = (
  err: HttpException,
  req: Request,
  res: Response,
  next: NextFunction
) => {
  const statusCode = err.statusCode || 500;
  const message = err.message || "Something went wrong";

  res.status(statusCode).send({
    status: "error",
    statusCode,
    message,
  });
};

export { errorHandler, HttpException };
