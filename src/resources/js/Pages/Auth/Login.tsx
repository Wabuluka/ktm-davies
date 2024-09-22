import { LoginAlert, LoginForm } from '@/Features/Auth';
import { GuestLayout } from '@/Layouts/Guest';
import ApplicationLogo from '@/UI/Components/MediaAndIcons/ApplicationLogo';
import { VStack } from '@chakra-ui/react';

type Props = {
  status: string;
  canResetPassword: boolean;
};

export default function Login({ status, canResetPassword }: Props) {
  return (
    <GuestLayout title="Login">
      <VStack align="stretch">
        <VStack>
          <ApplicationLogo w={48} transparent={true} />
        </VStack>

        <VStack align="stretch" spacing={8}>
          <LoginAlert status={status} />
          <LoginForm canResetPassword={canResetPassword} />
        </VStack>
      </VStack>
    </GuestLayout>
  );
}
