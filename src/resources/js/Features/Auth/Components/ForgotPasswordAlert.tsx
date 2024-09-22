import { Alert, AlertIcon, AlertProps } from '@chakra-ui/react';
import { FC } from 'react';

type Props = Omit<AlertProps, 'status'> & {
  status: string;
};

export const ForgotPasswordAlert: FC<Props> = ({
  status: statusText,
  ...props
}: Props) => {
  if (!statusText) return null;

  const status = statusText === 'passwords.sent' ? 'success' : 'info';

  return (
    <Alert variant="left-accent" status={status} {...props}>
      <AlertIcon />
      {statusText}
    </Alert>
  );
};
