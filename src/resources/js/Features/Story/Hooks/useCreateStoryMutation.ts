import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { StoryFormData } from '@/Features/Story';

export const useCreateStoryMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, StoryFormData>({
    mutationFn: async (story) => {
      const formData = new FormData();
      formData.append('title', story.title);

      if (story.trial_url) {
        formData.append('trial_url', story.trial_url);
      }

      story.creators.forEach((creator, i) => {
        formData.append(`creators[${i}][id]`, creator.id);
        formData.append(`creators[${i}][sort]`, String(creator.sort));
      });

      formData.append('thumbnail[operation]', story.thumbnail.operation);
      if (story.thumbnail) {
        if (story.thumbnail.operation === 'set') {
          formData.append('thumbnail[file]', story.thumbnail.file);
        }
      }

      return axios.post(route('stories.store'), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
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
