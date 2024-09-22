import { ForgotPasswordAlert, ForgotPasswordForm } from '@/Features/Auth';
import { GuestLayout } from '@/Layouts/Guest';
import { VStack } from '@chakra-ui/react';

type Props = {
  status: string;
};

export default function ForgotPassword({ status }: Props) {
  return (
    <GuestLayout title="Forgot password">
      <VStack align="stretch">
        <ForgotPasswordAlert status={status} />
        <ForgotPasswordForm />
      </VStack>
    </GuestLayout>
  );
}
