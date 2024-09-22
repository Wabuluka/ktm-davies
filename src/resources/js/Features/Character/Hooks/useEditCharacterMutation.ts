import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { CharacterFormData } from '@/Features/Character';
import { useToast } from '@chakra-ui/react';

export const useEditCharacterMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: number } & CharacterFormData
  >({
    mutationFn: ({ id, name, description, series_id, thumbnail }) => {
      const formData = new FormData();
      formData.append('name', name);
      description && formData.append('description', description);
      series_id && formData.append('series_id', series_id.toString());
      formData.append('thumbnail[operation]', thumbnail.operation);
      thumbnail.file && formData.append('thumbnail[file]', thumbnail.file);

      return axios.post(route('characters.update', id), formData, {
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
