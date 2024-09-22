import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import { WarningIcon } from '@chakra-ui/icons';
import {
  Box,
  Center,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  Text,
  VStack,
} from '@chakra-ui/react';
import { usePage } from '@inertiajs/react';
import { useUpdateProfile } from '../Hooks/useUpdateProfile';
import { User } from '../Types';
import { UpdateProfileAlert } from './UpdateProfileAlert';
import { UserAvatar } from './UserAvatar';

type Props = {
  mustVerifyEmail: boolean;
  status: string;
};

export default function UpdateProfileForm({ mustVerifyEmail, status }: Props) {
  const user = usePage<{ auth: { user: User } }>().props.auth.user;
  const { data, setData, onSubmit, errors, processing } = useUpdateProfile();

  return (
    <VStack spacing={4} align="stretch">
      {mustVerifyEmail && user.email_verified_at === null && (
        <VStack>
          <Box>
            <WarningIcon mr={2} />
            Your email address is unverified.
            <Link href={route('verification.send')} method="post">
              <Text decoration="underline">
                Click here to re-send the verification email.
              </Text>
            </Link>
          </Box>
          <UpdateProfileAlert status={status} />
        </VStack>
      )}

      <Box
        bg="gray.100"
        borderRadius={20}
        p={8}
        sx={{ input: { bg: 'white' } }}
      >
        <Center py={4}>
          <UserAvatar username={user.name} size="2xl" />
        </Center>

        <form onSubmit={onSubmit} className="mt-6 space-y-6">
          <VStack spacing={4}>
            <FormControl isInvalid={!!errors.name} isRequired>
              <FormLabel>Name</FormLabel>
              <Input
                type="name"
                name="name"
                autoCapitalize="name"
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                onSubmit={onSubmit}
              />
              <FormErrorMessage>{errors.name}</FormErrorMessage>
            </FormControl>

            <FormControl isInvalid={!!errors.email} isRequired>
              <FormLabel>Email</FormLabel>
              <Input
                type="email"
                name="email"
                autoCapitalize="email"
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                onSubmit={onSubmit}
              />
              <FormErrorMessage>{errors.email}</FormErrorMessage>
            </FormControl>

            <Box textAlign="center">
              <PrimaryButton isLoading={processing} type="submit">
                Save
              </PrimaryButton>
            </Box>
          </VStack>
        </form>
      </Box>
    </VStack>
  );
}
