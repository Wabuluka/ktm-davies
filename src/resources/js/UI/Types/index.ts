import { ButtonProps as ChakraButtonProps } from '@chakra-ui/react';

export type ButtonProps = Omit<
  ChakraButtonProps,
  'color' | 'bg' | 'bgColor' | 'backgroundColor'
>;

export type TreeItem<T> = T & {
  isActive?: boolean;
  children?: TreeItem<T>[];
};
