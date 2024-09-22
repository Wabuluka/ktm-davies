import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Genre } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useDeleteGenreMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Genre['id']>({
    mutationFn: (id) => {
      return axios.delete(route('genre.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
