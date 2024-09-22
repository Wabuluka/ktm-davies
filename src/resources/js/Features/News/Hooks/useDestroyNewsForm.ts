import { News } from '@/Features/News/Types';
import { useToast } from '@chakra-ui/react';
import { useForm } from '@inertiajs/react';
import { useCallback } from 'react';

type Props = {
  news: News;
};

export const useDestroyNewsForm = ({ news }: Props) => {
  const { delete: destroy, processing } = useForm();
  const toast = useToast();
  const onSuccess = useCallback(() => {
    const title = `${news.title}をDeleted successfully`;
    toast({ title, status: 'success' });
  }, [news.title, toast]);
  const onError = useCallback(() => {
    const title = `Failed to delete`;
    toast({ title, status: 'error' });
  }, [toast]);
  const onDestory = useCallback(() => {
    destroy(route('news.destroy', { news }), { onSuccess, onError });
  }, [destroy, news, onError, onSuccess]);

  return {
    onDestory,
    processing,
  };
};
