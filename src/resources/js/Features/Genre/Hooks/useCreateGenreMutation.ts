import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Genre } from '../Types';
import { formValues } from '../Components/Form';
import { useToast } from '@chakra-ui/react';

export const useCreateGenreMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, formValues, Genre['name']>({
    mutationFn: (genre) => {
      return axios.post(route('genre.store', { name: genre.name }));
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
