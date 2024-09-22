import {
  Drawer as ChakraDrawer,
  DrawerBody,
  DrawerCloseButton,
  DrawerContent,
  DrawerFooter,
  DrawerHeader,
  DrawerOverlay,
  DrawerProps,
} from '@chakra-ui/react';
import React from 'react';

type Props = Omit<DrawerProps, 'children'> & {
  children: [
    header: React.ReactNode,
    body: React.ReactNode,
    footer: React.ReactNode,
  ];
};

export const Drawer = ({
  children: [header, body, footer],
  ...props
}: Props) => {
  return (
    <ChakraDrawer placement="left" size="xl" {...props}>
      <DrawerOverlay />
      <DrawerContent>
        <DrawerCloseButton />
        <DrawerHeader>{header}</DrawerHeader>
        <DrawerBody>{body}</DrawerBody>
        <DrawerFooter>{footer}</DrawerFooter>
      </DrawerContent>
    </ChakraDrawer>
  );
};
