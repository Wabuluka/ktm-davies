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
import { useResetPassword } from '../Hooks/useResetPassword';
import { ResetPasswordProps } from '../Types';

export const ResetPasswordForm: FC<ResetPasswordProps> = ({ token, email }) => {
  const {
    data: { email: emailData, password, password_confirmation },
    processing,
    errors,
    onChange,
    onSubmit,
  } = useResetPassword({ token, email });

  return (
    <form onSubmit={onSubmit}>
      <VStack spacing={4}>
        <FormControl isRequired isInvalid={!!errors.email}>
          <FormLabel>Email</FormLabel>
          <Input
            type="email"
            name="email"
            autoComplete="email"
            value={emailData}
            onChange={onChange}
          />
          <FormErrorMessage>{errors.email}</FormErrorMessage>
        </FormControl>

        <FormControl isRequired isInvalid={!!errors.password}>
          <FormLabel>Password</FormLabel>
          <Input
            type="password"
            name="password"
            autoComplete="new-password"
            value={password}
            onChange={onChange}
          />
          <FormHelperText>Enter the new password.</FormHelperText>
          <FormErrorMessage>{errors.password}</FormErrorMessage>
        </FormControl>

        <FormControl isRequired isInvalid={!!errors.password_confirmation}>
          <FormLabel>Confirm password</FormLabel>
          <Input
            type="password"
            name="password_confirmation"
            value={password_confirmation}
            onChange={onChange}
          />
          <FormErrorMessage>{errors.password_confirmation}</FormErrorMessage>
          <FormHelperText>Re-Enter the new password.</FormHelperText>
        </FormControl>

        <Box pt={8}>
          <PrimaryButton type="submit" isLoading={processing}>
            Reset
          </PrimaryButton>
        </Box>
      </VStack>
    </form>
  );
};
