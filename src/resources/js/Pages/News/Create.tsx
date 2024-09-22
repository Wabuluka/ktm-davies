import { NewsForm } from '@/Features/News';
import { useStoreNewsForm } from '@/Features/News/Hooks/useStoreNewsForm';
import { Site } from '@/Features/Site';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { VStack } from '@chakra-ui/react';
import { BsNewspaper } from 'react-icons/bs';

type Props = {
  site: Site;
};

export default function Create({ site }: Props) {
  const { data, errors, setData, processing, onSubmit } = useStoreNewsForm({
    site,
  });
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    onSubmit();
  }

  return (
    <VStack spacing={12} align="stretch">
      <Heading as="h2" icon={<BsNewspaper />}>
        Create News
      </Heading>
      <NewsForm
        data={data}
        errors={errors}
        setData={setData}
        processing={processing}
        site={site}
        onSubmit={handleSubmit}
      />
    </VStack>
  );
}

Create.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="News">{page}</AuthenticatedSitePageLayout>
);
