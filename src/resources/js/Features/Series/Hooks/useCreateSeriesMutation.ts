import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { Series } from '../Types';
import { useToast } from '@chakra-ui/react';

export const useCreateSeriesMutation = () => {
  const toast = useToast();

  return useMutation<AxiosResponse, AxiosError, Series['name']>({
    mutationFn: (name) => {
      return axios.post(route('series.store', { name: name }));
    },
    onSuccess: () => {
      toast({ title: 'Saved successfully', status: 'success' });
    },
    onError: () => {
      toast({ title: 'Failed to save', status: 'error' });
    },
  });
};
