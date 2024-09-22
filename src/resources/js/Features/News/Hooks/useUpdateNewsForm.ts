import { useNewsForm } from '@/Features/News/Hooks/useNewsForm';
import { News } from '@/Features/News/Types';
import { useToast } from '@chakra-ui/react';
import { useCallback } from 'react';

type Props = {
  news: News;
};

export const useUpdateNewsForm = ({ news }: Props) => {
  const { data, errors, setData, post, processing } = useNewsForm({
    base: news,
  });
  const toast = useToast();
  const onSuccess = useCallback(() => {
    const title = `${data.title} was saved successfully`;
    toast({ title, status: 'success' });
  }, [data.title, toast]);
  const onError = useCallback(() => {
    const title = `Failed to save`;
    toast({ title, status: 'error' });
  }, [toast]);
  const onSubmit = useCallback(() => {
    post(route('news.update', { news }), {
      forceFormData: true,
      headers: { 'X-HTTP-Method-Override': 'PATCH' },
      onSuccess,
      onError,
    });
  }, [news, onError, onSuccess, post]);

  return {
    data,
    errors,
    setData,
    onSubmit,
    processing,
  };
};
