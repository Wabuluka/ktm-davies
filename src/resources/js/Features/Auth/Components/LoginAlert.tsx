import { Alert, AlertIcon, AlertProps } from '@chakra-ui/react';
import { FC, ReactNode } from 'react';

type Props = Omit<AlertProps, 'status'> & {
  status: string;
};

export const LoginAlert: FC<Props> = ({ status, ...props }: Props) => {
  if (!status) return null;

  const [alserStatus, children]: [AlertProps['status'], ReactNode] = ((
    status: string,
  ) => {
    switch (status) {
      case 'passwords.reset':
        return ['success', 'Password reset email has been sent.'];
      case 'user-has-been-deleted':
        return ['success', 'Your account has been deleted.'];
      default:
        return ['info', status];
    }
  })(status);

  return (
    <Alert variant="left-accent" status={alserStatus} {...props}>
      <AlertIcon />
      {children}
    </Alert>
  );
};
