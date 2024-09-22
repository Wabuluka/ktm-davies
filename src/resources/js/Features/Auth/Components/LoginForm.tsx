import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import {
  Checkbox,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { FC } from 'react';
import { useLogin } from '../Hooks/useLogin';

type Props = {
  canResetPassword?: boolean;
};

export const LoginForm: FC<Props> = ({ canResetPassword = false }) => {
  const {
    data: { email, password, remember },
    processing,
    errors,
    onChange,
    onSubmit,
  } = useLogin();

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
          <FormErrorMessage>{errors.email}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.password} isRequired>
          <FormLabel>Password</FormLabel>
          <Input
            type="password"
            name="password"
            autoComplete="current-password"
            value={password}
            onChange={onChange}
          />
          <FormErrorMessage>{errors.password}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.remember}>
          <Checkbox name="remember" checked={remember} onChange={onChange}>
            Remember me
          </Checkbox>
          <FormErrorMessage>{errors.remember}</FormErrorMessage>
        </FormControl>

        <VStack spacing={4} pt={8}>
          <PrimaryButton type="submit" isLoading={processing}>
            Login
          </PrimaryButton>

          {canResetPassword && (
            <Link
              href={route('password.request')}
              color="gray.700"
              fontSize="sm"
            >
              Forgot your password?
            </Link>
          )}
        </VStack>
      </VStack>
    </form>
  );
};
