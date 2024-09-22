import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Series } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useDeleteSeriesMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Series['id']>({
    mutationFn: (id) => {
      return axios.delete(route('series.destroy', id));
    },
    onSuccess: () => {
      toast({ title: 'Deleted successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to delete', status: 'error' });
    },
  });
};
