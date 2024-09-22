import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  Box,
  FormControl,
  FormErrorMessage,
  FormHelperText,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { FC } from 'react';
import { useForgotPassword } from '../Hooks/useForgotPassword';

export const ForgotPasswordForm: FC = () => {
  const {
    data: { email },
    onChange,
    onSubmit,
    processing,
    errors,
  } = useForgotPassword();

  return (
    <form onSubmit={onSubmit}>
      <VStack spacing={4}>
        <FormControl isInvalid={!!errors.email} isRequired>
          <FormLabel>Email</FormLabel>
          <Input
            type="email"
            name="email"
            autoComplete="email"
            value={email}
            onChange={onChange}
          />
          <FormHelperText>
            Enter the email address associated with your account.
          </FormHelperText>
          <FormErrorMessage>{errors.email}</FormErrorMessage>
        </FormControl>

        <Box pt={8}>
          <PrimaryButton type="submit" isLoading={processing}>
            Send
          </PrimaryButton>
        </Box>
      </VStack>
    </form>
  );
};
