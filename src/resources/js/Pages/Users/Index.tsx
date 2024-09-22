import { User } from '@/Features/Auth';
import { List } from '@/Features/Auth/Components/List';
import { AuthenticatedSystemPageLayout } from '@/Layouts/Authenticated';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import { Box, Flex, LinkBox, Spacer, VStack } from '@chakra-ui/react';
import { BsPerson, BsPlus } from 'react-icons/bs';
import { Heading } from '@/UI/Components/Typography/Heading';

type Props = {
  users: User[];
};

export default function Index({ users }: Props) {
  return (
    <AuthenticatedSystemPageLayout title="User Setting">
      <VStack spacing={12} align="stretch">
        <Box as="section" w="100%">
          <Heading icon={<BsPerson />} mb={8}>
            User List
          </Heading>
          <Flex w="100%" mb={8}>
            <Spacer />
            <PrimaryButton as={LinkBox} leftIcon={<BsPlus />}>
              <Link href={route('users.create')} overlay>
                New
              </Link>
            </PrimaryButton>
          </Flex>
          <List users={users} />
        </Box>
      </VStack>
    </AuthenticatedSystemPageLayout>
  );
}
