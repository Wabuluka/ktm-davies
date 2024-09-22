import ApplicationLogo from '@/UI/Components/MediaAndIcons/ApplicationLogo';
import { Box, Flex, FlexProps, Square } from '@chakra-ui/react';
import React from 'react';

type Props = Omit<FlexProps, 'h' | 'height'> & {
  height: FlexProps['height'];
};

export const Header: React.FC<Props> = ({ height, children, ...props }) => {
  return (
    <Flex
      as="header"
      align="center"
      color="white"
      bg="cyan.500"
      boxShadow="lg"
      height={height}
      zIndex={1}
      {...props}
    >
      <Square size={height}>
        <ApplicationLogo />
      </Square>

      <Box flexGrow={1} p={4}>
        {children}
      </Box>
    </Flex>
  );
};
