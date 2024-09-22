import { Box, Center, Flex } from '@chakra-ui/react';
import { Head } from '@inertiajs/react';
import React from 'react';

type Props = {
  title?: string;
  children: React.ReactNode;
};

export const GuestLayout: React.FC<Props> = ({ title, children }) => {
  return (
    <>
      {title && <Head title={title} />}

      <Flex h="100vh" bgGradient="linear(to-br, pink.300, cyan.300, cyan.900)">
        <Center h="100%" flexGrow={1}>
          <Box
            w={{ base: 'auto', sm: 'sm', lg: 'lg' }}
            bgColor="white"
            borderRadius={24}
            padding={12}
            boxShadow="md"
          >
            {children}
          </Box>
        </Center>
      </Flex>
    </>
  );
};
