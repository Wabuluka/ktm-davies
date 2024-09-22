import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation } from 'react-query';
import { ExternalLinkFormData } from '../Types';

export function useCreateExternalLinkMutation() {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, ExternalLinkFormData>({
    mutationFn: ({ title, url, thumbnail }) => {
      const formData = new FormData();
      formData.append('title', title);
      formData.append('url', url);
      formData.append('thumbnail[operation]', thumbnail.operation);
      if (thumbnail.operation === 'set') {
        formData.append('thumbnail[file]', thumbnail.file);
      }

      return axios.post(route('external-links.store'), formData, {
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
}
