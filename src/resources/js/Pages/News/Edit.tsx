import { News, NewsForm } from '@/Features/News';
import { useDestroyNewsForm } from '@/Features/News/Hooks/useDestroyNewsForm';
import { useUpdateNewsForm } from '@/Features/News/Hooks/useUpdateNewsForm';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { VStack } from '@chakra-ui/react';
import { BsNewspaper } from 'react-icons/bs';

type Props = {
  news: News;
};

export default function Edit({ news }: Props) {
  const { data, errors, setData, processing, onSubmit } = useUpdateNewsForm({
    news,
  });
  const { onDestory, ...destroForm } = useDestroyNewsForm({
    news,
  });
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    onSubmit();
  }

  return (
    <VStack spacing={12} align="stretch">
      <Heading as="h2" icon={<BsNewspaper />}>
        Edit News
      </Heading>
      <NewsForm
        data={data}
        errors={errors}
        setData={setData}
        processing={processing || destroForm.processing}
        newsId={news.id}
        site={news.category.site}
        currentEyecatchUrl={news.eyecatch?.original_url}
        onSubmit={handleSubmit}
        destroy={{
          handler: onDestory,
          processing: destroForm.processing,
        }}
      />
    </VStack>
  );
}

Edit.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="Edit News">
    {page}
  </AuthenticatedSitePageLayout>
);
