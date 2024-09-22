import { AuthenticatedSystemPageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, VStack, HStack, Alert, AlertIcon } from '@chakra-ui/react';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { BsPlus, BsShop } from 'react-icons/bs';
import { GoodsStore, GoodsStoreList } from '@/Features/GoodsStore';

type Props = {
  stores: GoodsStore[];
};

export default function Index({ stores }: Props) {
  return (
    <AuthenticatedSystemPageLayout title="Goods Store Setting">
      <VStack spacing={12} align="stretch">
        <Box as="section" w="100%">
          <Heading as="h2" mb={8} icon={<BsShop />}>
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
          <GoodsStoreList stores={stores} />
        </Box>
      </VStack>
    </AuthenticatedSystemPageLayout>
  );
}
