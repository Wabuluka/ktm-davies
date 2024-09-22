import DeleteUserForm from '@/Features/Auth/Components/DeleteUserForm';
import UpdatePasswordForm from '@/Features/Auth/Components/UpdatePasswordForm';
import UpdateProfileForm from '@/Features/Auth/Components/UpdateProfileForm';
import { AuthenticatedLayout } from '@/Layouts/Authenticated';
import { Box, Heading, VStack } from '@chakra-ui/react';

type Props = {
  status: string;
  mustVerifyEmail: boolean;
};

export default function Edit({ status, mustVerifyEmail }: Props) {
  return (
    <AuthenticatedLayout title="My profile">
      <VStack maxW="50rem" m="0 auto" align="stretch" spacing={8}>
        <Box as="section">
          <Heading as="h2" mb={8}>
            Update your profile
          </Heading>
          <UpdateProfileForm
            status={status}
            mustVerifyEmail={mustVerifyEmail}
          />
        </Box>

        <Box as="section">
          <Heading as="h2" mb={8}>
            Update your password
          </Heading>
          <UpdatePasswordForm />
        </Box>

        <Box as="section">
          <Heading as="h2" mb={8}>
            Danger zone
          </Heading>
          <DeleteUserForm />
        </Box>
      </VStack>
    </AuthenticatedLayout>
  );
}
