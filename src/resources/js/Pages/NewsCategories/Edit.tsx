import { NewsCategory } from '@/Features/NewsCategory';
import { NewsCategoryForm } from '@/Features/NewsCategory/Components/NewsCategoryForm';
import { useDestroyNewsCategoryForm } from '@/Features/NewsCategory/Hooks/useDestroyNewsCategoryForm';
import { useUpdateNewsCategoryForm } from '@/Features/NewsCategory/Hooks/useUpdateNewsCategoryForm';
import { AuthenticatedSitePageLayout } from '@/Layouts/Authenticated';
import { Heading } from '@/UI/Components/Typography/Heading';
import { VStack } from '@chakra-ui/react';
import { BsNewspaper } from 'react-icons/bs';

type Props = {
  category: NewsCategory;
};

export default function Edit({ category }: Props) {
  const { data, errors, clearErrors, setData, processing, onSubmit } =
    useUpdateNewsCategoryForm({
      category,
    });
  const { onDestory, ...destroyForm } = useDestroyNewsCategoryForm({
    category,
  });
  function handleDestroy() {
    clearErrors();
    onDestory();
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    destroyForm.clearErrors();
    onSubmit();
  }

  return (
    <VStack spacing={12} align="stretch">
      <Heading as="h2" icon={<BsNewspaper />}>
        Update News Category
      </Heading>
      <NewsCategoryForm
        data={data}
        errors={errors}
        setData={setData}
        processing={processing}
        onSubmit={handleSubmit}
        destroy={{
          handler: handleDestroy,
          errors: destroyForm.errors,
          processing: destroyForm.processing,
        }}
      />
    </VStack>
  );
}

Edit.layout = (page: React.ReactNode) => (
  <AuthenticatedSitePageLayout title="News Category">
    {page}
  </AuthenticatedSitePageLayout>
);
