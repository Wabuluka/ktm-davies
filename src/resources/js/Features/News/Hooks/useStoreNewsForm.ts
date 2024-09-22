import { useNewsForm } from '@/Features/News/Hooks/useNewsForm';
import { Site } from '@/Features/Site';
import { useToast } from '@chakra-ui/react';
import { useCallback } from 'react';

type Props = {
  site: Site;
};

export const useStoreNewsForm = ({ site }: Props) => {
  const { data, errors, setData, post, processing } = useNewsForm();
  const toast = useToast();
  const onSuccess = useCallback(() => {
    const title = `Created ${data.title}`;
    toast({ title, status: 'success' });
  }, [data.title, toast]);
  const onError = useCallback(() => {
    const title = `Failed to save`;
    toast({ title, status: 'error' });
  }, [toast]);
  const onSubmit = useCallback(() => {
    post(route('sites.news.store', { site }), { onSuccess, onError });
  }, [onError, onSuccess, post, site]);

  return {
    data,
    errors,
    setData,
    onSubmit,
    processing,
  };
};
