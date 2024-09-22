import { Alert, AlertIcon, AlertProps } from '@chakra-ui/react';
import { FC } from 'react';

type Props = Omit<AlertProps, 'status'> & {
  status: string;
};

export const UpdateProfileAlert: FC<Props> = ({
  status: statusText,
  ...props
}: Props) => {
  if (!statusText) return null;

  const status = statusText === 'verification-link-sent' ? 'success' : 'info';

  return (
    <Alert variant="left-accent" status={status} {...props}>
      <AlertIcon />A new verification link has been sent to your email address.
    </Alert>
  );
};
