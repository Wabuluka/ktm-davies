import { useToast } from '@chakra-ui/react';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useMutation } from 'react-query';
import { ExternalLinkFormData } from '../Types';

export function useEditExternalLinkMutation() {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: string | number } & ExternalLinkFormData
  >({
    mutationFn: ({ id, title, url, thumbnail }) => {
      const formData = new FormData();
      formData.append('title', title);
      formData.append('url', url);
      formData.append('thumbnail[operation]', thumbnail.operation);
      if (thumbnail.operation === 'set') {
        formData.append('thumbnail[file]', thumbnail.file);
      }

      return axios.post(route('external-links.update', id), formData, {
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
}
