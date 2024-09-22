import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { StoryFormData } from '@/Features/Story';

export const useEditStoryMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: string | number } & StoryFormData
  >({
    mutationFn: ({ id, title, trial_url, creators, thumbnail }) => {
      const formData = new FormData();

      formData.append('title', title);

      formData.append('trial_url', trial_url || '');

      creators.forEach((creator, i) => {
        formData.append(`creators[${i}][id]`, creator.id);
        formData.append(`creators[${i}][sort]`, String(creator.sort));
      });

      formData.append('thumbnail[operation]', thumbnail.operation);
      if (thumbnail.operation === 'set') {
        formData.append('thumbnail[file]', thumbnail.file);
      }

      return axios.post(route('stories.update', id), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'X-HTTP-Method-Override': 'PUT',
        },
      });
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
