import ProfileButton from '@/Features/Auth/Components/ProfileButton';
import {
  PageCategory,
  PageCategoryList,
} from '@/Features/Misc/PageCategoryList';
import { Box, Flex } from '@chakra-ui/react';
import { FC, ReactNode } from 'react';

type Props = {
  pageCategory?: PageCategory;
  children?: ReactNode;
};

export const Navigation: FC<Props> = ({ pageCategory, children }) => {
  return (
    <Flex h="100%" direction={{ base: 'column', lg: 'row' }}>
      <Flex
        as="nav"
        bg="gray.700"
        color="white"
        w={{ base: '100%', lg: 'auto' }}
        direction={{ base: 'row', lg: 'column' }}
        justify="space-between"
      >
        <PageCategoryList pageCagetory={pageCategory} />

        <Box borderColor="gray.500" borderTopWidth={2}>
          <ProfileButton />
        </Box>
      </Flex>

      {children && (
        <Box
          as="nav"
          bg="gray.500"
          color="white"
          w="100%"
          overflowX="scroll"
          whiteSpace="nowrap"
          p={8}
        >
          {children}
        </Box>
      )}
    </Flex>
  );
};
