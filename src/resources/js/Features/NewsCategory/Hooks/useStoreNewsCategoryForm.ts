import { useNewsCategoryForm } from '@/Features/NewsCategory/Hooks/useNewsCategoryForm';
import { Site } from '@/Features/Site';
import { useToast } from '@chakra-ui/react';
import { useCallback } from 'react';

type Props = {
  site: Site;
};

export function useStoreNewsCategoryForm({ site }: Props) {
  const { data, errors, setData, post, processing } = useNewsCategoryForm();
  const toast = useToast();
  const onSuccess = useCallback(() => {
    const title = `Created ${data.name}`;
    toast({ title, status: 'success' });
  }, [data.name, toast]);
  const onError = useCallback(() => {
    const title = `Failed to save`;
    toast({ title, status: 'error' });
  }, [toast]);
  const onSubmit = useCallback(() => {
    post(route('sites.news-categories.store', { site }), {
      onSuccess,
      onError,
    });
  }, [onError, onSuccess, post, site]);

  return {
    data,
    errors,
    setData,
    onSubmit,
    processing,
  };
}
