import { UseToastOptions } from '@chakra-ui/react';

const defaultOptions: UseToastOptions = {
  position: 'top-right',
  duration: 3000,
  isClosable: true,
};

export const toastOptions = { toastOptions: { defaultOptions } };
