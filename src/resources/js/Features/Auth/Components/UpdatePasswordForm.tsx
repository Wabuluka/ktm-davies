import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  Box,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { useUpdatePassword } from '../Hooks/useUpdatePassword';

export default function UpdatePasswordForm() {
  const {
    data,
    setData,
    onSubmit,
    errors,
    processing,
    passwordInput,
    currentPasswordInput,
  } = useUpdatePassword();

  return (
    <Box bg="gray.100" borderRadius={20} p={8} sx={{ input: { bg: 'white' } }}>
      <form onSubmit={onSubmit}>
        <VStack spacing={4}>
          <FormControl isInvalid={!!errors.current_password} isRequired>
            <FormLabel>Current password</FormLabel>
            <Input
              // id="current_password"
              ref={currentPasswordInput}
              type="password"
              name="current_password"
              value={data.current_password}
              onChange={(e) => setData('current_password', e.target.value)}
              onSubmit={onSubmit}
            />
            <FormErrorMessage>{errors.current_password}</FormErrorMessage>
          </FormControl>

          <FormControl isInvalid={!!errors.password} isRequired>
            <FormLabel>New password</FormLabel>
            <Input
              // id="password"
              ref={passwordInput}
              type="password"
              name="password"
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              onSubmit={onSubmit}
            />
            <FormErrorMessage>{errors.password}</FormErrorMessage>
          </FormControl>

          <FormControl isInvalid={!!errors.password_confirmation} isRequired>
            <FormLabel>New password confirmation</FormLabel>
            <Input
              // id="password"
              type="password"
              name="password_confirmation"
              value={data.password_confirmation}
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
    </Box>
  );
}
