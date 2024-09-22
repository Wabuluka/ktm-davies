import { Button } from '@chakra-ui/react';
import { FC } from 'react';
import { ButtonProps } from '../../../Types';

export const DangerButton: FC<ButtonProps> = ({ children, ...props }) => {
  return (
    <Button color="white" bg="red.500" {...props}>
      {children}
    </Button>
  );
};
