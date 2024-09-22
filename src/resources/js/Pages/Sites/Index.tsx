import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Text } from '@chakra-ui/react';

export default function Index() {
  return <Text color="red">TODO: Implements Sites/Index page.</Text>;
}

Index.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="サイト設定">
    {page}
  </AuthenticatedSitePageLayout>
);
