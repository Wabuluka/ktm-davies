import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  Box,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { User } from '../Types';
import { useUpdateUser } from '../Hooks/useUpdateUser';

type Props = {
  user: User;
};

export default function UpdateUsersForm({ user }: Props) {
  const { data, setData, onSubmit, errors, processing } = useUpdateUser({
    user,
  });

  return (
    <form className="mt-6 space-y-6" onSubmit={onSubmit}>
      <Input type="hidden" name="id" value={user.id}></Input>
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

        <FormControl isInvalid={!!errors.password} isRequired>
          <FormLabel>Password</FormLabel>
          <Input
            type="password"
            name="password"
            autoComplete="current-password"
            value={data.password}
            onChange={(e) => setData('password', e.target.value)}
            onSubmit={onSubmit}
          />
          <FormErrorMessage>{errors.password}</FormErrorMessage>
        </FormControl>

        <Box textAlign="center">
          <PrimaryButton isLoading={processing} type="submit">
            Save
          </PrimaryButton>
        </Box>
      </VStack>
    </form>
  );
}
