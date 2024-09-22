import { AxiosError } from 'axios';
import { LaravelValidationError } from '../Types';

export const isLaravelValidationError = (
  error: AxiosError | undefined,
): error is AxiosError<LaravelValidationError> => {
  return (
    typeof (error as AxiosError<LaravelValidationError>)?.response?.data
      .message === 'string' &&
    typeof (error as AxiosError<LaravelValidationError>)?.response?.data
      .errors === 'object'
  );
};
