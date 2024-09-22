import { ResetPasswordProps } from '@/Features/Auth';
import { ResetPasswordForm } from '@/Features/Auth/Components/ResetPasswordForm';
import { GuestLayout } from '@/Layouts/Guest';
import { Box } from '@chakra-ui/react';

export default function ResetPassword({ token, email }: ResetPasswordProps) {
  return (
    <GuestLayout title="Reset password">
      <Box w="100%">
        <ResetPasswordForm token={token} email={email} />
      </Box>
    </GuestLayout>
  );
}
