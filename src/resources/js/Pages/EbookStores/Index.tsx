import { AuthenticatedSystemPageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, VStack, HStack, Alert, AlertIcon } from '@chakra-ui/react';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { BsBookFill, BsPlus } from 'react-icons/bs';
import { EbookStore } from '@/Features/EbookStore';
import { EbookStoreList } from '@/Features/EbookStore/Components/EbookStoreList';

type Props = {
  stores: EbookStore[];
};

export default function Index({ stores }: Props) {
  return (
    <AuthenticatedSystemPageLayout title="Bookstore (which handles eBooks only) Setting">
      <VStack spacing={12} align="stretch">
        <Box as="section" w="100%">
          <Heading as="h2" mb={8} icon={<BsBookFill />}>
            Store List
          </Heading>
          <HStack gap={4} mb={8} alignItems="center">
            <Alert status="info" fontSize="sm">
              <AlertIcon />
              Please contact the development company to add a new bookstore.
            </Alert>
            <PrimaryButton leftIcon={<BsPlus />} isDisabled>
              New
            </PrimaryButton>
          </HStack>
          <EbookStoreList stores={stores} />
        </Box>
      </VStack>
    </AuthenticatedSystemPageLayout>
  );
}
