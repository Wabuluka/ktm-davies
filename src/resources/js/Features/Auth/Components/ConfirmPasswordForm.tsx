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
import { useConfirmPassword } from '../Hooks/useConfirmPassword';

export const ConfirmPasswordForm: FC = () => {
  const {
    data: { password },
    onChange,
    onSubmit,
    processing,
    errors,
  } = useConfirmPassword();

  return (
    <form onSubmit={onSubmit}>
      <VStack spacing={4}>
        <FormControl isInvalid={!!errors.password} isRequired>
          <FormLabel>Password</FormLabel>
          <Input
            type="password"
            name="password"
            value={password}
            autoComplete="current-password"
            autoFocus={true}
            onChange={onChange}
          />
          <FormHelperText>
            This is a secure area of the application. Please confirm your
            password before continuing.
          </FormHelperText>
          <FormErrorMessage>{errors.password}</FormErrorMessage>
        </FormControl>

        <Box pt={8}>
          <PrimaryButton type="submit" isLoading={processing}>
            Confirm
          </PrimaryButton>
        </Box>
      </VStack>
    </form>
  );
};
