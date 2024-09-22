import { User } from '@/Features/Auth';
import UpdateUsersForm from '@/Features/Auth/Components/UpdateUsersForm';
import { AuthenticatedSystemPageLayout } from '@/Layouts/Authenticated';

type Props = {
  user: User;
};

export default function Edit({ user }: Props) {
  return <UpdateUsersForm user={user} />;
}

Edit.layout = (page: React.ReactNode) => (
  <AuthenticatedSystemPageLayout title="Edit User">
    {page}
  </AuthenticatedSystemPageLayout>
);
