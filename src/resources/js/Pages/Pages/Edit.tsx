import { Form } from '@/Features/Page';
import { Page } from '@/Features/Page/Types';
import { Site } from '@/Features/Site/Types';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { VStack } from '@chakra-ui/react';
import { BsPencilFill } from 'react-icons/bs';

type Props = {
  site: Site;
  page: Page;
};

export default function Edit({ site, page }: Props) {
  return (
    <VStack align="stretch" spacing={8}>
      <Heading as="h2" icon={<BsPencilFill />}>
        Edit Site Page
      </Heading>
      <Form key={JSON.stringify({ site, page })} site={site} page={page} />
    </VStack>
  );
}

Edit.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="Edit Site Page">
    {page}
  </AuthenticatedSitePageLayout>
);
