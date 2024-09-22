import { ChevronDownIcon, ChevronUpIcon } from '@chakra-ui/icons';
import {
  Box,
  Button,
  Collapse,
  Flex,
  Heading,
  Hide,
  useDisclosure,
} from '@chakra-ui/react';

import { Head } from '@inertiajs/react';
import { FC } from 'react';
import { LayoutProps } from '../Types';
import { Footer } from './Footer';
import { Header } from './Header';
import { Navigation } from './Navigation';

export const AuthenticatedLayout: FC<LayoutProps> = ({
  title,
  pageCategory,
  navigation,
  children,
}) => {
  const headerHeight = { base: 12, lg: 20 };
  const navWidth = navigation ? '24rem' : 20;

  const { isOpen, onToggle } = useDisclosure();

  return (
    <>
      {title && <Head title={title} />}

      <Header height={headerHeight} pos="fixed" top={0} w="100%" zIndex={2}>
        <Heading
          as="h1"
          fontSize={{ base: 'xl', lg: '2xl' }}
          wordBreak="break-word"
          noOfLines={1}
          mx={{ base: 2, lg: 4 }}
        >
          {title}
        </Heading>
      </Header>

      <Flex mt={headerHeight} flexDir={{ base: 'column', lg: 'row' }}>
        <Box
          as="aside"
          w={{ base: '100%', lg: navWidth }}
          h={{ base: 'auto', lg: '100%' }}
          borderColor="gray.600"
          borderRightWidth={{ base: 0, lg: 2 }}
          pos="fixed"
          zIndex={2}
        >
          <Box h="100%" pb={{ base: 0, lg: headerHeight.lg }}>
            <Hide below="lg">
              <Navigation pageCategory={pageCategory}>{navigation}</Navigation>
            </Hide>

            <Hide above="lg">
              <Button w="100%" h={10} fontSize={24} onClick={onToggle}>
                {isOpen ? <ChevronUpIcon /> : <ChevronDownIcon />}
              </Button>

              <Collapse in={isOpen}>
                <Navigation pageCategory={pageCategory}>
                  {navigation}
                </Navigation>
              </Collapse>
            </Hide>
          </Box>
        </Box>

        <Box
          w="100%"
          pl={{ base: 0, lg: navWidth }}
          pt={{ base: 4, lg: 0 }}
          flexGrow={1}
        >
          <Box as="main" p={12} minH="100vh">
            {children}
          </Box>

          <Footer />
        </Box>
      </Flex>
    </>
  );
};
