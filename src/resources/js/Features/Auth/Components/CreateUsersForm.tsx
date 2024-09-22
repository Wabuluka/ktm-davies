import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  Box,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { useUsers } from '../Hooks/useUsers';

export default function CreateUsersForm() {
  const { setData, onSubmit, processing, errors } = useUsers();
  return (
    <form className="mt-6 space-y-6" onSubmit={onSubmit}>
      <VStack spacing={4}>
        <FormControl isInvalid={!!errors.name} isRequired>
          <FormLabel>Name</FormLabel>
          <Input
            type="name"
            name="name"
            autoCapitalize="name"
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
            onChange={(e) => setData('password', e.target.value)}
            onSubmit={onSubmit}
          />
          <FormErrorMessage>{errors.password}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.password_confirmation} isRequired>
          <FormLabel>Confirmed Password</FormLabel>
          <Input
            type="password"
            name="password_confirmation"
            autoComplete="confirmed-password"
            onChange={(e) => setData('password_confirmation', e.target.value)}
            onSubmit={onSubmit}
          />
          <FormErrorMessage>{errors.password_confirmation}</FormErrorMessage>
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
