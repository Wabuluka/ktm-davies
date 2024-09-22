import { Site } from '@/Features/Site/Types';
import { useForm } from '@inertiajs/react';
import { Page } from '../Types';
import { useToast } from '@chakra-ui/react';

type Argument = {
  site: Site;
  page: Partial<Page>;
};

export const useEditPage = ({ site, page }: Argument) => {
  const toast = useToast();
  const { data, setData, put, errors, processing } = useForm<Partial<Page>>({
    title: '',
    slug: '',
    content: '',
    ...page,
  });

  const editPage = (
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) => {
    e.preventDefault();
    put(route('sites.pages.update', { site, page }), {
      onSuccess: () => {
        toast({ title: 'Saved successfully', status: 'success' });
      },
      onError: () => {
        toast({ title: 'Failed to save', status: 'error' });
      },
    });
  };

  return {
    data,
    setData,
    editPage,
    errors,
    processing,
  };
};

export type UseEditPageReturn = ReturnType<typeof useEditPage>;
