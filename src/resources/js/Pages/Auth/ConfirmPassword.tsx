import { ConfirmPasswordForm } from '@/Features/Auth/Components/ConfirmPasswordForm';
import { AuthenticatedLayout } from '@/Layouts/Authenticated';

export default function ConfirmPassword() {
  return (
    <AuthenticatedLayout title="パスワードの再入力が必要です">
      <ConfirmPasswordForm />
    </AuthenticatedLayout>
  );
}
