import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Genre } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useEditGenreMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: Genre['id']; name: Genre['name'] }
  >({
    mutationFn: ({ id, name }) => {
      return axios.put(route('genre.update', [id, { name }]));
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
