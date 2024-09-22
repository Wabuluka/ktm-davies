import { Box, Text } from '@chakra-ui/react';
import React from 'react';

export const Footer: React.FC = () => {
  return (
    <Box as="footer" color="white" bg="cyan.500">
      <Text align="center">
        COPYRIGHT &copy; FREUDE GIZMO ALL RIGHTS RESERVED.
      </Text>
    </Box>
  );
};
