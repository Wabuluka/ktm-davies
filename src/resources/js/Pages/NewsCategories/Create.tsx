import { NewsCategoryForm } from '@/Features/NewsCategory/Components/NewsCategoryForm';
import { useStoreNewsCategoryForm } from '@/Features/NewsCategory/Hooks/useStoreNewsCategoryForm';
import { Site } from '@/Features/Site';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { VStack } from '@chakra-ui/react';
import { BsNewspaper } from 'react-icons/bs';

type Props = {
  site: Site;
};

export default function Create({ site }: Props) {
  const { data, errors, setData, processing, onSubmit } =
    useStoreNewsCategoryForm({
      site,
    });
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    onSubmit();
  }

  return (
    <VStack spacing={12} align="stretch">
      <Heading as="h2" icon={<BsNewspaper />}>
        Create News Category
      </Heading>
      <NewsCategoryForm
        data={data}
        errors={errors}
        setData={setData}
        processing={processing}
        onSubmit={handleSubmit}
      />
    </VStack>
  );
}

Create.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="News Category">
    {page}
  </AuthenticatedSitePageLayout>
);
