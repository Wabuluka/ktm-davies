import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Series } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useEditSeriesMutation = () => {
  const toast = useToast();

  return useMutation<
    AxiosResponse,
    AxiosError,
    { id: Series['id']; name: Series['name'] }
  >({
    mutationFn: ({ id, name }) => {
      return axios.put(route('series.update', [id, { name: name }]));
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
