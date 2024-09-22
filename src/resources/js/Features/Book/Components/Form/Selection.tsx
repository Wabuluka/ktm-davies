import { CloseIcon } from '@chakra-ui/icons';
import { Box, HStack, IconButton } from '@chakra-ui/react';
import React from 'react';

type Props = {
  onUnselect?: () => void;
  children?: React.ReactNode;
};

const Selection = ({ onUnselect, children }: Props) => {
  if (!children) return null;

  return (
    <HStack spacing={4}>
      <Box>{children}</Box>
      {!!onUnselect && (
        <IconButton
          as={CloseIcon}
          aria-label="Deselect"
          bg="red.500"
          color="white"
          p={2}
          onClick={onUnselect}
        />
      )}
    </HStack>
  );
};

export default Selection;
