import { AuthenticatedSystemPageLayout } from '@/Layouts/Authenticated';
import CreateUsersForm from '@/Features/Auth/Components/CreateUsersForm';

export default function Create() {
  return <CreateUsersForm />;
}

Create.layout = (page: React.ReactNode) => (
  <AuthenticatedSystemPageLayout title="Create User">
    {page}
  </AuthenticatedSystemPageLayout>
);
