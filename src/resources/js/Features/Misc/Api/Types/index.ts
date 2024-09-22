export type LaravelValidationError = {
  message: string;
  errors: Record<string, string[]>;
};
