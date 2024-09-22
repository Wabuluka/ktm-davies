import { NewsCategory } from '@/Features/NewsCategory/Types';
import { useToast } from '@chakra-ui/react';
import { useForm } from '@inertiajs/react';
import { useCallback } from 'react';

type Props = {
  category: NewsCategory;
};

export const useDestroyNewsCategoryForm = ({ category }: Props) => {
  const { delete: destroy, ...rest } = useForm<NewsCategory>();
  const toast = useToast();
  const onSuccess = useCallback(() => {
    const title = `${category.name}をDeleted successfully`;
    toast({ title, status: 'success' });
  }, [category.name, toast]);
  const onError = useCallback(() => {
    const title = `Failed to delete`;
    toast({ title, status: 'error' });
  }, [toast]);
  const onDestory = useCallback(() => {
    destroy(route('news-categories.destroy', category), { onSuccess, onError });
  }, [category, destroy, onError, onSuccess]);

  return { onDestory, ...rest };
};

export type UseDestroyNewsCategoryFormReturn = ReturnType<
  typeof useDestroyNewsCategoryForm
>;
