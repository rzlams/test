import * as winston from 'winston';
import * as expressWinston from 'express-winston';
/*
const {combine, timestamp, label, printf} = format;

// Logging levels severity from most important (error) to least important.
// error: 0, warn: 1, info: 2, verbose: 3, debug: 4, silly: 5

// Custom log format  -  Visit winston´s logform to see built-in formats
const myFormat = printf(({level, message, label, timestamp}) => {
  return `${timestamp} [${label}] ${level}: ${message}`;
  // return `${level}: ${message}`;
});

// define the custom settings for each transport (file, console)
const options = {
  file: {
    level: 'info',
    filename: `${process.env.PWD}/storage/logs/app.log`,
    handleExceptions: true,
    maxsize: 5242880, // 5MB
    maxFiles: 5,
  },
  console: {
    level: 'info',
    handleExceptions: true,
  },
};

// instantiate a new Winston Logger with the settings defined above
const logger: any = createLogger({
  transports: [
    new transports.File(options.file),
  ],
  exceptionHandlers: [
    new transports.Console(options.console),
  ],
  exitOnError: false, // If false, handled exceptions will not exit the process
  silent: false, // If true, all logs are suppressed
  format: combine(
      label({label: 'my label'}),
      timestamp(),
      myFormat,
  ),
  // format: format.combine(format.splat(), format.simple()),
  // level: 'info', // Log only if level is less than or equal to this level
});

    if (process.env.NODE_ENV !== 'production') {
  logger.add(new transports.Console(options.console));
  // logger.remove(file);
}
*/


const logger = expressWinston.logger({
  transports: [
    new winston.transports.File({
      level: 'debug',
      filename: `${process.env.PWD}/storage/logs/app.log`,
      handleExceptions: true,
      maxsize: 5242880, // 5MB
      maxFiles: 5,
    }),
    new winston.transports.Console({
      level: 'debug',
      handleExceptions: true,
    }),
  ],
  format: winston.format.combine(
      winston.format.colorize(),
      winston.format.json(),
  ),
  meta: true,
  msg: 'HTTP {{req.method}} {{req.url}}',
  expressFormat: true,
  colorize: false,
  ignoreRoute: function(req, res) {
    return false;
  },
});

export default logger;
