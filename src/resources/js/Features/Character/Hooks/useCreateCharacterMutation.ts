import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { useToast } from '@chakra-ui/react';
import { CharacterFormData } from '@/Features/Character';

export const useCreateCharacterMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, CharacterFormData>({
    mutationFn: async ({ name, description, series_id, thumbnail }) => {
      const formData = new FormData();
      formData.append('name', name);
      description && formData.append('description', description);
      series_id && formData.append('series_id', series_id.toString());
      formData.append('thumbnail[operation]', thumbnail.operation);
      thumbnail.file && formData.append('thumbnail[file]', thumbnail.file);

      return axios.post(route('characters.store'), formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
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
