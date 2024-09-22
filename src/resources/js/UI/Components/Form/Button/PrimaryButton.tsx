import { ButtonProps } from '@/UI/Types';
import { Button, forwardRef } from '@chakra-ui/react';
import { FC } from 'react';

export const PrimaryButton: FC<ButtonProps> = forwardRef(
  ({ children, ...props }, ref) => {
    return (
      <Button color="white" bg="cyan.500" ref={ref} {...props}>
        {children}
      </Button>
    );
  },
);
