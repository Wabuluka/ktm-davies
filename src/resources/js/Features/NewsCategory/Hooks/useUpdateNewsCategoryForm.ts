import { useNewsCategoryForm } from '@/Features/NewsCategory/Hooks/useNewsCategoryForm';
import { NewsCategory } from '@/Features/NewsCategory/Types';
import { useToast } from '@chakra-ui/react';
import { useCallback } from 'react';

type Props = {
  category: NewsCategory;
};

export function useUpdateNewsCategoryForm({ category }: Props) {
  const { data, patch, ...rest } = useNewsCategoryForm({
    category,
  });
  const toast = useToast();
  const onSuccess = useCallback(() => {
    const title = `${data.name} was saved successfully`;
    toast({ title, status: 'success' });
  }, [data.name, toast]);
  const onError = useCallback(() => {
    const title = `Failed to save`;
    toast({ title, status: 'error' });
  }, [toast]);
  const onSubmit = useCallback(() => {
    patch(route('news-categories.update', category), {
      onSuccess,
      onError,
    });
  }, [category, onError, onSuccess, patch]);

  return { data, onSubmit, ...rest };
}
